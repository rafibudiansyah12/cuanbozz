<?php

namespace App\Models;

use App\Support\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemOption extends Model
{
    use BelongsToStore;

    protected $fillable = ['store_id', 'order_item_id', 'group_name', 'option_name', 'price_delta'];

    protected function casts(): array
    {
        return ['price_delta' => 'integer'];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
