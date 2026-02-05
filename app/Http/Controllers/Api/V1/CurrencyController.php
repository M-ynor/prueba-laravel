<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CurrencyController extends Controller
{
    public function __construct(
        private CurrencyService $currencyService
    ) {}

    public function index(): JsonResponse
    {
        $currencies = $this->currencyService->getAllCurrencies();

        return response()->json([
            'success' => true,
            'data' => CurrencyResource::collection($currencies),
            'message' => 'Currencies retrieved successfully'
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $currency = $this->currencyService->getCurrency($id);

        if (!$currency) {
            return response()->json([
                'success' => false,
                'message' => 'Currency not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => new CurrencyResource($currency),
            'message' => 'Currency retrieved successfully'
        ]);
    }
}
