<?php

use App\Enums\AccountApprovalStatus;
use App\Enums\StoreRole;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

function ownerDashboardUser(string $role = 'owner'): array
{
    $owner = User::factory()->create([
        'approval_status' => AccountApprovalStatus::Approved,
    ]);
    $store = Store::query()->create([
        'owner_id' => $owner->id,
        'name' => 'Kedai Pemilik',
        'slug' => 'kedai-pemilik',
    ]);
    $member = $role === 'owner' ? $owner : User::factory()->create(['email_verified_at' => now()]);

    DB::table('store_members')->insert([
        'store_id' => $store->id,
        'user_id' => $owner->id,
        'role' => StoreRole::Owner->value,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    if (in_array($role, ['admin', 'cashier'], true)) {
        DB::table('store_members')->insert([
            'store_id' => $store->id,
            'user_id' => $member->id,
            'role' => StoreRole::from($role)->value,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return [$owner, $member, $store];
}

it('gives store admins operational screens and reports while reserving store settings for owners', function () {
    [, $admin] = ownerDashboardUser('admin');

    $this->actingAs($admin)->get(route('owner.products'))->assertOk();
    $this->get(route('owner.categories'))->assertOk();
    $this->get(route('owner.stock-adjustment'))->assertOk();
    $this->get(route('owner.orders'))->assertOk();
    $this->get(route('owner.reports'))->assertOk();
    $this->get(route('owner.store-profile'))->assertForbidden();
    $this->get(route('owner.members'))->assertForbidden();
    $this->get(route('owner.appearance'))->assertForbidden();
});

it('keeps store admin authorization isolated from other stores', function () {
    [, $admin, $storeA] = ownerDashboardUser('admin');
    $ownerB = User::factory()->create(['approval_status' => AccountApprovalStatus::Approved]);
    $storeB = Store::query()->create([
        'owner_id' => $ownerB->id,
        'name' => 'Toko Lain',
        'slug' => 'toko-lain',
    ]);

    $this->actingAs($admin);

    expect(Gate::forUser($admin)->allows('view', $storeA))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('view', $storeB))->toBeFalse();
});

it('lets the owner add an admin member', function () {
    [$owner, , $store] = ownerDashboardUser();

    $this->actingAs($owner)->post(route('owner.members.store'), [
        'name' => 'Admin Toko',
        'email' => 'admin@example.test',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
        'role' => 'admin',
    ])->assertRedirect(route('owner.members'));

    expect($store->members()->where('role', StoreRole::Admin)->exists())->toBeTrue();
});

it('lets an owner save the store theme and enforces readable primary colors', function () {
    [$owner, , $store] = ownerDashboardUser();
    $otherOwner = User::factory()->create(['approval_status' => AccountApprovalStatus::Approved]);
    $otherStore = Store::query()->create([
        'owner_id' => $otherOwner->id,
        'name' => 'Toko Lain',
        'slug' => 'toko-lain-tema',
    ]);

    $this->actingAs($owner)->put(route('owner.appearance.update'), [
        'primary' => '#2563EB',
        'accent' => '#D97706',
        'radius' => 'md',
    ])->assertRedirect(route('owner.appearance'));

    expect($store->fresh()->theme)->toBe([
        'primary' => '#2563EB',
        'accent' => '#D97706',
        'radius' => 'md',
    ])->and($otherStore->fresh()->theme)->toBeNull();

    $this->from(route('owner.appearance'))->put(route('owner.appearance.update'), [
        'primary' => '#777777',
        'accent' => '#D97706',
        'radius' => 'md',
    ])->assertRedirect(route('owner.appearance'))->assertSessionHasErrors('primary');
});

it('renders the owner dashboard without invented sample business data', function () {
    [$owner, , $store] = ownerDashboardUser();

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Beranda')
        ->assertSee('Ruang usaha Anda')
        ->assertSee('Kedai Pemilik')
        ->assertSee('Performa hari ini')
        ->assertSee('Aktivitas 7 hari')
        ->assertSee('Transaksi terakhir')
        ->assertSee('Belum ada data penjualan')
        ->assertDontSee('INV-20261018')
        ->assertDontSee('Kopi Susu Aren')
        ->assertSee(route('owner.pos'));
});

it('uses the same owner layout across menu screens and shows real store settings', function () {
    [$owner, , $store] = ownerDashboardUser();

    $this->actingAs($owner)->get(route('owner.products'))->assertOk()->assertSee('Katalog toko')->assertSee('Kedai Pemilik');
    $this->get(route('owner.products.create'))->assertOk()->assertSee('Informasi produk')->assertSee('Kedai Pemilik');
    $this->get(route('owner.categories'))->assertOk()->assertSee('Kategori produk')->assertSee('Kedai Pemilik');
    $this->get(route('owner.stock-adjustment'))->assertOk()->assertSee('Penyesuaian stok')->assertSee('Kedai Pemilik');
    $this->get(route('owner.stock-history'))->assertOk()->assertSee('Riwayat pergerakan stok')->assertSee('Kedai Pemilik');
    $this->get(route('owner.orders'))->assertOk()->assertSee('Riwayat transaksi')->assertSee('Kedai Pemilik');
    $this->get(route('owner.reports'))->assertOk()->assertSee('Laporan penjualan')->assertSee('Kedai Pemilik');
    $this->get(route('owner.store-profile'))->assertOk()->assertSee('Profil toko')->assertSee('Kedai Pemilik');
    $this->get(route('owner.members'))->assertOk()->assertSee('Kelola kasir')->assertSee('Kedai Pemilik');
    $this->get(route('owner.appearance'))->assertOk()->assertSee('Tampilan toko')->assertSee('Kedai Pemilik');
    $this->get(route('owner.payment-settings'))->assertOk()->assertSee('Hubungkan Midtrans')->assertSee('Belum dikonfigurasi');
    $this->get(route('owner.more'))->assertOk()->assertSee('Pintasan sesuai peran')->assertSee('Kedai Pemilik');
});

it('shows owner menu screens with honest empty states instead of demo records', function () {
    [$owner] = ownerDashboardUser();

    foreach ([
        route('owner.pos') => 'Katalog produk belum tersedia',
        route('owner.products') => 'Belum ada produk',
        route('owner.orders') => 'Belum ada transaksi',
        route('owner.reports') => 'Belum ada data penjualan',
        route('owner.members') => 'Belum ada anggota',
    ] as $url => $message) {
        $this->actingAs($owner)->get($url)->assertOk()->assertSee($message)->assertDontSee('Data contoh');
    }
});

it('continues sending developers to their separate dashboard', function () {
    $developer = User::factory()->create([
        'is_developer' => true,
        'approval_status' => AccountApprovalStatus::Approved,
    ]);

    $this->actingAs($developer)
        ->get(route('dashboard'))
        ->assertRedirect(route('developer.home'));
});

it('keeps owner-only screens away from cashiers and redirects cashiers to their POS', function () {
    [, $cashier] = ownerDashboardUser('cashier');

    $this->actingAs($cashier)->get(route('owner.pos'))->assertOk()->assertSee('Kasir');
    $this->get(route('owner.reports'))->assertForbidden();
    $this->get(route('owner.payment-settings'))->assertForbidden();
    $this->get(route('dashboard'))->assertRedirect(route('owner.pos'));
});

it('shows owner tools only to owners and never exposes Midtrans keys', function () {
    [$owner, , $store] = ownerDashboardUser();
    config([
        'services.midtrans.server_key' => 'server-secret-example',
        'services.midtrans.client_key' => 'client-secret-example',
        'services.midtrans.is_production' => false,
    ]);

    $this->actingAs($owner)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Tools toko')
        ->assertSee('Hubungkan Midtrans');

    $this->get(route('owner.payment-settings'))
        ->assertOk()
        ->assertSee('Kunci tersedia')
        ->assertSee('Sandbox')
        ->assertDontSee('server-secret-example')
        ->assertDontSee('client-secret-example');

    $cashier = User::factory()->create();
    DB::table('store_members')->insert([
        'store_id' => $store->id,
        'user_id' => $cashier->id,
        'role' => StoreRole::Cashier->value,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $this->actingAs($cashier)->get(route('owner.payment-settings'))->assertForbidden();
});

it('lets an owner create a cashier account for their own store', function () {
    [$owner, , $store] = ownerDashboardUser();

    $this->actingAs($owner)->post(route('owner.members.store'), [
        'name' => 'Kasir Baru',
        'email' => 'kasir-baru@example.test',
        'password' => 'kata-sandi-awal-aman',
        'password_confirmation' => 'kata-sandi-awal-aman',
    ])->assertRedirect(route('owner.members'));

    $cashier = User::query()->where('email', 'kasir-baru@example.test')->firstOrFail();
    expect($cashier->is_developer)->toBeFalse()
        ->and(Hash::check('kata-sandi-awal-aman', $cashier->password))->toBeTrue();

    $this->assertDatabaseHas('store_members', [
        'store_id' => $store->id,
        'user_id' => $cashier->id,
        'role' => StoreRole::Cashier->value,
        'is_active' => true,
    ]);

    $this->assertNotNull($cashier->email_verified_at);
    $this->actingAs($cashier)->get(route('owner.pos'))->assertOk();
    $this->get(route('owner.members'))->assertForbidden();
});

it('requires unique email and confirmed password when creating a cashier', function () {
    [$owner] = ownerDashboardUser();
    User::factory()->create(['email' => 'used@example.test']);

    $this->actingAs($owner)->from(route('owner.members'))->post(route('owner.members.store'), [
        'name' => 'Kasir Baru',
        'email' => 'used@example.test',
        'password' => 'kata-sandi-awal-aman',
        'password_confirmation' => 'tidak-sama',
    ])->assertSessionHasErrors(['email', 'password']);
});

it('lets an owner disable only cashier accounts from their own store', function () {
    [$owner, $cashier, $store] = ownerDashboardUser('cashier');
    $this->actingAs($owner);
    $member = $store->members()->where('user_id', $cashier->id)->firstOrFail();

    $this->patch(route('owner.members.update', $member), ['is_active' => '0'])
        ->assertRedirect(route('owner.members'));

    expect($member->fresh()->is_active)->toBeFalse();
    $this->actingAs($cashier)->get(route('owner.pos'))->assertRedirect(route('account.status'));
    $this->get(route('onboarding.create'))->assertRedirect(route('account.status'));
    $this->get(route('account.status'))->assertOk()->assertSee('dinonaktifkan oleh pemilik toko');
});

it('does not let an owner change a cashier from another store', function () {
    [$ownerA] = ownerDashboardUser();
    $ownerB = User::factory()->create(['approval_status' => AccountApprovalStatus::Approved]);
    $storeB = Store::query()->create([
        'owner_id' => $ownerB->id,
        'name' => 'Toko Lain',
        'slug' => 'toko-lain',
    ]);
    $cashierB = User::factory()->create();
    $memberId = DB::table('store_members')->insertGetId([
        'store_id' => $storeB->id,
        'user_id' => $cashierB->id,
        'role' => StoreRole::Cashier->value,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($ownerA)
        ->patch(route('owner.members.update', $memberId), ['is_active' => '0'])
        ->assertNotFound();

    $this->assertDatabaseHas('store_members', ['id' => $memberId, 'is_active' => true]);
});

it('does not show cashier members from another store', function () {
    [$ownerA] = ownerDashboardUser();
    $ownerB = User::factory()->create(['approval_status' => AccountApprovalStatus::Approved]);
    $storeB = Store::query()->create([
        'owner_id' => $ownerB->id,
        'name' => 'Toko Tetangga',
        'slug' => 'toko-tetangga',
    ]);
    $cashierB = User::factory()->create([
        'name' => 'Kasir Toko Tetangga',
        'email' => 'cashier-other-store@example.test',
    ]);

    DB::table('store_members')->insert([
        'store_id' => $storeB->id,
        'user_id' => $cashierB->id,
        'role' => StoreRole::Cashier->value,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($ownerA)
        ->get(route('owner.members'))
        ->assertOk()
        ->assertSee('Belum ada anggota')
        ->assertDontSee('Kasir Toko Tetangga')
        ->assertDontSee('cashier-other-store@example.test');
});
