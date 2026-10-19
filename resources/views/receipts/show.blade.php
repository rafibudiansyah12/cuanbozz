<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $order->order_number }} · {{ $store->name }}</title>
    @vite(['resources/css/app.css'])
    <x-theme-variables :primary="$store->theme['primary'] ?? null" :accent="$store->theme['accent'] ?? null" :radius="$store->theme['radius'] ?? null" />
    <style>
        @media print {
            body { min-width: 0; background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .receipt-paper { width: 72mm !important; max-width: none !important; margin: 0 auto !important; border: 0 !important; padding: 0 !important; box-shadow: none !important; }
            @page { size: 80mm auto; margin: 4mm; }
        }
    </style>
</head>
<body class="min-h-screen bg-primary-50 px-4 py-8 text-text sm:py-12">
    <main class="mx-auto max-w-md">
        <div class="no-print mb-5 flex flex-wrap gap-3">
            <button type="button" onclick="window.print()" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-primary-700 px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:bg-primary-800">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 8V3h10v5M7 17H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2M7 14h10v7H7z" stroke-linejoin="round"/><path d="M17 11h.01" stroke-linecap="round"/></svg>
                Cetak struk
            </button>
            <a href="{{ $shareUrl }}" target="_blank" rel="noopener" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-border bg-surface px-4 py-2 text-sm font-bold text-text shadow-sm transition hover:bg-primary-50">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.58 15.13L2 22l5.03-1.32A10 10 0 1 0 12 2Zm0 18.18a8.13 8.13 0 0 1-4.15-1.14l-.3-.18-2.99.79.8-2.91-.2-.3A8.17 8.17 0 1 1 12 20.18Zm4.48-6.12c-.25-.13-1.48-.73-1.71-.81-.23-.08-.4-.13-.57.13-.17.25-.65.81-.8.98-.15.17-.3.19-.55.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.52.11-.11.25-.3.38-.44.13-.15.17-.25.25-.42.08-.17.04-.32-.02-.45-.06-.13-.57-1.37-.78-1.88-.2-.49-.41-.42-.57-.43l-.49-.01c-.17 0-.45.06-.68.32-.23.25-.89.87-.89 2.12s.91 2.46 1.04 2.63c.13.17 1.79 2.73 4.33 3.83.61.26 1.08.42 1.45.53.61.19 1.17.16 1.61.1.49-.07 1.48-.61 1.69-1.2.21-.59.21-1.1.15-1.2-.06-.1-.23-.17-.49-.3Z"/></svg>
                WhatsApp
            </a>
            <a href="{{ route('owner.pos') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-border bg-surface px-4 py-2 text-sm font-semibold text-text transition hover:bg-primary-50">Transaksi berikutnya</a>
        </div>

        <article class="receipt-paper mx-auto w-full rounded-3xl border border-border/70 bg-surface p-6 shadow-xl shadow-primary-900/5 sm:p-8 print:rounded-none">
            <header class="text-center">
                @if($store->logo_path)<img src="{{ asset($store->logo_path) }}" alt="Logo {{ $store->name }}" class="mx-auto mb-3 h-14 max-w-32 object-contain">@endif
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-primary-700">Struk pesanan</p>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-text">{{ $store->name }}</h1>
                @if($store->phone)
                    <div class="mx-auto mt-4 inline-flex max-w-full items-center justify-center rounded-lg border-2 border-primary-600 px-3 py-1.5 text-sm font-bold text-primary-800">No. Telp {{ $store->phone }}</div>
                @endif
                @if($store->address)<p class="mt-3 text-xs leading-5 text-muted">{{ $store->address }}</p>@endif
                <div class="mt-4 flex justify-center"><span class="inline-flex items-center gap-2 rounded-full bg-success/10 px-3 py-1.5 text-xs font-bold text-success"><span class="h-2 w-2 rounded-full bg-success"></span>Lunas · {{ $paymentLabel }}</span></div>
            </header>

            <div class="my-5 border-y border-dashed border-border py-4 font-mono text-xs leading-6 sm:text-sm">
                <dl class="grid grid-cols-[5.5rem_minmax(0,1fr)] gap-x-2">
                    <dt class="text-muted">Pelanggan</dt><dd class="break-words font-semibold">{{ $order->customer_name ?: 'Pelanggan umum' }}</dd>
                    <dt class="text-muted">Layanan</dt><dd class="font-semibold">{{ $order->service_type === 'dine_in' ? 'Dine In' : 'Take Away' }}</dd>
                    @if($order->service_type === 'dine_in')<dt class="text-muted">Meja</dt><dd class="font-semibold">{{ $order->table_number }}</dd>@endif
                    <dt class="text-muted">Waktu</dt><dd>{{ $order->paid_at?->timezone(config('app.timezone'))->format('Y-m-d H:i:s') }}</dd>
                    <dt class="text-muted">No. struk</dt><dd class="break-all">{{ $order->order_number }}</dd>
                </dl>
            </div>

            <ol class="space-y-4 border-b border-dashed border-border pb-5 font-mono text-xs sm:text-sm">
                @foreach($order->items as $index => $item)
                    <li>
                        <div class="flex items-start justify-between gap-3">
                            <p class="min-w-0 break-words"><span class="mr-1 text-muted">{{ $index + 1 }}.</span>{{ $item->product_name }}</p>
                            <span class="shrink-0 font-semibold">Rp{{ number_format($item->line_total, 0, ',', '.') }}</span>
                        </div>
                        <p class="mt-1 pl-4 text-muted">{{ $item->quantity }} × Rp{{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        @foreach($item->options as $option)
                            <p class="pl-4 text-xs text-muted">{{ $option->group_name }}: {{ $option->option_name }}@if($option->price_delta !== 0) ({{ $option->price_delta > 0 ? '+' : '−' }}Rp{{ number_format(abs($option->price_delta), 0, ',', '.') }})@endif</p>
                        @endforeach
                        @if($item->note)<p class="mt-1 pl-4 text-xs text-muted">Catatan: {{ $item->note }}</p>@endif
                    </li>
                @endforeach
            </ol>

            <p class="flex justify-between border-b border-dashed border-border py-3 font-mono text-xs font-bold sm:text-sm"><span>Total QTY</span><span>{{ $order->items->sum('quantity') }}</span></p>

            <dl class="space-y-2 py-4 font-mono text-xs sm:text-sm">
                <div class="flex justify-between gap-3"><dt class="text-muted">Sub Total</dt><dd>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between gap-3 border-t border-border pt-3 text-base font-extrabold"><dt>Total</dt><dd>Rp{{ number_format($order->total, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">Bayar ({{ $paymentLabel }})</dt><dd>Rp{{ number_format($isCash ? $order->cash_received : $order->total, 0, ',', '.') }}</dd></div>
                @if($isCash)<div class="flex justify-between gap-3"><dt class="text-muted">Kembali</dt><dd>Rp{{ number_format($order->change_amount, 0, ',', '.') }}</dd></div>@endif
            </dl>

            <footer class="border-t border-dashed border-border pt-5 text-center">
                <p class="text-base font-bold text-text">Terima kasih sudah berbelanja</p>
                <p class="mt-1 text-xs text-muted">{{ $store->name }}</p>
                <p class="mt-4 font-mono text-[10px] text-muted">Simpan struk ini untuk referensi transaksi.</p>
            </footer>
        </article>
    </main>
</body>
</html>
