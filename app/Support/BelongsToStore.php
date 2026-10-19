<?php

namespace App\Support;

use App\Models\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Model;
use LogicException;

trait BelongsToStore
{
    protected static function bootBelongsToStore(): void
    {
        static::addGlobalScope(new StoreScope);

        static::creating(function (Model $model): void {
            $store = active_store();

            if ($store !== null) {
                $model->setAttribute('store_id', $store->getKey());

                return;
            }

            if ($model->getAttribute('store_id') === null) {
                throw new LogicException('A store context is required to create this record.');
            }
        });
    }
}
