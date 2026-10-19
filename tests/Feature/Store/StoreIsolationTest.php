<?php

use App\Enums\StoreRole;
use App\Models\Store;
use App\Models\StoreMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;

it('scopes store members to the active store and blocks cross-store writes', function () {
    $ownerA = User::factory()->create();
    $ownerB = User::factory()->create();
    $storeA = Store::query()->create([
        'owner_id' => $ownerA->id,
        'name' => 'Toko A',
        'slug' => 'toko-a',
    ]);
    $storeB = Store::query()->create([
        'owner_id' => $ownerB->id,
        'name' => 'Toko B',
        'slug' => 'toko-b',
    ]);

    $memberAId = DB::table('store_members')->insertGetId([
        'store_id' => $storeA->id,
        'user_id' => $ownerA->id,
        'role' => StoreRole::Owner->value,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $memberBId = DB::table('store_members')->insertGetId([
        'store_id' => $storeB->id,
        'user_id' => $ownerB->id,
        'role' => StoreRole::Owner->value,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($ownerA);

    expect(active_store()->is($storeA))->toBeTrue()
        ->and(StoreMember::query()->pluck('id')->all())->toBe([$memberAId])
        ->and(StoreMember::query()->whereKey($memberBId)->update(['is_active' => false]))->toBe(0)
        ->and(StoreMember::query()->whereKey($memberBId)->delete())->toBe(0)
        ->and(DB::table('store_members')->where('id', $memberBId)->value('is_active'))->toBe(1);
});

it('assigns new scoped records to the active store automatically', function () {
    $owner = User::factory()->create();
    $cashier = User::factory()->create();
    $store = Store::query()->create([
        'owner_id' => $owner->id,
        'name' => 'Toko Aktif',
        'slug' => 'toko-aktif',
    ]);
    $otherStoreOwner = User::factory()->create();
    $otherStore = Store::query()->create([
        'owner_id' => $otherStoreOwner->id,
        'name' => 'Toko Lain',
        'slug' => 'toko-lain',
    ]);

    DB::table('store_members')->insert([
        'store_id' => $store->id,
        'user_id' => $owner->id,
        'role' => StoreRole::Owner->value,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($owner);

    $member = StoreMember::query()->create([
        'store_id' => $otherStore->id,
        'user_id' => $cashier->id,
        'role' => StoreRole::Cashier,
    ]);

    expect($member->store_id)->toBe($store->id)
        ->and($member->role)->toBe(StoreRole::Cashier);
});

it('returns no active store and no scoped records for guests', function () {
    expect(active_store())->toBeNull()
        ->and(StoreMember::query()->count())->toBe(0);
});
