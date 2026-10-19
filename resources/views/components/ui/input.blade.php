@props(['label' => null, 'name', 'type' => 'text', 'error' => null])
<div class="space-y-1.5">
    @if ($label)<label for="{{ $name }}" class="block text-sm font-medium text-text">{{ $label }}</label>@endif
    @if ($type === 'password')
        <div class="relative" x-data="{ visible: false }">
            <input id="{{ $name }}" name="{{ $name }}" type="password" {{ $attributes->merge(['class' => 'ui-control pr-12']) }} x-bind:type="visible ? 'text' : 'password'" @if($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>
            <button type="button" class="absolute inset-y-0 right-0 inline-flex min-h-11 min-w-11 items-center justify-center rounded-control text-muted hover:text-text focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600" x-on:click="visible = !visible" x-bind:aria-label="visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" x-bind:aria-pressed="visible" x-bind:title="visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                <svg x-show="!visible" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg x-show="visible" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.2A10.8 10.8 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16 16 0 0 1-3.1 3.9M6.2 6.2C3.6 8 2.25 12 2.25 12s3.5 7 9.75 7a10 10 0 0 0 3-.45"/></svg>
            </button>
        </div>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" {{ $attributes->merge(['class' => 'ui-control']) }} @if($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>
    @endif
    @if ($error)<p id="{{ $name }}-error" class="text-sm text-danger">{{ $error }}</p>@endif
</div>
