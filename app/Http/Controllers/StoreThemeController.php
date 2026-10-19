<?php

namespace App\Http\Controllers;

use App\Actions\Stores\UpdateStoreTheme;
use App\Http\Requests\UpdateStoreThemeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class StoreThemeController extends Controller
{
    public function update(UpdateStoreThemeRequest $request, UpdateStoreTheme $updateStoreTheme): RedirectResponse
    {
        $store = $request->user()->ownedStores()->firstOrFail();
        Gate::authorize('updateTheme', $store);
        $updateStoreTheme->handle($store, $request->validated());

        return redirect()->route('owner.appearance')->with('status', 'Tampilan toko berhasil disimpan.');
    }
}
