@props(['label', 'name', 'checked' => false])
<label class="flex min-h-11 cursor-pointer items-center justify-between gap-4 text-sm font-medium text-text">
    <span>{{ $label }}</span>
    <input type="checkbox" name="{{ $name }}" role="switch" @checked($checked) {{ $attributes->class('h-6 w-11 cursor-pointer rounded-full border-border text-primary-600 focus:ring-primary-600') }}>
</label>
