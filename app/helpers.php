<?php

use App\Models\Store;
use App\Support\CurrentStore;

if (! function_exists('active_store')) {
    function active_store(): ?Store
    {
        return app(CurrentStore::class)->get();
    }
}
