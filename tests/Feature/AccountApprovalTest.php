<?php

use App\Enums\AccountApprovalStatus;
use App\Enums\StoreRole;
use App\Http\Middleware\EnsureDeveloper;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('sends owners without a store to onboarding before account review', function (string $status) {
    $user = User::factory()->create([
        'approval_status' => $status,
        'rejection_reason' => $status === 'rejected' ? 'Informasi toko belum lengkap.' : null,
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('onboarding.create'));

    $statusResponse = $this->get('/akun/status')->assertOk();

    if ($status === 'rejected') {
        $statusResponse->assertSee('Informasi toko belum lengkap.');
    } else {
        $statusResponse->assertSee('Menunggu persetujuan');
    }
})->with(['pending', 'rejected']);

it('allows approved users with a store to open business pages', function () {
    $user = User::factory()->create(['approval_status' => AccountApprovalStatus::Approved]);
    Store::query()->create(['owner_id' => $user->id, 'name' => 'Toko Disetujui', 'slug' => 'toko-disetujui']);

    $this->actingAs($user)->get('/dashboard')->assertOk();
});

it('blocks a cashier when the store owner is suspended', function () {
    $owner = User::factory()->create(['approval_status' => AccountApprovalStatus::Suspended]);
    $cashier = User::factory()->create();
    $store = Store::query()->create([
        'owner_id' => $owner->id,
        'name' => 'Toko Uji',
        'slug' => 'toko-uji',
    ]);

    DB::table('store_members')->insert([
        'store_id' => $store->id,
        'user_id' => $cashier->id,
        'role' => StoreRole::Cashier->value,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($cashier)
        ->get('/dashboard')
        ->assertRedirect(route('account.status'));

    $this->get('/akun/status')->assertOk()->assertSee('Akun ditangguhkan');
});

it('creates email registrations as pending non-developers even if role fields are submitted', function () {
    $this->post('/register', [
        'name' => 'Pendaftar Baru',
        'email' => 'pendaftar@example.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'is_developer' => true,
        'approval_status' => 'approved',
    ])->assertRedirect(route('verification.notice'));

    $user = User::query()->where('email', 'pendaftar@example.test')->firstOrFail();

    expect($user->approval_status)->toBe(AccountApprovalStatus::Pending)
        ->and($user->isDeveloper())->toBeFalse();
});

it('does not accept developer privileges from provider-style account attributes', function () {
    $user = User::query()->create([
        'name' => 'Google Account',
        'email' => 'google-user@example.test',
        'password' => 'password',
        'email_verified_at' => now(),
        'is_developer' => true,
        'approval_status' => AccountApprovalStatus::Approved,
    ]);

    expect($user->approval_status)->toBe(AccountApprovalStatus::Pending)
        ->and($user->isDeveloper())->toBeFalse();
});

it('routes developers to the developer panel and pending accounts to their status', function () {
    $developer = User::factory()->create([
        'is_developer' => true,
        'approval_status' => AccountApprovalStatus::Approved,
    ]);

    $this->post('/login', [
        'email' => $developer->email,
        'password' => 'password',
    ])->assertRedirect('/developer');

    auth()->logout();

    $pending = User::factory()->create(['approval_status' => AccountApprovalStatus::Pending]);

    $this->post('/login', [
        'email' => $pending->email,
        'password' => 'password',
    ])->assertRedirect(route('onboarding.create'));
});

it('returns not found when a non-developer fails the developer middleware', function () {
    $request = Request::create('/developer');
    $request->setUserResolver(fn () => User::factory()->create());

    expect(fn () => app(EnsureDeveloper::class)->handle($request, fn () => response('ok')))
        ->toThrow(HttpException::class);
});

it('creates a verified approved developer through the interactive command', function () {
    $this->artisan('developer:create')
        ->expectsQuestion('Nama developer', 'Developer Satu')
        ->expectsQuestion('Email developer', 'dev@example.test')
        ->expectsQuestion('Kata sandi developer', 'safe-password')
        ->expectsQuestion('Konfirmasi kata sandi', 'safe-password')
        ->expectsOutputToContain('Akun developer berhasil dibuat.')
        ->assertExitCode(0);

    $developer = User::query()->where('email', 'dev@example.test')->firstOrFail();

    expect($developer->isDeveloper())->toBeTrue()
        ->and($developer->approval_status)->toBe(AccountApprovalStatus::Approved)
        ->and($developer->email_verified_at)->not->toBeNull()
        ->and(Hash::check('safe-password', $developer->password))->toBeTrue();
});

it('rejects duplicate email in the developer command', function () {
    User::factory()->create(['email' => 'existing@example.test']);

    $this->artisan('developer:create')
        ->expectsQuestion('Nama developer', 'Developer Duplikat')
        ->expectsQuestion('Email developer', 'existing@example.test')
        ->expectsOutputToContain('The email has already been taken.')
        ->assertExitCode(1);

    expect(User::query()->where('email', 'existing@example.test')->count())->toBe(1);
});
