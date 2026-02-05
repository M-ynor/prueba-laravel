<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Services\ProductPriceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductPriceController extends Controller
{
    public function __construct(
        private ProductPriceService $productPriceService
    ) {}

    public function index(int $productId): JsonResponse
    {
        try {
            $prices = $this->productPriceService->getProductPrices($productId);

            return response()->json([
                'success' => true,
                'data' => ProductPriceResource::collection($prices),
                'message' => 'Prices retrieved successfully'
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
                'message' => 'Error retrieving prices',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreProductPriceRequest $request, int $productId): JsonResponse
    {
        try {
            $productPrice = $this->productPriceService->createOrUpdatePrice(
                $productId,
                $request->currency_id,
                $request->price
            );

            return response()->json([
                'success' => true,
                'data' => new ProductPriceResource($productPrice),
                'message' => 'Price created/updated successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error creating/updating price',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
