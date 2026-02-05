<?php

namespace App\Services;

use App\Helpers\LoggerHelper;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function getProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->getAllPaginated($filters, $perPage);
    }

    public function getProduct(int $id): ?Product
    {
        return $this->productRepository->findWithRelations($id);
    }

    public function createProduct(array $data): Product
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepository->create($data);

            LoggerHelper::info('Product created', [
                'product_id' => $product->id,
                'name' => $product->name,
                'action' => 'create',
            ]);

            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            LoggerHelper::error('Error creating product', [
                'error' => $e->getMessage(),
                'data' => $data,
                'action' => 'create',
            ]);
            throw $e;
        }
    }

    public function updateProduct(int $id, array $data): Product
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepository->findById($id);

            if (!$product) {
                throw new \Exception("Product not found with ID: {$id}");
            }

            $product = $this->productRepository->update($product, $data);

            LoggerHelper::info('Product updated', [
                'product_id' => $product->id,
                'name' => $product->name,
                'action' => 'update',
            ]);

            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            LoggerHelper::error('Error updating product', [
                'error' => $e->getMessage(),
                'product_id' => $id,
                'data' => $data,
                'action' => 'update',
            ]);
            throw $e;
        }
    }

    public function deleteProduct(int $id): bool
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepository->findById($id);

            if (!$product) {
                throw new \Exception("Product not found with ID: {$id}");
            }

            $result = $this->productRepository->delete($product);

            LoggerHelper::info('Product deleted', [
                'product_id' => $id,
                'action' => 'delete',
            ]);

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            LoggerHelper::error('Error deleting product', [
                'error' => $e->getMessage(),
                'product_id' => $id,
                'action' => 'delete',
            ]);
            throw $e;
        }
    }

    public function calculateTotalCost(Product $product): float
    {
        return $product->total_cost;
    }

    public function convertPrice(Product $product, float $targetExchangeRate): float
    {
        $baseCurrencyRate = $product->currency->exchange_rate;
        $priceInBaseCurrency = $product->price / $baseCurrencyRate;
        $convertedPrice = $priceInBaseCurrency * $targetExchangeRate;

        return round($convertedPrice, 2);
    }
}
