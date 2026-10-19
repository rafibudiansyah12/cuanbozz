<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Support\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use BelongsToStore;

    protected $fillable = [
        'store_id', 'cashier_id', 'order_number', 'subtotal', 'total', 'cash_received',
        'change_amount', 'payment_method', 'status', 'paid_at', 'customer_name', 'table_number', 'service_type', 'receipt_token',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'total' => 'integer',
            'cash_received' => 'integer',
            'change_amount' => 'integer',
            'payment_method' => PaymentMethod::class,
            'status' => OrderStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
