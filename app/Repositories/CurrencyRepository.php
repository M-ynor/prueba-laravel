<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Currency Repository
 * 
 * Handles all data access operations for currencies.
 */
class CurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * Get all currencies.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): Collection
    {
        return Currency::orderBy('name')->get();
    }

    /**
     * Find a currency by ID.
     *
     * @param int $id
     * @return \App\Models\Currency|null
     */
    public function findById(int $id): ?Currency
    {
        return Currency::find($id);
    }

    /**
     * Create a new currency.
     *
     * @param array $data
     * @return \App\Models\Currency
     */
    public function create(array $data): Currency
    {
        return Currency::create($data);
    }

    /**
     * Update a currency.
     *
     * @param \App\Models\Currency $currency
     * @param array $data
     * @return \App\Models\Currency
     */
    public function update(Currency $currency, array $data): Currency
    {
        $currency->update($data);
        return $currency->fresh();
    }
}
