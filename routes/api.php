<?php

use App\Http\Controllers\Api\V1\CurrencyController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductPriceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('currencies', CurrencyController::class)->only(['index', 'show']);
        Route::apiResource('products', ProductController::class);
        Route::prefix('products/{productId}')->group(function () {
            Route::get('/prices', [ProductPriceController::class, 'index']);
            Route::post('/prices', [ProductPriceController::class, 'store']);
        });
    });
});
