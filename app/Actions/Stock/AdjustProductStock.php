<?php

namespace App\Actions\Stock;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\StoreActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdjustProductStock
{
    public function handle(Store $store, User $user, int $productId, int $quantity, string $reason): Product
    {
        return DB::transaction(function () use ($store, $user, $productId, $quantity, $reason): Product {
            $product = Product::query()
                ->where('store_id', $store->id)
                ->lockForUpdate()
                ->findOrFail($productId);

            if (! $product->track_stock) {
                throw ValidationException::withMessages([
                    'product_id' => 'Stok produk ini tidak dilacak, jadi tidak dapat disesuaikan.',
                ]);
            }

            $previousStock = $product->stock;
            $newStock = $previousStock + $quantity;

            if ($newStock < 0) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok {$product->name} tidak bisa kurang dari nol. Stok saat ini: {$product->stock}.",
                ]);
            }

            if ($newStock > 4294967295) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah stok melebihi batas yang diizinkan.',
                ]);
            }

            $product->update(['stock' => $newStock]);

            StockMovement::query()->create([
                'store_id' => $store->id,
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => StockMovementType::Adjustment,
                'quantity' => $quantity,
                'stock_after' => $newStock,
                'reason' => trim($reason),
            ]);

            StoreActivityLog::query()->create([
                'store_id' => $store->id,
                'user_id' => $user->id,
                'action' => 'stock.adjusted',
                'subject_type' => 'product',
                'subject_id' => $product->id,
                'subject_name' => $product->name,
                'description' => 'Mengubah stok dari '.$previousStock.' menjadi '.$newStock.' '.$product->unit,
                'details' => ['before' => $previousStock, 'after' => $newStock, 'quantity' => $quantity, 'reason' => trim($reason)],
            ]);

            return $product->refresh();
        }, 3);
    }
}
