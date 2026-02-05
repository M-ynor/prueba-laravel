<?php

use App\Http\Controllers\Api\V1\CurrencyController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductPriceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    
    // Protected routes with Sanctum authentication
    Route::middleware('auth:sanctum')->group(function () {
        
        // Currency routes
        Route::apiResource('currencies', CurrencyController::class)->only(['index', 'show']);
        
        // Product routes
        Route::apiResource('products', ProductController::class);
        
        // Product prices routes
        Route::prefix('products/{productId}')->group(function () {
            Route::get('/prices', [ProductPriceController::class, 'index']);
            Route::post('/prices', [ProductPriceController::class, 'store']);
        });
    });
});
