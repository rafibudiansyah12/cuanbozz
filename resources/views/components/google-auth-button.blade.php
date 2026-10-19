@props(['label' => 'Lanjutkan dengan Google'])

<a href="{{ route('auth.google.redirect') }}" {{ $attributes->class('inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-control border border-border bg-surface px-4 py-3 text-sm font-semibold text-text shadow-sm transition hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600') }}>
    <span aria-hidden="true" class="text-base font-bold">G</span>
    <span>{{ $label }}</span>
</a>
