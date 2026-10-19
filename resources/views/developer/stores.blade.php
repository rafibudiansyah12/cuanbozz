<x-developer-layout :navigation-items="$navigation">
    <div class="space-y-4">
        <div><h1 class="text-2xl font-bold text-text">Toko</h1><p class="text-sm text-muted">Daftar toko lintas platform dan status pemiliknya.</p></div>
        <x-ui.card>
            <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                <x-ui.input class="flex-1" label="Cari toko atau pemilik" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama toko, nama atau email pemilik" />
                <x-ui.button type="submit" class="sm:self-end">Cari</x-ui.button>
            </form>
        </x-ui.card>
        <x-ui.card padding="p-0">
            @if($stores->isEmpty())
                <x-ui.empty-state class="p-5" title="Toko tidak ditemukan" description="Ubah kata pencarian." />
            @else
                <ul class="divide-y divide-border">
                    @foreach($stores as $store)
                        <li class="space-y-3 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0"><h2 class="truncate font-semibold text-text">{{ $store->name }}</h2><p class="truncate text-sm text-muted">{{ $store->owner->name }} · {{ $store->owner->email }}</p></div>
                                <x-ui.badge :variant="$store->owner->approval_status === \App\Enums\AccountApprovalStatus::Approved && $store->is_active ? 'success' : 'danger'">{{ $store->owner->approval_status->value }} · {{ $store->is_active ? 'Toko aktif' : 'Toko ditangguhkan' }}</x-ui.badge>
                            </div>
                            <div class="flex items-center justify-between gap-2 text-xs text-muted">
                                <span>{{ $store->phone ?? 'Tanpa telepon' }}</span>
                                @if($store->is_active && $store->owner->approval_status === \App\Enums\AccountApprovalStatus::Approved)
                                    <form method="POST" action="{{ route('developer.stores.suspend', $store) }}" onsubmit="return confirm('Tangguhkan {{ addslashes($store->name) }} dan blokir pemilik serta kasirnya?')">@csrf<x-ui.button type="submit" variant="danger">Tangguhkan</x-ui.button></form>
                                @elseif(! $store->is_active && $store->owner->approval_status === \App\Enums\AccountApprovalStatus::Suspended)
                                    <form method="POST" action="{{ route('developer.stores.reactivate', $store) }}" onsubmit="return confirm('Aktifkan kembali {{ addslashes($store->name) }}?')">@csrf<x-ui.button type="submit">Aktifkan kembali</x-ui.button></form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
            <div class="border-t border-border p-4">{{ $stores->links() }}</div>
        </x-ui.card>
    </div>
</x-developer-layout>

