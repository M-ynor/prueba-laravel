<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Repositories\Contracts\ProductPriceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductPriceRepository implements ProductPriceRepositoryInterface
{
    public function getPricesForProduct(Product $product): Collection
    {
        return $product->productPrices()
            ->with('currency')
            ->orderBy('currency_id')
            ->get();
    }

    public function createOrUpdate(int $productId, int $currencyId, float $price): ProductPrice
    {
        return ProductPrice::updateOrCreate(
            [
                'product_id' => $productId,
                'currency_id' => $currencyId,
            ],
            [
                'price' => $price,
            ]
        );
    }

    public function findByProductAndCurrency(int $productId, int $currencyId): ?ProductPrice
    {
        return ProductPrice::where('product_id', $productId)
            ->where('currency_id', $currencyId)
            ->first();
    }

    public function deleteForProduct(Product $product): int
    {
        return $product->productPrices()->delete();
    }
}
