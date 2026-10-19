<x-owner-layout title="Beranda" :preview-mode="$previewMode ?? false" :store="$store ?? null">
    <div class="space-y-5 sm:space-y-6">
        @if($previewMode ?? false)
            <div class="rounded-xl border border-warning/30 bg-warning/10 px-4 py-3 text-sm text-text">Pratinjau lokal. Data usaha belum tersedia di database.</div>
        @endif
        <section class="relative isolate overflow-hidden rounded-3xl border border-primary-200/70 bg-gradient-to-br from-primary-100/90 via-surface/80 to-accent/20 p-5 text-text shadow-sm backdrop-blur sm:p-7 lg:p-9">
            <div class="absolute inset-0 bg-gradient-to-r from-surface/45 via-transparent to-primary-100/30" aria-hidden="true"></div>
            <div class="relative z-10 max-w-3xl">
                <p class="text-sm font-medium text-muted">{{ now()->translatedFormat('l, j F Y') }}</p>
                <p class="mt-4 text-xs font-bold uppercase tracking-[0.2em] text-primary-800">Selamat datang di</p>
                <h2 class="mt-1 break-words text-4xl font-extrabold leading-[1.05] tracking-tight text-primary-900 sm:text-5xl lg:text-6xl">{{ $store?->name ?? 'Toko Anda' }}</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-text/80 sm:text-base">Selamat datang kembali. Kelola tokomu dengan ringkasan penjualan, katalog, dan aktivitas harian.</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ ($previewMode ?? false) ? route('owner.preview', ['screen' => 'pos']) : route('owner.pos') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-control bg-primary-700 px-4 py-2 text-sm font-semibold text-on-primary shadow-sm transition hover:bg-primary-800">Buka kasir <span aria-hidden="true">→</span></a>
                    <a href="{{ ($previewMode ?? false) ? route('owner.preview', ['screen' => 'products']) : route('owner.products') }}" class="inline-flex min-h-11 items-center rounded-control border border-primary-200/80 bg-surface/55 px-4 text-sm font-semibold text-primary-900 transition hover:bg-surface/85">Kelola produk</a>
                </div>
            </div>
            <div aria-hidden="true" class="pointer-events-none absolute -right-12 -top-20 h-64 w-64 rounded-full border-[32px] border-primary-500/10 sm:-right-8 sm:top-1/2 sm:-translate-y-1/2"></div>
            <div aria-hidden="true" class="pointer-events-none absolute bottom-0 right-12 hidden h-28 w-28 rounded-full bg-accent/15 blur-sm sm:block"></div>
        </section>

        <div class="flex flex-wrap items-center justify-between gap-2">
            <div><p class="text-xs font-semibold uppercase tracking-wider text-muted">Ringkasan</p><h2 class="mt-1 text-lg font-bold text-text sm:text-xl">Performa hari ini</h2></div>
            <x-ui.badge variant="muted">Ringkasan toko</x-ui.badge>
        </div>

        <section aria-label="Ringkasan performa" class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach(['Omzet hari ini', 'Transaksi hari ini', 'Rata-rata transaksi', 'Produk aktif'] as $metric)
                <x-ui.card class="min-w-0 p-4 sm:p-5">
                    <p class="text-xs font-medium leading-5 text-muted sm:text-sm">{{ $metric }}</p>
                    <p class="mt-4 text-2xl font-bold tracking-tight text-muted">—</p>
                    <p class="mt-1 text-xs text-muted">Belum ada data</p>
                </x-ui.card>
            @endforeach
        </section>

        <section class="grid gap-5 xl:grid-cols-[minmax(0,1.6fr)_minmax(300px,1fr)]">
            <x-ui.card class="min-w-0 p-4 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div><p class="text-sm text-muted">Ringkasan penjualan</p><h2 class="mt-1 text-lg font-bold text-text">Aktivitas 7 hari</h2></div>
                    <a href="{{ ($previewMode ?? false) ? route('owner.preview', ['screen' => 'report']) : route('owner.reports') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-primary-700 hover:text-primary-900">Buka laporan <span class="ml-1" aria-hidden="true">→</span></a>
                </div>
                <x-ui.empty-state class="mt-4" title="Belum ada data penjualan" description="Grafik akan muncul setelah toko mencatat transaksi." />
            </x-ui.card>

            <x-ui.card class="min-w-0 p-4 sm:p-6">
                <div class="flex items-start justify-between gap-3">
                    <div><p class="text-sm text-muted">Inventaris</p><h2 class="mt-1 text-lg font-bold text-text">Stok menipis</h2></div>
                </div>
                <x-ui.empty-state class="mt-4" title="Belum ada produk" description="Tambahkan produk agar stok dan peringatannya bisa dipantau." />
                <a href="{{ ($previewMode ?? false) ? route('owner.preview', ['screen' => 'product-form']) : route('owner.products.create') }}" class="mt-2 inline-flex min-h-11 items-center text-sm font-semibold text-primary-700">Tambah produk <span class="ml-1" aria-hidden="true">→</span></a>
            </x-ui.card>
        </section>

        <section class="grid gap-5 xl:grid-cols-2">
            <x-ui.card class="min-w-0 p-4 sm:p-6">
                <div class="flex items-center justify-between gap-3"><div><p class="text-sm text-muted">Aktivitas terbaru</p><h2 class="mt-1 text-lg font-bold text-text">Transaksi terakhir</h2></div><a href="{{ ($previewMode ?? false) ? route('owner.preview', ['screen' => 'orders']) : route('owner.orders') }}" class="inline-flex min-h-11 shrink-0 items-center text-sm font-semibold text-primary-700">Buka riwayat</a></div>
                <x-ui.empty-state class="mt-4" title="Belum ada transaksi" description="Transaksi yang selesai akan tampil di sini." />
            </x-ui.card>

            <x-ui.card class="min-w-0 p-4 sm:p-6">
                <div class="flex items-center justify-between gap-3"><div><p class="text-sm text-muted">Produk</p><h2 class="mt-1 text-lg font-bold text-text">Produk terlaris</h2></div><a href="{{ ($previewMode ?? false) ? route('owner.preview', ['screen' => 'report']) : route('owner.reports') }}" class="inline-flex min-h-11 shrink-0 items-center text-sm font-semibold text-primary-700">Buka laporan</a></div>
                <x-ui.empty-state class="mt-4" title="Belum ada produk terjual" description="Produk terlaris akan dihitung dari transaksi toko." />
            </x-ui.card>
        </section>
        @if(($previewMode ?? false) || (auth()->check() && auth()->user()->ownedStores()->exists()))
            <section aria-labelledby="owner-tools-title" class="space-y-3">
                <div><p class="text-xs font-semibold uppercase tracking-wider text-muted">Khusus pemilik</p><h2 id="owner-tools-title" class="mt-1 text-lg font-bold text-text sm:text-xl">Tools toko</h2></div>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach([
                        ['title' => 'Hubungkan Midtrans', 'description' => 'Atur kesiapan pembayaran nontunai.', 'screen' => 'payments', 'route' => 'owner.payment-settings'],
                        ['title' => 'Kelola kasir', 'description' => 'Atur anggota dan akses toko.', 'screen' => 'members', 'route' => 'owner.members'],
                        ['title' => 'Profil dan tampilan toko', 'description' => 'Periksa identitas dan tema toko.', 'screen' => 'store-profile', 'route' => 'owner.store-profile'],
                        ['title' => 'Kelola stok', 'description' => 'Sesuaikan stok dan lihat riwayatnya.', 'screen' => 'stock-adjustment', 'route' => 'owner.stock-adjustment'],
                    ] as $tool)
                        <a href="{{ ($previewMode ?? false) ? route('owner.preview', ['screen' => $tool['screen']]) : route($tool['route']) }}" class="group flex min-h-28 items-start justify-between gap-3 rounded-2xl border border-border bg-surface p-4 shadow-sm transition hover:border-primary-300 hover:shadow-md">
                            <span><span class="block font-semibold text-text">{{ $tool['title'] }}</span><span class="mt-1 block text-sm leading-5 text-muted">{{ $tool['description'] }}</span></span>
                            <span aria-hidden="true" class="mt-0.5 text-lg text-primary-700 transition group-hover:translate-x-0.5">→</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-owner-layout>
