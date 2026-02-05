# Introduction

RESTful API for product management with multi-currency support. Built with Laravel 11 following architecture and development best practices.

<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>

This API lets you manage products with prices in multiple currencies.

## Features

- Full product CRUD
- Multi-currency support
- Laravel Sanctum authentication
- Robust validation
- Layered architecture (Repository-Service-Controller)

## Authentication

All endpoints require a Bearer token. Use the token in the header `Authorization: Bearer {token}`.

<aside class="notice">You can see code examples in different languages in the right panel.</aside>

---

## Endpoints overview

### Currencies

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/currencies` | List all currencies |
| GET | `/api/v1/currencies/{id}` | Get currency by ID |

### Products

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/products` | Paginated list. Query params: `name`, `currency_id`, `min_price`, `max_price`, `per_page`, `sort_by`, `sort_order` |
| POST | `/api/v1/products` | Create product. Body: `name`, `description`, `price`, `currency_id`, `tax_cost`, `manufacturing_cost` |
| GET | `/api/v1/products/{id}` | Get product by ID with relations |
| PUT/PATCH | `/api/v1/products/{id}` | Update product (partial allowed) |
| DELETE | `/api/v1/products/{id}` | Soft delete product |

### Product prices

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/products/{productId}/prices` | List prices for product in all currencies |
| POST | `/api/v1/products/{productId}/prices` | Create or update price. Body: `currency_id`, `price` |

### Response format

Success: `{ "success": true, "data": {...}, "message": "..." }`  
Error: `{ "success": false, "message": "...", "errors": {...} }`

HTTP codes: 200 OK, 201 Created, 401 Unauthenticated, 404 Not Found, 422 Validation Error, 500 Server Error.
