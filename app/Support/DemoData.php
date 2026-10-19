<?php

namespace App\Support;

class DemoData
{
    // TODO: ganti dengan data asli di task terkait.
    public static function all(): array
    {
        return [
            'store' => [
                'name' => 'Kedai Senja',
                'address' => 'Jl. Melati No. 12, Bandung',
                'phone' => '0812 3456 7890',
                'logo' => 'KS',
            ],
            'categories' => ['Semua', 'Kopi', 'Non-kopi', 'Makanan'],
            'products' => [
                ['name' => 'Kopi Susu Aren', 'category' => 'Kopi', 'price' => 18000, 'stock' => 12, 'unit' => 'gelas', 'track_stock' => true, 'icon' => '☕', 'options' => ['Ukuran' => ['single', 'Regular', 'Large'], 'Tambahan' => ['multiple', 'Extra shot', 'Oat milk']]],
                ['name' => 'Es Cokelat', 'category' => 'Non-kopi', 'price' => 20000, 'stock' => 8, 'unit' => 'gelas', 'track_stock' => true, 'icon' => '🥤', 'options' => ['Ukuran' => ['single', 'Regular', 'Large']]],
                ['name' => 'Roti Bakar', 'category' => 'Makanan', 'price' => 15000, 'stock' => 0, 'unit' => 'porsi', 'track_stock' => false, 'icon' => '🍞', 'options' => ['Topping' => ['multiple', 'Keju', 'Cokelat']]],
                ['name' => 'Teh Lemon', 'category' => 'Non-kopi', 'price' => 12000, 'stock' => 16, 'unit' => 'gelas', 'track_stock' => true, 'icon' => '🍋', 'options' => ['Ukuran' => ['single', 'Regular', 'Large']]],
            ],
            'cart' => [
                ['name' => 'Kopi Susu Aren', 'option' => 'Large, Extra shot', 'note' => 'Es sedikit', 'quantity' => 2, 'unit_price' => 26000],
                ['name' => 'Roti Bakar', 'option' => 'Keju', 'note' => null, 'quantity' => 1, 'unit_price' => 18000],
            ],
            'option_groups' => [
                ['name' => 'Ukuran', 'selection_type' => 'single', 'is_required' => true, 'min_select' => 1, 'max_select' => 1, 'options' => [['name' => 'Regular', 'price_delta' => 0], ['name' => 'Large', 'price_delta' => 4000]]],
                ['name' => 'Tambahan', 'selection_type' => 'multiple', 'is_required' => false, 'min_select' => 0, 'max_select' => null, 'options' => [['name' => 'Extra shot', 'price_delta' => 4000], ['name' => 'Oat milk', 'price_delta' => 5000]]],
            ],
            'product_form' => ['name' => 'Kopi Susu Aren', 'description' => 'Espresso, susu segar, dan gula aren.', 'sku' => 'KOPI-001', 'category' => 'Kopi', 'price' => 18000, 'unit' => 'gelas', 'stock' => 12, 'low_stock_threshold' => 5],
            'discount' => ['type' => 'percent', 'value' => 10, 'amount' => 7000],
            'cart_totals' => ['item_count' => 3, 'subtotal' => 70000, 'total' => 63000],
            'report_date' => '18 Oktober 2026',
            'orders' => [
                ['number' => 'INV-20261018-0042', 'customer' => 'Meja 03', 'time' => '14:32', 'items' => 3, 'total' => 63000, 'method' => 'Tunai', 'status' => 'Lunas', 'cashier' => 'Ayu'],
                ['number' => 'INV-20261018-0041', 'customer' => 'Bungkus', 'time' => '14:20', 'items' => 2, 'total' => 38000, 'method' => 'QRIS', 'status' => 'Lunas', 'cashier' => 'Raka'],
                ['number' => 'INV-20261018-0040', 'customer' => 'Meja 01', 'time' => '13:58', 'items' => 1, 'total' => 20000, 'method' => 'QRIS', 'status' => 'Menunggu', 'cashier' => 'Ayu'],
            ],
            'movements' => [
                ['product' => 'Kopi Susu Aren', 'type' => 'Penjualan', 'quantity' => -2, 'after' => 12, 'actor' => 'Ayu', 'time' => '14:32'],
                ['product' => 'Biji Kopi Arabika', 'type' => 'Penyesuaian', 'quantity' => 5, 'after' => 24, 'actor' => 'Raka', 'time' => '11:06'],
                ['product' => 'Es Cokelat', 'type' => 'Penjualan', 'quantity' => -1, 'after' => 8, 'actor' => 'Ayu', 'time' => '10:42'],
            ],
            'members' => [
                ['name' => 'Ayu Lestari', 'email' => 'ayu@example.test', 'role' => 'Kasir', 'active' => true],
                ['name' => 'Raka Putra', 'email' => 'raka@example.test', 'role' => 'Kasir', 'active' => true],
            ],
            'stores' => [
                ['name' => 'Kedai Senja', 'owner' => 'Nadia Putri', 'members' => 3, 'status' => 'Aktif'],
                ['name' => 'Toko Sumber Rejeki', 'owner' => 'Budi Santoso', 'members' => 2, 'status' => 'Aktif'],
                ['name' => 'Dapur Nusa', 'owner' => 'Sari Dewi', 'members' => 1, 'status' => 'Ditinjau'],
            ],
            'users' => [
                ['name' => 'Nadia Putri', 'email' => 'nadia@example.test', 'store' => 'Kedai Senja', 'joined' => '18 Okt 2026'],
                ['name' => 'Budi Santoso', 'email' => 'budi@example.test', 'store' => 'Toko Sumber Rejeki', 'joined' => '15 Okt 2026'],
                ['name' => 'Sari Dewi', 'email' => 'sari@example.test', 'store' => 'Dapur Nusa', 'joined' => '12 Okt 2026'],
            ],
            'summary' => ['revenue' => 2450000, 'orders' => 42, 'average' => 58333],
            'chart' => [32, 54, 41, 72, 63, 88, 58],
            'best_sellers' => [
                ['name' => 'Kopi Susu Aren', 'quantity' => 86, 'revenue' => 1548000],
                ['name' => 'Es Cokelat', 'quantity' => 54, 'revenue' => 1080000],
                ['name' => 'Roti Bakar', 'quantity' => 39, 'revenue' => 585000],
            ],
            'payment' => ['total' => 63000, 'cash_received' => 100000, 'change' => 37000, 'order_number' => 'INV-20261018-0042', 'date' => '18 Oktober 2026', 'time' => '14:32', 'cashier' => 'Ayu Lestari', 'method' => 'Tunai'],
            'presets' => config('design-system.presets'),
            'default_primary' => config('design-system.default_primary'),
        ];
    }
}
