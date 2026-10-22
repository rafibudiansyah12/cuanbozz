<?php

namespace App\Actions\Catalog;

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionGroup;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\StoreActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateProduct
{
    public function handle(Store $store, User $user, array $attributes): Product
    {
        return DB::transaction(function () use ($store, $user, $attributes): Product {
            $categoryName = trim((string) ($attributes['category'] ?? ''));
            $category = $categoryName === ''
                ? null
                : Category::query()->firstOrCreate(['store_id' => $store->id, 'name' => $categoryName]);

            $product = Product::query()->create([
                'store_id' => $store->id,
                'category_id' => $category?->id,
                'name' => $attributes['name'],
                'description' => $attributes['description'] ?? null,
                'sku' => $attributes['sku'] ?? null,
                'price' => (int) $attributes['price'],
                'unit' => $attributes['unit'] ?? 'pcs',
                'track_stock' => (bool) ($attributes['track_stock'] ?? false),
                'stock' => (int) ($attributes['track_stock'] ?? false) ? (int) ($attributes['stock'] ?? 0) : 0,
                'low_stock_threshold' => (int) ($attributes['low_stock_threshold'] ?? 0),
                'is_active' => true,
            ]);

            if ($product->track_stock && $product->stock > 0) {
                StockMovement::query()->create([
                    'store_id' => $store->id,
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => StockMovementType::Initial,
                    'quantity' => $product->stock,
                    'stock_after' => $product->stock,
                    'reason' => 'Stok awal produk',
                ]);
            }

            foreach ($attributes['option_groups'] ?? [] as $groupIndex => $groupData) {
                $group = ProductOptionGroup::query()->create([
                    'store_id' => $store->id,
                    'product_id' => $product->id,
                    'name' => $groupData['name'],
                    'selection_type' => $groupData['selection_type'],
                    'is_required' => (bool) ($groupData['is_required'] ?? false),
                    'min_select' => (int) $groupData['min_select'],
                    'max_select' => filled($groupData['max_select'] ?? null) ? (int) $groupData['max_select'] : null,
                    'sort_order' => $groupIndex,
                ]);

                foreach ($groupData['options'] as $optionIndex => $optionData) {
                    ProductOption::query()->create([
                        'store_id' => $store->id,
                        'option_group_id' => $group->id,
                        'name' => $optionData['name'],
                        'price_delta' => (int) $optionData['price_delta'],
                        'is_active' => true,
                        'sort_order' => $optionIndex,
                    ]);
                }
            }

            StoreActivityLog::query()->create([
                'store_id' => $store->id,
                'user_id' => $user->id,
                'action' => 'product.created',
                'subject_type' => 'product',
                'subject_id' => $product->id,
                'subject_name' => $product->name,
                'description' => 'Menambahkan produk'.($product->track_stock ? ' dengan stok awal '.$product->stock.' '.$product->unit : ' dengan stok tidak terbatas'),
                'details' => ['sku' => $product->sku, 'price' => $product->price, 'track_stock' => $product->track_stock],
            ]);

            return $product;
        });
    }
}
