<div class="space-y-5">
    <div class="flex items-center justify-between gap-3"><div><p class="text-sm text-muted">{{ $demo['report_date'] }}</p><h2 class="text-xl font-bold text-text">Ringkasan hari ini</h2></div><x-ui.badge variant="success">Toko aktif</x-ui.badge></div>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
        <x-ui.card><p class="text-xs text-muted">Omzet hari ini</p><p class="mt-2 text-lg font-bold text-text">Rp{{ number_format($demo['summary']['revenue'], 0, ',', '.') }}</p></x-ui.card>
        <x-ui.card><p class="text-xs text-muted">Transaksi</p><p class="mt-2 text-lg font-bold text-text">{{ $demo['summary']['orders'] }}</p></x-ui.card>
        <x-ui.card class="col-span-2 sm:col-span-1"><p class="text-xs text-muted">Rata-rata transaksi</p><p class="mt-2 text-lg font-bold text-text">Rp{{ number_format($demo['summary']['average'], 0, ',', '.') }}</p></x-ui.card>
    </div>
    <x-ui.card>
        <div class="flex items-center justify-between"><h3 class="font-semibold text-text">Penjualan 7 hari</h3><a href="/demo/report{{ $query }}" class="inline-flex min-h-11 items-center text-sm font-medium text-primary-700">Lihat laporan</a></div>
        <div class="mt-5 flex h-40 items-end justify-between gap-2" role="img" aria-label="Grafik contoh penjualan tujuh hari">
            @foreach($demo['chart'] as $index => $height)
                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2"><div class="w-full rounded-t-control bg-primary-500" style="height: {{ $height }}%"></div><span class="text-[10px] text-muted">{{ ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'][$index] }}</span></div>
            @endforeach
        </div>
    </x-ui.card>
    <x-ui.card>
        <div class="flex items-center justify-between"><h3 class="font-semibold text-text">Produk terlaris</h3><a href="/demo/report{{ $query }}" class="inline-flex min-h-11 items-center text-sm font-medium text-primary-700">Semua</a></div>
        <ul class="mt-2 divide-y divide-border">@foreach($demo['best_sellers'] as $product)<li class="flex min-h-14 items-center justify-between gap-3 py-2"><span class="text-sm font-medium text-text">{{ $product['name'] }}</span><span class="text-sm text-muted">{{ $product['quantity'] }} terjual</span></li>@endforeach</ul>
    </x-ui.card>
</div>
