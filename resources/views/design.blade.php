<x-app-layout>
    <x-slot name="title">Design System · CuanBozz</x-slot>
    <div x-data="themePreview({ presets: @js(config('design-system.presets')), defaultPrimary: @js(config('design-system.default_primary')) })" class="space-y-6">
        <header class="space-y-1">
            <x-ui.badge>Pratinjau lokal</x-ui.badge>
            <h1 class="text-2xl font-bold text-text">Design system</h1>
            <p class="text-sm text-muted">Komponen UI CuanBozz dan tema yang bisa dicoba langsung.</p>
        </header>

        <x-ui.card>
            <h2 class="font-semibold text-text">Warna utama</h2>
            <p class="mt-1 text-sm text-muted">Pilih preset atau masukkan hex. Perubahan diterapkan langsung ke seluruh token primary.</p>
            <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3">
                <template x-for="preset in presets" :key="preset.name">
                    <button type="button" class="ui-button min-w-0 border border-border bg-surface text-text" x-on:click="choose(preset.primary)">
                        <span class="h-5 w-5 rounded-full bg-primary-600" aria-hidden="true"></span>
                        <span x-text="preset.name"></span>
                    </button>
                </template>
            </div>
            <label class="mt-4 flex min-h-11 items-center gap-3 text-sm font-medium text-text">
                <span>Warna custom</span>
                <input type="color" class="h-11 w-14 cursor-pointer rounded-control border border-border bg-surface p-1" x-model="primary" x-on:input="choose($event.target.value)" aria-label="Pilih warna utama">
                <input type="text" class="ui-control max-w-36" x-model="primary" x-on:input="choose($event.target.value)" aria-label="Kode hex warna utama">
            </label>
            <div class="mt-4 flex min-h-14 items-center gap-3 rounded-control bg-primary-600 px-4 text-on-primary">
                <span class="font-semibold">Preview tombol utama</span>
                <span class="ml-auto text-sm" x-text="primary"></span>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="font-semibold text-text">Tombol dan badge</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                <x-ui.button>Primary</x-ui.button>
                <x-ui.button variant="secondary">Secondary</x-ui.button>
                <x-ui.button variant="danger">Danger</x-ui.button>
                <x-ui.button variant="ghost">Ghost</x-ui.button>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <x-ui.badge>Primary</x-ui.badge>
                <x-ui.badge variant="success">Success</x-ui.badge>
                <x-ui.badge variant="warning">Warning</x-ui.badge>
                <x-ui.badge variant="danger">Danger</x-ui.badge>
                <x-ui.badge variant="muted">Muted</x-ui.badge>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="font-semibold text-text">Formulir</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <x-ui.input label="Nama produk" name="sample-name" placeholder="Contoh: Kopi susu" />
                <x-ui.select label="Kategori" name="sample-category"><option>Pilih kategori</option><option>Minuman</option></x-ui.select>
                <x-ui.textarea label="Catatan" name="sample-note" placeholder="Tulis catatan" />
                <x-ui.toggle label="Produk aktif" name="sample-toggle" checked />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="font-semibold text-text">Kartu dan dialog</h2>
            <div class="mt-3 flex items-center justify-between gap-4 rounded-control bg-primary-50 p-4">
                <div><p class="font-medium text-text">Kartu ringkasan</p><p class="text-sm text-muted">Contoh komponen kartu</p></div>
                <span class="text-lg font-bold text-primary-700">Rp25.000</span>
            </div>
            <div class="mt-4">
                <x-ui.modal name="demo-modal" title="Contoh bottom sheet">
                    <x-slot name="trigger"><x-ui.button variant="secondary" x-on:click="$dispatch('open-modal', 'demo-modal')">Buka modal / bottom sheet</x-ui.button></x-slot>
                    <p class="text-sm text-muted">Pada ponsel dialog tampil dari bawah. Pada layar lebih lebar dialog berada di tengah.</p>
                    <div class="mt-4 flex justify-end"><x-ui.button x-on:click="$dispatch('close-modal')">Tutup</x-ui.button></div>
                </x-ui.modal>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="font-semibold text-text">Status dan pemuatan</h2>
            <div class="mt-3"><x-ui.toast message="Perubahan tersimpan." /></div>
            <div class="mt-4"><x-ui.empty-state title="Belum ada produk" description="Produk yang ditambahkan akan muncul di sini." /></div>
            <div class="mt-4 max-w-sm"><x-ui.skeleton /></div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="font-semibold text-text">Token primary</h2>
            <div class="mt-3 grid grid-cols-5 gap-2 sm:grid-cols-10">
                @foreach([50, 100, 200, 300, 400, 500, 600, 700, 800, 900] as $shade)
                    <div class="flex h-16 items-end rounded-control bg-primary-{{ $shade }} p-1 text-xs text-text">{{ $shade }}</div>
                @endforeach
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
