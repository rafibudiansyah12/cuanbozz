<?php

namespace App\Models;

use App\Support\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToStore;

    protected $fillable = [
        'store_id', 'category_id', 'name', 'description', 'image_path', 'sku', 'price', 'unit',
        'track_stock', 'stock', 'low_stock_threshold', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'track_stock' => 'boolean',
            'stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function optionGroups(): HasMany
    {
        return $this->hasMany(ProductOptionGroup::class)->orderBy('sort_order')->orderBy('id');
    }
}
