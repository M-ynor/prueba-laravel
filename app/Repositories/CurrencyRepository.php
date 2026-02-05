<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    public function getAll(): Collection
    {
        return Currency::orderBy('name')->get();
    }

    public function findById(int $id): ?Currency
    {
        return Currency::find($id);
    }

    public function create(array $data): Currency
    {
        return Currency::create($data);
    }

    public function update(Currency $currency, array $data): Currency
    {
        $currency->update($data);
        return $currency->fresh();
    }
}
