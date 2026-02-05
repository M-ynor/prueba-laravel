<?php

namespace App\Repositories\Contracts;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

/**
 * Currency Repository Interface
 * 
 * Defines the contract for currency data access operations.
 */
interface CurrencyRepositoryInterface
{
    /**
     * Get all currencies.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): Collection;

    /**
     * Find a currency by ID.
     *
     * @param int $id
     * @return \App\Models\Currency|null
     */
    public function findById(int $id): ?Currency;

    /**
     * Create a new currency.
     *
     * @param array $data
     * @return \App\Models\Currency
     */
    public function create(array $data): Currency;

    /**
     * Update a currency.
     *
     * @param \App\Models\Currency $currency
     * @param array $data
     * @return \App\Models\Currency
     */
    public function update(Currency $currency, array $data): Currency;
}
