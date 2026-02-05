<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Services\ProductPriceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Product Prices
 * 
 * @group Product Prices
 * 
 * API endpoints para gestionar precios de productos en diferentes divisas.
 */
class ProductPriceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param \App\Services\ProductPriceService $productPriceService
     */
    public function __construct(
        private ProductPriceService $productPriceService
    ) {}

    /**
     * Listar precios de producto
     * 
     * Obtiene todos los precios de un producto en diferentes divisas.
     *
     * @urlParam productId int required ID del producto. Example: 1
     * 
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "product_id": 1,
     *       "currency": {
     *         "id": 2,
     *         "name": "Euro",
     *         "symbol": "EUR",
     *         "exchange_rate": 0.92
     *       },
     *       "price": 919.99
     *     }
     *   ],
     *   "message": "Precios obtenidos exitosamente"
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "Producto no encontrado"
     * }
     *
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $productId): JsonResponse
    {
        try {
            $prices = $this->productPriceService->getProductPrices($productId);

            return response()->json([
                'success' => true,
                'data' => ProductPriceResource::collection($prices),
                'message' => 'Precios obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los precios',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Crear/actualizar precio
     * 
     * Crea o actualiza el precio de un producto en una divisa específica.
     *
     * @urlParam productId int required ID del producto. Example: 1
     * 
     * @bodyParam currency_id int required ID de la divisa. Example: 2
     * @bodyParam price number required Precio en la divisa especificada. Example: 850.50
     * 
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "product_id": 1,
     *     "currency": {
     *       "id": 2,
     *       "name": "Euro",
     *       "symbol": "EUR",
     *       "exchange_rate": 0.92
     *     },
     *     "price": 850.50
     *   },
     *   "message": "Precio creado/actualizado exitosamente"
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "Product not found with ID: 1"
     * }
     *
     * @param \App\Http\Requests\StoreProductPriceRequest $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
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
                'message' => 'Precio creado/actualizado exitosamente'
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
                'message' => 'Error al crear/actualizar el precio',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
