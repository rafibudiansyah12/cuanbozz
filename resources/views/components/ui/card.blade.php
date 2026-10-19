@props(['padding' => 'p-4'])
<section {{ $attributes->class('rounded-control border border-border/70 bg-surface/70 shadow-lg shadow-primary-900/5 backdrop-blur-xl '.$padding) }}>{{ $slot }}</section>
