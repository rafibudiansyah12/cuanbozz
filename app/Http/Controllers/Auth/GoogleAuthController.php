<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountApprovalStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret') || ! config('services.google.redirect')) {
            return redirect()->route('login')->with('status', 'Login Google belum dikonfigurasi. Isi kredensial Google OAuth di file .env.');
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = mb_strtolower(trim((string) $googleUser->getEmail()));
            $googleId = (string) $googleUser->getId();
            $raw = $googleUser->getRaw();
            $emailVerified = filter_var($raw['email_verified'] ?? $raw['verified_email'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if ($email === '' || $googleId === '' || ! $emailVerified) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Google harus memberikan alamat email yang sudah terverifikasi untuk masuk.',
                ]);
            }

            $user = DB::transaction(function () use ($email, $googleId, $googleUser): User {
                $user = User::query()->where('google_id', $googleId)->lockForUpdate()->first();
                $user ??= User::query()->where('email', $email)->lockForUpdate()->first();

                if ($user !== null) {
                    if ($user->google_id !== null && ! hash_equals($user->google_id, $googleId)) {
                        throw ValidationException::withMessages([
                            'email' => 'Email ini sudah terhubung ke akun Google yang berbeda.',
                        ]);
                    }

                    $user->forceFill([
                        'google_id' => $googleId,
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ])->save();

                    return $user;
                }

                $user = new User;
                $user->forceFill([
                    'name' => $googleUser->getName() ?: str($email)->before('@')->toString(),
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => null,
                    'email_verified_at' => now(),
                    'approval_status' => AccountApprovalStatus::Pending,
                    'is_developer' => false,
                ])->save();

                return $user;
            });

            Auth::login($user);
            request()->session()->regenerate();

            if ($user->isDeveloper()) {
                return redirect()->route('developer.home');
            }

            if (! $user->ownedStores()->exists() && ! $user->storeMemberships()->where('is_active', true)->exists()) {
                return redirect()->route('onboarding.create');
            }

            if ($user->approval_status !== AccountApprovalStatus::Approved) {
                return redirect()->route('account.status');
            }

            return redirect()->route('dashboard');
        } catch (ValidationException $exception) {
            return redirect()->route('login')->withErrors($exception->errors());
        } catch (Throwable) {
            return redirect()->route('login')->withErrors([
                'email' => 'Login Google gagal. Silakan coba lagi.',
            ]);
        }
    }
}
