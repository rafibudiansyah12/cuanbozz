@props(['items' => null])
@php
    $items ??= [
        ['label' => 'Kasir', 'href' => '/kasir', 'icon' => '▦'],
        ['label' => 'Produk', 'href' => '/produk', 'icon' => '□'],
        ['label' => 'Riwayat', 'href' => '/riwayat', 'icon' => '◷'],
        ['label' => 'Laporan', 'href' => '/laporan', 'icon' => '▤'],
        ['label' => 'Lainnya', 'href' => '/lainnya', 'icon' => '•••'],
    ];
@endphp
<nav aria-label="Navigasi utama" class="fixed inset-x-0 bottom-0 z-40 border-t border-border bg-surface/95 pb-[env(safe-area-inset-bottom)] backdrop-blur">
    <ul class="mx-auto grid max-w-xl" style="grid-template-columns: repeat({{ count($items) }}, minmax(0, 1fr))">
        @foreach($items as $item)
            @php($hrefPath = parse_url($item['href'], PHP_URL_PATH) ?: $item['href'])
            <li><a href="{{ $item['href'] }}" @if(request()->is(ltrim($hrefPath, '/')) || request()->is(ltrim($hrefPath, '/').'/*')) aria-current="page" @endif class="relative mx-1 flex min-h-14 flex-col items-center justify-center gap-0.5 rounded-2xl px-1 text-[11px] font-medium text-muted transition hover:text-primary-700 aria-[current=page]:bg-primary-50/85 aria-[current=page]:font-bold aria-[current=page]:text-primary-800"><span class="flex h-5 w-5 items-center justify-center" aria-hidden="true">
                @switch($item['label'])
                    @case('Kasir')<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="15" rx="2"/><path d="M7 9h10M7 13h2m3 0h2m3 0h.01M7 17h10" stroke-linecap="round"/></svg>@break
                    @case('Produk')<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 9 5-9 5-9-5 9-5Z" stroke-linejoin="round"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5" stroke-linejoin="round"/></svg>@break
                    @case('Riwayat')<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 4h8l3 3v13H5V4h3Z" stroke-linejoin="round"/><path d="M9 12h6M9 16h6M9 8h2" stroke-linecap="round"/></svg>@break
                    @case('Laporan')<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5m0 14h16M7 15l4-4 3 2 5-6" stroke-linecap="round" stroke-linejoin="round"/></svg>@break
                    @default<svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
                @endswitch
            </span><span>{{ $item['label'] }}</span>@if(! empty($item['badge']))<span class="absolute right-1/4 top-0.5 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-danger px-1 text-[10px] font-bold text-surface">{{ $item['badge'] > 99 ? '99+' : $item['badge'] }}</span>@endif</a></li>
        @endforeach
    </ul>
</nav>
