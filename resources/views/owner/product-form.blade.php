<x-owner-layout title="Tambah produk" :preview-mode="$previewMode" :store="$store">
    <x-ui.card class="mx-auto max-w-3xl space-y-5">
        <div><p class="text-sm text-muted">Katalog toko</p><h2 class="mt-1 text-xl font-bold text-text">Informasi produk</h2><p class="mt-1 text-sm text-muted">Produk aktif akan langsung tersedia di halaman kasir.</p></div>
        @if($errors->any())<ul class="space-y-1 rounded-xl border border-danger/20 bg-danger/5 p-3 text-sm text-danger">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
        <form method="POST" action="{{ route('owner.products.store') }}" class="space-y-5" x-data="{ trackStock: @js((bool) old('track_stock', true)) }">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.input name="name" label="Nama produk" :value="old('name')" placeholder="Contoh: Kopi susu" required />
                <x-ui.input name="sku" label="SKU" :value="old('sku')" placeholder="Opsional, misalnya KOP-001" />
                <x-ui.input name="category" label="Kategori" :value="old('category')" placeholder="Contoh: Minuman" />
                <x-ui.input name="price" label="Harga jual (rupiah)" type="number" :value="old('price')" min="0" step="1" inputmode="numeric" placeholder="25000" required />
                <x-ui.input name="unit" label="Satuan" :value="old('unit', 'pcs')" placeholder="pcs, gelas, porsi" required />
                <x-ui.input name="stock" label="Stok awal" type="number" :value="old('stock', 0)" min="0" step="1" inputmode="numeric" x-bind:required="trackStock" x-bind:disabled="!trackStock" />
                <x-ui.input name="low_stock_threshold" label="Peringatan stok menipis" type="number" :value="old('low_stock_threshold', 0)" min="0" step="1" inputmode="numeric" />
            </div>
            <x-ui.textarea name="description" label="Deskripsi (opsional)" placeholder="Deskripsi singkat produk">{{ old('description') }}</x-ui.textarea>
            <section x-data="productOptionsForm({ initialGroups: @js(old('option_groups', [])) })" class="space-y-4 rounded-2xl border border-border/70 bg-primary-50/40 p-4 sm:p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div><h3 class="font-bold text-text">Ukuran dan tambahan</h3><p class="mt-1 text-sm text-muted">Atur pilihan seperti ukuran minuman atau topping. Selisih harga dapat nol.</p></div>
                    <button type="button" @click="addGroup()" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-primary-200 bg-surface px-3 text-sm font-semibold text-primary-800 hover:bg-primary-50">Tambah grup</button>
                </div>
                <template x-if="optionGroups.length === 0"><p class="rounded-xl border border-dashed border-border bg-surface/70 px-3 py-4 text-sm text-muted">Belum ada pilihan tambahan untuk produk ini.</p></template>
                <template x-for="(group, groupIndex) in optionGroups" :key="groupIndex">
                    <div class="space-y-3 rounded-xl border border-border bg-surface p-3 sm:p-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="block text-sm font-semibold text-text">Nama grup<input x-model="group.name" :name="`option_groups[${groupIndex}][name]`" required maxlength="100" class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-3 py-2 text-sm font-normal text-text" placeholder="Ukuran atau Topping"></label>
                            <label class="block text-sm font-semibold text-text">Cara memilih<select x-model="group.selection_type" :name="`option_groups[${groupIndex}][selection_type]`" @change="if (group.selection_type === 'single') group.max_select = 1" class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-3 py-2 text-sm font-normal text-text"><option value="single">Pilih satu</option><option value="multiple">Bisa pilih beberapa</option></select></label>
                            <label class="flex min-h-11 items-center gap-2 text-sm font-medium text-text"><input type="checkbox" x-model="group.is_required" :name="`option_groups[${groupIndex}][is_required]`" value="1" @change="group.min_select = group.is_required ? Math.max(1, Number(group.min_select)) : 0" class="h-4 w-4 rounded border-border text-primary-600 focus:ring-primary-600">Wajib dipilih</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="block text-xs font-semibold text-muted">Minimal<select x-model.number="group.min_select" :name="`option_groups[${groupIndex}][min_select]`" class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-3 text-sm font-normal text-text"><template x-for="count in Array.from({ length: group.options.length + 1 }, (_, i) => i)" :key="count"><option :value="count" x-text="count"></option></template></select></label>
                                <label class="block text-xs font-semibold text-muted">Maksimal<input x-model="group.max_select" :name="`option_groups[${groupIndex}][max_select]`" type="number" min="1" max="50" :disabled="group.selection_type === 'single'" class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-3 text-sm font-normal text-text disabled:opacity-60" placeholder="Tanpa batas"></label>
                            </div>
                        </div>
                        <div class="space-y-2 border-t border-border pt-3">
                            <template x-for="(option, optionIndex) in group.options" :key="optionIndex">
                                <div class="grid grid-cols-[minmax(0,1fr)_7rem_2.75rem] items-end gap-2">
                                    <label class="block text-xs font-semibold text-muted">Nama pilihan<input x-model="option.name" :name="`option_groups[${groupIndex}][options][${optionIndex}][name]`" required maxlength="100" class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-3 text-sm font-normal text-text" placeholder="Regular, Large, Keju"></label>
                                    <label class="block text-xs font-semibold text-muted">Selisih Rp<input x-model="option.price_delta" :name="`option_groups[${groupIndex}][options][${optionIndex}][price_delta]`" type="number" required class="mt-1 min-h-11 w-full rounded-xl border border-border bg-surface px-2 text-sm font-normal text-text" placeholder="0"></label>
                                    <button type="button" @click="removeOption(group, optionIndex)" :disabled="group.options.length <= 1" class="min-h-11 min-w-11 rounded-xl text-danger hover:bg-danger/10 disabled:opacity-40" aria-label="Hapus pilihan">×</button>
                                </div>
                            </template>
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <button type="button" @click="addOption(group)" class="inline-flex min-h-11 items-center text-sm font-semibold text-primary-700">+ Tambah pilihan</button>
                                <button type="button" @click="removeGroup(groupIndex)" class="inline-flex min-h-11 items-center text-sm font-semibold text-danger">Hapus grup</button>
                            </div>
                        </div>
                    </div>
                </template>
            </section>
            <div class="rounded-2xl border border-border/70 bg-primary-50/60 p-4">
                <input type="hidden" name="track_stock" value="0">
                <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm font-semibold text-text"><input type="checkbox" name="track_stock" value="1" x-model="trackStock" class="h-5 w-5 rounded border-border text-primary-600 focus:ring-primary-600"><span>Lacak stok produk ini</span></label>
                <p class="ml-8 text-xs leading-5 text-muted">Stok yang dilacak berkurang otomatis setelah transaksi tunai selesai.</p>
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-border pt-4 sm:flex-row sm:justify-end">
                <x-ui.button href="{{ route('owner.products') }}" variant="secondary">Batal</x-ui.button>
                <x-ui.button type="submit">Simpan produk</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-owner-layout>
