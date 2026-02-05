<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): bool;

    public function findWithRelations(int $id): ?Product;
}
