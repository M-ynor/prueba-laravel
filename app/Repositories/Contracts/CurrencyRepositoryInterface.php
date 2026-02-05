<?php

namespace App\Repositories\Contracts;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

interface CurrencyRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): ?Currency;

    public function create(array $data): Currency;

    public function update(Currency $currency, array $data): Currency;
}
