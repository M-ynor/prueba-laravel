<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Products API",
 *     description="API RESTful para gestión de productos con soporte multi-divisa",
 *     @OA\Contact(
 *         email="admin@productos-api.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter token in format: Bearer {token}"
 * )
 *
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     required={"id", "name", "price", "currency_id", "tax_cost", "manufacturing_cost"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Laptop Dell XPS 13"),
 *     @OA\Property(property="description", type="string", example="High-performance laptop"),
 *     @OA\Property(property="price", type="number", format="float", example=999.99),
 *     @OA\Property(property="currency_id", type="integer", example=1),
 *     @OA\Property(property="tax_cost", type="number", format="float", example=150.00),
 *     @OA\Property(property="manufacturing_cost", type="number", format="float", example=500.00),
 *     @OA\Property(property="total_cost", type="number", format="float", example=1649.99),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Currency",
 *     type="object",
 *     required={"id", "name", "symbol", "exchange_rate"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="US Dollar"),
 *     @OA\Property(property="symbol", type="string", example="USD"),
 *     @OA\Property(property="exchange_rate", type="number", format="float", example=1.000000),
 *     @OA\Property(property="formatted_name", type="string", example="US Dollar (USD)"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="ProductPrice",
 *     type="object",
 *     required={"id", "product_id", "currency_id", "price"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="currency_id", type="integer", example=2),
 *     @OA\Property(property="price", type="number", format="float", example=850.50),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
abstract class Controller
{
    //
}
