<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-700"><x-application-logo class="h-7 w-7 fill-current" /></span>
            <h1 class="pt-2 text-2xl font-bold tracking-tight text-text">Buat akun CuanBozz</h1>
            <p class="text-sm leading-6 text-muted">Daftar untuk mulai menyiapkan toko Anda.</p>
        </div>

        <x-google-auth-button label="Daftar dengan Google" />
        <div class="flex items-center gap-3 text-xs font-medium text-muted"><span class="h-px flex-1 bg-border"></span><span>atau pakai email</span><span class="h-px flex-1 bg-border"></span></div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <x-input-label for="name" value="Nama lengkap" />
                <x-text-input id="name" class="mt-1.5 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Anda" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-1.5 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password" value="Kata sandi" />
                <x-text-input id="password" class="mt-1.5 block w-full" type="password" name="password" required autocomplete="new-password" placeholder="Buat kata sandi" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" value="Ulangi kata sandi" />
                <x-text-input id="password_confirmation" class="mt-1.5 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang kata sandi" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            <x-ui.button type="submit" class="w-full">Buat akun</x-ui.button>
        </form>

        <p class="text-center text-sm text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-primary-700 hover:text-primary-900">Masuk</a></p>
    </div>
</x-guest-layout>
