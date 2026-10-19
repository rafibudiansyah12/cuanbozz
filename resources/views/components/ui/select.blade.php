@props(['label' => null, 'name', 'error' => null])
<div class="space-y-1.5">
    @if ($label)<label for="{{ $name }}" class="block text-sm font-medium text-text">{{ $label }}</label>@endif
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'ui-control']) }} @if($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>{{ $slot }}</select>
    @if ($error)<p id="{{ $name }}-error" class="text-sm text-danger">{{ $error }}</p>@endif
</div>
