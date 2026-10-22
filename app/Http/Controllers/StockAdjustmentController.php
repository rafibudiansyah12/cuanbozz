<?php

namespace App\Http\Controllers;

use App\Actions\Stock\AdjustProductStock;
use App\Http\Requests\AdjustProductStockRequest;
use Illuminate\Http\RedirectResponse;

class StockAdjustmentController extends Controller
{
    public function store(AdjustProductStockRequest $request, AdjustProductStock $adjustProductStock): RedirectResponse
    {
        $store = active_store();
        abort_unless($store !== null, 404);

        $product = $adjustProductStock->handle(
            $store,
            $request->user(),
            (int) $request->validated('product_id'),
            (int) $request->validated('quantity') * ($request->validated('direction') === 'out' ? -1 : 1),
            $request->validated('reason'),
        );

        return redirect()
            ->route('owner.stock-adjustment', ['product' => $product->id])
            ->with('status', "Stok {$product->name} berhasil diperbarui menjadi {$product->stock} {$product->unit}.");
    }
}
