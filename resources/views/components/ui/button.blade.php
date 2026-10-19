@props(['variant' => 'primary', 'type' => 'button', 'href' => null])
@php
    $variants = [
        'primary' => 'bg-primary-600 text-on-primary hover:bg-primary-700',
        'secondary' => 'border border-border bg-surface text-text hover:bg-primary-50',
        'danger' => 'bg-danger text-surface hover:bg-danger/90',
        'ghost' => 'bg-surface/0 text-text hover:bg-primary-50',
    ];
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'ui-button '.$variants[$variant]]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => $type, 'class' => 'ui-button '.$variants[$variant]]) }}>{{ $slot }}</button>
@endif
