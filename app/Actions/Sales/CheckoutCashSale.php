<?php

namespace App\Actions\Sales;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\StockMovementType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\StoreActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutCashSale
{
    public function handle(Store $store, User $cashier, array $items, int $cashReceived, ?string $customerName = null, ?string $tableNumber = null, string $serviceType = 'take_away'): Order
    {
        return DB::transaction(function () use ($store, $cashier, $items, $cashReceived, $customerName, $tableNumber, $serviceType): Order {
            $itemRows = array_values($items);
            $quantities = collect($itemRows)
                ->groupBy(fn (array $item) => (int) $item['product_id'])
                ->map(fn ($rows) => $rows->sum(fn (array $item) => (int) $item['quantity']));
            $products = Product::query()
                ->with(['optionGroups.options'])
                ->where('store_id', $store->id)
                ->whereIn('id', $quantities->keys())
                ->where('is_active', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $quantities->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Ada produk yang tidak tersedia di toko ini. Muat ulang daftar produk dan coba lagi.',
                ]);
            }

            $lines = [];
            $subtotal = 0;
            foreach ($itemRows as $index => $item) {
                $product = $products->get((int) $item['product_id']);
                $requestedIds = array_map('intval', $item['options'] ?? []);
                $selectedOptions = $product->optionGroups
                    ->flatMap(fn ($group) => $group->options->where('is_active', true))
                    ->whereIn('id', $requestedIds)
                    ->keyBy('id');

                if ($selectedOptions->count() !== count($requestedIds)) {
                    throw ValidationException::withMessages([
                        "items.{$index}.options" => 'Ada pilihan yang tidak tersedia untuk produk ini.',
                    ]);
                }

                foreach ($product->optionGroups as $group) {
                    $selectedCount = $selectedOptions->where('option_group_id', $group->id)->count();
                    $minimum = $group->is_required ? max(1, $group->min_select) : $group->min_select;
                    $maximum = $group->max_select ?? ($group->selection_type === 'single' ? 1 : PHP_INT_MAX);

                    if ($selectedCount < $minimum || $selectedCount > $maximum || ($group->selection_type === 'single' && $selectedCount > 1)) {
                        throw ValidationException::withMessages([
                            "items.{$index}.options" => "Pilihan pada grup {$group->name} belum sesuai aturan.",
                        ]);
                    }
                }

                $unitPrice = $product->price + $selectedOptions->sum('price_delta');

                if ($unitPrice < 0) {
                    throw ValidationException::withMessages([
                        "items.{$index}.options" => 'Total selisih opsi tidak boleh membuat harga produk negatif.',
                    ]);
                }

                $lineTotal = $unitPrice * (int) $item['quantity'];
                $subtotal += $lineTotal;
                $lines[] = [
                    'product' => $product,
                    'quantity' => (int) $item['quantity'],
                    'note' => $item['note'] ?? null,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'options' => $selectedOptions->values(),
                ];
            }

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);

                if ($product->track_stock && $product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi. Stok tersedia: {$product->stock}.",
                    ]);
                }
            }

            if ($cashReceived < $subtotal) {
                throw ValidationException::withMessages([
                    'cash_received' => 'Uang yang diterima belum cukup untuk membayar total transaksi.',
                ]);
            }

            $order = Order::query()->create([
                'store_id' => $store->id,
                'cashier_id' => $cashier->id,
                'order_number' => 'CB-'.now()->format('ymdHis').'-'.Str::upper(Str::random(5)),
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'cash_received' => $cashReceived,
                'change_amount' => $cashReceived - $subtotal,
                'payment_method' => PaymentMethod::Cash,
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
                'customer_name' => $customerName,
                'table_number' => $tableNumber,
                'service_type' => $serviceType,
                'receipt_token' => Str::random(64),
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];
                $orderItem = OrderItem::query()->create([
                    'store_id' => $store->id,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                    'note' => $line['note'],
                ]);

                foreach ($line['options'] as $option) {
                    $orderItem->options()->create([
                        'store_id' => $store->id,
                        'group_name' => $option->group->name,
                        'option_name' => $option->name,
                        'price_delta' => $option->price_delta,
                    ]);
                }

                if (! $product->track_stock) {
                    continue;
                }

                $product->decrement('stock', $line['quantity']);
                $product->refresh();

                StockMovement::query()->create([
                    'store_id' => $store->id,
                    'product_id' => $product->id,
                    'user_id' => $cashier->id,
                    'order_id' => $order->id,
                    'type' => StockMovementType::Sale,
                    'quantity' => -$line['quantity'],
                    'stock_after' => $product->stock,
                    'reason' => 'Penjualan '.$order->order_number,
                ]);
            }

            StoreActivityLog::query()->create([
                'store_id' => $store->id,
                'user_id' => $cashier->id,
                'action' => 'order.paid',
                'subject_type' => 'order',
                'subject_id' => $order->id,
                'subject_name' => $order->order_number,
                'description' => 'Menyelesaikan transaksi tunai senilai Rp '.number_format($order->total, 0, ',', '.'),
                'details' => ['order_number' => $order->order_number, 'total' => $order->total, 'payment_method' => $order->payment_method->value],
            ]);

            return $order->load(['items', 'cashier']);
        }, 3);
    }
}
