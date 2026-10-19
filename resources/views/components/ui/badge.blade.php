@props(['variant' => 'primary'])
@php($variants = ['primary' => 'bg-primary-50 text-primary-800', 'success' => 'bg-success/10 text-success', 'warning' => 'bg-warning/10 text-warning', 'danger' => 'bg-danger/10 text-danger', 'muted' => 'bg-primary-50 text-muted'])
<span {{ $attributes->class('inline-flex min-h-6 items-center rounded-full px-2.5 py-1 text-xs font-semibold '.$variants[$variant]) }}>{{ $slot }}</span>
