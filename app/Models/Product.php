<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Product Model
 * 
 * Represents products in the system with pricing and cost information.
 * 
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property int $currency_id
 * @property float $tax_cost
 * @property float $manufacturing_cost
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_id',
        'tax_cost',
        'manufacturing_cost',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'tax_cost' => 'decimal:2',
        'manufacturing_cost' => 'decimal:2',
    ];

    /**
     * Get the currency that owns the product (base currency).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get all prices for this product in different currencies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Scope a query to filter by product name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Scope a query to filter by currency.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $currencyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCurrency(Builder $query, int $currencyId): Builder
    {
        return $query->where('currency_id', $currencyId);
    }

    /**
     * Scope a query to filter by price range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float|null $minPrice
     * @param float|null $maxPrice
     * @return \Illuminate\Database\Eloquent\Builder
     */
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

    /**
     * Get total cost including tax and manufacturing.
     *
     * @return float
     */
    public function getTotalCostAttribute(): float
    {
        return (float) ($this->price + $this->tax_cost + $this->manufacturing_cost);
    }
}
