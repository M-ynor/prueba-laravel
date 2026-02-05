<?php

namespace App\Services;

use App\Helpers\LoggerHelper;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Repositories\Contracts\ProductPriceRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CurrencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductPriceService
{
    public function __construct(
        private ProductPriceRepositoryInterface $productPriceRepository,
        private ProductRepositoryInterface $productRepository,
        private CurrencyRepositoryInterface $currencyRepository
    ) {}

    public function getProductPrices(int $productId): Collection
    {
        $product = $this->productRepository->findById($productId);

        if (!$product) {
            throw new \Exception("Product not found with ID: {$productId}");
        }

        return $this->productPriceRepository->getPricesForProduct($product);
    }

    public function createOrUpdatePrice(int $productId, int $currencyId, float $price): ProductPrice
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepository->findById($productId);
            if (!$product) {
                throw new \Exception("Product not found with ID: {$productId}");
            }

            $currency = $this->currencyRepository->findById($currencyId);
            if (!$currency) {
                throw new \Exception("Currency not found with ID: {$currencyId}");
            }

            $productPrice = $this->productPriceRepository->createOrUpdate(
                $productId,
                $currencyId,
                $price
            );

            LoggerHelper::info('Product price created/updated', [
                'product_id' => $productId,
                'currency_id' => $currencyId,
                'price' => $price,
                'action' => 'create_or_update',
            ]);

            DB::commit();

            return $productPrice->load('currency');
        } catch (\Exception $e) {
            DB::rollBack();
            LoggerHelper::error('Error creating/updating product price', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'currency_id' => $currencyId,
                'price' => $price,
                'action' => 'create_or_update',
            ]);
            throw $e;
        }
    }

    public function calculatePricesInAllCurrencies(Product $product): array
    {
        $currencies = $this->currencyRepository->getAll();
        $baseCurrencyRate = $product->currency->exchange_rate;
        $prices = [];

        foreach ($currencies as $currency) {
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
