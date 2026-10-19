<?php

use App\Enums\AccountApprovalStatus;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

function fakeGoogleUser(array $attributes = []): GoogleUser
{
    return GoogleUser::fake(array_merge([
        'id' => 'google-user-123',
        'name' => 'Google Applicant',
        'email' => 'google-applicant@example.test',
        'email_verified' => true,
    ], $attributes));
}

it('shows Google sign-in buttons on the public, login, and registration screens', function () {
    $this->get('/')->assertOk()->assertSee('Daftar dengan Google');
    $this->get('/login')->assertOk()->assertSee('Lanjutkan dengan Google');
    $this->get('/register')->assertOk()->assertSee('Daftar dengan Google');
});

it('redirects to Google OAuth', function () {
    config([
        'services.google.client_id' => 'test-client-id',
        'services.google.client_secret' => 'test-client-secret',
        'services.google.redirect' => 'http://localhost/auth/google/callback',
    ]);

    Socialite::fake('google', fakeGoogleUser());

    $this->get(route('auth.google.redirect'))
        ->assertRedirect('https://socialite.fake/google/authorize');
});

it('creates verified pending non-developer accounts for new Google users', function () {
    Socialite::fake('google', fakeGoogleUser());

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('onboarding.create'));

    $user = User::query()->where('email', 'google-applicant@example.test')->firstOrFail();

    expect($user->approval_status)->toBe(AccountApprovalStatus::Pending)
        ->and($user->is_developer)->toBeFalse()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->password)->toBeNull()
        ->and($user->google_id)->toBe('google-user-123');

    $this->assertAuthenticatedAs($user);
});

it('links Google to an existing developer account without changing its role', function () {
    $developer = User::factory()->create([
        'name' => 'Existing Developer',
        'email' => 'google-applicant@example.test',
        'is_developer' => true,
        'approval_status' => AccountApprovalStatus::Approved,
    ]);

    Socialite::fake('google', fakeGoogleUser());

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('developer.home'));

    expect($developer->fresh()->is_developer)->toBeTrue()
        ->and($developer->fresh()->approval_status)->toBe(AccountApprovalStatus::Approved)
        ->and($developer->fresh()->google_id)->toBe('google-user-123');

    $this->assertAuthenticatedAs($developer);
});

it('rejects Google identities without a verified email', function () {
    Socialite::fake('google', fakeGoogleUser([
        'id' => 'unverified-google-user',
        'email' => 'unverified@example.test',
        'email_verified' => false,
    ]));

    $this->get(route('auth.google.callback'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertDatabaseMissing('users', ['email' => 'unverified@example.test']);
});

it('returns a clear message when Google OAuth credentials are missing', function () {
    config([
        'services.google.client_id' => null,
        'services.google.client_secret' => null,
        'services.google.redirect' => null,
    ]);

    $this->get(route('auth.google.redirect'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('status');
});
