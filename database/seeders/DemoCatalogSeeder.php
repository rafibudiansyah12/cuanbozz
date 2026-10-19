<?php

namespace Database\Seeders;

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemoCatalogSeeder extends Seeder
{
    private const OPTIONS = [
        'COF-001' => [
            ['name' => 'Suhu', 'selection_type' => 'single', 'required' => true, 'min' => 1, 'max' => 1, 'items' => [['Panas', 0], ['Dingin', 0]]],
            ['name' => 'Ukuran', 'selection_type' => 'single', 'required' => true, 'min' => 1, 'max' => 1, 'items' => [['Regular', 0], ['Large', 5000]]],
            ['name' => 'Tambahan', 'selection_type' => 'multiple', 'required' => false, 'min' => 0, 'max' => 3, 'items' => [['Extra shot', 5000], ['Oat milk', 6000], ['Boba', 4000]]],
        ],
        'COF-002' => [
            ['name' => 'Suhu', 'selection_type' => 'single', 'required' => true, 'min' => 1, 'max' => 1, 'items' => [['Panas', 0], ['Dingin', 0]]],
            ['name' => 'Ukuran', 'selection_type' => 'single', 'required' => true, 'min' => 1, 'max' => 1, 'items' => [['Regular', 0], ['Large', 5000]]],
            ['name' => 'Tambahan', 'selection_type' => 'multiple', 'required' => false, 'min' => 0, 'max' => 3, 'items' => [['Extra shot', 5000], ['Oat milk', 6000]]],
        ],
        'COF-003' => [
            ['name' => 'Suhu', 'selection_type' => 'single', 'required' => true, 'min' => 1, 'max' => 1, 'items' => [['Panas', 0], ['Dingin', 0]]],
        ],
        'COF-004' => [
            ['name' => 'Suhu', 'selection_type' => 'single', 'required' => true, 'min' => 1, 'max' => 1, 'items' => [['Panas', 0], ['Dingin', 0]]],
            ['name' => 'Ukuran', 'selection_type' => 'single', 'required' => true, 'min' => 1, 'max' => 1, 'items' => [['Regular', 0], ['Large', 5000]]],
        ],
        'NCO-001' => [
            ['name' => 'Suhu', 'selection_type' => 'single', 'required' => true, 'min' => 1, 'max' => 1, 'items' => [['Panas', 0], ['Dingin', 0]]],
            ['name' => 'Ukuran', 'selection_type' => 'single', 'required' => true, 'min' => 1, 'max' => 1, 'items' => [['Regular', 0], ['Large', 5000]]],
            ['name' => 'Tambahan', 'selection_type' => 'multiple', 'required' => false, 'min' => 0, 'max' => 2, 'items' => [['Boba', 4000], ['Extra matcha', 5000]]],
        ],
        'NCO-002' => [
            ['name' => 'Suhu', 'selection_type' => 'single', 'required' => true, 'min' => 1, 'max' => 1, 'items' => [['Panas', 0], ['Dingin', 0]]],
        ],
        'FOD-001' => [
            ['name' => 'Topping', 'selection_type' => 'multiple', 'required' => false, 'min' => 0, 'max' => 2, 'items' => [['Cokelat', 3000], ['Keju', 4000]]],
        ],
    ];

    private const PRODUCTS = [
        ['sku' => 'COF-001', 'name' => 'Cappuccino', 'category' => 'Kopi', 'description' => 'Espresso dengan susu lembut dan foam.', 'price' => 28000, 'unit' => 'gelas', 'stock' => 20, 'image_path' => 'images/products/cappuccino.jpg'],
        ['sku' => 'COF-002', 'name' => 'Coffee Latte', 'category' => 'Kopi', 'description' => 'Kopi susu dengan rasa ringan dan seimbang.', 'price' => 30000, 'unit' => 'gelas', 'stock' => 18, 'image_path' => 'images/products/coffee-latte.jpg'],
        ['sku' => 'COF-003', 'name' => 'Americano', 'category' => 'Kopi', 'description' => 'Espresso dengan tambahan air panas.', 'price' => 22000, 'unit' => 'gelas', 'stock' => 25, 'image_path' => 'images/products/americano.jpg'],
        ['sku' => 'COF-004', 'name' => 'V60', 'category' => 'Kopi', 'description' => 'Kopi seduh manual dengan rasa yang bersih.', 'price' => 26000, 'unit' => 'gelas', 'stock' => 12, 'image_path' => 'images/products/v60.jpg'],
        ['sku' => 'NCO-001', 'name' => 'Matcha Latte', 'category' => 'Non-kopi', 'description' => 'Matcha dengan susu yang creamy.', 'price' => 28000, 'unit' => 'gelas', 'stock' => 15, 'image_path' => 'images/products/matcha-latte.jpg'],
        ['sku' => 'NCO-002', 'name' => 'Teh Lemon', 'category' => 'Non-kopi', 'description' => 'Teh segar dengan lemon dan sedikit madu.', 'price' => 18000, 'unit' => 'gelas', 'stock' => 25, 'image_path' => 'images/products/lemon-tea.jpg'],
        ['sku' => 'FOD-001', 'name' => 'Croissant', 'category' => 'Makanan', 'description' => 'Pastry renyah untuk teman minum kopi.', 'price' => 20000, 'unit' => 'pcs', 'stock' => 8, 'image_path' => 'images/products/croissant.jpg'],
        ['sku' => 'SNA-001', 'name' => 'Donat Cokelat', 'category' => 'Camilan', 'description' => 'Donat lembut dengan lapisan cokelat.', 'price' => 12000, 'unit' => 'pcs', 'stock' => 10, 'image_path' => 'images/products/donat-cokelat.jpg'],
        ['sku' => 'DES-001', 'name' => 'Brownies', 'category' => 'Dessert', 'description' => 'Brownies cokelat dengan tekstur fudgy.', 'price' => 16000, 'unit' => 'pcs', 'stock' => 6, 'image_path' => 'images/products/brownies.jpg'],
    ];

    public function run(): void
    {
        Store::query()->with('owner')->get()->each(function (Store $store): void {
            if ($store->owner === null) {
                $this->command?->warn("Catalog contoh dilewati: toko {$store->name} tidak memiliki pemilik.");

                return;
            }

            Auth::login($store->owner);

            if (! active_store()?->is($store)) {
                Auth::logout();
                $this->command?->warn("Catalog contoh dilewati: toko {$store->name} tidak punya konteks anggota aktif.");

                return;
            }

            if ($store->products()->exists()) {
                DB::transaction(function (): void {
                    foreach (self::PRODUCTS as $data) {
                        Product::query()
                            ->where('sku', $data['sku'])
                            ->where('name', $data['name'])
                            ->whereNull('image_path')
                            ->update(['image_path' => $data['image_path']]);
                    }
                    $this->seedSampleOptions();
                });

                Auth::logout();

                return;
            }

            DB::transaction(function () use ($store): void {
                foreach (self::PRODUCTS as $data) {
                    $category = Category::query()->firstOrCreate(
                        ['name' => $data['category']],
                        ['store_id' => $store->id],
                    );

                    $product = Product::query()->create([
                        'store_id' => $store->id,
                        'category_id' => $category->id,
                        'name' => $data['name'],
                        'description' => $data['description'],
                        'image_path' => $data['image_path'],
                        'sku' => $data['sku'],
                        'price' => $data['price'],
                        'unit' => $data['unit'],
                        'track_stock' => true,
                        'stock' => $data['stock'],
                        'low_stock_threshold' => 3,
                        'is_active' => true,
                    ]);

                    StockMovement::query()->create([
                        'store_id' => $store->id,
                        'product_id' => $product->id,
                        'user_id' => $store->owner_id,
                        'type' => StockMovementType::Initial,
                        'quantity' => $product->stock,
                        'stock_after' => $product->stock,
                        'reason' => 'Stok awal katalog contoh',
                    ]);
                }
                $this->seedSampleOptions();
            });

            Auth::logout();
            $this->command?->info("9 produk contoh ditambahkan ke toko {$store->name}.");
        });
    }

    private function seedSampleOptions(): void
    {
        foreach (self::OPTIONS as $sku => $groups) {
            $product = Product::query()->where('sku', $sku)->first();
            if (! $product || $product->optionGroups()->exists()) {
                continue;
            }

            foreach ($groups as $groupIndex => $data) {
                $group = $product->optionGroups()->create([
                    'store_id' => $product->store_id,
                    'name' => $data['name'],
                    'selection_type' => $data['selection_type'],
                    'is_required' => $data['required'],
                    'min_select' => $data['min'],
                    'max_select' => $data['max'],
                    'sort_order' => $groupIndex,
                ]);

                foreach ($data['items'] as $sortOrder => [$name, $priceDelta]) {
                    $group->options()->create([
                        'store_id' => $product->store_id,
                        'name' => $name,
                        'price_delta' => $priceDelta,
                        'sort_order' => $sortOrder,
                    ]);
                }
            }
        }
    }
}
