<?php

use App\Enums\AccountApprovalStatus;
use App\Enums\StoreRole;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOptionGroup;
use App\Models\Store;
use App\Models\User;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

function cashierSalesStore(string $suffix): array
{
    $owner = User::factory()->create([
        'approval_status' => AccountApprovalStatus::Approved,
    ]);
    $cashier = User::factory()->create([
        'email_verified_at' => now(),
        'approval_status' => AccountApprovalStatus::Approved,
    ]);
    $store = Store::query()->create([
        'owner_id' => $owner->id,
        'name' => "Toko {$suffix}",
        'slug' => strtolower("toko-{$suffix}"),
    ]);

    foreach ([[$owner, StoreRole::Owner], [$cashier, StoreRole::Cashier]] as [$user, $role]) {
        DB::table('store_members')->insert([
            'store_id' => $store->id,
            'user_id' => $user->id,
            'role' => $role->value,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return [$owner, $cashier, $store];
}

it('lets the owner add a product that a cashier can sell for cash', function () {
    [$owner, $cashier, $store] = cashierSalesStore('Jual Tunai');

    $this->actingAs($owner)->post(route('owner.products.store'), [
        'name' => 'Kopi Susu',
        'sku' => 'KOPI-001',
        'category' => 'Minuman',
        'price' => 18000,
        'unit' => 'gelas',
        'track_stock' => '1',
        'stock' => 8,
        'low_stock_threshold' => 2,
    ])->assertRedirect(route('owner.products'));

    $product = Product::query()->where('store_id', $store->id)->firstOrFail();

    expect($product->category->name)->toBe('Minuman')
        ->and($product->stock)->toBe(8);

    $this->actingAs($cashier)->get(route('owner.pos'))
        ->assertOk()
        ->assertSee('Kopi Susu')
        ->assertSee('KOPI-001');

    $checkout = $this->post(route('owner.checkout.cash'), [
        'items' => [['product_id' => $product->id, 'quantity' => 2, 'note' => 'Tanpa gula']],
        'cash_received' => 50000,
        'customer_name' => 'Nadia',
        'service_type' => 'dine_in',
        'table_number' => '03',
    ])->assertRedirect();

    $order = Order::query()->with('items')->firstOrFail();
    $receiptUrl = $checkout->headers->get('Location');

    expect($order->store_id)->toBe($store->id)
        ->and($order->cashier_id)->toBe($cashier->id)
        ->and($order->total)->toBe(36000)
        ->and($order->cash_received)->toBe(50000)
        ->and($order->change_amount)->toBe(14000)
        ->and($order->customer_name)->toBe('Nadia')
        ->and($order->table_number)->toBe('03')
        ->and($order->receipt_token)->toHaveLength(64)
        ->and($order->items)->toHaveCount(1)
        ->and($order->items->first()->note)->toBe('Tanpa gula')
        ->and($product->fresh()->stock)->toBe(6)
        ->and(DB::table('stock_movements')->where('order_id', $order->id)->value('quantity'))->toBe(-2);

    $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_method' => 'cash', 'status' => 'paid']);
    $this->actingAs($cashier)->get(route('owner.orders'))
        ->assertOk()
        ->assertSee($order->order_number)
        ->assertSee('Rp14.000');

    $this->app['auth']->forgetGuards();

    $this->get($receiptUrl)
        ->assertOk()
        ->assertSee('Nadia')
        ->assertSee('Meja')
        ->assertSee('03')
        ->assertSee('Kopi Susu')
        ->assertSee('Dine In')
        ->assertSee('Tanpa gula')
        ->assertSee('Cetak struk')
        ->assertSee('WhatsApp')
        ->assertSee('Bayar (Tunai)')
        ->assertSee('Kembali');

    [, , $otherStore] = cashierSalesStore('Struk B');
    $crossStoreReceiptUrl = URL::temporarySignedRoute('receipts.show', now()->addMinutes(5), [
        'store' => $otherStore->id,
        'order' => $order->id,
        'token' => $order->receipt_token,
    ]);

    $this->get($crossStoreReceiptUrl)->assertNotFound();
});

it('saves selected sizes and toppings as price snapshots on a cash sale', function () {
    [, $cashier, $store] = cashierSalesStore('Opsi Produk');
    $product = Product::query()->create([
        'store_id' => $store->id,
        'name' => 'Kopi Susu',
        'price' => 18000,
        'unit' => 'gelas',
        'track_stock' => false,
        'is_active' => true,
    ]);
    $size = ProductOptionGroup::query()->create([
        'store_id' => $store->id, 'product_id' => $product->id, 'name' => 'Ukuran',
        'selection_type' => 'single', 'is_required' => true, 'min_select' => 1, 'max_select' => 1,
    ]);
    $large = $size->options()->create(['store_id' => $store->id, 'name' => 'Large', 'price_delta' => 5000]);
    $topping = ProductOptionGroup::query()->create([
        'store_id' => $store->id, 'product_id' => $product->id, 'name' => 'Topping',
        'selection_type' => 'multiple', 'is_required' => false, 'min_select' => 0, 'max_select' => 2,
    ]);
    $boba = $topping->options()->create(['store_id' => $store->id, 'name' => 'Boba', 'price_delta' => 4000]);

    $response = $this->actingAs($cashier)->post(route('owner.checkout.cash'), [
        'items' => [['product_id' => $product->id, 'quantity' => 2, 'options' => [$large->id, $boba->id]]],
        'cash_received' => 60000,
        'service_type' => 'take_away',
    ])->assertRedirect();

    $order = Order::query()->with('items.options')->firstOrFail();
    expect($order->total)->toBe(54000)
        ->and($order->service_type)->toBe('take_away')
        ->and($order->items->first()->unit_price)->toBe(27000)
        ->and($order->items->first()->options)->toHaveCount(2);

    $this->app['auth']->forgetGuards();
    $this->get($response->headers->get('Location'))
        ->assertOk()
        ->assertSee('Take Away')
        ->assertSee('Ukuran: Large')
        ->assertSee('Topping: Boba');
});

it('rejects product options that belong to another store', function () {
    [, $cashier, $storeA] = cashierSalesStore('Opsi A');
    [, , $storeB] = cashierSalesStore('Opsi B');
    $productA = Product::query()->create([
        'store_id' => $storeA->id, 'name' => 'Produk A', 'price' => 10000, 'unit' => 'pcs', 'track_stock' => false, 'is_active' => true,
    ]);
    $productB = Product::query()->create([
        'store_id' => $storeB->id, 'name' => 'Produk B', 'price' => 10000, 'unit' => 'pcs', 'track_stock' => false, 'is_active' => true,
    ]);
    $groupB = ProductOptionGroup::query()->create([
        'store_id' => $storeB->id, 'product_id' => $productB->id, 'name' => 'Ukuran',
        'selection_type' => 'single', 'is_required' => false, 'min_select' => 0, 'max_select' => 1,
    ]);
    $optionB = $groupB->options()->create(['store_id' => $storeB->id, 'name' => 'Besar', 'price_delta' => 5000]);

    $this->actingAs($cashier)->post(route('owner.checkout.cash'), [
        'items' => [['product_id' => $productA->id, 'quantity' => 1, 'options' => [$optionB->id]]],
        'cash_received' => 15000,
    ])->assertSessionHasErrors('items.0.options');

    expect(Order::query()->count())->toBe(0);
});

it('keeps cash register sales isolated to the cashier store', function () {
    [, $cashier, $storeA] = cashierSalesStore('Toko A');
    [$ownerB, , $storeB] = cashierSalesStore('Toko B');
    $productB = Product::query()->create([
        'store_id' => $storeB->id,
        'name' => 'Produk Toko B',
        'price' => 12000,
        'unit' => 'pcs',
        'track_stock' => true,
        'stock' => 5,
        'is_active' => true,
    ]);

    $this->actingAs($cashier)->post(route('owner.checkout.cash'), [
        'items' => [['product_id' => $productB->id, 'quantity' => 1]],
        'cash_received' => 12000,
    ])->assertSessionHasErrors('items');

    expect(Order::query()->count())->toBe(0)
        ->and($productB->fresh()->stock)->toBe(5);

    $this->actingAs($ownerB)->get(route('owner.pos'))->assertOk()->assertSee('Produk Toko B');
});

it('does not let a cashier create products', function () {
    [, $cashier] = cashierSalesStore('Kasir Produk');

    $this->actingAs($cashier)->post(route('owner.products.store'), [
        'name' => 'Produk dari kasir',
        'price' => 10000,
        'unit' => 'pcs',
        'track_stock' => '0',
    ])->assertForbidden();
});

it('shows local product photos from the database catalog in the cashier screen', function () {
    [$owner] = cashierSalesStore('Foto Produk');

    $this->seed(DemoCatalogSeeder::class);

    $this->actingAs($owner)->get(route('owner.pos'))
        ->assertOk()
        ->assertSee('cappuccino.jpg')
        ->assertSee('donat-cokelat.jpg')
        ->assertSee('Panas')
        ->assertSee('Dingin')
        ->assertSee('Extra shot')
        ->assertSee('Cokelat');
});
