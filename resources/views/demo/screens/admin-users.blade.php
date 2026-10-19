<div class="space-y-4">
    <div><x-ui.badge>Admin platform</x-ui.badge><h2 class="mt-2 font-semibold text-text">Daftar pengguna</h2><p class="text-sm text-muted">Pengguna terdaftar pada platform contoh.</p></div>
    <label><span class="sr-only">Cari pengguna</span><input type="search" class="ui-control" placeholder="Cari nama atau email"></label>
    <x-ui.card padding="p-0"><ul class="divide-y divide-border">@foreach($demo['users'] as $user)<li class="flex min-h-20 items-center gap-3 p-3"><div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 font-semibold text-primary-700">{{ substr($user['name'], 0, 1) }}</div><div class="min-w-0 flex-1"><p class="font-semibold text-text">{{ $user['name'] }}</p><p class="truncate text-xs text-muted">{{ $user['email'] }}</p><p class="text-xs text-muted">{{ $user['store'] }} · {{ $user['joined'] }}</p></div><button type="button" class="min-h-11 rounded-control px-3 text-sm text-danger">Nonaktifkan</button></li>@endforeach</ul></x-ui.card>
    <x-ui.button href="/demo/admin-stores{{ $query }}" variant="secondary">Kembali ke toko</x-ui.button>
</div>
