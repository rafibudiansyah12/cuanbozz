<div class="space-y-5">
    <header><h1 class="text-2xl font-bold text-text">Buat akun</h1><p class="mt-1 text-sm text-muted">Mulai kelola toko Anda dengan CuanBozz.</p></header>
    <form class="space-y-4" x-on:submit.prevent>
        <x-ui.input label="Nama lengkap" name="name" placeholder="Nama Anda" />
        <x-ui.input label="Email" name="email" type="email" placeholder="nama@email.com" />
        <x-ui.input label="Kata sandi" name="password" type="password" placeholder="Minimal 8 karakter" />
        <x-ui.input label="Ulangi kata sandi" name="password_confirmation" type="password" placeholder="Ketik ulang kata sandi" />
        <x-ui.button type="submit" class="w-full">Daftar</x-ui.button>
        <x-ui.button variant="secondary" class="w-full"><span aria-hidden="true">G</span> Daftar dengan Google</x-ui.button>
    </form>
    <p class="text-center text-sm text-muted">Sudah punya akun? <a href="/demo/login" class="font-semibold text-primary-700">Masuk</a></p>
</div>
