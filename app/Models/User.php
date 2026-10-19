<?php

namespace App\Models;

use App\Enums\AccountApprovalStatus;
use App\Enums\StoreRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = [
        'approval_status' => 'pending',
        'is_developer' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approval_status' => AccountApprovalStatus::class,
            'approved_at' => 'datetime',
            'is_developer' => 'boolean',
        ];
    }

    public function isDeveloper(): bool
    {
        return $this->is_developer;
    }

    public function accountApprovalUser(): self
    {
        if ($this->isDeveloper()) {
            return $this;
        }

        $membership = $this->storeMemberships()
            ->whereIn('role', [StoreRole::Admin->value, StoreRole::Cashier->value])
            ->where('is_active', true)
            ->with('store.owner')
            ->first();

        return $membership?->store?->owner ?? $this;
    }

    public function ownedStores(): HasMany
    {
        return $this->hasMany(Store::class, 'owner_id');
    }

    public function storeMemberships(): HasMany
    {
        return $this->hasMany(StoreMember::class);
    }
}
