<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_id',
        'tax_cost',
        'manufacturing_cost',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tax_cost' => 'decimal:2',
        'manufacturing_cost' => 'decimal:2',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeByCurrency(Builder $query, int $currencyId): Builder
    {
        return $query->where('currency_id', $currencyId);
    }

    public function scopeByPriceRange(Builder $query, ?float $minPrice, ?float $maxPrice): Builder
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    public function getTotalCostAttribute(): float
    {
        return (float) ($this->price + $this->tax_cost + $this->manufacturing_cost);
    }
}
