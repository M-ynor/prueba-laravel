<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Currency Model
 * 
 * Represents currencies in the system for multi-currency support.
 * 
 * @property int $id
 * @property string $name
 * @property string $symbol
 * @property float $exchange_rate
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Currency extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exchange_rate' => 'decimal:6',
    ];

    /**
     * Get all products with this currency as base currency.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all product prices in this currency.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Get formatted symbol with name.
     *
     * @return string
     */
    public function getFormattedNameAttribute(): string
    {
        return "{$this->name} ({$this->symbol})";
    }
}
