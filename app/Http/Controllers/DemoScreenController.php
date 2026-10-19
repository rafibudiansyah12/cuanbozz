<?php

namespace App\Http\Controllers;

use App\Support\DemoData;
use Illuminate\Http\Request;

class DemoScreenController extends Controller
{
    private const SCREENS = [
        'home' => 'Beranda demo',
        'login' => 'Masuk',
        'register' => 'Daftar',
        'forgot-password' => 'Lupa kata sandi',
        'onboarding' => 'Buat toko',
        'dashboard' => 'Dashboard',
        'pos' => 'Kasir',
        'payment' => 'Pembayaran',
        'receipt' => 'Struk transaksi',
        'products' => 'Produk',
        'product-form' => 'Form produk',
        'categories' => 'Kategori',
        'stock-adjustment' => 'Penyesuaian stok',
        'stock-history' => 'Riwayat stok',
        'orders' => 'Riwayat transaksi',
        'order-detail' => 'Detail transaksi',
        'report' => 'Laporan',
        'store-profile' => 'Profil toko',
        'members' => 'Kelola kasir',
        'appearance' => 'Tampilan toko',
        'admin-stores' => 'Admin · Toko',
        'admin-users' => 'Admin · Pengguna',
        'more' => 'Lainnya',
    ];

    public function __invoke(Request $request, ?string $screen = 'home')
    {
        abort_unless(isset(self::SCREENS[$screen]), 404);

        $role = config('app.env') === 'local' && $request->query('role') === 'cashier'
            ? 'cashier'
            : 'owner';

        $ownerOnlyScreens = ['dashboard', 'report', 'product-form', 'categories', 'stock-adjustment', 'stock-history', 'store-profile', 'members', 'appearance', 'admin-stores', 'admin-users'];

        if ($role === 'cashier' && in_array($screen, $ownerOnlyScreens, true)) {
            abort(404);
        }

        $demo = DemoData::all();
        if ($role === 'cashier') {
            $demo['orders'] = array_values(array_filter($demo['orders'], fn (array $order) => $order['cashier'] === 'Ayu'));
        }

        $query = $role === 'cashier' ? '?role=cashier' : '';
        $roleSwitchScreen = $role === 'owner' && in_array($screen, $ownerOnlyScreens, true)
            ? 'home'
            : $screen;
        $navigation = [
            ['label' => 'Kasir', 'href' => '/demo/pos'.$query, 'icon' => '▦'],
            ['label' => 'Produk', 'href' => '/demo/products'.$query, 'icon' => '□'],
            ['label' => 'Riwayat', 'href' => '/demo/orders'.$query, 'icon' => '◷'],
        ];

        if ($role === 'owner') {
            $navigation[] = ['label' => 'Laporan', 'href' => '/demo/report'.$query, 'icon' => '▤'];
        }

        $navigation[] = ['label' => 'Lainnya', 'href' => '/demo/more'.$query, 'icon' => '•••'];

        return view('demo.screen', [
            'screen' => $screen,
            'screenTitle' => self::SCREENS[$screen],
            'role' => $role,
            'query' => $query,
            'roleSwitchScreen' => $roleSwitchScreen,
            'navigation' => $navigation,
            'demo' => $demo,
        ]);
    }
}
