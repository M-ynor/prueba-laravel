<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Product Service
 * 
 * Handles business logic for product operations.
 */
class ProductService
{
    /**
     * Create a new ProductService instance.
     *
     * @param \App\Repositories\Contracts\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * Get paginated list of products with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->getAllPaginated($filters, $perPage);
    }

    /**
     * Get a single product by ID with relationships.
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function getProduct(int $id): ?Product
    {
        return $this->productRepository->findWithRelations($id);
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return \App\Models\Product
     * @throws \Exception
     */
    public function createProduct(array $data): Product
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepository->create($data);

            Log::info('Product created', [
                'product_id' => $product->id,
                'name' => $product->name
            ]);

            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing product.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Product
     * @throws \Exception
     */
    public function updateProduct(int $id, array $data): Product
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepository->findById($id);

            if (!$product) {
                throw new \Exception("Product not found with ID: {$id}");
            }

            $product = $this->productRepository->update($product, $data);

            Log::info('Product updated', [
                'product_id' => $product->id,
                'name' => $product->name
            ]);

            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product', [
                'error' => $e->getMessage(),
                'product_id' => $id,
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a product.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteProduct(int $id): bool
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepository->findById($id);

            if (!$product) {
                throw new \Exception("Product not found with ID: {$id}");
            }

            $result = $this->productRepository->delete($product);

            Log::info('Product deleted', [
                'product_id' => $id
            ]);

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product', [
                'error' => $e->getMessage(),
                'product_id' => $id
            ]);
            throw $e;
        }
    }

    /**
     * Calculate total cost of a product.
     *
     * @param \App\Models\Product $product
     * @return float
     */
    public function calculateTotalCost(Product $product): float
    {
        return $product->total_cost;
    }

    /**
     * Convert product price to different currency.
     *
     * @param \App\Models\Product $product
     * @param float $targetExchangeRate
     * @return float
     */
    public function convertPrice(Product $product, float $targetExchangeRate): float
    {
        $baseCurrencyRate = $product->currency->exchange_rate;
        
        // Convert to base currency first, then to target currency
        $priceInBaseCurrency = $product->price / $baseCurrencyRate;
        $convertedPrice = $priceInBaseCurrency * $targetExchangeRate;
        
        return round($convertedPrice, 2);
    }
}
