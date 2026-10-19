<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Store;
use App\Support\CurrentStore;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function __invoke(Request $request, int $store, int $order, string $token, CurrentStore $currentStore): View
    {
        $storeModel = Store::query()->findOrFail($store);
        abort_unless($storeModel->is_active, 404);

        $currentStore->set($storeModel);

        $orderModel = Order::query()
            ->with(['items.options', 'cashier'])
            ->whereKey($order)
            ->where('receipt_token', $token)
            ->where('status', 'paid')
            ->firstOrFail();

        return view('receipts.show', [
            'store' => $storeModel,
            'order' => $orderModel,
            'paymentLabel' => $orderModel->payment_method === PaymentMethod::Cash ? 'Tunai' : 'QRIS',
            'isCash' => $orderModel->payment_method === PaymentMethod::Cash,
            'shareUrl' => 'https://wa.me/?text='.rawurlencode($this->shareMessage($storeModel, $orderModel, $request->fullUrl())),
        ]);
    }

    private function shareMessage(Store $store, Order $order, string $url): string
    {
        $lines = ["Struk {$store->name}", "Transaksi: {$order->order_number}"];

        if ($order->customer_name) {
            $lines[] = "Pelanggan: {$order->customer_name}";
        }

        if ($order->table_number) {
            $lines[] = "Meja: {$order->table_number}";
        }

        $lines[] = 'Total: Rp'.number_format($order->total, 0, ',', '.');
        $lines[] = $url;

        return implode("\n", $lines);
    }
}
