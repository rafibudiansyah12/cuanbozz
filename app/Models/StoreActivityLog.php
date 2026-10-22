<?php

namespace App\Models;

use App\Support\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class StoreActivityLog extends Model
{
    use BelongsToStore;

    public $timestamps = false;

    protected $fillable = [
        'store_id', 'user_id', 'action', 'subject_type', 'subject_id', 'subject_name', 'description', 'details', 'created_at',
    ];

    protected function casts(): array
    {
        return ['details' => 'array', 'created_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Store activity logs are append-only.'));
        static::deleting(fn () => throw new LogicException('Store activity logs are append-only.'));
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
