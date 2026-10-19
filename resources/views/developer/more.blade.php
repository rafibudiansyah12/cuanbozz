<x-developer-layout :navigation-items="$navigation">
    <div class="space-y-4"><div><h1 class="text-2xl font-bold text-text">Lainnya</h1><p class="text-sm text-muted">Pengaturan developer dan catatan platform.</p></div>
        <x-ui.card padding="p-0"><nav aria-label="Menu lainnya" class="divide-y divide-border"><a class="flex min-h-14 items-center justify-between px-4 font-medium text-text" href="{{ route('developer.activity') }}">Log Aktivitas <span aria-hidden="true">→</span></a><a class="flex min-h-14 items-center justify-between px-4 font-medium text-text" href="{{ route('developer.account.edit') }}">Akun Saya <span aria-hidden="true">→</span></a><form method="POST" action="{{ route('logout') }}" class="px-4 py-2">@csrf<x-ui.button type="submit" variant="ghost" class="w-full">Keluar</x-ui.button></form></nav></x-ui.card>
    </div>
</x-developer-layout>
