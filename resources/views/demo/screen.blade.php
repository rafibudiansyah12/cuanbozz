@if(in_array($screen, ['login', 'register', 'forgot-password'], true))
    <x-guest-layout>
        <div class="mb-5 flex items-center justify-between gap-3">
            <a href="/demo{{ $query }}" class="inline-flex min-h-11 items-center font-medium text-primary-700">← Semua layar</a>
            <x-ui.badge variant="warning">Data contoh</x-ui.badge>
        </div>
        @include('demo.screens.'.$screen)
    </x-guest-layout>
@else
<x-app-layout :navigation-items="$navigation">
    <x-slot name="header">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-text">{{ $screenTitle }}</h1>
                <p class="mt-1 text-sm text-muted">{{ $demo['store']['name'] }} · tampilan contoh, belum terhubung ke data asli</p>
            </div>
            <x-ui.badge variant="warning">Data contoh</x-ui.badge>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="/demo{{ $query }}" class="inline-flex min-h-11 items-center text-xs font-medium text-muted hover:text-primary-700">Layar</a>
        <a href="/demo/{{ $role === 'cashier' ? $screen : $roleSwitchScreen }}{{ $role === 'cashier' ? '' : '?role=cashier' }}" class="inline-flex min-h-11 items-center text-xs font-medium text-primary-700">
            {{ $role === 'cashier' ? 'Pemilik' : 'Kasir' }}
        </a>
    </x-slot>

    @include('demo.screens.'.$screen)
</x-app-layout>
@endif
