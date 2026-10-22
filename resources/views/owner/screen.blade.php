@php
    $screenLink = fn (string $routeName, string $screenName) => $previewMode
        ? route('owner.preview', ['screen' => $screenName])
        : route($routeName);
    $products = $products ?? collect();
    $orders = $orders ?? collect();
@endphp

<x-owner-layout :title="$title" :preview-mode="$previewMode" :store="$store">
    <div class="space-y-5 sm:space-y-6">
        @if($previewMode)
            <div class="rounded-xl border border-warning/30 bg-warning/10 px-4 py-3 text-sm text-text">
                Pratinjau lokal. Halaman ini belum terhubung ke penyimpanan atau aksi bisnis.
            </div>
        @endif

        @switch($screen)
            @case('pos')
                <div x-data="posScreen({ products: @js($products), sampleMode: @js($previewMode), initialItems: @js(old('items', [])), initialCash: @js(old('cash_received', '')), initialServiceType: @js(old('service_type', 'take_away')) })" class="space-y-4 sm:space-y-5">
                    @if(! $previewMode && $products->isEmpty())
                        <div class="flex flex-col gap-3 rounded-xl border border-warning/30 bg-warning/10 px-4 py-3 sm:flex-row sm:items-center sm:justify-between"><p class="text-sm leading-5 text-text"><span class="font-semibold">Katalog produk belum tersedia.</span> Tambahkan produk dahulu agar bisa melayani transaksi.</p>@if($isOwner || $isAdmin)<x-ui.button href="{{ route('owner.products.create') }}" variant="secondary" class="shrink-0">Tambah produk</x-ui.button>@endif</div>
                    @endif
                    <section class="grid min-w-0 items-start gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(20rem,23rem)] xl:gap-5 2xl:grid-cols-[minmax(0,1fr)_24rem]">
                        <div class="min-w-0 space-y-4 sm:space-y-5">
                            <div class="flex gap-2 rounded-2xl border border-border/70 bg-surface/75 p-2 shadow-sm backdrop-blur-xl">
                                <label class="flex min-h-11 min-w-0 flex-1 items-center gap-3 px-2 text-muted">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5" stroke-linecap="round"/></svg>
                                    <span class="sr-only">Cari produk</span><input x-model="search" type="search" placeholder="Cari nama produk atau SKU" class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-text placeholder:text-muted focus:ring-0" />
                                </label>
                                <button type="button" @click="showFilters = !showFilters" :aria-expanded="showFilters.toString()" class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-xl bg-primary-600 px-4 text-sm font-semibold text-on-primary transition hover:bg-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600" aria-label="Filter produk">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h10m4 0h2M4 17h2m4 0h10" stroke-linecap="round"/><circle cx="16" cy="7" r="2"/><circle cx="8" cy="17" r="2"/></svg><span class="hidden sm:inline">Filter</span>
                                </button>
                            </div>
                            <div x-cloak x-show="showFilters" x-transition class="rounded-2xl border border-border/70 bg-surface/85 px-4 py-3 shadow-sm"><label class="flex min-h-10 cursor-pointer items-center gap-3 text-sm text-text"><input type="checkbox" x-model="availableOnly" class="h-4 w-4 rounded border-border text-primary-600 focus:ring-primary-600"><span>Sembunyikan produk yang stoknya habis</span></label></div>

                            <div class="-mx-1 overflow-x-auto px-1 pb-1 scrollbar-hide">
                                <div class="flex w-max gap-2" role="group" aria-label="Kategori produk">
                                    <template x-for="category in categories" :key="category">
                                        <button type="button" @click="activeCategory = category" :aria-pressed="activeCategory === category" :class="activeCategory === category ? 'border-primary-600 bg-primary-600 text-on-primary shadow-sm' : 'border-border bg-surface/75 text-text hover:border-primary-300 hover:bg-primary-50'" class="min-h-11 rounded-full border px-5 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600" x-text="category"></button>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-end justify-between gap-3 px-1">
                                <div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Menu produk</p><h2 class="mt-1 text-xl font-bold tracking-tight text-text">Pilih produk</h2></div>
                                <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-800" x-text="`${filteredProducts.length} produk`"></span>
                            </div>

                            <template x-if="filteredProducts.length">
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
                                    <template x-for="product in filteredProducts" :key="product.id">
                                        <article class="group flex min-w-0 flex-col rounded-2xl border border-border/70 bg-surface/85 p-3 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-md sm:p-4">
                                            <div class="flex min-w-0 gap-3">
                                                <div class="h-24 w-20 shrink-0 overflow-hidden rounded-xl bg-primary-50 sm:h-28 sm:w-24">
                                                    <img x-show="product.image_url" x-on:error="product.image_url = null" :src="product.image_url" :alt="product.name" class="h-full w-full object-cover" loading="lazy">
                                                    <div x-show="!product.image_url" class="flex h-full w-full items-center justify-center text-primary-700" aria-hidden="true"><svg viewBox="0 0 64 80" class="h-16 w-14" fill="none"><path d="M14 19h34l-4 48a6 6 0 0 1-6 5H24a6 6 0 0 1-6-5l-4-48Z" class="fill-surface" stroke="currentColor" stroke-width="2.5"/><path d="M16 37h30l-2 26a5 5 0 0 1-5 5H24a5 5 0 0 1-5-5l-3-26Z" class="fill-primary-300"/><path d="M11 15h40v6H11zM25 8h13v7H25z" class="fill-primary-700"/></svg></div>
                                                </div>
                                                <div class="min-w-0 flex-1 py-1">
                                                    <h3 class="break-words text-sm font-bold leading-5 text-text [overflow-wrap:anywhere]" x-text="product.name"></h3>
                                                    <p class="mt-1 text-sm font-bold leading-5 text-primary-700" x-text="formatRupiah(product.price)"></p>
                                                    <p class="mt-1 line-clamp-2 text-xs leading-5 text-muted" x-text="product.description"></p>
                                                    <div class="mt-2 flex flex-wrap items-center gap-2"><span class="inline-flex rounded-full bg-primary-50 px-2.5 py-1 text-[11px] font-semibold text-primary-800" x-text="product.category"></span><span class="text-[11px] text-muted" x-text="product.sku"></span></div>
                                                    <p class="mt-2 text-xs" :class="product.track_stock ? (product.stock <= product.low_stock_threshold ? 'font-semibold text-warning' : 'text-muted') : 'text-muted'" x-text="product.track_stock ? `Stok ${product.stock} ${product.unit}` : 'Stok tidak terbatas'"></p>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between gap-2 border-t border-border/70 pt-3">
                                                <span class="text-xs text-muted">Siap dipesan</span>
                                                <button type="button" @click="beginConfigure(product)" :disabled="product.track_stock && product.stock < 1" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-primary-200 bg-surface px-3 text-xs font-bold text-primary-800 transition hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 disabled:cursor-not-allowed disabled:opacity-50"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg><span x-text="product.track_stock && product.stock < 1 ? 'Stok habis' : (product.option_groups?.length ? 'Pilih' : 'Tambah')"></span></button>
                                            </div>
                                        </article>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!filteredProducts.length">
                                <div class="rounded-2xl border border-dashed border-border bg-surface/60 px-5 py-10 text-center"><p class="font-semibold text-text">Produk tidak ditemukan</p><p class="mt-1 text-sm text-muted">Coba kata kunci atau kategori lain.</p></div>
                            </template>
                        </div>

                        <aside id="pos-cart" class="min-w-0 scroll-mt-24 rounded-2xl border border-border/70 bg-surface/90 p-4 shadow-sm backdrop-blur-xl sm:p-5 xl:sticky xl:top-24 xl:max-h-[calc(100vh-7rem)] xl:overflow-y-auto xl:overscroll-contain">
                            @unless($previewMode)<form method="POST" action="{{ route('owner.checkout.cash') }}" x-on:submit="if (!canCheckout) $event.preventDefault()">@csrf<template x-for="(item, index) in cart" :key="`checkout-${item.key}`"><span><input type="hidden" :name="`items[${index}][product_id]`" :value="item.id"><input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity"><input type="hidden" :name="`items[${index}][note]`" :value="item.note"><template x-for="option in item.options" :key="option.id"><input type="hidden" :name="`items[${index}][options][]`" :value="option.id"></template></span></template>@endunless
                            <div class="flex items-start justify-between gap-3 border-b border-border/70 pb-4">
                                <div><p class="text-xs font-semibold uppercase tracking-[0.15em] text-primary-700">Pesanan saat ini</p><h2 class="mt-1 text-xl font-bold text-text">Keranjang</h2></div>
                                <span class="rounded-full bg-primary-50 px-3 py-1.5 text-xs font-bold text-primary-800" x-text="`${itemCount} item`"></span>
                            </div>
                            <template x-if="cart.length">
                                <div class="divide-y divide-border/70">
                                    <template x-for="item in cart" :key="item.key">
                                        <div class="flex gap-3 py-3">
                                            <div class="h-14 w-12 shrink-0 overflow-hidden rounded-lg bg-primary-50"><img x-show="item.image_url" x-on:error="item.image_url = null" :src="item.image_url" :alt="item.name" class="h-full w-full object-cover" loading="lazy"><div x-show="!item.image_url" class="flex h-full items-center justify-center text-primary-700" aria-hidden="true"><svg viewBox="0 0 64 80" class="h-10 w-8" fill="none"><path d="M16 20h32l-4 43a5 5 0 0 1-5 5H25a5 5 0 0 1-5-5l-4-43Z" class="fill-surface" stroke="currentColor" stroke-width="4"/><path d="M18 42h28l-2 19H22l-4-19Z" class="fill-primary-300"/><path d="M12 16h40v6H12z" class="fill-primary-700"/></svg></div></div>
                                            <div class="min-w-0 flex-1"><div class="flex justify-between gap-2"><p class="truncate text-sm font-semibold text-text" x-text="item.name"></p><button type="button" @click="removeFromCart(item.key)" class="min-h-11 min-w-11 text-muted hover:text-danger" :aria-label="`Hapus ${item.name}`">×</button></div><template x-if="item.options.length"><p class="mt-1 text-xs leading-5 text-muted" x-text="item.options.map((option) => option.name).join(', ')"></p></template><p class="mt-1 text-xs text-muted" x-text="formatRupiah(unitPrice(item))"></p><div class="mt-2 flex items-center justify-between"><div class="inline-flex items-center rounded-lg border border-border"><button type="button" @click="changeQuantity(item.key, -1)" class="min-h-11 min-w-11 rounded-l-lg text-text hover:bg-primary-50" :aria-label="`Kurangi ${item.name}`">−</button><span class="min-w-8 text-center text-xs font-semibold text-text" x-text="item.quantity"></span><button type="button" @click="changeQuantity(item.key, 1)" class="min-h-11 min-w-11 rounded-r-lg text-text hover:bg-primary-50" :aria-label="`Tambah ${item.name}`">+</button></div><span class="text-sm font-semibold text-text" x-text="formatRupiah(unitPrice(item) * item.quantity)"></span></div><label class="mt-2 block text-xs font-medium text-muted">Catatan item<input x-model="item.note" maxlength="500" type="text" class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-3 py-2 text-sm font-normal text-text focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/20" placeholder="Contoh: tanpa gula"></label></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!cart.length">
                                <div class="my-5 flex min-h-36 flex-col items-center justify-center rounded-xl border border-dashed border-border bg-primary-50/40 px-4 text-center"><span class="mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-surface text-primary-700" aria-hidden="true"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 4h2l2.2 11.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 1.9-1.4L21 9H6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg></span><p class="text-sm font-semibold text-text">Keranjang masih kosong</p><p class="mt-1 text-xs text-muted">Pilih produk untuk memulai pesanan.</p></div>
                            </template>
                                <div class="space-y-3 border-t border-border/70 pt-4">
                                @unless($previewMode)
                                    <input type="hidden" name="service_type" :value="serviceType">
                                    <fieldset class="space-y-2"><legend class="text-sm font-semibold text-text">Jenis pesanan</legend><div class="grid grid-cols-2 gap-2"><button type="button" @click="serviceType = 'dine_in'" :aria-pressed="serviceType === 'dine_in'" :class="serviceType === 'dine_in' ? 'border-primary-600 bg-primary-50 text-primary-900' : 'border-border bg-surface text-text'" class="min-h-11 rounded-xl border px-3 text-sm font-semibold">Dine In</button><button type="button" @click="serviceType = 'take_away'" :aria-pressed="serviceType === 'take_away'" :class="serviceType === 'take_away' ? 'border-primary-600 bg-primary-50 text-primary-900' : 'border-border bg-surface text-text'" class="min-h-11 rounded-xl border px-3 text-sm font-semibold">Take Away</button></div></fieldset>
                                    <div class="grid gap-3">
                                        <label class="block text-sm font-semibold text-text">Nama pelanggan <input name="customer_name" value="{{ old('customer_name') }}" maxlength="100" autocomplete="name" class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-3 py-2 text-base font-normal text-text shadow-sm focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/20" placeholder="Nama pelanggan">@error('customer_name')<span class="mt-1 block text-sm font-medium text-danger">{{ $message }}</span>@enderror</label>
                                        <label x-show="serviceType === 'dine_in'" class="block text-sm font-semibold text-text">Nomor meja <input name="table_number" value="{{ old('table_number') }}" maxlength="20" :required="serviceType === 'dine_in'" class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-3 py-2 text-base font-normal text-text shadow-sm focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/20" placeholder="Contoh: 03">@error('table_number')<span class="mt-1 block text-sm font-medium text-danger">{{ $message }}</span>@enderror</label>
                                    </div>
                                @endunless
                                <div class="flex items-center justify-between text-sm text-muted"><span>Subtotal</span><span class="font-semibold text-text" x-text="formatRupiah(subtotal)"></span></div>
                                <div class="flex items-center justify-between border-t border-border/70 pt-3"><span class="font-bold text-text">Total</span><span class="text-lg font-extrabold text-primary-800" x-text="formatRupiah(subtotal)"></span></div>
                                @unless($previewMode)
                                    <label for="cash_received" class="block text-sm font-semibold text-text">Uang diterima</label>
                                    <div class="relative"><span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm font-semibold text-muted">Rp</span><input id="cash_received" name="cash_received" x-model.number="cashReceived" type="number" min="0" step="1" inputmode="numeric" required class="min-h-11 w-full rounded-xl border border-border bg-surface py-2 pl-10 pr-3 text-base text-text shadow-sm focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/20" placeholder="0"></div>
                                    @error('cash_received')<p class="text-sm font-medium text-danger">{{ $message }}</p>@enderror
                                    @error('items')<p class="text-sm font-medium text-danger">{{ $message }}</p>@enderror
                                    <div class="flex items-center justify-between rounded-xl bg-primary-50/70 px-3 py-2.5 text-sm"><span class="text-muted">Kembalian</span><span class="font-bold text-primary-800" x-text="formatRupiah(changeDue)"></span></div>
                                    <button type="submit" :disabled="!canCheckout" class="min-h-12 w-full rounded-xl bg-primary-600 px-4 text-sm font-bold text-on-primary shadow-sm transition hover:bg-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 disabled:cursor-not-allowed disabled:opacity-50">Bayar tunai</button>
                                    <p class="text-center text-[11px] leading-4 text-muted">QRIS akan tersedia setelah payment gateway aktif.</p>
                                @else
                                    <button type="button" disabled class="min-h-12 w-full rounded-xl bg-primary-600 px-4 text-sm font-bold text-on-primary shadow-sm disabled:cursor-not-allowed disabled:opacity-50">Bayar tunai</button>
                                    <p class="text-center text-[11px] leading-4 text-muted">Contoh keranjang untuk pratinjau desain.</p>
                                @endunless
                            </div>
                            @unless($previewMode)</form>@endunless
                        </aside>
                    </section>
                    <template x-if="configureProduct">
                        <div class="fixed inset-0 z-50 flex items-end justify-center bg-text/50 p-0 backdrop-blur-sm sm:items-center sm:p-4" @click.self="configureProduct = null" @keydown.escape.window="configureProduct = null">
                            <section role="dialog" aria-modal="true" aria-labelledby="product-option-title" class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-t-3xl border border-border bg-surface p-5 shadow-2xl sm:rounded-3xl sm:p-6">
                                <div class="flex items-start justify-between gap-3 border-b border-border pb-4"><div><p class="text-xs font-semibold uppercase tracking-wide text-primary-700">Sesuaikan pesanan</p><h2 id="product-option-title" class="mt-1 text-xl font-bold text-text" x-text="configureProduct.name"></h2><p class="mt-1 text-sm text-muted">Harga dasar <span x-text="formatRupiah(configureProduct.price)"></span></p></div><button type="button" @click="configureProduct = null" class="min-h-11 min-w-11 rounded-xl text-muted hover:bg-primary-50" aria-label="Tutup pilihan">×</button></div>
                                <div class="space-y-5 py-4">
                                    <template x-for="group in configureProduct.option_groups" :key="group.id">
                                        <fieldset class="space-y-2"><legend class="flex w-full items-center justify-between gap-2 text-sm font-bold text-text"><span x-text="group.name"></span><span class="text-xs font-medium text-muted" x-text="group.is_required ? 'Wajib dipilih' : (group.selection_type === 'multiple' ? `Pilih ${group.min_select}–${group.max_select ?? 'beberapa'}` : 'Pilih satu')"></span></legend>
                                            <div class="space-y-1 rounded-2xl border border-border p-2">
                                                <template x-for="option in group.options" :key="option.id">
                                                    <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-xl px-3 py-2 text-sm hover:bg-primary-50"><input :type="group.selection_type === 'multiple' ? 'checkbox' : 'radio'" :name="`option-group-${group.id}`" :value="option.id" x-model="optionSelections[group.id]" class="h-4 w-4 border-border text-primary-700 focus:ring-primary-600"><span class="min-w-0 flex-1 font-medium text-text" x-text="option.name"></span><span class="shrink-0 text-xs font-semibold text-muted" x-text="formatDelta(option.price_delta)"></span></label>
                                                </template>
                                            </div>
                                        </fieldset>
                                    </template>
                                    <p x-show="optionError" x-text="optionError" class="rounded-xl bg-danger/10 px-3 py-2 text-sm font-medium text-danger"></p>
                                </div>
                                <div class="flex gap-3 border-t border-border pt-4"><button type="button" @click="configureProduct = null" class="min-h-12 flex-1 rounded-xl border border-border px-4 text-sm font-semibold text-text">Batal</button><button type="button" @click="addConfiguredProduct()" class="min-h-12 flex-1 rounded-xl bg-primary-700 px-4 text-sm font-bold text-on-primary">Tambah ke keranjang</button></div>
                            </section>
                        </div>
                    </template>
                    <template x-if="!sampleMode && cart.length"><div class="fixed inset-x-3 bottom-20 z-20 lg:hidden"><button type="button" @click="document.getElementById('pos-cart').scrollIntoView({ behavior: 'smooth', block: 'start' })" class="flex min-h-14 w-full items-center justify-between gap-3 rounded-2xl bg-primary-700 px-4 text-sm font-bold text-on-primary shadow-lg"><span x-text="`Keranjang (${itemCount} item)`"></span><span x-text="formatRupiah(subtotal)"></span></button></div></template>
                </div>
                @break

            @case('products')
                <x-ui.card class="space-y-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div><p class="text-sm text-muted">Katalog toko</p><h2 class="mt-1 text-xl font-bold text-text">Produk</h2><p class="mt-1 text-sm text-muted">Kelola produk yang tersedia di kasir.</p></div>
                        @if($isOwner || $isAdmin)<x-ui.button href="{{ $screenLink('owner.products.create', 'product-form') }}">Tambah produk</x-ui.button>@endif
                    </div>
                    @if(session('status'))<x-ui.toast>{{ session('status') }}</x-ui.toast>@endif
                    @if($products->isEmpty())
                        <x-ui.empty-state title="Belum ada produk" description="Tambahkan produk terlebih dahulu agar kasir dapat membuat transaksi." />
                        @if($isOwner || $isAdmin)<x-ui.button href="{{ $screenLink('owner.products.create', 'product-form') }}">Tambah produk</x-ui.button>@endif
                    @else
                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach($products as $product)
                                <article class="rounded-2xl border border-border/70 bg-surface/75 p-4">
                                    @if($product['image_url'])<img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}" class="mb-3 h-40 w-full rounded-xl object-cover" loading="lazy">@endif
                                    <div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="truncate font-semibold text-text">{{ $product['name'] }}</h3><p class="mt-1 text-xs text-muted">{{ $product['sku'] ?: 'Tanpa SKU' }} · {{ $product['category'] }}</p></div><span class="shrink-0 font-bold text-primary-800">Rp{{ number_format($product['price'], 0, ',', '.') }}</span></div>
                                    <div class="mt-3 flex items-center justify-between gap-2 border-t border-border/70 pt-3 text-xs"><span class="text-muted">{{ $product['track_stock'] ? "Stok: {$product['stock']} {$product['unit']}" : 'Stok tidak terbatas' }}</span>@if($product['track_stock'] && $product['low_stock'])<span class="rounded-full bg-warning/15 px-2.5 py-1 font-semibold text-warning">Stok menipis</span>@endif</div>
                                    @if($product['track_stock'])<a href="{{ route('owner.stock-adjustment', ['product' => $product['id']]) }}" class="mt-3 inline-flex min-h-11 items-center justify-center rounded-xl border border-border px-3 text-sm font-semibold text-primary-800 transition hover:bg-primary-50">Sesuaikan stok</a>@endif
                                </article>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>
                @break

            @case('categories')
                <x-ui.card class="space-y-5">
                    <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-sm text-muted">Katalog toko</p><h2 class="mt-1 text-xl font-bold text-text">Kategori produk</h2><p class="mt-1 text-sm text-muted">Kelompokkan produk agar mudah ditemukan.</p></div><x-ui.button disabled>Tambah kategori</x-ui.button></div>
                    <x-ui.empty-state title="Belum ada kategori" description="Kategori akan membantu Anda menyusun katalog produk." />
                </x-ui.card>
                @break

            @case('stock-adjustment')
                <x-ui.card class="mx-auto max-w-2xl space-y-5">
                    <div><p class="text-sm text-muted">Inventaris</p><h2 class="mt-1 text-xl font-bold text-text">Penyesuaian stok</h2><p class="mt-1 text-sm text-muted">Pilih produk, tentukan stok masuk atau keluar, lalu masukkan jumlahnya. Setiap perubahan tercatat atas nama Anda.</p></div>
                    @if(session('status'))<x-ui.toast>{{ session('status') }}</x-ui.toast>@endif
                    @if($errors->any())<div class="rounded-xl border border-danger/20 bg-danger/5 p-3 text-sm text-danger">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
                    <form method="POST" action="{{ route('owner.stock-adjustment.store') }}" class="space-y-4">
                        @csrf
                        <label for="product_id" class="block text-sm font-semibold text-text">Produk
                            <select id="product_id" name="product_id" required class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-3 py-2 text-base font-normal text-text shadow-sm focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/20">
                                <option value="">Pilih produk</option>
                                @foreach($products as $product)
                                    @if($product['track_stock'])<option value="{{ $product['id'] }}" @selected((string) old('product_id', request('product')) === (string) $product['id'])>{{ $product['name'] }} · stok {{ $product['stock'] }} {{ $product['unit'] }}</option>@endif
                                @endforeach
                            </select>
                        </label>
                        @error('product_id')<p class="text-sm font-medium text-danger">{{ $message }}</p>@enderror
                        <fieldset class="space-y-2"><legend class="text-sm font-semibold text-text">Jenis perubahan</legend><div class="grid grid-cols-2 gap-3"><label class="flex min-h-12 cursor-pointer items-center gap-2 rounded-xl border border-border px-3 text-sm font-semibold text-text has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50"><input type="radio" name="direction" value="in" @checked(old('direction', 'in') === 'in') class="h-4 w-4 border-border text-primary-700 focus:ring-primary-600">Stok masuk</label><label class="flex min-h-12 cursor-pointer items-center gap-2 rounded-xl border border-border px-3 text-sm font-semibold text-text has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50"><input type="radio" name="direction" value="out" @checked(old('direction') === 'out') class="h-4 w-4 border-border text-primary-700 focus:ring-primary-600">Stok keluar</label></div></fieldset>
                        @error('direction')<p class="text-sm font-medium text-danger">{{ $message }}</p>@enderror
                        <label for="quantity" class="block text-sm font-semibold text-text">Jumlah barang
                            <input id="quantity" name="quantity" type="number" min="1" max="4294967295" step="1" inputmode="numeric" value="{{ old('quantity') }}" required class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-3 py-2 text-base font-normal text-text shadow-sm focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/20" placeholder="Contoh: 10">
                        </label>
                        @error('quantity')<p class="text-sm font-medium text-danger">{{ $message }}</p>@enderror
                        <p class="-mt-3 text-xs text-muted">Masukkan jumlah saja. Stok keluar akan ditolak jika melebihi persediaan yang ada.</p>
                        <label for="reason" class="block text-sm font-semibold text-text">Alasan
                            <textarea id="reason" name="reason" rows="3" maxlength="255" required class="mt-1 w-full rounded-xl border border-border bg-surface px-3 py-2 text-base font-normal text-text shadow-sm focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/20" placeholder="Contoh: stok barang masuk dari pemasok">{{ old('reason') }}</textarea>
                        </label>
                        @error('reason')<p class="text-sm font-medium text-danger">{{ $message }}</p>@enderror
                        <div class="flex flex-col-reverse gap-3 border-t border-border pt-4 sm:flex-row sm:justify-between"><x-ui.button href="{{ $screenLink('owner.products', 'products') }}" variant="secondary">Kembali ke produk</x-ui.button><x-ui.button type="submit">Simpan perubahan stok</x-ui.button></div>
                    </form>
                </x-ui.card>
                @break

            @case('stock-history')
                <x-ui.card class="space-y-5">
                    <div><p class="text-sm text-muted">Inventaris toko</p><h2 class="mt-1 text-xl font-bold text-text">Manajemen stok</h2><p class="mt-1 text-sm text-muted">Lihat jumlah persediaan, produk yang perlu di-restok, dan aktivitas stok terbaru.</p></div>
                    @php($trackedProducts = $inventoryProducts->where('track_stock', true))
                    @php($lowStockCount = $trackedProducts->filter(fn ($product) => $product->stock <= $product->low_stock_threshold)->count())
                    <div class="grid gap-3 sm:grid-cols-3"><article class="rounded-xl border border-border bg-surface p-4"><p class="text-xs font-medium text-muted">Produk terlacak</p><p class="mt-1 text-2xl font-bold text-text">{{ $trackedProducts->count() }}</p></article><article class="rounded-xl border border-border bg-surface p-4"><p class="text-xs font-medium text-muted">Perlu restok</p><p class="mt-1 text-2xl font-bold {{ $lowStockCount ? 'text-warning' : 'text-text' }}">{{ $lowStockCount }}</p></article><article class="rounded-xl border border-border bg-surface p-4"><p class="text-xs font-medium text-muted">Stok tidak terbatas</p><p class="mt-1 text-2xl font-bold text-text">{{ $inventoryProducts->where('track_stock', false)->count() }}</p></article></div>
                    <section class="space-y-3"><div class="flex flex-wrap items-center justify-between gap-2"><h3 class="font-bold text-text">Persediaan produk</h3><a href="{{ $screenLink('owner.stock-adjustment', 'stock-adjustment') }}" class="inline-flex min-h-11 items-center rounded-xl border border-border px-3 text-sm font-semibold text-primary-800 hover:bg-primary-50">Sesuaikan stok</a></div>
                        @if($inventoryProducts->isEmpty())<x-ui.empty-state title="Belum ada produk" description="Produk toko akan muncul di sini setelah ditambahkan." />@else
                            <div class="grid gap-3 sm:grid-cols-2">@foreach($inventoryProducts as $product)<article class="rounded-xl border border-border bg-surface p-4"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><h4 class="break-words font-semibold text-text">{{ $product->name }}</h4><p class="mt-1 text-xs text-muted">{{ $product->track_stock ? 'Stok saat ini' : 'Persediaan' }}</p></div>@if($product->track_stock)<span class="shrink-0 text-right"><strong class="block text-lg text-text">{{ number_format($product->stock) }}</strong><span class="text-xs text-muted">{{ $product->unit }}</span></span>@else<span class="rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success">Unlimited</span>@endif</div>@if($product->track_stock)<div class="mt-3 flex items-center justify-between gap-2 border-t border-border/70 pt-3"><span class="text-xs {{ $product->stock <= $product->low_stock_threshold ? 'font-semibold text-warning' : 'text-muted' }}">{{ $product->stock <= $product->low_stock_threshold ? 'Perlu restok' : 'Stok aman' }} · batas {{ $product->low_stock_threshold }}</span><a href="{{ route('owner.stock-adjustment', ['product' => $product->id]) }}" class="inline-flex min-h-10 items-center rounded-lg px-2 text-xs font-bold text-primary-800 hover:bg-primary-50">Ubah stok</a></div>@endif</article>@endforeach</div>
                        @endif
                    </section>
                    <section class="space-y-3 border-t border-border pt-5"><div><h3 class="font-bold text-text">Aktivitas stok</h3><p class="mt-1 text-sm text-muted">50 perubahan terbaru, termasuk transaksi penjualan.</p></div>
                        @if($stockMovements->isEmpty())<x-ui.empty-state title="Belum ada aktivitas stok" description="Penjualan dan penyesuaian stok akan tercatat di sini." />@else
                            <div class="space-y-3">@foreach($stockMovements as $movement)<article class="rounded-2xl border border-border/70 bg-surface/70 p-4"><div class="flex flex-wrap items-start justify-between gap-3"><div><h4 class="font-semibold text-text">{{ $movement->product?->name ?? 'Produk' }}</h4><p class="mt-1 text-xs text-muted">{{ $movement->created_at?->timezone(config('app.timezone'))->translatedFormat('j M Y, H:i') }} · {{ $movement->user?->name ?? 'Pengguna' }}</p></div><span class="rounded-full bg-primary-50 px-3 py-1 text-sm font-bold {{ $movement->quantity > 0 ? 'text-success' : 'text-warning' }}">{{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</span></div><p class="mt-3 text-sm text-muted">{{ $movement->reason ?: ucfirst(str_replace('_', ' ', $movement->type->value)) }}</p><p class="mt-1 text-xs text-muted">Stok setelah perubahan: {{ $movement->stock_after }}</p></article>@endforeach</div>
                        @endif
                    </section>
                </x-ui.card>
                @break

            @case('activity')
                @php($activities = $activities ?? collect())
                <x-ui.card class="space-y-5">
                    <div><p class="text-sm text-muted">Transparansi toko</p><h2 class="mt-1 text-xl font-bold text-text">Log aktivitas</h2><p class="mt-1 text-sm text-muted">Perubahan produk, stok, dan transaksi beserta akun yang melakukannya.</p></div>
                    @if($activities->isEmpty())
                        <x-ui.empty-state title="Belum ada aktivitas" description="Aktivitas produk, stok, dan transaksi akan tercatat di sini." />
                    @else
                        <ol class="space-y-3">
                            @foreach($activities as $activity)
                                @php($activityLabel = match($activity->action) { 'product.created' => 'Produk ditambahkan', 'product.updated' => 'Produk diubah', 'stock.adjusted' => 'Stok disesuaikan', 'order.paid' => 'Transaksi selesai', default => 'Aktivitas toko' })
                                <li class="rounded-2xl border border-border/70 bg-surface/70 p-4 sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-800">{{ $activityLabel }}</span><span class="font-semibold text-text">{{ $activity->subject_name ?? 'Toko' }}</span></div><p class="mt-2 text-sm text-text">{{ $activity->description }}</p><p class="mt-1 break-all text-xs text-muted">Oleh {{ $activity->actor?->name ?? 'Akun dihapus' }}{{ $activity->actor?->email ? ' · '.$activity->actor->email : '' }}</p></div>
                                        <time class="shrink-0 text-xs text-muted" datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->timezone(config('app.timezone'))->translatedFormat('j M Y, H:i') }}</time>
                                    </div>
                                    @if($activity->action === 'stock.adjusted' && filled(data_get($activity->details, 'reason')))<p class="mt-3 border-t border-border/70 pt-3 text-xs text-muted">Alasan: {{ data_get($activity->details, 'reason') }}</p>@endif
                                    @if($activity->action === 'order.paid')<p class="mt-3 border-t border-border/70 pt-3 text-xs text-muted">Metode pembayaran: {{ strtoupper(data_get($activity->details, 'payment_method', 'cash')) }}</p>@endif
                                </li>
                            @endforeach
                        </ol>
                        <p class="text-xs text-muted">Menampilkan maksimal 100 aktivitas terbaru. Catatan aktivitas tidak dapat diubah atau dihapus.</p>
                    @endif
                </x-ui.card>
                @break

            @case('orders')
                <x-ui.card class="space-y-5">
                    <div><p class="text-sm text-muted">Aktivitas toko</p><h2 class="mt-1 text-xl font-bold text-text">Riwayat transaksi</h2><p class="mt-1 text-sm text-muted">Cari transaksi dan periksa status pembayarannya.</p></div>
                    @if(session('status'))<x-ui.toast>{{ session('status') }}</x-ui.toast>@endif
                    @if($orders->isEmpty())
                        <x-ui.empty-state title="Belum ada transaksi" description="Transaksi yang dibuat dari kasir akan tampil di sini." />
                    @else
                        <div class="space-y-3">
                            @foreach($orders as $order)
                                <article class="rounded-2xl border border-border/70 bg-surface/70 p-4 sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-border/70 pb-3"><div><h3 class="font-bold text-text">{{ $order->order_number }}</h3><p class="mt-1 text-xs text-muted">{{ $order->created_at->translatedFormat('j M Y, H:i') }} · {{ $order->cashier->name }}</p></div><span class="rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success">Lunas · Tunai</span></div>
                                    <ul class="mt-3 space-y-2">@foreach($order->items as $item)<li class="flex justify-between gap-3 text-sm"><span class="min-w-0 truncate text-muted">{{ $item->product_name }} <span class="text-text">× {{ $item->quantity }}</span></span><span class="shrink-0 font-medium text-text">Rp{{ number_format($item->line_total, 0, ',', '.') }}</span></li>@endforeach</ul>
                                    <div class="mt-3 space-y-1 border-t border-border/70 pt-3 text-sm"><div class="flex justify-between"><span class="text-muted">Total</span><span class="font-bold text-text">Rp{{ number_format($order->total, 0, ',', '.') }}</span></div><div class="flex justify-between text-xs"><span class="text-muted">Uang diterima</span><span class="text-text">Rp{{ number_format($order->cash_received, 0, ',', '.') }}</span></div><div class="flex justify-between text-xs"><span class="text-muted">Kembalian</span><span class="text-text">Rp{{ number_format($order->change_amount, 0, ',', '.') }}</span></div></div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>
                @break

            @case('report')
                <div class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-sm text-muted">Performa usaha</p><h2 class="mt-1 text-xl font-bold text-text">Laporan penjualan</h2></div><x-ui.select name="period" label="Periode" disabled><option>7 hari terakhir</option></x-ui.select></div>
                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                    @foreach(['Omzet', 'Transaksi lunas', 'Rata-rata transaksi', 'Produk terlaris'] as $metric)
                        <x-ui.card><p class="text-sm text-muted">{{ $metric }}</p><p class="mt-3 text-2xl font-bold text-muted">—</p></x-ui.card>
                    @endforeach
                </div>
                <x-ui.card><h3 class="font-semibold text-text">Tren penjualan</h3><x-ui.empty-state class="mt-4" title="Belum ada data penjualan" description="Grafik akan tersedia setelah transaksi tercatat." /></x-ui.card>
                <x-ui.card><h3 class="font-semibold text-text">Produk terlaris</h3><x-ui.empty-state class="mt-4" title="Belum ada data produk" description="Produk terlaris akan dihitung dari transaksi toko." /></x-ui.card>
                @break

            @case('store-profile')
                <x-ui.card class="mx-auto max-w-2xl space-y-5">
                    <div><p class="text-sm text-muted">Pengaturan</p><h2 class="mt-1 text-xl font-bold text-text">Profil toko</h2><p class="mt-1 text-sm text-muted">Informasi yang tersimpan untuk toko Anda.</p></div>
                    <x-ui.input name="name" label="Nama toko" :value="$store?->name" disabled />
                    <x-ui.textarea name="address" label="Alamat" disabled>{{ $store?->address }}</x-ui.textarea>
                    <x-ui.input name="phone" label="Nomor telepon" :value="$store?->phone" disabled />
                    <x-ui.button class="w-full sm:w-auto" disabled>Simpan perubahan</x-ui.button>
                    <p class="text-xs text-muted">Pengubahan profil toko belum tersedia.</p>
                </x-ui.card>
                @break

            @case('members')
                <x-ui.card class="space-y-5">
                    <div><p class="text-sm text-muted">Tim toko</p><h2 class="mt-1 text-xl font-bold text-text">Kelola anggota</h2><p class="mt-1 text-sm text-muted">Buat akun admin toko atau kasir dan atur aksesnya.</p></div>
                    @if(session('status'))<x-ui.toast>{{ session('status') }}</x-ui.toast>@endif
                    <form method="POST" action="{{ route('owner.members.store') }}" class="space-y-4 rounded-2xl border border-border bg-primary-50 p-4 sm:p-5">
                        @csrf
                        <h3 class="font-semibold text-text">Tambah anggota</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-ui.input name="name" label="Nama kasir" :value="old('name')" required autocomplete="name" />
                            <x-ui.input name="email" label="Email login" type="email" :value="old('email')" required autocomplete="email" />
                            <x-ui.input name="password" label="Kata sandi awal" type="password" required autocomplete="new-password" />
                            <x-ui.input name="password_confirmation" label="Ulangi kata sandi" type="password" required autocomplete="new-password" />
                            <x-ui.select name="role" label="Peran" required>
                                <option value="cashier" @selected(old('role', 'cashier') === 'cashier')>Kasir</option>
                                <option value="admin" @selected(old('role') === 'admin')>Admin toko</option>
                            </x-ui.select>
                        </div>
                        @if($errors->any())<ul class="space-y-1 text-sm text-danger">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
                        <p class="text-xs leading-5 text-muted">Gunakan kata sandi minimal 10 karakter dan sampaikan kepada kasir lewat cara yang aman.</p>
                        <x-ui.button type="submit">Buat akun anggota</x-ui.button>
                    </form>
                    @if($members->isEmpty())
                        <x-ui.empty-state title="Belum ada anggota" description="Admin toko dan kasir yang Anda tambahkan akan muncul di daftar ini." />
                    @else
                        <ul class="divide-y divide-border">
                            @foreach($members as $member)
                                <li class="flex min-h-16 items-center gap-3 py-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 font-bold text-primary-800">{{ mb_strtoupper(mb_substr($member->user->name, 0, 1)) }}</span>
                                    <span class="min-w-0 flex-1"><span class="block truncate text-sm font-semibold text-text">{{ $member->user->name }}</span><span class="block truncate text-xs text-muted">{{ $member->user->email }}</span></span>
                                    <div class="flex shrink-0 flex-col items-end gap-1">
                                        <span class="text-xs font-medium text-muted">{{ $member->role === \App\Enums\StoreRole::Admin ? 'Admin toko' : 'Kasir' }}</span>
                                        <x-ui.badge :variant="$member->is_active ? 'success' : 'muted'">{{ $member->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                                        <form method="POST" action="{{ route('owner.members.update', $member) }}">@csrf @method('PATCH')<input type="hidden" name="is_active" value="{{ $member->is_active ? '0' : '1' }}"><button class="min-h-11 px-2 text-xs font-semibold text-primary-700" type="submit">{{ $member->is_active ? 'Nonaktifkan' : 'Aktifkan kembali' }}</button></form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <p class="text-xs text-muted">Admin toko dapat mengelola produk, stok, transaksi, dan laporan. Pengaturan toko dan anggota tetap khusus owner.</p>
                </x-ui.card>
                @break

            @case('appearance')
                @php($storeTheme = $store?->theme ?? [])
                <div class="mx-auto max-w-4xl space-y-5" x-data="themePreview({ presets: @js(config('design-system.presets')), defaultPrimary: @js(old('primary', $storeTheme['primary'] ?? config('design-system.default_primary'))), defaultAccent: @js(old('accent', $storeTheme['accent'] ?? config('design-system.tokens.accent'))) })">
                    <section class="rounded-3xl border border-primary-200/70 bg-gradient-to-br from-primary-100/65 via-surface/60 to-accent/15 p-5 shadow-lg shadow-primary-900/5 backdrop-blur-xl sm:p-7">
                        <div><p class="text-sm font-semibold text-primary-800">Personalisasi toko</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-text">Atur warna tokomu</h2><p class="mt-1 max-w-2xl text-sm leading-6 text-muted">Tema ini akan dipakai owner, admin, dan kasir. Pilih warna utama yang tetap mudah dibaca.</p></div>
                        <div class="mt-5 flex flex-wrap gap-3 rounded-2xl border border-border/70 bg-surface/55 p-4 backdrop-blur-md">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-600 text-sm font-bold text-on-primary shadow-sm">Aa</span>
                            <div class="min-w-0 flex-1"><p class="font-semibold text-text">{{ $store?->name ?? 'Toko Anda' }}</p><p class="text-sm text-muted">Contoh tampilan warna toko</p></div>
                            <span class="inline-flex min-h-10 items-center rounded-control bg-accent/15 px-4 text-sm font-semibold text-primary-900">Aksen</span>
                        </div>
                        <p x-show="!isReadable" x-cloak class="mt-3 text-sm font-medium text-danger">Warna utama ini belum memenuhi kontras teks WCAG AA 4.5:1.</p>
                        <p class="mt-2 text-xs text-muted">Rasio kontras warna utama: <span class="font-semibold text-text" x-text="contrastRatio.toFixed(2) + ':1'"></span></p>
                    </section>

                    @if(session('status'))<x-ui.toast>{{ session('status') }}</x-ui.toast>@endif
                    @if($errors->any())<ul class="space-y-1 rounded-xl border border-danger/20 bg-danger/5 p-3 text-sm text-danger">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif

                    <form method="POST" action="{{ route('owner.appearance.update') }}" class="space-y-5 rounded-3xl border border-border/70 bg-surface/65 p-5 shadow-lg shadow-primary-900/5 backdrop-blur-xl sm:p-7">
                        @csrf
                        @method('PUT')
                        <div>
                            <h3 class="font-bold text-text">Warna tema</h3>
                            <p class="mt-1 text-sm text-muted">Pilih preset atau gunakan pemilih warna untuk mengatur warna sendiri.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach(config('design-system.presets') as $preset)
                                <button type="button" x-on:click="choose('{{ $preset['primary'] }}')" class="min-h-11 rounded-xl border border-border/70 bg-surface/65 px-3 text-sm font-semibold text-text transition hover:border-primary-400 hover:bg-primary-50/75">{{ $preset['name'] }}</button>
                            @endforeach
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-ui.input name="primary" label="Warna utama" type="color" :value="old('primary', $storeTheme['primary'] ?? config('design-system.default_primary'))" x-model="primary" x-on:input="choose(primary)" />
                            <x-ui.input name="accent" label="Warna aksen" type="color" :value="old('accent', $storeTheme['accent'] ?? config('design-system.tokens.accent'))" x-model="accent" x-on:input="chooseAccent(accent)" />
                            <x-ui.select name="radius" label="Bentuk sudut">
                                <option value="md" @selected(old('radius', $storeTheme['radius'] ?? config('design-system.default_radius')) === 'md')>Kecil</option>
                                <option value="lg" @selected(old('radius', $storeTheme['radius'] ?? config('design-system.default_radius')) === 'lg')>Sedang</option>
                                <option value="xl" @selected(old('radius', $storeTheme['radius'] ?? config('design-system.default_radius')) === 'xl')>Besar</option>
                            </x-ui.select>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border/70 pt-4">
                            <p class="max-w-xl text-xs leading-5 text-muted">Warna utama dengan kontras rendah ditolak agar teks tetap terbaca. Tema tersimpan berlaku untuk seluruh anggota toko.</p>
                            <x-ui.button type="submit">Simpan tampilan</x-ui.button>
                        </div>
                    </form>
                </div>
                @break

            @case('payments')
                <div class="mx-auto max-w-4xl space-y-5">
                    <section class="rounded-2xl bg-primary-700 p-5 text-on-primary shadow-sm sm:p-7">
                        <p class="text-sm font-semibold text-on-primary/75">Integrasi pembayaran</p>
                        <h2 class="mt-2 text-2xl font-bold tracking-tight">Hubungkan Midtrans</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-on-primary/80">Siapkan pembayaran nontunai untuk toko Anda. Kunci server tetap disimpan di konfigurasi server dan tidak pernah ditampilkan di halaman ini.</p>
                    </section>

                    <x-ui.card class="space-y-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div><p class="text-sm text-muted">Status konfigurasi</p><h3 class="mt-1 text-lg font-bold text-text">Midtrans Snap</h3></div>
                            <x-ui.badge :variant="$midtransReady ? 'success' : 'warning'">{{ $midtransReady ? 'Kunci tersedia' : 'Belum dikonfigurasi' }}</x-ui.badge>
                        </div>

                        <dl class="grid gap-4 rounded-xl border border-border bg-primary-50 p-4 sm:grid-cols-2">
                            <div><dt class="text-xs font-semibold uppercase tracking-wide text-muted">Mode</dt><dd class="mt-1 text-sm font-semibold text-text">{{ $midtransProduction ? 'Produksi' : 'Sandbox' }}</dd></div>
                            <div><dt class="text-xs font-semibold uppercase tracking-wide text-muted">Status transaksi</dt><dd class="mt-1 text-sm font-semibold text-text">Belum diaktifkan</dd></div>
                        </dl>

                        @if($midtransReady)
                            <p class="text-sm leading-6 text-muted">Kunci server dan client sudah terisi. Koneksi pembayaran belum diuji dan checkout Midtrans belum aktif sampai modul transaksi diintegrasikan.</p>
                        @else
                            <div class="space-y-3 text-sm leading-6 text-text">
                                <p>Tambahkan kunci dari dashboard Midtrans ke file <code class="rounded bg-primary-50 px-1.5 py-1 font-mono text-xs">.env</code> pada server aplikasi:</p>
                                <pre class="overflow-x-auto rounded-xl border border-border bg-primary-50 p-4 text-xs text-text"><code>MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false</code></pre>
                                <p class="text-muted">Gunakan kunci Sandbox untuk pengembangan. Setelah mengubah konfigurasi, muat ulang konfigurasi aplikasi. Jangan bagikan kunci server melalui chat atau browser.</p>
                            </div>
                        @endif

                        <div class="border-t border-border pt-4">
                            <x-ui.button class="w-full sm:w-auto" disabled>Uji koneksi belum tersedia</x-ui.button>
                            <p class="mt-2 text-xs text-muted">Pengaturan ini membaca konfigurasi platform. Penyimpanan kunci per toko dan uji koneksi memerlukan modul pembayaran.</p>
                        </div>
                    </x-ui.card>
                </div>
                @break

            @default
                <x-ui.card class="space-y-4">
                    <div><p class="text-sm text-muted">{{ $isOwner ? 'Pengelolaan toko' : 'Akun' }}</p><h2 class="mt-1 text-xl font-bold text-text">{{ $isOwner ? 'Pengaturan lainnya' : 'Menu lainnya' }}</h2></div>
                    <nav class="divide-y divide-border" aria-label="Pengaturan lainnya">
                        @if($isOwner)
                            <a href="{{ $screenLink('owner.store-profile', 'store-profile') }}" class="flex min-h-14 items-center justify-between gap-3 text-sm font-medium text-text">Profil toko <span class="text-muted" aria-hidden="true">→</span></a>
                            <a href="{{ $screenLink('owner.members', 'members') }}" class="flex min-h-14 items-center justify-between gap-3 text-sm font-medium text-text">Kelola kasir <span class="text-muted" aria-hidden="true">→</span></a>
                            <a href="{{ $screenLink('owner.appearance', 'appearance') }}" class="flex min-h-14 items-center justify-between gap-3 text-sm font-medium text-text">Tampilan toko <span class="text-muted" aria-hidden="true">→</span></a>
                            <a href="{{ $screenLink('owner.payment-settings', 'payments') }}" class="flex min-h-14 items-center justify-between gap-3 text-sm font-medium text-text">Hubungkan Midtrans <span class="text-muted" aria-hidden="true">→</span></a>
                            <a href="{{ $screenLink('owner.stock-adjustment', 'stock-adjustment') }}" class="flex min-h-14 items-center justify-between gap-3 text-sm font-medium text-text">Penyesuaian stok <span class="text-muted" aria-hidden="true">→</span></a>
                            <a href="{{ $screenLink('owner.stock-history', 'stock-history') }}" class="flex min-h-14 items-center justify-between gap-3 text-sm font-medium text-text">Manajemen stok <span class="text-muted" aria-hidden="true">→</span></a>
                        @else
                            <a href="{{ route('profile.edit') }}" class="flex min-h-14 items-center justify-between gap-3 text-sm font-medium text-text">Profil akun <span class="text-muted" aria-hidden="true">→</span></a>
                        @endif
                    </nav>
                </x-ui.card>
        @endswitch
    </div>
</x-owner-layout>
