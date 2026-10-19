<x-guest-layout>
    <div class="mx-auto w-full max-w-xl space-y-6 text-left">
        <div class="space-y-2 text-center">
            <x-ui.badge variant="success">Langkah 1 dari 1</x-ui.badge>
            <h1 class="text-2xl font-bold text-text">Buat toko Anda</h1>
            <p class="text-sm leading-6 text-muted">Lengkapi informasi toko. Setelah dikirim, akun Anda menunggu persetujuan developer.</p>
        </div>

        <x-ui.card class="space-y-5">
            <form method="POST" action="{{ route('onboarding.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <x-ui.input name="name" label="Nama toko" autocomplete="organization" required :error="$errors->first('name')" :value="old('name')" placeholder="Contoh: Warung Berkah" />
                <x-ui.textarea name="address" label="Alamat toko" rows="3" required :error="$errors->first('address')" placeholder="Alamat lengkap toko">{{ old('address') }}</x-ui.textarea>
                <x-ui.input name="phone" label="Nomor telepon" type="tel" autocomplete="tel" required :error="$errors->first('phone')" :value="old('phone')" placeholder="08xxxxxxxxxx" />
                <div class="space-y-1.5">
                    <label for="logo" class="block text-sm font-medium text-text">Logo toko <span class="font-normal text-muted">(opsional)</span></label>
                    <input id="logo" name="logo" type="file" accept="image/*" class="ui-control py-2" aria-describedby="logo-help">
                    <p id="logo-help" class="text-xs text-muted">Format gambar, maksimal 2 MB.</p>
                    @error('logo') <p class="text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-xl border border-border bg-primary-50 p-4 text-sm leading-6 text-text">
                    <strong>Persetujuan akun</strong>
                    <p class="mt-1 text-muted">Informasi toko akan ditinjau. Anda dapat mulai memakai fitur setelah akun disetujui.</p>
                </div>

                <x-ui.button type="submit" class="w-full">Kirim data toko</x-ui.button>
            </form>
        </x-ui.card>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <x-ui.button type="submit" variant="ghost">Keluar</x-ui.button>
        </form>
    </div>
</x-guest-layout>
