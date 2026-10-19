<x-guest-layout wide>
    <div class="space-y-12 sm:space-y-16">
        <header class="flex min-h-12 items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="flex min-h-11 items-center gap-2 font-bold text-text">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600 text-on-primary"><x-application-logo class="h-6 w-6 fill-current" /></span>
                <span class="text-lg">CuanBozz</span>
            </a>
            <nav aria-label="Navigasi utama" class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center rounded-xl px-3 text-sm font-semibold text-text hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600">Masuk</a>
                <x-ui.button href="{{ route('register') }}" class="px-3 sm:px-4">Daftar</x-ui.button>
            </nav>
        </header>

        <main class="grid items-center gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16">
            <section class="space-y-6 text-center lg:text-left">
                <div class="inline-flex min-h-9 items-center gap-2 rounded-full border border-border bg-surface px-3 text-xs font-semibold text-primary-800 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-success"></span>
                    Kasir digital untuk usaha yang terus tumbuh
                </div>
                <div class="space-y-4">
                    <h1 class="mx-auto max-w-2xl text-4xl font-bold tracking-tight text-text sm:text-5xl lg:mx-0 lg:text-6xl">Kelola toko lebih <span class="text-primary-700">tenang</span>, setiap hari.</h1>
                    <p class="mx-auto max-w-xl text-base leading-7 text-muted sm:text-lg lg:mx-0">Catat transaksi, pantau produk, dan pahami perkembangan usaha dari satu tempat yang mudah digunakan.</p>
                </div>
                <div class="flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">
                    <x-ui.button href="{{ route('login') }}" class="w-full sm:w-auto">Masuk ke akun</x-ui.button>
                    <x-ui.button href="{{ route('register') }}" variant="secondary" class="w-full sm:w-auto">Buat akun gratis</x-ui.button>
                </div>
                <div class="mx-auto max-w-sm space-y-3 lg:mx-0">
                    <div class="flex items-center gap-3 text-xs font-medium text-muted"><span class="h-px flex-1 bg-border"></span><span>atau daftar lebih cepat</span><span class="h-px flex-1 bg-border"></span></div>
                    <x-google-auth-button label="Daftar dengan Google" />
                </div>
                @if(app()->environment('local'))
                    <a href="/demo" class="inline-flex min-h-11 items-center justify-center rounded-control px-3 text-sm font-semibold text-muted hover:text-primary-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600">Lihat layar contoh <span class="ml-2" aria-hidden="true">→</span></a>
                @endif
            </section>

            <section aria-label="Pratinjau dashboard CuanBozz" class="relative mx-auto w-full max-w-xl">
                <div class="absolute -inset-4 -z-10 rounded-[2rem] bg-primary-50 sm:-inset-6"></div>
                <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-xl">
                    <div class="flex items-center justify-between border-b border-border px-4 py-4 sm:px-6">
                        <div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600 text-on-primary"><x-application-logo class="h-6 w-6 fill-current" /></span><div><p class="text-sm font-bold text-text">Ringkasan toko</p><p class="text-xs text-muted">Gambaran usaha Anda</p></div></div>
                        <span class="rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success">Hari ini</span>
                    </div>
                    <div class="space-y-4 p-4 sm:p-6">
                        <div class="grid grid-cols-2 gap-3">
                            <article class="rounded-xl border border-border p-4"><p class="text-xs font-medium text-muted">Transaksi</p><p class="mt-2 text-2xl font-bold text-text">24</p><p class="mt-1 text-xs text-success">Tercatat hari ini</p></article>
                            <article class="rounded-xl border border-border p-4"><p class="text-xs font-medium text-muted">Produk aktif</p><p class="mt-2 text-2xl font-bold text-text">128</p><p class="mt-1 text-xs text-muted">Mudah dipantau</p></article>
                        </div>
                        <article class="rounded-xl border border-border p-4 sm:p-5">
                            <div class="flex items-center justify-between gap-3"><div><p class="text-sm font-semibold text-text">Aktivitas penjualan</p><p class="mt-1 text-xs text-muted">Contoh ringkasan mingguan</p></div><span class="text-sm font-bold text-primary-700">+12%</span></div>
                            <div class="mt-6 flex h-24 items-end gap-2" aria-hidden="true">
                                <span class="h-8 flex-1 rounded-t-md bg-primary-200"></span><span class="h-12 flex-1 rounded-t-md bg-primary-300"></span><span class="h-10 flex-1 rounded-t-md bg-primary-400"></span><span class="h-16 flex-1 rounded-t-md bg-primary-500"></span><span class="h-14 flex-1 rounded-t-md bg-primary-400"></span><span class="h-20 flex-1 rounded-t-md bg-primary-600"></span><span class="h-16 flex-1 rounded-t-md bg-primary-500"></span>
                            </div>
                            <div class="mt-2 flex justify-between text-[10px] text-muted"><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span></div>
                        </article>
                        <div class="flex items-center gap-3 rounded-xl bg-primary-50 p-3"><span class="flex h-9 w-9 items-center justify-center rounded-full bg-surface text-primary-700">✓</span><p class="text-xs leading-5 text-text"><strong>Semua tercatat rapi.</strong> Aktivitas toko mudah ditinjau kapan saja.</p></div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="flex flex-col items-center justify-between gap-2 border-t border-border pt-5 text-xs text-muted sm:flex-row">
            <span>© {{ date('Y') }} CuanBozz</span>
            <span>Kasir sederhana, kendali lebih jelas.</span>
        </footer>
    </div>
</x-guest-layout>
