<?php

namespace App\Http\Controllers;

use App\Actions\Stores\CreateStore;
use App\Http\Requests\StoreOnboardingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StoreOnboardingController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = request()->user();

        if ($user->isDeveloper()) {
            return redirect()->route('developer.home');
        }

        if ($user->ownedStores()->exists()) {
            return $user->approval_status->value === 'approved'
                ? redirect()->route('dashboard')
                : redirect()->route('account.status');
        }

        return view('onboarding.store');
    }

    public function store(StoreOnboardingRequest $request, CreateStore $createStore): RedirectResponse
    {
        $user = $request->user();

        abort_if($user->isDeveloper(), 404);

        if ($user->ownedStores()->exists()) {
            return redirect()->route('account.status');
        }

        $logoPath = $request->file('logo')?->store('store-logos', 'public');

        try {
            $createStore->handle($user, [
                'name' => $request->validated('name'),
                'address' => $request->validated('address'),
                'phone' => $request->validated('phone'),
                'logo_path' => $logoPath,
            ]);
        } catch (\Throwable $exception) {
            if ($logoPath !== null) {
                Storage::disk('public')->delete($logoPath);
            }

            throw $exception;
        }

        return redirect()->route('account.status')->with('status', 'Data toko berhasil dikirim untuk ditinjau.');
    }
}
