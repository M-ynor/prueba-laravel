<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Product Repository
 * 
 * Handles all data access operations for products.
 */
class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Get all products with pagination and filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::with(['currency', 'productPrices.currency']);

        // Apply filters
        if (!empty($filters['name'])) {
            $query->byName($filters['name']);
        }

        if (!empty($filters['currency_id'])) {
            $query->byCurrency($filters['currency_id']);
        }

        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $query->byPriceRange(
                $filters['min_price'] ?? null,
                $filters['max_price'] ?? null
            );
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Find a product by ID.
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update a product.
     *
     * @param \App\Models\Product $product
     * @param array $data
     * @return \App\Models\Product
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    /**
     * Delete a product (soft delete).
     *
     * @param \App\Models\Product $product
     * @return bool
     */
    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    /**
     * Get product with all relationships.
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findWithRelations(int $id): ?Product
    {
        return Product::with(['currency', 'productPrices.currency'])->find($id);
    }
}
