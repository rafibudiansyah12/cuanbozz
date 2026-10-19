<?php

namespace App\Policies;

use App\Enums\StoreRole;
use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function updateTheme(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id;
    }

    public function view(User $user, Store $store): bool
    {
        if ($store->owner_id === $user->id) {
            return true;
        }

        return $user->storeMemberships()
            ->where('store_id', $store->id)
            ->whereIn('role', [StoreRole::Admin->value, StoreRole::Cashier->value])
            ->where('is_active', true)
            ->exists();
    }

    public function manageCatalog(User $user, Store $store): bool
    {
        if ($store->owner_id === $user->id) {
            return true;
        }

        return $user->storeMemberships()
            ->where('store_id', $store->id)
            ->where('role', StoreRole::Admin->value)
            ->where('is_active', true)
            ->exists();
    }

    public function adjustStock(User $user, Store $store): bool
    {
        return $this->view($user, $store);
    }
}
