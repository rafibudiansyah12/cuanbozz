<div class="mx-auto max-w-xl space-y-5">
    <a href="/demo/pos{{ $query }}" class="inline-flex min-h-11 items-center text-sm font-medium text-primary-700">← Kembali ke keranjang</a>
    <x-ui.card>
        <div class="flex items-center justify-between"><h2 class="font-semibold text-text">Pembayaran</h2><x-ui.badge variant="warning">Data contoh</x-ui.badge></div>
        <p class="mt-3 text-sm text-muted">Total yang harus dibayar</p><p class="text-3xl font-bold text-text">Rp{{ number_format($demo['payment']['total'], 0, ',', '.') }}</p>
        <div class="mt-4 grid grid-cols-2 gap-2"><button type="button" class="min-h-11 rounded-control bg-primary-600 px-3 text-sm font-semibold text-on-primary">Tunai</button><button type="button" class="min-h-11 rounded-control border border-border bg-surface px-3 text-sm font-semibold text-text">Nontunai</button></div>
    </x-ui.card>
    <x-ui.card>
        <h3 class="font-semibold text-text">Tunai</h3>
        <x-ui.input class="mt-3" label="Uang diterima" name="cash_received" type="number" value="{{ $demo['payment']['cash_received'] }}" />
        <div class="mt-4 flex justify-between rounded-control bg-primary-50 p-4"><span class="text-sm text-muted">Kembalian</span><span class="font-bold text-text">Rp{{ number_format($demo['payment']['change'], 0, ',', '.') }}</span></div>
        <x-ui.button href="/demo/receipt{{ $query }}" class="mt-4 w-full">Selesaikan pembayaran tunai</x-ui.button>
    </x-ui.card>
    <x-ui.card>
        <div class="flex items-center justify-between"><h3 class="font-semibold text-text">Nontunai · QRIS</h3><x-ui.badge variant="warning">Menunggu</x-ui.badge></div>
        <p class="mt-1 text-sm text-muted">Pindai QR di bawah untuk menyelesaikan pembayaran.</p>
        <div class="mx-auto mt-4 grid aspect-square w-48 grid-cols-9 gap-0.5 border border-border bg-surface p-2" role="img" aria-label="Placeholder QR pembayaran contoh">
            @foreach([1,1,1,1,1,0,1,1,1,1,0,1,0,1,1,0,1,0,1,1,1,1,0,1,0,1,1,1,1,1,1,1,0,0,1,1,1,0,0,1,0,1,0,1,0,1,1,1,1,1,0,1,1,1,0,0,1,0,1,1,1,0,1,1,0,1,0,1,0,0,1,1,1,1,1,1,0,1,0,1,1] as $pixel)
                <span class="{{ $pixel ? 'bg-text' : 'bg-surface' }}"></span>
            @endforeach
        </div>
        <div class="mt-4 flex flex-wrap gap-2"><x-ui.badge variant="warning">Menunggu pembayaran</x-ui.badge><x-ui.badge variant="success">Lunas</x-ui.badge><x-ui.badge variant="danger">Kedaluwarsa</x-ui.badge></div>
        <p class="mt-2 text-xs text-muted">Status contoh untuk desain. Tidak terhubung ke payment gateway.</p>
    </x-ui.card>
</div>
