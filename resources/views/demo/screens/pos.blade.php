<div class="space-y-4" x-data="{ selectedCategory: 'Semua' }">
    <div class="flex items-center gap-2">
        <label class="relative min-w-0 flex-1"><span class="sr-only">Cari produk</span><input type="search" class="ui-control pl-10" placeholder="Cari nama produk atau SKU"><span class="absolute left-3 top-3 text-muted" aria-hidden="true">⌕</span></label>
        <x-ui.button variant="secondary" aria-label="Pindai barcode">▥</x-ui.button>
    </div>
    <div class="flex gap-2 overflow-x-auto pb-1" aria-label="Filter kategori">
        @foreach($demo['categories'] as $category)
            <button type="button" class="min-h-11 shrink-0 rounded-full border px-4 text-sm font-medium" x-on:click="selectedCategory = '{{ $category }}'" x-bind:class="selectedCategory === '{{ $category }}' ? 'border-primary-600 bg-primary-600 text-on-primary' : 'border-border bg-surface text-text'">{{ $category }}</button>
        @endforeach
    </div>
    <div class="grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($demo['products'] as $product)
            <article class="flex min-w-0 min-h-40 flex-col rounded-control border border-border bg-surface p-3 shadow-sm">
                <div class="flex h-16 items-center justify-center rounded-control bg-primary-50 text-3xl" aria-hidden="true">{{ $product['icon'] }}</div>
                <h2 class="mt-2 text-sm font-semibold text-text">{{ $product['name'] }}</h2>
                <p class="text-xs text-muted">{{ $product['category'] }} · {{ $product['unit'] }}</p>
                <div class="mt-auto flex items-end justify-between gap-2 pt-3">
                    <div><p class="font-bold text-primary-700">Rp{{ number_format($product['price'], 0, ',', '.') }}</p>@if($product['track_stock'])<p class="text-[11px] text-muted">Stok {{ $product['stock'] }}</p>@else<x-ui.badge variant="muted">Tanpa stok</x-ui.badge>@endif</div>
                    <button type="button" class="min-h-11 min-w-11 rounded-control bg-primary-600 text-xl font-semibold text-on-primary" x-on:click="$dispatch('open-modal', 'product-options')" aria-label="Pilih {{ $product['name'] }}">+</button>
                </div>
            </article>
        @endforeach
    </div>
    <div class="fixed inset-x-0 bottom-[calc(3.5rem+env(safe-area-inset-bottom))] z-30 px-4 pb-2">
        <div class="mx-auto max-w-xl"><x-ui.modal name="cart-sheet" title="Keranjang · {{ $demo['cart_totals']['item_count'] }} item">
            <x-slot name="trigger"><button type="button" class="flex min-h-14 w-full items-center justify-between rounded-control bg-primary-700 px-4 font-semibold text-on-primary shadow-lg" x-on:click="$dispatch('open-modal', 'cart-sheet')"><span>{{ $demo['cart_totals']['item_count'] }} item · Rp{{ number_format($demo['cart_totals']['subtotal'], 0, ',', '.') }}</span><span>Lihat keranjang ↑</span></button></x-slot>
            <ul class="divide-y divide-border">@foreach($demo['cart'] as $item)<li class="flex items-center justify-between gap-3 py-3"><div><p class="font-medium text-text">{{ $item['name'] }}</p><p class="text-xs text-muted">{{ $item['option'] }} · {{ $item['note'] ?? 'Tanpa catatan' }}</p></div><div class="text-right"><p class="text-sm font-semibold text-text">{{ $item['quantity'] }} × Rp{{ number_format($item['unit_price'], 0, ',', '.') }}</p><button type="button" class="min-h-11 text-sm text-danger">Hapus</button></div></li>@endforeach</ul>
            <div class="space-y-3 border-t border-border pt-4"><p class="flex justify-between text-sm text-muted"><span>Subtotal</span><span>Rp{{ number_format($demo['cart_totals']['subtotal'], 0, ',', '.') }}</span></p><p class="flex justify-between text-sm text-muted"><span>Diskon ({{ $demo['discount']['value'] }}%)</span><span>−Rp{{ number_format($demo['discount']['amount'], 0, ',', '.') }}</span></p><p class="flex justify-between font-bold text-text"><span>Total</span><span>Rp{{ number_format($demo['cart_totals']['total'], 0, ',', '.') }}</span></p>
                <div class="grid grid-cols-2 gap-2"><x-ui.button variant="secondary" x-on:click="$dispatch('open-modal', 'discount-sheet')">Atur diskon</x-ui.button><x-ui.button href="/demo/payment{{ $query }}">Lanjut bayar</x-ui.button></div>
            </div>
        </x-ui.modal></div>
    </div>
    <x-ui.modal name="product-options" title="Pilih opsi produk">
        <x-slot name="trigger"></x-slot>
        <div class="space-y-4">
            @foreach($demo['option_groups'] as $group)
                <fieldset><legend class="font-medium text-text">{{ $group['name'] }} @if($group['is_required'])<span class="text-danger">*</span>@else<span class="text-xs font-normal text-muted">(boleh pilih lebih dari satu)</span>@endif</legend><div class="mt-2 space-y-2">
                    @foreach($group['options'] as $option)
                        <label class="flex min-h-11 items-center gap-3 text-sm text-text"><input type="{{ $group['selection_type'] === 'single' ? 'radio' : 'checkbox' }}" name="option_{{ $loop->parent->index }}{{ $group['selection_type'] === 'multiple' ? '[]' : '' }}" @if($group['selection_type'] === 'single' && $loop->first) checked @endif class="{{ $group['selection_type'] === 'single' ? 'text-primary-600 focus:ring-primary-600' : 'rounded-control border-border text-primary-600 focus:ring-primary-600' }}">{{ $option['name'] }} <span class="ml-auto text-muted">{{ $option['price_delta'] < 0 ? '−' : '+' }}Rp{{ number_format(abs($option['price_delta']), 0, ',', '.') }}</span></label>
                    @endforeach
                </div></fieldset>
            @endforeach
            <x-ui.textarea label="Catatan item" name="item-note" placeholder="Contoh: es sedikit, gula terpisah" />
            <x-ui.button class="w-full" x-on:click="$dispatch('close-modal')">Tambah ke keranjang</x-ui.button>
        </div>
    </x-ui.modal>
    <x-ui.modal name="discount-sheet" title="Diskon transaksi"><x-slot name="trigger"></x-slot><div class="space-y-4"><x-ui.select label="Jenis diskon" name="discount_type"><option>Persentase</option><option>Nominal rupiah</option></x-ui.select><x-ui.input label="Nilai diskon" name="discount_value" type="number" value="10" /><x-ui.button class="w-full" x-on:click="$dispatch('close-modal')">Terapkan diskon</x-ui.button></div></x-ui.modal>
</div>
