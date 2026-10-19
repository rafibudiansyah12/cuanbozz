<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'CuanBozz') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <x-theme-variables :primary="$theme['primary'] ?? ($primaryColor ?? null)" :accent="$theme['accent'] ?? null" :radius="$theme['radius'] ?? null" />
    </head>
    <body class="min-h-screen overflow-x-hidden bg-surface font-sans text-text antialiased">
        <div class="min-h-screen pb-20">
            <header class="sticky top-0 z-30 border-b border-border bg-surface">
                <div class="mx-auto flex min-h-14 max-w-7xl items-center justify-between px-4">
                    <a href="{{ url('/') }}" class="flex min-h-11 items-center gap-2 font-semibold text-primary-700" aria-label="CuanBozz beranda">
                        <x-application-logo class="h-7 w-7 fill-current" />
                        <span>CuanBozz</span>
                    </a>
                    <div class="flex items-center gap-3">{{ $headerActions ?? '' }}</div>
                </div>
                @isset($header)
                    <div class="mx-auto max-w-7xl px-4 pb-3">{{ $header }}</div>
                @endisset
            </header>
            <main class="mx-auto min-w-0 w-full max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>
        <x-ui.bottom-nav :items="$navigationItems ?? null" />
    </body>
</html>
