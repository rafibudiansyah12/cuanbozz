<?php

namespace App\Support;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CurrentStore
{
    private bool $resolved = false;

    private ?int $resolvedUserId = null;

    private ?Store $store = null;

    public function set(Store $store): void
    {
        $this->resolved = true;
        $this->resolvedUserId = Auth::id();
        $this->store = $store;
    }

    public function get(): ?Store
    {
        $userId = Auth::id();

        if ($this->resolved && $this->resolvedUserId === $userId) {
            return $this->store;
        }

        $this->resolved = true;
        $this->resolvedUserId = $userId;
        $this->store = null;

        if ($userId === null) {
            return null;
        }

        $storeIds = DB::table('store_members')
            ->join('stores', 'stores.id', '=', 'store_members.store_id')
            ->where('store_members.user_id', $userId)
            ->where('store_members.is_active', true)
            ->where('stores.is_active', true)
            ->limit(2)
            ->pluck('stores.id');

        if ($storeIds->count() !== 1) {
            return null;
        }

        $this->store = Store::query()->find($storeIds->first());

        return $this->store;
    }
}
