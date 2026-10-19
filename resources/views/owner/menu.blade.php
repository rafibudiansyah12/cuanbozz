@php
    $screenLink = fn (string $routeName, string $screenName) => $previewMode
        ? route('owner.preview', ['screen' => $screenName])
        : route($routeName);
    $menuItems = [];

    if ($isOwner || $isAdmin) {
        $menuItems[] = ['group' => 'Inventaris', 'title' => 'Penyesuaian stok', 'description' => 'Perbarui jumlah stok dan catat alasannya.', 'route' => 'owner.stock-adjustment', 'screen' => 'stock-adjustment'];
        $menuItems[] = ['group' => 'Inventaris', 'title' => 'Riwayat stok', 'description' => 'Lihat perubahan stok yang tercatat.', 'route' => 'owner.stock-history', 'screen' => 'stock-history'];
    }

    if ($isOwner) {
        $menuItems[] = ['group' => 'Pengaturan toko', 'title' => 'Profil toko', 'description' => 'Ubah identitas dan informasi toko.', 'route' => 'owner.store-profile', 'screen' => 'store-profile'];
        $menuItems[] = ['group' => 'Pengaturan toko', 'title' => 'Anggota toko', 'description' => 'Atur akses admin toko dan kasir.', 'route' => 'owner.members', 'screen' => 'members'];
        $menuItems[] = ['group' => 'Pengaturan toko', 'title' => 'Tampilan toko', 'description' => 'Sesuaikan warna dan tampilan aplikasi.', 'route' => 'owner.appearance', 'screen' => 'appearance'];
        $menuItems[] = ['group' => 'Pengaturan toko', 'title' => 'Pembayaran', 'description' => 'Periksa konfigurasi pembayaran nontunai.', 'route' => 'owner.payment-settings', 'screen' => 'payments'];
    }
@endphp

<x-owner-layout title="Menu" :preview-mode="$previewMode" :store="$store">
    <div class="space-y-6">
        <section class="rounded-3xl border border-primary-200/70 bg-gradient-to-br from-primary-100/60 via-surface/60 to-accent/15 p-5 shadow-lg shadow-primary-900/5 backdrop-blur-xl sm:p-7">
            <p class="text-sm font-semibold text-primary-800">Menu toko</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-text">Pintasan sesuai peran</h2>
            <p class="mt-1 text-sm text-muted">Pengaturan dan alat kerja dikelompokkan agar mudah ditemukan.</p>
        </section>

        @foreach(collect($menuItems)->groupBy('group') as $group => $items)
            <section class="space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-muted">{{ $group }}</h3>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($items as $item)
                        <a href="{{ $screenLink($item['route'], $item['screen']) }}" class="group rounded-2xl border border-border/70 bg-surface/65 p-4 shadow-lg shadow-primary-900/5 backdrop-blur-xl transition hover:-translate-y-0.5 hover:border-primary-300 hover:bg-surface/85 hover:shadow-xl">
                            <span class="text-xs font-bold uppercase tracking-wider text-primary-700">{{ $item['group'] }}</span>
                            <span class="mt-2 block text-base font-bold text-text">{{ $item['title'] }}</span>
                            <span class="mt-1 block text-sm leading-5 text-muted">{{ $item['description'] }}</span>
                            <span class="mt-3 block text-sm font-semibold text-primary-800">Buka menu <span aria-hidden="true">→</span></span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach

    </div>
</x-owner-layout>
