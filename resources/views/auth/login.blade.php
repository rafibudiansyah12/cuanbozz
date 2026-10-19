<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-700"><x-application-logo class="h-7 w-7 fill-current" /></span>
            <h1 class="pt-2 text-2xl font-bold tracking-tight text-text">Selamat datang kembali</h1>
            <p class="text-sm leading-6 text-muted">Masuk untuk melanjutkan ke akun CuanBozz Anda.</p>
        </div>

        <x-auth-session-status class="rounded-xl bg-success/10 px-4 py-3 text-sm text-success" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <x-input-label for="password" value="Kata sandi" />
                    @if(Route::has('password.request'))<a class="inline-flex min-h-11 items-center rounded-control text-xs font-semibold text-primary-700 hover:text-primary-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600" href="{{ route('password.request') }}">Lupa kata sandi?</a>@endif
                </div>
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <label for="remember_me" class="flex min-h-11 items-center gap-2 text-sm text-muted">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-border text-primary-700 focus:ring-primary-600" name="remember">
                <span>Ingat saya</span>
            </label>

            <x-ui.button type="submit" class="w-full">Masuk</x-ui.button>
        </form>

        <div class="flex items-center gap-3 text-xs font-medium text-muted"><span class="h-px flex-1 bg-border"></span><span>atau</span><span class="h-px flex-1 bg-border"></span></div>
        <x-google-auth-button />

        <p class="text-center text-sm text-muted">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-primary-700 hover:text-primary-900">Daftar sekarang</a></p>
    </div>
</x-guest-layout>
