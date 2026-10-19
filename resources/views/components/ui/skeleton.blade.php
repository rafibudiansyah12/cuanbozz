@props(['lines' => 3])
<div {{ $attributes->class('animate-pulse space-y-3') }} aria-busy="true" aria-label="Memuat">
    @for($line = 0; $line < $lines; $line++)
        <div class="h-4 rounded bg-primary-100 {{ $line === $lines - 1 ? 'w-2/3' : 'w-full' }}"></div>
    @endfor
</div>
