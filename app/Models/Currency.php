<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function getFormattedNameAttribute(): string
    {
        return "{$this->name} ({$this->symbol})";
    }
}
