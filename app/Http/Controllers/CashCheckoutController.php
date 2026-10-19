<?php

namespace App\Http\Controllers;

use App\Actions\Sales\CheckoutCashSale;
use App\Http\Requests\CheckoutCashRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class CashCheckoutController extends Controller
{
    public function store(CheckoutCashRequest $request, CheckoutCashSale $checkoutCashSale): RedirectResponse
    {
        $store = active_store();
        abort_unless($store !== null, 404);

        $order = $checkoutCashSale->handle(
            $store,
            $request->user(),
            $request->validated('items'),
            (int) $request->validated('cash_received'),
            $request->validated('customer_name'),
            $request->validated('table_number'),
            $request->validated('service_type'),
        );

        return redirect()->to(URL::temporarySignedRoute('receipts.show', now()->addDays(30), [
            'store' => $store->id,
            'order' => $order->id,
            'token' => $order->receipt_token,
        ]));
    }
}
