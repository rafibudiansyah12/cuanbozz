@props(['navigationItems' => []])

@php
    $secondaryLinks = [
        ['label' => 'Log Aktivitas', 'route' => 'developer.activity', 'active' => request()->routeIs('developer.activity')],
        ['label' => 'Akun Saya', 'route' => 'developer.account.edit', 'active' => request()->routeIs('developer.account.*')],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim($__env->yieldContent('title', 'Panel Developer')) }} · CuanBozz</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-theme-variables />
</head>
<body class="min-h-screen min-w-[360px] bg-surface font-sans text-text antialiased">
    <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="min-h-screen">
        <a href="#main-content" class="sr-only z-50 rounded-control bg-surface p-3 text-text focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Lewati ke konten</a>

        <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-text/40 md:hidden" @click="sidebarOpen = false" aria-hidden="true"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 flex w-[min(18rem,85vw)] flex-col border-r border-border bg-surface shadow-xl transition-transform duration-200 md:translate-x-0 md:shadow-none">
            <div class="flex min-h-20 items-center justify-between border-b border-border px-5">
                <a href="{{ route('developer.home') }}" class="flex min-h-11 items-center gap-3 rounded-control text-text focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600 text-on-primary">
                        <x-application-logo class="h-6 w-6 fill-current" />
                    </span>
                    <span><span class="block text-base font-bold leading-tight">CuanBozz</span><span class="mt-1 block text-xs font-medium text-muted">Panel developer</span></span>
                </a>
                <button type="button" @click="sidebarOpen = false" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-control text-muted hover:bg-primary-50 hover:text-text focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 md:hidden" aria-label="Tutup menu">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke-linecap="round" /></svg>
                </button>
            </div>

            <nav aria-label="Navigasi developer" class="flex-1 space-y-2 overflow-y-auto px-3 py-5">
                <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-muted">Menu utama</p>
                @foreach($navigationItems as $item)
                    @php
                        $active = match ($item['label']) {
                            'Beranda' => request()->routeIs('developer.home'),
                            'Pendaftaran' => request()->routeIs('developer.registrations*'),
                            'Toko' => request()->routeIs('developer.stores*'),
                            'Pengguna' => request()->routeIs('developer.users*'),
                            'Lainnya' => request()->routeIs('developer.more', 'developer.activity', 'developer.account.*'),
                            default => false,
                        };
                    @endphp
                    <a href="{{ $item['href'] }}" @if($active) aria-current="page" @endif @click="sidebarOpen = false" class="group flex min-h-12 items-center gap-3 rounded-xl px-3 text-sm font-semibold transition {{ $active ? 'bg-primary-50 text-primary-800' : 'text-muted hover:bg-primary-50 hover:text-text' }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $active ? 'bg-primary-600 text-on-primary' : 'bg-surface text-muted group-hover:text-primary-700' }}">
                            @switch($item['label'])
                                @case('Beranda')<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="5" rx="2"/><rect x="13" y="10" width="8" height="11" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/></svg>@break
                                @case('Pendaftaran')<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 4h8l3 3v13H5V4h3Z" stroke-linejoin="round"/><path d="M9 12h6M9 16h6M9 8h2" stroke-linecap="round"/><path d="m15 4 4 4"/></svg>@break
                                @case('Toko')<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 10v10h16V10M3 9l2-5h14l2 5" stroke-linejoin="round"/><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0M9 20v-6h6v6" stroke-linejoin="round"/></svg>@break
                                @case('Pengguna')<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 5.5a3.5 3.5 0 0 1 0 6.8M17 14a5.5 5.5 0 0 1 4.5 5.4" stroke-linecap="round"/></svg>@break
                                @default<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/></svg>
                            @endswitch
                        </span>
                        <span class="min-w-0 flex-1">{{ $item['label'] }}</span>
                        @if(! empty($item['badge']))<span class="inline-flex min-h-6 min-w-6 items-center justify-center rounded-full bg-danger px-1.5 text-xs font-bold text-surface">{{ $item['badge'] > 99 ? '99+' : $item['badge'] }}</span>@endif
                    </a>
                @endforeach

                <div class="px-3 pb-2 pt-7 text-[11px] font-bold uppercase tracking-wider text-muted">Pengaturan</div>
                @foreach($secondaryLinks as $link)
                    <a href="{{ route($link['route']) }}" @if($link['active']) aria-current="page" @endif @click="sidebarOpen = false" class="flex min-h-12 items-center gap-3 rounded-xl px-3 text-sm font-semibold transition {{ $link['active'] ? 'bg-primary-50 text-primary-800' : 'text-muted hover:bg-primary-50 hover:text-text' }}">
                        @if($link['route'] === 'developer.activity')
                            <svg viewBox="0 0 24 24" class="ml-2 h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 19V5m0 14h16M7 15l4-4 3 2 5-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" class="ml-2 h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20a7 7 0 0 1 14 0" stroke-linecap="round"/></svg>
                        @endif
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-border p-4">
                <div class="flex items-center gap-3 rounded-xl bg-primary-50 p-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-on-primary">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="min-w-0 flex-1"><span class="block truncate text-sm font-semibold text-text">{{ auth()->user()->name }}</span><span class="block truncate text-xs text-muted">Developer</span></span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">@csrf
                    <button type="submit" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 text-sm font-semibold text-muted transition hover:bg-danger/10 hover:text-danger focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3m9-8h7a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="min-w-0 md:pl-72">
            <header class="sticky top-0 z-30 flex min-h-16 items-center justify-between border-b border-border bg-surface/95 px-4 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" @click="sidebarOpen = true" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl border border-border bg-surface text-text hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 md:hidden" aria-label="Buka menu">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>
                    </button>
                    <span class="text-sm font-semibold text-muted">Developer</span>
                    <span class="text-muted" aria-hidden="true">/</span>
                    <span class="text-sm font-bold text-text">{{ request()->routeIs('developer.home') ? 'Beranda' : (request()->routeIs('developer.registrations*') ? 'Pendaftaran' : (request()->routeIs('developer.stores*') ? 'Toko' : (request()->routeIs('developer.users*') ? 'Pengguna' : (request()->routeIs('developer.activity') ? 'Log Aktivitas' : (request()->routeIs('developer.account.*') ? 'Akun Saya' : 'Lainnya'))))) }}</span>
                </div>
                <a href="{{ route('developer.account.edit') }}" class="inline-flex min-h-11 items-center gap-2 rounded-xl px-2 text-sm font-semibold text-text hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600" aria-label="Buka akun saya">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-800">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="hidden max-w-40 truncate sm:block">{{ auth()->user()->name }}</span>
                </a>
            </header>
            <main id="main-content" class="mx-auto min-w-0 w-full max-w-[1440px] px-4 py-5 sm:px-6 sm:py-7 lg:px-8 lg:py-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
