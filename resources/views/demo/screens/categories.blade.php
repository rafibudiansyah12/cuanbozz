<div class="space-y-4">
    <x-ui.card><h2 class="font-semibold text-text">Kategori produk</h2><div class="mt-3 flex flex-wrap gap-2">@foreach(array_slice($demo['categories'], 1) as $category)<x-ui.badge>{{ $category }}</x-ui.badge>@endforeach</div></x-ui.card>
    <x-ui.card><h3 class="font-semibold text-text">Tambah kategori</h3><div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end"><x-ui.input label="Nama kategori" name="category_name" placeholder="Contoh: Camilan" /><x-ui.button>Tambah kategori</x-ui.button></div></x-ui.card>
    <x-ui.card><p class="text-sm text-muted">Kategori mengelompokkan produk agar kasir lebih cepat menemukan barang.</p><x-ui.button href="/demo/products{{ $query }}" variant="secondary" class="mt-3">Kembali ke produk</x-ui.button></x-ui.card>
</div>
