<div class="space-y-4">
    <div><x-ui.badge>Admin platform</x-ui.badge><h2 class="mt-2 font-semibold text-text">Daftar toko</h2><p class="text-sm text-muted">Ringkasan toko contoh di platform.</p></div>
    <label><span class="sr-only">Cari toko</span><input type="search" class="ui-control" placeholder="Cari nama toko atau pemilik"></label>
    <x-ui.card padding="p-0"><ul class="divide-y divide-border">@foreach($demo['stores'] as $store)<li class="flex min-h-20 items-center justify-between gap-3 p-3"><div><p class="font-semibold text-text">{{ $store['name'] }}</p><p class="text-xs text-muted">Pemilik: {{ $store['owner'] }} · {{ $store['members'] }} anggota</p></div><div class="flex items-center gap-2"><x-ui.badge variant="{{ $store['status'] === 'Aktif' ? 'success' : 'warning' }}">{{ $store['status'] }}</x-ui.badge><button type="button" class="min-h-11 min-w-11 rounded-control text-muted" aria-label="Menu toko">•••</button></div></li>@endforeach</ul></x-ui.card>
    <x-ui.button href="/demo/admin-users{{ $query }}" variant="secondary">Kelola pengguna</x-ui.button>
</div>
