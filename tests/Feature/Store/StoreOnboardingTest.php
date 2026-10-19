<?php

use App\Enums\AccountApprovalStatus;
use App\Enums\StoreRole;
use App\Models\Store;
use App\Models\User;

it('shows the onboarding form to verified users who do not have a store', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'approval_status' => AccountApprovalStatus::Pending,
    ]);

    $this->actingAs($user)
        ->get(route('onboarding.create'))
        ->assertOk()
        ->assertSee('Buat toko Anda')
        ->assertSee('Nama toko');
});

it('requires email verification before store onboarding', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('onboarding.create'))
        ->assertRedirect(route('verification.notice'));
});

it('creates the owner store and owner membership then sends the account to review', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'approval_status' => AccountApprovalStatus::Pending,
    ]);

    $this->actingAs($user)
        ->post(route('onboarding.store'), [
            'name' => 'Warung Sederhana',
            'address' => 'Jl. Melati No. 1',
            'phone' => '081234567890',
            'owner_id' => 999999,
        ])
        ->assertRedirect(route('account.status'))
        ->assertSessionHas('status');

    $store = Store::query()->where('owner_id', $user->id)->firstOrFail();
    expect($store->name)->toBe('Warung Sederhana')
        ->and($store->slug)->toStartWith('warung-sederhana-')
        ->and($user->fresh()->approval_status)->toBe(AccountApprovalStatus::Pending);

    $this->assertDatabaseHas('store_members', [
        'store_id' => $store->id,
        'user_id' => $user->id,
        'role' => StoreRole::Owner->value,
        'is_active' => true,
    ]);

    $this->get(route('account.status'))->assertOk()->assertSee('Menunggu persetujuan');
    $this->get('/dashboard')->assertRedirect(route('account.status'));
});

it('keeps one store per account and does not let another owner attach their store', function () {
    $ownerA = User::factory()->create([
        'email_verified_at' => now(),
        'approval_status' => AccountApprovalStatus::Pending,
    ]);
    $ownerB = User::factory()->create(['email_verified_at' => now()]);
    $storeB = Store::query()->create([
        'owner_id' => $ownerB->id,
        'name' => 'Toko B',
        'slug' => 'toko-b',
    ]);

    $this->actingAs($ownerA)
        ->post(route('onboarding.store'), [
            'name' => 'Toko A',
            'address' => 'Alamat A',
            'phone' => '081111111111',
            'store_id' => $storeB->id,
        ])
        ->assertRedirect(route('account.status'));

    expect($ownerA->ownedStores()->count())->toBe(1)
        ->and($ownerA->ownedStores()->first()->id)->not->toBe($storeB->id)
        ->and($ownerB->ownedStores()->count())->toBe(1);

    $this->get(route('onboarding.create'))->assertRedirect(route('account.status'));
    $this->post(route('onboarding.store'), [
        'name' => 'Toko Kedua',
        'address' => 'Alamat lain',
        'phone' => '082222222222',
    ])->assertRedirect(route('account.status'));

    expect($ownerA->ownedStores()->count())->toBe(1);
});

it('allows an approved owner to continue to the dashboard after onboarding', function () {
    $owner = User::factory()->create([
        'approval_status' => AccountApprovalStatus::Approved,
        'email_verified_at' => now(),
    ]);
    Store::query()->create(['owner_id' => $owner->id, 'name' => 'Toko Aktif', 'slug' => 'toko-aktif']);

    $this->actingAs($owner)->get('/dashboard')->assertOk();
});
