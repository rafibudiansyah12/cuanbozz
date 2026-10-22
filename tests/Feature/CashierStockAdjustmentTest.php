<?php

use App\Enums\AccountApprovalStatus;
use App\Enums\StoreRole;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function stockAdjustmentStore(string $name): array
{
    $owner = User::factory()->create(['approval_status' => AccountApprovalStatus::Approved]);
    $cashier = User::factory()->create([
        'email_verified_at' => now(),
        'approval_status' => AccountApprovalStatus::Approved,
    ]);
    $store = Store::query()->create([
        'owner_id' => $owner->id,
        'name' => $name,
        'slug' => str($name)->slug().'-'.uniqid(),
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

it('lets a cashier adjust tracked stock with a reason and records the actor', function () {
    [$owner, $cashier, $store] = stockAdjustmentStore('Kasir Stok');
    $this->actingAs($cashier);
    $product = Product::query()->create([
        'store_id' => $store->id,
        'name' => 'Kopi Susu',
        'price' => 18000,
        'unit' => 'gelas',
        'track_stock' => true,
        'stock' => 5,
        'is_active' => true,
    ]);

    $this->get(route('owner.stock-adjustment', ['product' => $product->id]))
        ->assertOk()
        ->assertSee('Penyesuaian stok')
        ->assertSee('Kopi Susu');

    $this->post(route('owner.stock-adjustment.store'), [
        'product_id' => $product->id,
        'direction' => 'in',
        'quantity' => 4,
        'reason' => 'Barang masuk dari pemasok',
    ])->assertRedirect(route('owner.stock-adjustment', ['product' => $product->id]));

    expect($product->fresh()->stock)->toBe(9);
    $this->assertDatabaseHas('stock_movements', [
        'store_id' => $store->id,
        'product_id' => $product->id,
        'user_id' => $cashier->id,
        'type' => 'adjustment',
        'quantity' => 4,
        'stock_after' => 9,
        'reason' => 'Barang masuk dari pemasok',
    ]);

    $this->post(route('owner.stock-adjustment.store'), [
        'product_id' => $product->id,
        'direction' => 'out',
        'quantity' => 2,
        'reason' => 'Produk rusak',
    ])->assertRedirect(route('owner.stock-adjustment', ['product' => $product->id]));

    expect($product->fresh()->stock)->toBe(7)
        ->and(DB::table('stock_movements')->where('product_id', $product->id)->count())->toBe(2);

    $this->actingAs($owner)->get(route('owner.stock-history'))
        ->assertOk()
        ->assertSee('Manajemen stok')
        ->assertSee('Aktivitas stok')
        ->assertSee('Barang masuk dari pemasok')
        ->assertSee('Produk rusak')
        ->assertSee($cashier->name);
});

it('rejects adjustments that would make stock negative or change untracked stock', function () {
    [, $cashier, $store] = stockAdjustmentStore('Validasi Stok');
    $this->actingAs($cashier);
    $tracked = Product::query()->create([
        'store_id' => $store->id, 'name' => 'Stok Terbatas', 'price' => 1000, 'unit' => 'pcs', 'track_stock' => true, 'stock' => 2,
    ]);
    $untracked = Product::query()->create([
        'store_id' => $store->id, 'name' => 'Stok Bebas', 'price' => 1000, 'unit' => 'pcs', 'track_stock' => false,
    ]);

    $this->post(route('owner.stock-adjustment.store'), [
        'product_id' => $tracked->id,
        'direction' => 'out',
        'quantity' => 3,
        'reason' => 'Koreksi stok',
    ])->assertSessionHasErrors('quantity');

    $this->post(route('owner.stock-adjustment.store'), [
        'product_id' => $untracked->id,
        'direction' => 'in',
        'quantity' => 3,
        'reason' => 'Barang masuk',
    ])->assertSessionHasErrors('product_id');

    expect($tracked->fresh()->stock)->toBe(2)
        ->and($untracked->fresh()->stock)->toBe(0)
        ->and(DB::table('stock_movements')->count())->toBe(0);
});

it('does not allow a cashier to adjust stock from another store', function () {
    [, $cashier, $storeA] = stockAdjustmentStore('Toko Stok A');
    [$ownerB, , $storeB] = stockAdjustmentStore('Toko Stok B');
    $this->actingAs($ownerB);
    $productB = Product::query()->create([
        'store_id' => $storeB->id,
        'name' => 'Produk Toko B',
        'price' => 1000,
        'unit' => 'pcs',
        'track_stock' => true,
        'stock' => 8,
    ]);

    $this->actingAs($cashier)->post(route('owner.stock-adjustment.store'), [
        'product_id' => $productB->id,
        'direction' => 'out',
        'quantity' => 2,
        'reason' => 'Percobaan lintas toko',
    ])->assertNotFound();

    expect($productB->fresh()->stock)->toBe(8)
        ->and(DB::table('stock_movements')->where('store_id', $storeA->id)->count())->toBe(0)
        ->and(DB::table('stock_movements')->where('store_id', $storeB->id)->count())->toBe(0);
});
