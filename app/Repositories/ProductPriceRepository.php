<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Repositories\Contracts\ProductPriceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Product Price Repository
 * 
 * Handles all data access operations for product prices.
 */
class ProductPriceRepository implements ProductPriceRepositoryInterface
{
    /**
     * Get all prices for a product.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPricesForProduct(Product $product): Collection
    {
        return $product->productPrices()
            ->with('currency')
            ->orderBy('currency_id')
            ->get();
    }

    /**
     * Create or update a product price.
     *
     * @param int $productId
     * @param int $currencyId
     * @param float $price
     * @return \App\Models\ProductPrice
     */
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

    /**
     * Find a product price by product and currency.
     *
     * @param int $productId
     * @param int $currencyId
     * @return \App\Models\ProductPrice|null
     */
    public function findByProductAndCurrency(int $productId, int $currencyId): ?ProductPrice
    {
        return ProductPrice::where('product_id', $productId)
            ->where('currency_id', $currencyId)
            ->first();
    }

    /**
     * Delete all prices for a product.
     *
     * @param \App\Models\Product $product
     * @return int
     */
    public function deleteForProduct(Product $product): int
    {
        return $product->productPrices()->delete();
    }
}
