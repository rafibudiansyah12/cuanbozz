@props(['name', 'title' => null, 'bottomSheet' => false])
<div x-data="{ open: false, touchStart: 0 }" x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true" x-on:close-modal.window="open = false" x-on:keydown.escape.window="open = false" {{ $attributes }}>
    {{ $trigger ?? '' }}
    <template x-teleport="body">
        <div x-cloak x-show="open" class="fixed inset-0 z-50 flex items-end justify-center bg-text/50 p-0 sm:items-center sm:p-4" x-transition.opacity>
            <button class="absolute inset-0 h-full w-full cursor-default" type="button" aria-label="Tutup dialog" x-on:click="open = false"></button>
            <section role="dialog" aria-modal="true" @if($title) aria-label="{{ $title }}" @endif class="relative z-10 max-h-[90vh] w-full overflow-y-auto rounded-t-2xl border border-border bg-surface p-5 shadow-xl sm:max-w-lg sm:rounded-control" x-on:touchstart="touchStart = $event.changedTouches[0].screenY" x-on:touchend="if ($event.changedTouches[0].screenY - touchStart > 80) open = false" x-transition>
                <div class="mx-auto mb-4 h-1.5 w-10 rounded-full bg-border sm:hidden" aria-hidden="true"></div>
                <div class="mb-4 flex items-center justify-between gap-4">
                    @if($title)<h2 class="text-lg font-semibold text-text">{{ $title }}</h2>@endif
                    <button type="button" class="ml-auto min-h-11 min-w-11 rounded-control text-muted hover:bg-primary-50" x-on:click="open = false" aria-label="Tutup">&times;</button>
                </div>
                {{ $slot }}
            </section>
        </div>
    </template>
</div>
