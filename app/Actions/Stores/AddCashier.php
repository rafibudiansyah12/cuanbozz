<?php

namespace App\Actions\Stores;

use App\Enums\StoreRole;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddCashier
{
    public function handle(User $owner, Store $store, array $attributes): User
    {
        abort_unless($store->owner_id === $owner->id, 403);

        return DB::transaction(function () use ($store, $attributes): User {
            $cashier = User::query()->forceCreate([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
                'email_verified_at' => now(),
                'is_developer' => false,
            ]);

            $store->members()->create([
                'user_id' => $cashier->id,
                'role' => StoreRole::from($attributes['role']),
                'is_active' => true,
            ]);

            return $cashier;
        });
    }
}
