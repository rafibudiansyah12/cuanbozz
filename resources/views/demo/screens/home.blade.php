<div class="space-y-6">
    <x-ui.card>
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-control bg-primary-600 text-lg font-bold text-on-primary">{{ $demo['store']['logo'] }}</div>
            <div><h2 class="text-lg font-semibold text-text">CuanBozz UI demo</h2><p class="text-sm text-muted">Pilih layar untuk menjelajahi rancangan statis.</p></div>
        </div>
    </x-ui.card>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach([
            'login' => 'Masuk', 'register' => 'Daftar', 'forgot-password' => 'Lupa kata sandi', 'onboarding' => 'Buat toko',
            'dashboard' => 'Dashboard', 'pos' => 'Kasir & keranjang', 'payment' => 'Pembayaran', 'receipt' => 'Struk',
            'products' => 'Daftar produk', 'product-form' => 'Form produk & opsi', 'categories' => 'Kategori',
            'stock-adjustment' => 'Penyesuaian stok', 'stock-history' => 'Riwayat stok', 'orders' => 'Riwayat transaksi',
            'order-detail' => 'Detail & pembatalan', 'report' => 'Laporan', 'store-profile' => 'Profil toko',
            'members' => 'Kelola kasir', 'appearance' => 'Tampilan toko', 'admin-stores' => 'Admin: toko', 'admin-users' => 'Admin: pengguna',
        ] as $target => $label)
            @if($role !== 'cashier' || ! in_array($target, ['dashboard', 'report', 'product-form', 'categories', 'stock-adjustment', 'stock-history', 'store-profile', 'members', 'appearance', 'admin-stores', 'admin-users'], true))
                <a href="/demo/{{ $target }}{{ $query }}" class="flex min-h-14 items-center justify-between gap-3 rounded-control border border-border bg-surface p-4 text-sm font-medium text-text hover:border-primary-600 hover:bg-primary-50">
                    <span>{{ $label }}</span><span class="text-primary-700" aria-hidden="true">→</span>
                </a>
            @endif
        @endforeach
    </div>
    <x-ui.card>
        <h2 class="font-semibold text-text">Mode saat ini</h2>
        <p class="mt-1 text-sm text-muted">{{ $role === 'cashier' ? 'Kasir: menu laporan dan pengaturan toko disembunyikan.' : 'Pemilik: seluruh menu toko tersedia.' }}</p>
        <div class="mt-3 flex flex-wrap gap-2"><x-ui.button href="/demo/pos{{ $query }}">Mulai dari kasir</x-ui.button><x-ui.button href="/_design" variant="secondary">Lihat komponen T0.2</x-ui.button></div>
    </x-ui.card>
</div>
