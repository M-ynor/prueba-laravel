<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Collection;

interface ProductPriceRepositoryInterface
{
    public function getPricesForProduct(Product $product): Collection;

    public function createOrUpdate(int $productId, int $currencyId, float $price): ProductPrice;

    public function findByProductAndCurrency(int $productId, int $currencyId): ?ProductPrice;

    public function deleteForProduct(Product $product): int;
}
