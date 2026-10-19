<?php

namespace App\Actions\Stores;

use App\Enums\StoreRole;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateStore
{
    public function handle(User $owner, array $attributes): Store
    {
        return DB::transaction(function () use ($owner, $attributes): Store {
            if ($owner->ownedStores()->exists()) {
                throw new \LogicException('Akun ini sudah memiliki toko.');
            }

            $baseSlug = Str::slug($attributes['name']) ?: 'toko';
            $slug = $baseSlug.'-'.$owner->id;
            $suffix = 1;

            while (Store::query()->where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$owner->id.'-'.$suffix++;
            }

            $store = $owner->ownedStores()->create([
                ...$attributes,
                'slug' => $slug,
                'is_active' => true,
            ]);

            $store->members()->create([
                'user_id' => $owner->id,
                'role' => StoreRole::Owner,
                'is_active' => true,
            ]);

            return $store;
        });
    }
}
