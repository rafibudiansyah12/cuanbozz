<div x-data="themePreview({ presets: @js($demo['presets']), defaultPrimary: @js($demo['default_primary']) })" class="mx-auto max-w-2xl space-y-4">
    <x-ui.card>
        <h2 class="text-lg font-semibold text-text">Tampilan toko</h2><p class="mt-1 text-sm text-muted">Tema berlaku untuk pemilik dan seluruh kasir toko. Mode terang.</p>
        <h3 class="mt-5 text-sm font-semibold text-text">Pilih preset warna</h3>
        <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
            <template x-for="preset in presets" :key="preset.name"><button type="button" class="flex min-h-11 items-center gap-2 rounded-control border border-border px-3 text-sm font-medium text-text" x-on:click="choose(preset.primary)" x-bind:aria-pressed="primary.toLowerCase() === preset.primary.toLowerCase()"><span class="h-5 w-5 rounded-full bg-primary-600" aria-hidden="true"></span><span x-text="preset.name"></span></button></template>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <label class="space-y-1.5"><span class="block text-sm font-medium text-text">Warna utama (hex)</span><input type="text" class="ui-control" x-model="primary" x-on:input="choose($event.target.value)" aria-label="Kode hex warna utama"></label>
            <label class="space-y-1.5"><span class="block text-sm font-medium text-text">Pilih warna</span><input type="color" class="ui-control p-1" x-model="primary" x-on:input="choose($event.target.value)" aria-label="Pemilih warna utama"></label>
        </div>
        <div class="mt-4 flex min-h-14 items-center justify-between gap-3 rounded-control bg-primary-600 px-4 text-on-primary"><span class="font-semibold">Preview warna utama</span><span class="text-sm" x-text="primary"></span></div>
        <div x-show="!isReadable" x-cloak role="alert" class="mt-3 rounded-control border border-danger bg-danger/10 p-3 text-sm text-danger">Kontras warna belum memenuhi WCAG AA 4.5:1. Pilih warna lain.</div>
        <p class="mt-2 text-sm" x-bind:class="isReadable ? 'text-success' : 'text-danger'">Rasio kontras: <span x-text="contrastRatio.toFixed(2)"></span>:1 <span x-text="isReadable ? '· memenuhi WCAG AA' : '· perlu diperbaiki'"></span></p>
        <x-ui.select class="mt-4" label="Radius komponen" name="radius"><option>md</option><option selected>lg</option><option>xl</option></x-ui.select>
        <x-ui.button class="mt-4" x-bind:disabled="!isReadable">Simpan tampilan</x-ui.button>
    </x-ui.card>
    <x-ui.card><h3 class="font-semibold text-text">Preview halaman kasir</h3><div class="mt-3 rounded-control border border-border p-3"><div class="flex items-center justify-between"><span class="font-semibold text-text">{{ $demo['store']['name'] }}</span><x-ui.badge variant="success">Buka</x-ui.badge></div><div class="mt-3 grid grid-cols-2 gap-2"><div class="rounded-control bg-primary-50 p-3 text-sm text-primary-800">Kopi Susu Aren</div><div class="rounded-control bg-surface p-3 text-sm text-text ring-1 ring-border">Es Cokelat</div></div><x-ui.button class="mt-3 w-full">Bayar · Rp{{ number_format($demo['cart_totals']['total'], 0, ',', '.') }}</x-ui.button></div></x-ui.card>
</div>
