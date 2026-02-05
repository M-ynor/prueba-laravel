<?php

namespace App\Services;

use App\Repositories\Contracts\CurrencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CurrencyService
{
    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository
    ) {}

    public function getAllCurrencies(): Collection
    {
        return $this->currencyRepository->getAll();
    }

    public function getCurrency(int $id)
    {
        return $this->currencyRepository->findById($id);
    }

    public function convertAmount(float $amount, float $fromExchangeRate, float $toExchangeRate): float
    {
        $amountInBaseCurrency = $amount / $fromExchangeRate;
        $convertedAmount = $amountInBaseCurrency * $toExchangeRate;

        return round($convertedAmount, 2);
    }
}
