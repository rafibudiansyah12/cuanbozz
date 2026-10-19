<?php

namespace App\Http\Controllers;

use App\Actions\Catalog\CreateProduct;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function create(): View
    {
        $store = active_store();
        abort_unless($store !== null, 404);
        Gate::authorize('manageCatalog', $store);

        return view('owner.product-form', [
            'store' => $store,
            'previewMode' => false,
        ]);
    }

    public function store(StoreProductRequest $request, CreateProduct $createProduct): RedirectResponse
    {
        $store = active_store();
        abort_unless($store !== null, 404);

        $product = $createProduct->handle($store, $request->user(), $request->validated());

        return redirect()->route('owner.products')->with('status', "Produk {$product->name} berhasil ditambahkan.");
    }
}
