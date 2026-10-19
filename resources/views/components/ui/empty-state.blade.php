@props(['title', 'description' => null])
<div {{ $attributes->class('flex flex-col items-center justify-center rounded-control border border-dashed border-border px-5 py-10 text-center') }}>
    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary-50 text-primary-700" aria-hidden="true">{{ $icon ?? '—' }}</div>
    <h3 class="font-semibold text-text">{{ $title }}</h3>
    @if($description)<p class="mt-1 max-w-sm text-sm text-muted">{{ $description }}</p>@endif
    @if(isset($action))<div class="mt-4">{{ $action }}</div>@endif
</div>
