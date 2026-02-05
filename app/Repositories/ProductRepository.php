<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::with(['currency', 'productPrices.currency']);

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

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function findWithRelations(int $id): ?Product
    {
        return Product::with(['currency', 'productPrices.currency'])->find($id);
    }
}
