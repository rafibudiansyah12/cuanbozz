<div class="space-y-5">
    <header><h1 class="text-2xl font-bold text-text">Lupa kata sandi?</h1><p class="mt-1 text-sm text-muted">Masukkan email. Kami akan mengirim tautan untuk membuat kata sandi baru.</p></header>
    <form class="space-y-4" x-on:submit.prevent>
        <x-ui.input label="Email" name="email" type="email" placeholder="nama@email.com" />
        <x-ui.button type="submit" class="w-full">Kirim tautan reset</x-ui.button>
    </form>
    <a href="/demo/login" class="inline-flex min-h-11 items-center text-sm font-semibold text-primary-700">← Kembali ke masuk</a>
</div>
