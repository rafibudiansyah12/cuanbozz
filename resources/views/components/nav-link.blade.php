@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex min-h-11 items-center px-1 pt-1 border-b-2 border-primary-600 text-sm font-medium leading-5 text-text focus:outline-none focus:border-primary-600 transition duration-150 ease-in-out'
            : 'inline-flex min-h-11 items-center px-1 pt-1 border-b-2 border-border text-sm font-medium leading-5 text-muted hover:text-text hover:border-border focus:outline-none focus:text-text focus:border-border transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
