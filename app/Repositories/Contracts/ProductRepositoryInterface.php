<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Product Repository Interface
 * 
 * Defines the contract for product data access operations.
 */
interface ProductRepositoryInterface
{
    /**
     * Get all products with pagination and filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a product by ID.
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findById(int $id): ?Product;

    /**
     * Create a new product.
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function create(array $data): Product;

    /**
     * Update a product.
     *
     * @param \App\Models\Product $product
     * @param array $data
     * @return \App\Models\Product
     */
    public function update(Product $product, array $data): Product;

    /**
     * Delete a product (soft delete).
     *
     * @param \App\Models\Product $product
     * @return bool
     */
    public function delete(Product $product): bool;

    /**
     * Get product with all relationships.
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findWithRelations(int $id): ?Product;
}
