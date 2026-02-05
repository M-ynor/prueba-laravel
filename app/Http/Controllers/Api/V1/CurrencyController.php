<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Currencies
 * 
 * @group Currencies
 * 
 * API endpoints para gestionar divisas del sistema.
 */
class CurrencyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param \App\Services\CurrencyService $currencyService
     */
    public function __construct(
        private CurrencyService $currencyService
    ) {}

    /**
     * Listar divisas
     * 
     * Obtiene todas las divisas disponibles en el sistema.
     * 
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "US Dollar",
     *       "code": "USD",
     *       "symbol": "$",
     *       "exchange_rate": 1.00
     *     },
     *     {
     *       "id": 2,
     *       "name": "Euro",
     *       "code": "EUR",
     *       "symbol": "€",
     *       "exchange_rate": 0.92
     *     }
     *   ],
     *   "message": "Divisas obtenidas exitosamente"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $currencies = $this->currencyService->getAllCurrencies();

        return response()->json([
            'success' => true,
            'data' => CurrencyResource::collection($currencies),
            'message' => 'Divisas obtenidas exitosamente'
        ]);
    }

    /**
     * Ver divisa
     * 
     * Obtiene una divisa específica por ID.
     *
     * @urlParam id int required ID de la divisa. Example: 1
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "US Dollar",
     *     "code": "USD",
     *     "symbol": "$",
     *     "exchange_rate": 1.00
     *   },
     *   "message": "Divisa obtenida exitosamente"
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "Currency not found with ID: 1"
     * }
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $currency = $this->currencyService->getCurrency($id);

        if (!$currency) {
            return response()->json([
                'success' => false,
                'message' => 'Divisa no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => new CurrencyResource($currency),
            'message' => 'Divisa obtenida exitosamente'
        ]);
    }
}
