<div class="space-y-5">
    <header><h1 class="text-2xl font-bold text-text">Masuk ke akun</h1><p class="mt-1 text-sm text-muted">Kelola penjualan toko Anda dalam satu tempat.</p></header>
    <form class="space-y-4" x-on:submit.prevent>
        <x-ui.input label="Email" name="email" type="email" placeholder="nama@email.com" />
        <x-ui.input label="Kata sandi" name="password" type="password" placeholder="Masukkan kata sandi" />
        <div class="flex items-center justify-between gap-3">
            <x-ui.toggle label="Ingat saya" name="remember" />
            <a href="/demo/forgot-password" class="inline-flex min-h-11 items-center text-sm font-medium text-primary-700">Lupa kata sandi?</a>
        </div>
        <x-ui.button type="submit" class="w-full">Masuk</x-ui.button>
        <div class="flex items-center gap-3 text-xs text-muted"><span class="h-px flex-1 bg-border"></span>atau<span class="h-px flex-1 bg-border"></span></div>
        <x-ui.button variant="secondary" class="w-full"><span aria-hidden="true">G</span> Masuk dengan Google</x-ui.button>
    </form>
    <p class="text-center text-sm text-muted">Belum punya akun? <a href="/demo/register" class="font-semibold text-primary-700">Daftar</a></p>
</div>
