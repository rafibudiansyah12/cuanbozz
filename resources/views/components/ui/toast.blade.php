@props(['type' => 'success', 'message' => null])
@php($tone = ['success' => 'border-success text-success', 'warning' => 'border-warning text-warning', 'danger' => 'border-danger text-danger'][$type] ?? 'border-success text-success')
<div x-data="{ visible: true }" x-show="visible" x-init="setTimeout(() => visible = false, 4000)" role="status" {{ $attributes->class('fixed inset-x-4 top-4 z-50 mx-auto flex min-h-11 max-w-md items-center justify-between gap-3 rounded-control border-l-4 bg-surface p-3 text-sm shadow-lg '.$tone) }}>
    <span>{{ $message ?? $slot }}</span><button type="button" class="min-h-11 min-w-11" aria-label="Tutup notifikasi" x-on:click="visible = false">&times;</button>
</div>
