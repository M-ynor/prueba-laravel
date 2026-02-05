<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Collection;

/**
 * Product Price Repository Interface
 * 
 * Defines the contract for product price data access operations.
 */
interface ProductPriceRepositoryInterface
{
    /**
     * Get all prices for a product.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPricesForProduct(Product $product): Collection;

    /**
     * Create or update a product price.
     *
     * @param int $productId
     * @param int $currencyId
     * @param float $price
     * @return \App\Models\ProductPrice
     */
    public function createOrUpdate(int $productId, int $currencyId, float $price): ProductPrice;

    /**
     * Find a product price by product and currency.
     *
     * @param int $productId
     * @param int $currencyId
     * @return \App\Models\ProductPrice|null
     */
    public function findByProductAndCurrency(int $productId, int $currencyId): ?ProductPrice;

    /**
     * Delete all prices for a product.
     *
     * @param \App\Models\Product $product
     * @return int
     */
    public function deleteForProduct(Product $product): int;
}
