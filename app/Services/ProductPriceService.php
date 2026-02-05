<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Repositories\Contracts\ProductPriceRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CurrencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Product Price Service
 * 
 * Handles business logic for product price operations.
 */
class ProductPriceService
{
    /**
     * Create a new ProductPriceService instance.
     *
     * @param \App\Repositories\Contracts\ProductPriceRepositoryInterface $productPriceRepository
     * @param \App\Repositories\Contracts\ProductRepositoryInterface $productRepository
     * @param \App\Repositories\Contracts\CurrencyRepositoryInterface $currencyRepository
     */
    public function __construct(
        private ProductPriceRepositoryInterface $productPriceRepository,
        private ProductRepositoryInterface $productRepository,
        private CurrencyRepositoryInterface $currencyRepository
    ) {}

    /**
     * Get all prices for a product.
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getProductPrices(int $productId): Collection
    {
        $product = $this->productRepository->findById($productId);

        if (!$product) {
            throw new \Exception("Product not found with ID: {$productId}");
        }

        return $this->productPriceRepository->getPricesForProduct($product);
    }

    /**
     * Create or update a product price.
     *
     * @param int $productId
     * @param int $currencyId
     * @param float $price
     * @return \App\Models\ProductPrice
     * @throws \Exception
     */
    public function createOrUpdatePrice(int $productId, int $currencyId, float $price): ProductPrice
    {
        try {
            DB::beginTransaction();

            // Validate product exists
            $product = $this->productRepository->findById($productId);
            if (!$product) {
                throw new \Exception("Product not found with ID: {$productId}");
            }

            // Validate currency exists
            $currency = $this->currencyRepository->findById($currencyId);
            if (!$currency) {
                throw new \Exception("Currency not found with ID: {$currencyId}");
            }

            $productPrice = $this->productPriceRepository->createOrUpdate(
                $productId,
                $currencyId,
                $price
            );

            Log::info('Product price created/updated', [
                'product_id' => $productId,
                'currency_id' => $currencyId,
                'price' => $price
            ]);

            DB::commit();

            return $productPrice->load('currency');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating/updating product price', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'currency_id' => $currencyId,
                'price' => $price
            ]);
            throw $e;
        }
    }

    /**
     * Calculate product price in all available currencies.
     *
     * @param \App\Models\Product $product
     * @return array
     */
    public function calculatePricesInAllCurrencies(Product $product): array
    {
        $currencies = $this->currencyRepository->getAll();
        $baseCurrencyRate = $product->currency->exchange_rate;
        $prices = [];

        foreach ($currencies as $currency) {
            // Convert price to target currency
            $priceInBaseCurrency = $product->price / $baseCurrencyRate;
            $convertedPrice = $priceInBaseCurrency * $currency->exchange_rate;

            $prices[] = [
                'currency_id' => $currency->id,
                'currency_name' => $currency->name,
                'currency_symbol' => $currency->symbol,
                'price' => round($convertedPrice, 2),
            ];
        }

        return $prices;
    }
}
