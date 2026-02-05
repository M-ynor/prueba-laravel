<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Products
 * 
 * @group Products
 * 
 * API endpoints para gestionar productos del sistema.
 */
class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param \App\Services\ProductService $productService
     */
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Listar productos
     * 
     * Obtiene una lista paginada de productos con filtros opcionales.
     *
     * @queryParam name string Filtrar por nombre de producto (búsqueda parcial). Example: laptop
     * @queryParam currency_id int Filtrar por ID de divisa. Example: 1
     * @queryParam min_price number Precio mínimo. Example: 100.00
     * @queryParam max_price number Precio máximo. Example: 1000.00
     * @queryParam per_page int Items por página. Example: 15
     * @queryParam sort_by string Campo para ordenar. Example: price
     * @queryParam sort_order string Orden (asc/desc). Example: desc
     * 
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Laptop Dell XPS 13",
     *       "description": "High-performance laptop",
     *       "price": 999.99,
     *       "currency": {
     *         "id": 1,
     *         "name": "US Dollar",
     *         "symbol": "USD"
     *       },
     *       "tax_cost": 150.00,
     *       "manufacturing_cost": 500.00,
     *       "total_cost": 1649.99
     *     }
     *   ],
     *   "meta": {
     *     "total": 1,
     *     "per_page": 15,
     *     "current_page": 1,
     *     "last_page": 1
     *   },
     *   "message": "Productos obtenidos exitosamente"
     * }
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            'message' => 'Productos obtenidos exitosamente'
        ]);
    }

    /**
     * Crear producto
     * 
     * Crea un nuevo producto en el sistema.
     *
     * @bodyParam name string required Nombre del producto. Example: Laptop Dell XPS 13
     * @bodyParam description string Descripción del producto. Example: High-performance laptop
     * @bodyParam price number required Precio del producto. Example: 999.99
     * @bodyParam currency_id int required ID de la divisa base. Example: 1
     * @bodyParam tax_cost number required Costo de impuestos. Example: 150.00
     * @bodyParam manufacturing_cost number required Costo de fabricación. Example: 500.00
     * 
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Laptop Dell XPS 13",
     *     "description": "High-performance laptop",
     *     "price": 999.99,
     *     "currency": {
     *       "id": 1,
     *       "name": "US Dollar",
     *       "symbol": "USD"
     *     },
     *     "tax_cost": 150.00,
     *     "manufacturing_cost": 500.00,
     *     "total_cost": 1649.99
     *   },
     *   "message": "Producto creado exitosamente"
     * }
     * 
     * @response 422 {
     *   "success": false,
     *   "message": "Error de validación",
     *   "errors": {
     *     "name": ["El nombre del producto es obligatorio."]
     *   }
     * }
     *
     * @param \App\Http\Requests\StoreProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->validated());

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product->load(['currency', 'productPrices.currency'])),
                'message' => 'Producto creado exitosamente'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Ver producto
     * 
     * Obtiene un producto específico por ID con todas sus relaciones (divisa, precios en otras divisas).
     *
     * @urlParam id int required ID del producto. Example: 1
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Laptop Dell XPS 13",
     *     "description": "High-performance laptop",
     *     "price": 999.99,
     *     "currency": {
     *       "id": 1,
     *       "name": "US Dollar",
     *       "symbol": "USD"
     *     },
     *     "tax_cost": 150.00,
     *     "manufacturing_cost": 500.00,
     *     "total_cost": 1649.99,
     *     "prices": []
     *   },
     *   "message": "Producto obtenido exitosamente"
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "Producto no encontrado"
     * }
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
            'message' => 'Producto obtenido exitosamente'
        ]);
    }

    /**
     * Actualizar producto
     * 
     * Actualiza un producto existente (actualización parcial permitida).
     *
     * @urlParam id int required ID del producto. Example: 1
     * 
     * @bodyParam name string Nombre del producto. Example: Laptop Dell XPS 13 - Updated
     * @bodyParam description string Descripción del producto. Example: Updated description
     * @bodyParam price number Precio del producto. Example: 1099.99
     * @bodyParam currency_id int ID de la divisa. Example: 1
     * @bodyParam tax_cost number Costo de impuestos. Example: 165.00
     * @bodyParam manufacturing_cost number Costo de fabricación. Example: 520.00
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Laptop Dell XPS 13 - Updated",
     *     "price": 1099.99,
     *     "tax_cost": 165.00
     *   },
     *   "message": "Producto actualizado exitosamente"
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "Producto no encontrado"
     * }
     *
     * @param \App\Http\Requests\UpdateProductRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($id, $request->validated());

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product->load(['currency', 'productPrices.currency'])),
                'message' => 'Producto actualizado exitosamente'
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
                'message' => 'Error al actualizar el producto',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar producto
     * 
     * Elimina un producto del sistema (soft delete).
     *
     * @urlParam id int required ID del producto. Example: 1
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "Producto eliminado exitosamente"
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "Producto no encontrado"
     * }
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
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
                'message' => 'Error al eliminar el producto',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
