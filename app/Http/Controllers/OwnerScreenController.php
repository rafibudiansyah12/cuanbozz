<?php

namespace App\Http\Controllers;

use App\Enums\StoreRole;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\StoreActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OwnerScreenController extends Controller
{
    private const TITLES = [
        'dashboard' => 'Beranda',
        'pos' => 'Kasir',
        'products' => 'Produk',
        'product-form' => 'Tambah produk',
        'categories' => 'Kategori',
        'stock-adjustment' => 'Penyesuaian stok',
        'stock-history' => 'Manajemen stok',
        'orders' => 'Riwayat transaksi',
        'activity' => 'Log aktivitas',
        'report' => 'Laporan',
        'store-profile' => 'Profil toko',
        'members' => 'Kelola kasir',
        'appearance' => 'Tampilan toko',
        'payments' => 'Pembayaran Midtrans',
        'more' => 'Menu',
    ];

    private const CASHIER_SCREENS = ['pos', 'products', 'orders', 'more', 'stock-adjustment'];

    private const ADMIN_BLOCKED_SCREENS = ['store-profile', 'members', 'appearance', 'payments'];

    public function show(Request $request): View|RedirectResponse
    {
        $screen = $request->route('screen');
        abort_unless(is_string($screen) && isset(self::TITLES[$screen]) && $screen !== 'dashboard', 404);

        $user = $request->user();
        $store = $user->ownedStores()->first();

        if ($store === null) {
            $store = active_store();
        }

        if ($store === null) {
            return redirect()->route('onboarding.create');
        }

        $membership = $user->storeMemberships()->where('store_id', $store->id)->where('is_active', true)->first();
        $role = $store->owner_id === $user->id ? StoreRole::Owner : $membership?->role;
        $isOwner = $role === StoreRole::Owner;
        $isAdmin = $role === StoreRole::Admin;

        Gate::authorize('view', $store);

        if (! $isOwner && ! $isAdmin && ! in_array($screen, self::CASHIER_SCREENS, true)) {
            abort(403);
        }

        if ($isAdmin && in_array($screen, self::ADMIN_BLOCKED_SCREENS, true)) {
            abort(403);
        }

        abort_unless($screen !== 'activity' || $isOwner, 403);

        $midtransReady = filled(config('services.midtrans.server_key'))
            && filled(config('services.midtrans.client_key'));
        $midtransProduction = (bool) config('services.midtrans.is_production');

        $members = $isOwner
            ? $store->members()
                ->whereIn('role', [StoreRole::Admin->value, StoreRole::Cashier->value])
                ->with('user')
                ->latest()
                ->get()
            : collect();

        $data = [];

        if (in_array($screen, ['pos', 'products', 'stock-adjustment'], true)) {
            $data['products'] = Product::query()
                ->with(['category', 'optionGroups.options'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(fn (Product $product) => [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'category' => $product->category?->name ?? 'Lainnya',
                    'description' => $product->description ?? 'Produk toko',
                    'image_url' => $product->image_path ? asset($product->image_path) : null,
                    'price' => $product->price,
                    'unit' => $product->unit,
                    'stock' => $product->stock,
                    'low_stock_threshold' => $product->low_stock_threshold,
                    'track_stock' => $product->track_stock,
                    'low_stock' => $product->track_stock && $product->stock <= $product->low_stock_threshold,
                    'option_groups' => $product->optionGroups->map(fn ($group) => [
                        'id' => $group->id,
                        'name' => $group->name,
                        'selection_type' => $group->selection_type,
                        'is_required' => $group->is_required,
                        'min_select' => $group->min_select,
                        'max_select' => $group->max_select,
                        'options' => $group->options->where('is_active', true)->values()->map(fn ($option) => [
                            'id' => $option->id,
                            'name' => $option->name,
                            'price_delta' => $option->price_delta,
                        ]),
                    ]),
                ]);
        }

        if ($screen === 'stock-history') {
            $data['inventoryProducts'] = Product::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'unit', 'stock', 'track_stock', 'low_stock_threshold']);
            $data['stockMovements'] = StockMovement::query()
                ->with(['product', 'user'])
                ->latest('created_at')
                ->limit(50)
                ->get();
        }

        if ($screen === 'orders') {
            $orders = Order::query()->with(['items', 'cashier']);

            if (! $isOwner && ! $isAdmin) {
                $orders->where('cashier_id', $user->id);
            }

            $data['orders'] = $orders->latest()->limit(50)->get();
        }

        if ($screen === 'activity') {
            $data['activities'] = StoreActivityLog::query()
                ->with('actor')
                ->latest('created_at')
                ->limit(100)
                ->get();
        }

        return $this->screen($screen, $store, $members, $isOwner, $isAdmin, false, $midtransReady, $midtransProduction, $data);
    }

    public function preview(Request $request, ?string $screen = 'dashboard'): View
    {
        abort_unless(app()->environment('local'), 404);
        abort_unless(is_string($screen) && isset(self::TITLES[$screen]), 404);

        if ($screen === 'dashboard') {
            return view('dashboard', ['previewMode' => true, 'store' => null]);
        }

        return $this->screen($screen, null, collect(), true, false, true, false, false);
    }

    private function screen(string $screen, ?Store $store, $members, bool $isOwner, bool $isAdmin, bool $previewMode, bool $midtransReady, bool $midtransProduction, array $data = []): View
    {
        if ($screen === 'more') {
            return view('owner.menu', [
                'store' => $store,
                'isOwner' => $isOwner,
                'isAdmin' => $isAdmin,
                'previewMode' => $previewMode,
            ]);
        }

        return view('owner.screen', array_merge([
            'screen' => $screen,
            'title' => self::TITLES[$screen],
            'store' => $store,
            'members' => $members,
            'isOwner' => $isOwner,
            'isAdmin' => $isAdmin,
            'previewMode' => $previewMode,
            'midtransReady' => $midtransReady,
            'midtransProduction' => $midtransProduction,
        ], $data));
    }
}
