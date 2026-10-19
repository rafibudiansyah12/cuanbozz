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
        <x-theme-variables />
    </head>
    <body class="min-h-screen bg-surface px-4 py-6 font-sans text-text antialiased sm:px-6 sm:py-8 {{ $wide ? '' : 'flex items-center justify-center' }}">
        <main class="mx-auto w-full {{ $wide ? 'max-w-7xl' : 'max-w-md' }}">
            @unless($wide)
                <a href="{{ url('/') }}" class="mx-auto mb-6 flex min-h-11 w-fit items-center gap-2 font-semibold text-primary-700">
                    <x-application-logo class="h-8 w-8 fill-current" />
                    <span>CuanBozz</span>
                </a>
            @endunless
            @if($wide)
                {{ $slot }}
            @else
                <section class="rounded-control border border-border bg-surface p-5 shadow-sm sm:p-8">
                    {{ $slot }}
                </section>
            @endif
        </main>
    </body>
</html>
