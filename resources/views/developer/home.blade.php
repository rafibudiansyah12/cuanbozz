<x-developer-layout :navigation-items="$navigation">
    <div class="space-y-5">
        <div><p class="text-sm text-muted">Panel Developer</p><h1 class="text-2xl font-bold text-text">Beranda</h1></div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            @foreach([
                ['label' => 'Menunggu', 'value' => $counts['pending'], 'variant' => 'warning'],
                ['label' => 'Aktif', 'value' => $counts['approved'], 'variant' => 'success'],
                ['label' => 'Ditolak', 'value' => $counts['rejected'], 'variant' => 'danger'],
                ['label' => 'Ditangguhkan', 'value' => $counts['suspended'], 'variant' => 'muted'],
                ['label' => 'Total toko', 'value' => $counts['stores'], 'variant' => 'primary'],
            ] as $card)
                <x-ui.card><p class="text-xs text-muted">{{ $card['label'] }}</p><p class="mt-2 text-2xl font-bold text-text">{{ number_format($card['value']) }}</p><x-ui.badge class="mt-2" :variant="$card['variant']">{{ $card['label'] }}</x-ui.badge></x-ui.card>
            @endforeach
        </div>

        <x-ui.card>
            <div class="flex items-center justify-between gap-3"><div><h2 class="font-semibold text-text">Pendaftar menunggu</h2><p class="text-sm text-muted">5 pendaftar terbaru</p></div><x-ui.button href="{{ route('developer.registrations', ['status' => 'pending']) }}" variant="secondary">Lihat semua</x-ui.button></div>
            @if($latestApplicants->isEmpty())
                <x-ui.empty-state class="mt-4" title="Tidak ada pendaftar menunggu" description="Pendaftaran baru akan muncul di sini." />
            @else
                <ul class="mt-3 divide-y divide-border">@foreach($latestApplicants as $applicant)<li class="flex min-h-16 items-center justify-between gap-3 py-2"><div class="min-w-0"><p class="truncate font-medium text-text">{{ $applicant->name }}</p><p class="truncate text-sm text-muted">{{ $applicant->email }} · {{ $applicant->ownedStores->first()?->name ?? 'Belum ada toko' }}</p></div><a class="inline-flex min-h-11 shrink-0 items-center text-sm font-semibold text-primary-700" href="{{ route('developer.registrations.show', $applicant) }}">Tinjau</a></li>@endforeach</ul>
            @endif
        </x-ui.card>
    </div>
</x-developer-layout>
