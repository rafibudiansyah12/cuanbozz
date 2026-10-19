<?php

namespace App\Http\Controllers;

use App\Actions\Stores\AddCashier;
use App\Enums\StoreRole;
use App\Http\Requests\AddCashierRequest;
use App\Models\StoreMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreMemberController extends Controller
{
    public function store(AddCashierRequest $request, AddCashier $addCashier): RedirectResponse
    {
        $owner = $request->user();
        $store = $owner->ownedStores()->firstOrFail();

        $addCashier->handle($owner, $store, $request->validated());

        $role = $request->validated('role') === 'admin' ? 'admin toko' : 'kasir';

        return redirect()->route('owner.members')->with('status', "Akun {$role} berhasil ditambahkan. Bagikan email dan kata sandinya secara aman.");
    }

    public function update(Request $request, StoreMember $member): RedirectResponse
    {
        $store = $request->user()->ownedStores()->firstOrFail();
        abort_unless($member->store_id === $store->id && in_array($member->role, [StoreRole::Admin, StoreRole::Cashier], true), 404);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $member->forceFill(['is_active' => $validated['is_active']])->save();

        return redirect()->route('owner.members')->with('status', $member->is_active ? 'Akses anggota diaktifkan.' : 'Akses anggota dinonaktifkan.');
    }
}
