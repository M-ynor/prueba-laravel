<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['name', 'currency_id', 'min_price', 'max_price', 'sort_by', 'sort_order']);
        $perPage = $request->input('per_page', 15);

        $products = $this->productService->getProducts($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'meta' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
            'message' => 'Products retrieved successfully'
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->validated());

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product->load(['currency', 'productPrices.currency'])),
                'message' => 'Product created successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
            'message' => 'Product retrieved successfully'
        ]);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($id, $request->validated());

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product->load(['currency', 'productPrices.currency'])),
                'message' => 'Product updated successfully'
            ]);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error updating product',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error deleting product',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
