<div class="space-y-4">
    <x-ui.card><h2 class="font-semibold text-text">Menu lainnya</h2><p class="mt-1 text-sm text-muted">Pintasan menu berdasarkan peran saat ini: {{ $role === 'cashier' ? 'Kasir' : 'Pemilik' }}.</p></x-ui.card>
    <div class="grid gap-3 sm:grid-cols-2">
        @if($role === 'owner')
            @foreach(['store-profile' => 'Profil toko', 'members' => 'Kelola kasir', 'appearance' => 'Tampilan toko', 'report' => 'Laporan'] as $target => $label)
                <a href="/demo/{{ $target }}{{ $query }}" class="flex min-h-14 items-center justify-between rounded-control border border-border bg-surface p-4 font-medium text-text hover:bg-primary-50"><span>{{ $label }}</span><span class="text-primary-700">→</span></a>
            @endforeach
            <a href="/demo/admin-stores{{ $query }}" class="flex min-h-14 items-center justify-between rounded-control border border-border bg-surface p-4 font-medium text-text hover:bg-primary-50"><span>Admin platform · toko</span><span class="text-primary-700">→</span></a>
            <a href="/demo/admin-users{{ $query }}" class="flex min-h-14 items-center justify-between rounded-control border border-border bg-surface p-4 font-medium text-text hover:bg-primary-50"><span>Admin platform · pengguna</span><span class="text-primary-700">→</span></a>
        @else
            <a href="/demo/pos{{ $query }}" class="flex min-h-14 items-center rounded-control border border-border bg-surface p-4 font-medium text-text">Kasir</a>
            <a href="/demo/orders{{ $query }}" class="flex min-h-14 items-center rounded-control border border-border bg-surface p-4 font-medium text-text">Transaksi saya</a>
        @endif
    </div>
</div>
