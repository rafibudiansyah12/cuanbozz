<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class DeveloperAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'developer_id',
        'action',
        'target_user_id',
        'note',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Developer audit logs are append-only.'));
        static::deleting(fn () => throw new LogicException('Developer audit logs are append-only.'));
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
