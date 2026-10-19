<?php

namespace App\Models;

use App\Support\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOptionGroup extends Model
{
    use BelongsToStore;

    protected $fillable = [
        'store_id', 'product_id', 'name', 'selection_type', 'is_required', 'min_select', 'max_select', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_required' => 'boolean', 'min_select' => 'integer', 'max_select' => 'integer', 'sort_order' => 'integer'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class, 'option_group_id')->orderBy('sort_order')->orderBy('id');
    }
}
