<div class="mx-auto max-w-xl space-y-5">
    <div><x-ui.badge>Langkah 1 dari 1</x-ui.badge><h2 class="mt-2 text-xl font-bold text-text">Buat toko pertama Anda</h2><p class="mt-1 text-sm text-muted">Informasi ini bisa diubah kembali nanti.</p></div>
    <x-ui.card>
        <form class="space-y-4" x-on:submit.prevent>
            <x-ui.input label="Nama toko" name="name" placeholder="Contoh: Kedai Senja" />
            <x-ui.textarea label="Alamat toko" name="address" placeholder="Jalan, kota, dan kode pos" />
            <x-ui.input label="Nomor telepon" name="phone" type="tel" placeholder="08xx xxxx xxxx" />
            <div class="space-y-1.5"><label for="logo" class="block text-sm font-medium text-text">Logo toko <span class="font-normal text-muted">(opsional)</span></label><input id="logo" type="file" accept="image/*" class="ui-control file:mr-3 file:min-h-11 file:border-0 file:bg-surface file:font-medium file:text-primary-700"></div>
            <div class="flex justify-end"><x-ui.button type="submit">Simpan dan lanjutkan</x-ui.button></div>
        </form>
    </x-ui.card>
</div>
