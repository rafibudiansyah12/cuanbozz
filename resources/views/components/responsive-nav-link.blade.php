@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex min-h-11 w-full items-center ps-3 pe-4 py-2 border-l-4 border-primary-600 text-start text-base font-medium text-primary-700 bg-primary-50 focus:outline-none focus:text-primary-700 focus:bg-primary-50 focus:border-primary-600 transition duration-150 ease-in-out'
            : 'flex min-h-11 w-full items-center ps-3 pe-4 py-2 border-l-4 border-border text-start text-base font-medium text-muted hover:text-text hover:bg-primary-50 hover:border-border focus:outline-none focus:text-text focus:bg-primary-50 focus:border-border transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
