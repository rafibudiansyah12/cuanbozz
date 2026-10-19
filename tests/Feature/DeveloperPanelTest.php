<?php

use App\Enums\AccountApprovalStatus;
use App\Models\DeveloperAuditLog;
use App\Models\Store;
use App\Models\User;

function developerUser(): User
{
    return User::factory()->create([
        'name' => 'Platform Developer',
        'email' => 'platform-developer@example.test',
        'is_developer' => true,
        'approval_status' => AccountApprovalStatus::Approved,
    ]);
}

function pendingApplicant(string $email = 'applicant@example.test'): User
{
    $user = User::factory()->create([
        'name' => 'Applicant Account',
        'email' => $email,
        'approval_status' => AccountApprovalStatus::Pending,
        'is_developer' => false,
    ]);

    Store::query()->create([
        'owner_id' => $user->id,
        'name' => 'Toko '.$user->id,
        'slug' => 'toko-'.$user->id,
    ]);

    return $user;
}

it('renders each developer panel screen and its navigation', function (string $path, string $heading) {
    $this->actingAs(developerUser())
        ->get($path)
        ->assertOk()
        ->assertSee($heading)
        ->assertSee('Pendaftaran');
})->with([
    ['/developer', 'Beranda'],
    ['/developer/registrations', 'Pendaftaran'],
    ['/developer/stores', 'Toko'],
    ['/developer/users', 'Pengguna'],
    ['/developer/activity', 'Log Aktivitas'],
    ['/developer/more', 'Lainnya'],
    ['/developer/account', 'Akun Saya'],
]);

it('returns 404 for non-developers on every developer screen', function () {
    $this->actingAs(User::factory()->create())
        ->get('/developer')
        ->assertNotFound();

    $this->get('/developer/registrations')->assertNotFound();
    $this->get('/developer/stores')->assertNotFound();
    $this->get('/developer/users')->assertNotFound();
    $this->get('/developer/activity')->assertNotFound();
    $this->get('/developer/account')->assertNotFound();
});

it('uses the responsive developer sidebar instead of the cashier bottom navigation', function () {
    $this->actingAs(developerUser())
        ->get('/developer')
        ->assertOk()
        ->assertSee('Navigasi developer')
        ->assertSee('Buka menu')
        ->assertSee('Log Aktivitas')
        ->assertSee('Akun Saya')
        ->assertDontSee('Navigasi utama');
});

it('redirects developers from the legacy dashboard to the developer panel', function () {
    $this->actingAs(developerUser())
        ->get('/dashboard')
        ->assertRedirect('/developer');
});

it('takes signed-in developers from the public landing page to the panel', function () {
    $this->actingAs(developerUser())
        ->get('/')
        ->assertRedirect('/developer');
});

it('approves a pending account and records one audit entry', function () {
    $developer = developerUser();
    $applicant = pendingApplicant();

    $this->actingAs($developer)
        ->post(route('developer.registrations.approve', $applicant))
        ->assertSessionHasNoErrors();

    expect($applicant->fresh()->approval_status)->toBe(AccountApprovalStatus::Approved)
        ->and($applicant->fresh()->approved_by)->toBe($developer->id)
        ->and($applicant->fresh()->approved_at)->not->toBeNull();

    expect(DeveloperAuditLog::query()->where('target_user_id', $applicant->id)->count())->toBe(1)
        ->and(DeveloperAuditLog::query()->where('target_user_id', $applicant->id)->value('action'))->toBe('approve');
});

it('rejects only with a reason and records the rejection', function () {
    $developer = developerUser();
    $applicant = pendingApplicant();

    $this->actingAs($developer)
        ->post(route('developer.registrations.reject', $applicant), ['reason' => ''])
        ->assertSessionHasErrors('reason');

    expect($applicant->fresh()->approval_status)->toBe(AccountApprovalStatus::Pending)
        ->and(DeveloperAuditLog::query()->count())->toBe(0);

    $this->post(route('developer.registrations.reject', $applicant), ['reason' => 'Alamat toko belum jelas.'])
        ->assertSessionHasNoErrors();

    expect($applicant->fresh()->approval_status)->toBe(AccountApprovalStatus::Rejected)
        ->and($applicant->fresh()->rejection_reason)->toBe('Alamat toko belum jelas.')
        ->and(DeveloperAuditLog::query()->where('action', 'reject')->value('note'))->toBe('Alamat toko belum jelas.');
});

it('suspends and reactivates accounts with one audit log per action', function () {
    $developer = developerUser();
    $account = User::factory()->create(['approval_status' => AccountApprovalStatus::Approved]);
    Store::query()->create(['owner_id' => $account->id, 'name' => 'Toko Aktif', 'slug' => 'toko-aktif']);

    $this->actingAs($developer)->post(route('developer.registrations.suspend', $account));
    expect($account->fresh()->approval_status)->toBe(AccountApprovalStatus::Suspended);

    $this->post(route('developer.registrations.reactivate', $account));
    expect($account->fresh()->approval_status)->toBe(AccountApprovalStatus::Approved)
        ->and(DeveloperAuditLog::query()->where('target_user_id', $account->id)->orderBy('id')->pluck('action')->all())
        ->toBe(['suspend', 'reactivate']);
});

it('suspends and reactivates a store and its owner transactionally', function () {
    $developer = developerUser();
    $owner = User::factory()->create(['approval_status' => AccountApprovalStatus::Approved]);
    $store = Store::query()->create(['owner_id' => $owner->id, 'name' => 'Warung Satu', 'slug' => 'warung-satu']);

    $this->actingAs($developer)->post(route('developer.stores.suspend', $store));
    expect($store->fresh()->is_active)->toBeFalse()
        ->and($owner->fresh()->approval_status)->toBe(AccountApprovalStatus::Suspended);

    $this->post(route('developer.stores.reactivate', $store));
    expect($store->fresh()->is_active)->toBeTrue()
        ->and($owner->fresh()->approval_status)->toBe(AccountApprovalStatus::Approved)
        ->and(DeveloperAuditLog::query()->where('target_user_id', $owner->id)->count())->toBe(2);
});

it('rejects duplicate approval and does not add another audit entry', function () {
    $developer = developerUser();
    $account = User::factory()->create(['approval_status' => AccountApprovalStatus::Approved]);
    Store::query()->create(['owner_id' => $account->id, 'name' => 'Toko Duplikat', 'slug' => 'toko-duplikat']);

    $this->actingAs($developer)->post(route('developer.registrations.approve', $account))
        ->assertSessionHasErrors('status');

    expect(DeveloperAuditLog::query()->count())->toBe(0);
});

it('does not allow a developer to change their own account status', function () {
    $developer = developerUser();

    $this->actingAs($developer)->post(route('developer.registrations.suspend', $developer))
        ->assertSessionHasErrors('target');

    expect($developer->fresh()->approval_status)->toBe(AccountApprovalStatus::Approved)
        ->and(DeveloperAuditLog::query()->count())->toBe(0);
});

it('lists users without exposing other developer accounts', function () {
    $developer = developerUser();
    User::factory()->create(['name' => 'Regular User']);
    User::factory()->create(['name' => 'Hidden Developer', 'is_developer' => true]);

    $this->actingAs($developer)->get('/developer/users')
        ->assertOk()
        ->assertSee('Regular User')
        ->assertDontSee('Hidden Developer');
});

it('allows developers to update their password after confirming the current password', function () {
    $developer = developerUser();

    $this->actingAs($developer)
        ->put(route('developer.account.update'), [
            'current_password' => 'password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])
        ->assertSessionHasNoErrors();

    expect(password_verify('new-secure-password', $developer->fresh()->password))->toBeTrue();
});
