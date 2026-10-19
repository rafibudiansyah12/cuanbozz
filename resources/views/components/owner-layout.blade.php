@props(['title' => 'Beranda', 'previewMode' => null, 'store' => null])

@php
    $previewMode = $previewMode ?? request()->routeIs('owner.preview');
    $ownerName = auth()->user()?->name ?? 'Pemilik Demo';
    $isOwner = $previewMode || (auth()->user()?->ownedStores()->exists() ?? false);
    $memberRole = $previewMode || $isOwner ? null : auth()->user()?->storeMemberships()->where('store_id', $store?->id)->where('is_active', true)->value('role');
    $isAdmin = $memberRole === \App\Enums\StoreRole::Admin->value;
    $storeName = $store?->name ?? 'Toko Anda';
    $roleName = $previewMode || $isOwner ? 'Pemilik toko' : ($isAdmin ? 'Admin' : 'Kasir');
    $storeTheme = $store?->theme ?? [];
    $screenHref = fn (string $routeName, string $screen) => $previewMode
        ? route('owner.preview', ['screen' => $screen])
        : route($routeName);
    $links = $isOwner ? [
        ['group' => 'Utama', 'label' => 'Beranda', 'href' => $previewMode ? route('owner.preview') : route('dashboard'), 'icon' => 'home', 'active' => request()->routeIs('dashboard') || (request()->routeIs('owner.preview') && in_array(request()->route('screen'), [null, 'dashboard'], true))],
        ['group' => 'Operasional', 'label' => 'Kasir', 'href' => $screenHref('owner.pos', 'pos'), 'icon' => 'cashier', 'active' => request()->routeIs('owner.pos') || request()->route('screen') === 'pos'],
        ['group' => 'Katalog', 'label' => 'Produk', 'href' => $screenHref('owner.products', 'products'), 'icon' => 'products', 'active' => request()->routeIs('owner.products*') || request()->route('screen') === 'products'],
        ['group' => 'Katalog', 'label' => 'Kategori', 'href' => $screenHref('owner.categories', 'categories'), 'icon' => 'category', 'active' => request()->routeIs('owner.categories') || request()->route('screen') === 'categories'],
        ['group' => 'Penjualan', 'label' => 'Transaksi', 'href' => $screenHref('owner.orders', 'orders'), 'icon' => 'orders', 'active' => request()->routeIs('owner.orders*') || request()->route('screen') === 'orders'],
        ['group' => 'Penjualan', 'label' => 'Laporan', 'href' => $screenHref('owner.reports', 'report'), 'icon' => 'report', 'active' => request()->routeIs('owner.reports') || request()->route('screen') === 'report'],
    ] : ($isAdmin ? [
        ['group' => 'Operasional', 'label' => 'Kasir', 'href' => $screenHref('owner.pos', 'pos'), 'icon' => 'cashier', 'active' => request()->routeIs('owner.pos') || request()->route('screen') === 'pos'],
        ['group' => 'Katalog', 'label' => 'Produk', 'href' => $screenHref('owner.products', 'products'), 'icon' => 'products', 'active' => request()->routeIs('owner.products*') || request()->route('screen') === 'products'],
        ['group' => 'Katalog', 'label' => 'Kategori', 'href' => $screenHref('owner.categories', 'categories'), 'icon' => 'category', 'active' => request()->routeIs('owner.categories') || request()->route('screen') === 'categories'],
        ['group' => 'Penjualan', 'label' => 'Transaksi', 'href' => $screenHref('owner.orders', 'orders'), 'icon' => 'orders', 'active' => request()->routeIs('owner.orders*') || request()->route('screen') === 'orders'],
        ['group' => 'Penjualan', 'label' => 'Laporan', 'href' => $screenHref('owner.reports', 'report'), 'icon' => 'report', 'active' => request()->routeIs('owner.reports') || request()->route('screen') === 'report'],
    ] : [
        ['group' => 'Operasional', 'label' => 'Kasir', 'href' => $screenHref('owner.pos', 'pos'), 'icon' => 'cashier', 'active' => request()->routeIs('owner.pos') || request()->route('screen') === 'pos'],
        ['group' => 'Katalog', 'label' => 'Produk', 'href' => $screenHref('owner.products', 'products'), 'icon' => 'products', 'active' => request()->routeIs('owner.products*') || request()->route('screen') === 'products'],
        ['group' => 'Riwayat', 'label' => 'Transaksi saya', 'href' => $screenHref('owner.orders', 'orders'), 'icon' => 'orders', 'active' => request()->routeIs('owner.orders*') || request()->route('screen') === 'orders'],
    ]);
    $groupedLinks = collect($links)->groupBy('group');
    $linkGroups = collect(['Utama', 'Operasional', 'Katalog', 'Penjualan', 'Riwayat'])
        ->filter(fn (string $group) => $groupedLinks->has($group))
        ->mapWithKeys(fn (string $group) => [$group => $groupedLinks->get($group)]);
    $inventory = ($isOwner || $isAdmin) ? [
            ['label' => 'Penyesuaian stok', 'href' => $screenHref('owner.stock-adjustment', 'stock-adjustment'), 'screen' => 'stock-adjustment', 'icon' => 'adjustment'],
            ['label' => 'Riwayat stok', 'href' => $screenHref('owner.stock-history', 'stock-history'), 'screen' => 'stock-history', 'icon' => 'history'],
    ] : [];
    $settings = $isOwner ? [
            ['label' => 'Profil toko', 'href' => $screenHref('owner.store-profile', 'store-profile'), 'screen' => 'store-profile', 'icon' => 'store'],
            ['label' => 'Anggota toko', 'href' => $screenHref('owner.members', 'members'), 'screen' => 'members', 'icon' => 'team'],
            ['label' => 'Tampilan toko', 'href' => $screenHref('owner.appearance', 'appearance'), 'screen' => 'appearance', 'icon' => 'theme'],
            ['label' => 'Pembayaran', 'href' => $screenHref('owner.payment-settings', 'payments'), 'screen' => 'payments', 'icon' => 'payment'],
    ] : [];
    $settingsActive = collect($settings)->contains(fn (array $item) => request()->route('screen') === $item['screen']);
    $bottomLinks = $isOwner
        ? [
            ['label' => 'Kasir', 'href' => $screenHref('owner.pos', 'pos'), 'icon' => 'cashier'],
            ['label' => 'Produk', 'href' => $screenHref('owner.products', 'products'), 'icon' => 'products'],
            ['label' => 'Riwayat', 'href' => $screenHref('owner.orders', 'orders'), 'icon' => 'orders'],
            ['label' => 'Laporan', 'href' => $screenHref('owner.reports', 'report'), 'icon' => 'report'],
            ['label' => 'Menu', 'href' => $screenHref('owner.more', 'more'), 'icon' => 'more'],
        ]
        : ($isAdmin ? [
            ['label' => 'Kasir', 'href' => $screenHref('owner.pos', 'pos'), 'icon' => 'cashier'],
            ['label' => 'Produk', 'href' => $screenHref('owner.products', 'products'), 'icon' => 'products'],
            ['label' => 'Riwayat', 'href' => $screenHref('owner.orders', 'orders'), 'icon' => 'orders'],
            ['label' => 'Laporan', 'href' => $screenHref('owner.reports', 'report'), 'icon' => 'report'],
            ['label' => 'Menu', 'href' => $screenHref('owner.more', 'more'), 'icon' => 'more'],
        ] : [
            ['label' => 'Kasir', 'href' => $screenHref('owner.pos', 'pos'), 'icon' => 'cashier'],
            ['label' => 'Produk', 'href' => $screenHref('owner.products', 'products'), 'icon' => 'products'],
            ['label' => 'Riwayat', 'href' => $screenHref('owner.orders', 'orders'), 'icon' => 'orders'],
            ['label' => 'Menu', 'href' => $screenHref('owner.more', 'more'), 'icon' => 'more'],
        ]);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} &middot; CuanBozz</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-theme-variables :primary="$storeTheme['primary'] ?? null" :accent="$storeTheme['accent'] ?? null" :radius="$storeTheme['radius'] ?? null" />
</head>
<body class="min-h-screen min-w-[360px] bg-gradient-to-br from-primary-50 via-surface to-accent/10 font-sans text-text antialiased">
    <a href="#owner-main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-control focus:bg-surface focus:p-3">Lewati ke konten</a>
    <aside class="fixed inset-y-0 left-0 z-30 hidden w-60 flex-col overflow-y-auto border-r border-border/70 bg-surface/90 shadow-xl shadow-primary-900/5 backdrop-blur-2xl lg:flex">
        <div class="min-h-24 border-b border-border px-5 py-4">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-600 text-on-primary"><x-application-logo class="h-7 w-7 fill-current" /></span>
            <span class="min-w-0 flex-1"><span class="block truncate text-xl font-extrabold leading-tight tracking-tight text-text" title="{{ $storeName }}">{{ $storeName }}</span><span class="mt-1 block truncate text-sm font-semibold leading-5 text-muted">{{ $ownerName }}</span><span class="block text-xs font-medium text-muted">{{ $roleName }}</span></span>
        </a>
        </div>
        <nav aria-label="Navigasi toko" class="flex-1 space-y-2 px-3 pb-5 pt-6">
            <p class="px-3 pb-1 text-[10px] font-bold uppercase tracking-[0.18em] text-muted">Navigasi utama</p>
            @foreach($linkGroups as $groupName => $groupLinks)
                @if($groupLinks->count() > 1)
                    <details class="group/menu-section" @if($groupLinks->contains(fn (array $link) => $link['active'])) open @endif>
                        <summary class="flex min-h-12 cursor-pointer list-none items-center gap-3 rounded-xl border border-transparent px-3 text-sm font-bold text-text transition hover:border-border/70 hover:bg-surface/80 [&::-webkit-details-marker]:hidden">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-700" aria-hidden="true">
                                @if($groupName === 'Katalog')<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg>@else<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5m0 14h16M7 15l4-4 3 2 5-6" stroke-linecap="round" stroke-linejoin="round"/></svg>@endif
                            </span>
                            <span class="flex-1">{{ $groupName }}</span><svg viewBox="0 0 24 24" class="h-4 w-4 text-muted transition group-open/menu-section:rotate-180" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </summary>
                        <div class="ml-4 mt-2 space-y-1 border-l border-primary-200/70 pb-2 pl-3">
                            @foreach($groupLinks as $link)
                                <a href="{{ $link['href'] }}" @if($link['active']) aria-current="page" @endif class="group flex min-h-10 items-center gap-2.5 rounded-lg px-2 text-sm font-medium transition {{ $link['active'] ? 'bg-primary-100/75 text-primary-900' : 'text-muted hover:bg-primary-50/70 hover:text-primary-900' }}">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-surface/70 text-primary-700 transition group-hover:bg-surface" aria-hidden="true">
                                        @switch($link['icon'])
                                            @case('products')<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 9 5-9 5-9-5 9-5Z" stroke-linejoin="round"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5M12 8v5" stroke-linejoin="round"/></svg>@break
                                            @case('category')<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/></svg>@break
                                            @default<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 4h8l3 3v13H5V4h3Z" stroke-linejoin="round"/><path d="M9 12h6M9 16h6M9 8h2" stroke-linecap="round"/></svg>
                                        @endswitch
                                    </span><span>{{ $link['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </details>
                @else
                    @foreach($groupLinks as $link)
                        <a href="{{ $link['href'] }}" @if($link['active']) aria-current="page" @endif class="group flex min-h-12 items-center gap-3 rounded-xl border px-3 text-sm font-semibold transition {{ $link['active'] ? 'border-primary-200/80 bg-gradient-to-r from-primary-100/90 via-primary-50/85 to-surface/70 text-primary-900 shadow-sm' : 'border-transparent text-muted hover:border-border hover:bg-primary-50/70 hover:text-text' }}">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg {{ $link['active'] ? 'bg-surface/80 text-primary-800 shadow-sm' : 'bg-primary-50 text-primary-700 group-hover:bg-surface' }}">
                                @if($link['icon'] === 'home')<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1V10Z" stroke-linejoin="round"/></svg>@else<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="15" rx="2"/><path d="M7 9h10M7 13h2m3 0h2m3 0h.01M7 17h10" stroke-linecap="round"/></svg>@endif
                            </span>{{ $link['label'] }}
                        </a>
                    @endforeach
                @endif
            @endforeach
            @if($inventory)
                <details class="group/menu-section" @if(collect($inventory)->contains(fn (array $link) => request()->route('screen') === $link['screen'])) open @endif>
                    <summary class="flex min-h-12 cursor-pointer list-none items-center gap-3 rounded-xl border border-transparent px-3 text-sm font-bold text-text transition hover:border-border/70 hover:bg-surface/80 [&::-webkit-details-marker]:hidden">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-700" aria-hidden="true"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 7 9-4 9 4-9 4-9-4Z" stroke-linejoin="round"/><path d="m3 12 9 4 9-4M3 17l9 4 9-4" stroke-linejoin="round"/></svg></span>
                        <span class="flex-1">Stok</span><svg viewBox="0 0 24 24" class="h-4 w-4 text-muted transition group-open/menu-section:rotate-180" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </summary>
                    <div class="ml-4 mt-2 space-y-1 border-l border-primary-200/70 pb-2 pl-3">
                        @foreach($inventory as $link)
                            <a href="{{ $link['href'] }}" @if(request()->route('screen') === $link['screen']) aria-current="page" @endif class="group flex min-h-10 items-center gap-2.5 rounded-lg px-2 text-sm font-medium transition {{ request()->route('screen') === $link['screen'] ? 'bg-primary-100/75 text-primary-900' : 'text-muted hover:bg-primary-50/70 hover:text-primary-900' }}">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-surface/70 text-primary-700 transition group-hover:bg-surface" aria-hidden="true">@if($link['icon'] === 'adjustment')<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h10m4 0h2M4 17h2m4 0h10" stroke-linecap="round"/><circle cx="16" cy="7" r="2"/><circle cx="8" cy="17" r="2"/></svg>@else<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 2.6-6.4L3 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5m4-1v5l3 2" stroke-linecap="round" stroke-linejoin="round"/></svg>@endif</span><span>{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </details>
            @endif
            @if($settings)
                <details class="group/settings mt-3 border-t border-border/70 pt-3" @if($settingsActive) open @endif>
                    <summary class="flex min-h-12 cursor-pointer list-none items-center gap-3 rounded-xl px-3 text-sm font-bold text-text transition hover:bg-primary-50/70 [&::-webkit-details-marker]:hidden">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-100/70 text-primary-800" aria-hidden="true"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/><circle cx="9" cy="6" r="2" fill="currentColor"/><circle cx="15" cy="12" r="2" fill="currentColor"/><circle cx="7" cy="18" r="2" fill="currentColor"/></svg></span>
                        <span class="flex-1">Pengaturan toko</span><svg viewBox="0 0 24 24" class="h-4 w-4 text-muted transition group-open/settings:rotate-180" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </summary>
                    <div class="ml-4 mt-2 space-y-1 border-l border-primary-200/70 pb-2 pl-3">
                                @foreach($settings as $link)
                                    <a href="{{ $link['href'] }}" @if(request()->route('screen') === $link['screen']) aria-current="page" @endif class="group flex min-h-10 items-center gap-2.5 rounded-lg px-2 text-sm font-medium transition {{ request()->route('screen') === $link['screen'] ? 'bg-primary-100/75 text-primary-900' : 'text-muted hover:bg-primary-50/70 hover:text-primary-900' }}">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-surface/70 text-primary-700 transition group-hover:bg-surface" aria-hidden="true">
                                @switch($link['icon'])
                                    @case('adjustment')<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h10m4 0h2M4 17h2m4 0h10" stroke-linecap="round"/><circle cx="16" cy="7" r="2"/><circle cx="8" cy="17" r="2"/></svg>@break
                                    @case('history')<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 2.6-6.4L3 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3v5h5m4-1v5l3 2" stroke-linecap="round" stroke-linejoin="round"/></svg>@break
                                    @case('store')<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10v10h16V10M3 10l2-6h14l2 6M3 10a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0M9 20v-5h6v5" stroke-linecap="round" stroke-linejoin="round"/></svg>@break
                                    @case('team')<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0m2-9a3 3 0 1 0-1-5.8M18 14a5 5 0 0 1 3 4.6" stroke-linecap="round"/></svg>@break
                                    @case('theme')<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3a9 9 0 1 0 0 18h1.2a2 2 0 0 0 1.5-3.3 1.8 1.8 0 0 1 1.4-3h1A4.9 4.9 0 0 0 22 9.8C22 6 17.5 3 12 3Z"/><circle cx="7.5" cy="10" r=".8"/><circle cx="11" cy="7.5" r=".8"/><circle cx="15" cy="8" r=".8"/></svg>@break
                                    @default<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18m-14 5h4" stroke-linecap="round"/></svg>
                                @endswitch
                                        </span><span>{{ $link['label'] }}</span>
                                    </a>
                                @endforeach
                    </div>
                </details>
            @endif
        </nav>
        <div class="border-t border-border p-4">
            @auth
                <form method="POST" action="{{ route('logout') }}" class="mt-2">@csrf
                    <button type="submit" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 text-sm font-semibold text-muted transition hover:bg-danger/10 hover:text-danger"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-8h7a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-7" stroke-linecap="round" stroke-linejoin="round"/></svg>Keluar</button>
                </form>
            @endauth
        </div>
    </aside>
        <div class="min-w-0 lg:pl-60">
        <header class="sticky top-0 z-20 flex min-h-16 items-center justify-between gap-4 border-b border-border/70 bg-surface/65 px-4 shadow-sm backdrop-blur-2xl sm:px-6 lg:px-8">
            <h1 class="min-w-0 truncate text-xl font-extrabold leading-tight tracking-tight text-primary-900 sm:text-2xl">{{ $title }}</h1>
            <div class="flex items-center gap-3">
                <span class="hidden text-sm text-muted md:block">{{ now()->translatedFormat('l, j F Y') }}</span>
                @if($previewMode)
                    <span class="flex h-10 w-10 items-center justify-center rounded-full border border-border bg-primary-50 font-bold text-primary-800" aria-label="{{ $ownerName }}">{{ mb_strtoupper(mb_substr($ownerName, 0, 1)) }}</span>
                @else
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="true" aria-label="Menu akun {{ $ownerName }}" class="flex min-h-11 items-center gap-2 rounded-full border border-border bg-surface/80 p-1 pr-3 text-sm font-semibold text-text shadow-sm transition hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 font-bold text-primary-800">{{ mb_strtoupper(mb_substr($ownerName, 0, 1)) }}</span>
                            <span class="hidden max-w-36 truncate sm:block">{{ $ownerName }}</span>
                            <svg viewBox="0 0 24 24" class="hidden h-4 w-4 text-muted sm:block" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div x-cloak x-show="open" x-transition.origin.top.right class="absolute right-0 top-full z-40 mt-2 w-60 rounded-2xl border border-border bg-surface p-2 shadow-xl" role="menu">
                            <div class="border-b border-border px-3 py-2"><p class="truncate text-sm font-semibold text-text">{{ $ownerName }}</p><p class="text-xs text-muted">{{ auth()->user()?->email }}</p></div>
                            <a href="{{ route('profile.edit') }}" role="menuitem" class="mt-1 flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm font-medium text-text transition hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600">
                                <svg viewBox="0 0 24 24" class="h-5 w-5 text-primary-700" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20a7 7 0 0 1 14 0" stroke-linecap="round"/><path d="m17 5 2 2m-1-3 2 2" stroke-linecap="round"/></svg>Profil & keamanan akun
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-border pt-1">@csrf
                                <button type="submit" role="menuitem" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 text-left text-sm font-medium text-danger transition hover:bg-danger/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-8h7a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-7" stroke-linecap="round" stroke-linejoin="round"/></svg>Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </header>
        <main id="owner-main" class="mx-auto w-full max-w-[1440px] px-4 pb-24 pt-5 sm:px-6 sm:pb-28 sm:pt-7 lg:px-8 lg:py-8">{{ $slot }}</main>
    </div>
    <div class="lg:hidden"><x-ui.bottom-nav :items="$bottomLinks" /></div>
</body>
</html>
