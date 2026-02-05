<?php

namespace App\Services;

use App\Repositories\Contracts\CurrencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Currency Service
 * 
 * Handles business logic for currency operations.
 */
class CurrencyService
{
    /**
     * Create a new CurrencyService instance.
     *
     * @param \App\Repositories\Contracts\CurrencyRepositoryInterface $currencyRepository
     */
    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository
    ) {}

    /**
     * Get all available currencies.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCurrencies(): Collection
    {
        return $this->currencyRepository->getAll();
    }

    /**
     * Get a single currency by ID.
     *
     * @param int $id
     * @return \App\Models\Currency|null
     */
    public function getCurrency(int $id)
    {
        return $this->currencyRepository->findById($id);
    }

    /**
     * Convert amount from one currency to another.
     *
     * @param float $amount
     * @param float $fromExchangeRate
     * @param float $toExchangeRate
     * @return float
     */
    public function convertAmount(float $amount, float $fromExchangeRate, float $toExchangeRate): float
    {
        // Convert to base currency first
        $amountInBaseCurrency = $amount / $fromExchangeRate;
        
        // Convert to target currency
        $convertedAmount = $amountInBaseCurrency * $toExchangeRate;
        
        return round($convertedAmount, 2);
    }
}
