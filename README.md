# Products API - Laravel 11

RESTful API for product management with multi-currency support, built with Laravel 11 following architecture and development best practices.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Project Architecture](#project-architecture)
- [Best Practices](#best-practices)
- [API Endpoints](#api-endpoints)
- [Documentation](#documentation)
- [Testing](#testing)
- [Security](#security)
- [Troubleshooting](#troubleshooting)

## Features

- **Full CRUD** for products with soft deletes
- **Multi-currency support** with per-product price management
- **Authentication** with Laravel Sanctum (API tokens)
- **Robust validation** via Form Requests
- **Layered architecture** (Controller → Service → Repository → Model)
- **API Resources** for consistent response transformation
- **Centralized** and consistent error handling
- **Documentation** (Laravel Scribe, Postman, OpenAPI)
- **Testing** with PHPUnit (Feature tests)

## Requirements

- PHP >= 8.2
- Composer >= 2.0
- SQLite 3
- Extensions: PDO, SQLite, OpenSSL, Mbstring

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd prueba-laravel
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed
```

### 5. Start the server

```bash
php artisan serve
```

API will be available at `http://localhost:8000`

## Configuration

### Database

The project uses SQLite by default. Configuration in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

### Sanctum (Authentication)

To generate API tokens, see `GENERATE_TOKEN.md` or run:

```bash
php artisan tinker
>>> $user = User::first()
>>> $token = $user->createToken('api-token')->plainTextToken
```

Use this token in the header `Authorization: Bearer {token}` for all requests.

## Project Architecture

The application uses a **layered architecture** to separate concerns and keep code clean and scalable:

```
┌─────────────────┐
│   HTTP Request  │
└────────┬────────┘
         │
┌────────▼────────┐
│   Controller    │  ← Handles HTTP requests/responses
└────────┬────────┘
         │
┌────────▼────────┐
│     Service     │  ← Business logic and orchestration
└────────┬────────┘
         │
┌────────▼────────┐
│   Repository    │  ← Data access (queries)
└────────┬────────┘
         │
┌────────▼────────┐
│ Eloquent Model  │  ← ORM and relations
└────────┬────────┘
         │
┌────────▼────────┐
│    Database     │
└─────────────────┘
```

See `STRUCTURE.md` for folder-by-folder description.

### Directory Structure

```
app/
├── Http/
│   ├── Controllers/Api/V1/    # Versioned API controllers
│   ├── Requests/               # Form Requests (validation)
│   └── Resources/              # API Resources (transformation)
├── Models/                     # Eloquent Models
├── Repositories/               # Repository Pattern
│   ├── Contracts/              # Interfaces
│   ├── ProductRepository.php
│   ├── CurrencyRepository.php
│   └── ProductPriceRepository.php
├── Services/                   # Business Logic Layer
│   ├── ProductService.php
│   ├── ProductPriceService.php
│   └── CurrencyService.php
└── Exceptions/                 # Custom Exceptions
```

## Best Practices

### 1. **Repository Pattern**

Separates data access logic from the rest of the application.

**Benefits:**
- Decoupled data layer
- Easier testing with mocks
- Centralized complex queries
- Swap ORM without affecting business logic

**Example:**

```php
interface ProductRepositoryInterface
{
    public function getAllPaginated(array $filters, int $perPage): LengthAwarePaginator;
    public function findById(int $id): ?Product;
    // ...
}

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllPaginated(array $filters, int $perPage): LengthAwarePaginator
    {
        return Product::with(['currency', 'productPrices.currency'])
            ->when($filters['name'] ?? null, fn($q, $name) => $q->byName($name))
            ->paginate($perPage);
    }
}
```

### 2. **Service Layer**

Holds all business logic.

**Benefits:**
- Thin controllers focused on HTTP
- Reusable logic across controllers
- Easier unit testing
- Centralized transaction handling

**Example:**

```php
class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function createProduct(array $data): Product
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->create($data);
            Log::info('Product created', ['id' => $product->id]);
            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

### 3. **Form Requests**

Validation in dedicated classes.

**Benefits:**
- Cleaner controllers
- Reusable validation rules
- Custom error messages
- Easy maintenance

**Example:**

```php
class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            // ...
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            // ...
        ];
    }
}
```

### 4. **API Resources**

Consistent data transformation for responses.

**Benefits:**
- Consistent response format
- Hide/expose data by context
- Easier API versioning
- Conditional relations

**Example:**

```php
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'prices' => ProductPriceResource::collection($this->whenLoaded('productPrices')),
        ];
    }
}
```

### 5. **Dependency Injection**

Dependencies injected via constructors.

**Benefits:**
- Testable code
- Low coupling
- Easy mocking in tests
- Dependency inversion (SOLID)

**Binding en AppServiceProvider:**

```php
$this->app->bind(
    ProductRepositoryInterface::class,
    ProductRepository::class
);
```

### 6. **Model Scopes**

Reusable, readable queries.

**Example:**

```php
class Product extends Model
{
    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', "%{$name}%");
    }
}

// Usage:
Product::byName('laptop')->get();
```

### 7. **Soft Deletes**

Deleted products are marked, not removed.

**Benefits:**
- Full audit trail
- Data recovery
- Referential integrity

### 8. **Centralized Exception Handling**

Consistent error handling in `bootstrap/app.php`:

```php
$exceptions->render(function (ValidationException $e, $request) {
    if ($request->is('api/*')) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    }
});
```

### 9. **Eager Loading**

Prevents N+1 queries:

```php
Product::with(['currency', 'productPrices.currency'])->get();
```

### 10. **Type Hinting and Return Types**

Strongly typed code:

```php
public function getProduct(int $id): ?Product
{
    return $this->productRepository->findWithRelations($id);
}
```

## API Endpoints

### Base URL

```
http://localhost:8000/api/v1
```

### Authentication

All routes require Bearer token authentication:

```
Authorization: Bearer {your-token}
```

### Currencies

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/currencies` | List all currencies |
| GET | `/currencies/{id}` | Get currency by ID |

### Products

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/products` | Paginated product list with filters |
| POST | `/products` | Create product |
| GET | `/products/{id}` | Get product by ID |
| PUT | `/products/{id}` | Update product |
| DELETE | `/products/{id}` | Delete product (soft delete) |

**GET /products query params:**
- `name`: Filter by name (partial match)
- `currency_id`: Filter by currency
- `min_price`: Minimum price
- `max_price`: Maximum price
- `per_page`: Items per page (default: 15)

### Product Prices

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/products/{id}/prices` | List product prices |
| POST | `/products/{id}/prices` | Create/update price for a currency |

### Example Request

```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop Dell XPS 13",
    "description": "High-performance laptop",
    "price": 999.99,
    "currency_id": 1,
    "tax_cost": 150.00,
    "manufacturing_cost": 500.00
  }'
```

### Response Format

**Success:**

```json
{
  "success": true,
  "data": { ... },
  "message": "Operation successful",
  "meta": {
    "pagination": { ... }
  }
}
```

**Error:**

```json
{
  "success": false,
  "message": "Error description",
  "errors": { ... }
}
```

## Documentation

- **Web docs:** `http://localhost:8000/docs` (Laravel Scribe)
- **Postman:** `http://localhost:8000/docs/postman` or `storage/app/private/scribe/collection.json`
- **OpenAPI:** `http://localhost:8000/docs/openapi` or `storage/app/private/scribe/openapi.yaml`
- **Regenerate:** `php artisan scribe:generate`

## Testing

```bash
php artisan test
php artisan test --testsuite=Feature
php artisan test --coverage
```

## Monitoring & Logging

### Laravel Telescope

Telescope provides debugging and monitoring for your API.

**Access:** `http://localhost:8000/telescope`

**Features:**
- Request/Response monitoring
- Query logging
- Exception tracking
- Job monitoring
- Cache operations
- Log viewer

**Configuration:**
- Enabled in `.env`: `TELESCOPE_ENABLED=true`
- Only tracks `api/*` paths (configured in `config/telescope.php`)
- Available in `local` environment by default

### Structured Logging

Structured JSON logging for better log analysis.

**Log files:**
- Standard: `storage/logs/laravel.log`
- Structured (JSON): `storage/logs/laravel-structured.log`

**Usage:**

```php
use App\Helpers\LoggerHelper;

LoggerHelper::info('Product created', [
    'product_id' => 1,
    'action' => 'create',
]);

LoggerHelper::apiRequest('POST', '/api/v1/products', [
    'ip' => $request->ip(),
]);
```

**Log levels:** `info`, `error`, `warning`, `debug`

**Auto-logging:**
- API requests/responses logged automatically via `LogApiRequest` middleware
- Service operations logged in ProductService and ProductPriceService

## Security

1. **Laravel Sanctum** – token authentication
2. **Rate limiting** – 60 req/min default
3. **CORS** – allowed origins
4. **Mass assignment** – `$fillable` on models
5. **SQL injection** – Eloquent only
6. **Validation** – Form Requests
7. **Soft deletes** – audit trail

Production: set `APP_DEBUG=false`, `APP_ENV=production`.

## Troubleshooting

- **Unauthenticated:** use header `Authorization: Bearer {your-token}`
- **Database locked:** use MySQL/PostgreSQL in production
- **Class not found:** `composer dump-autoload`
- **Migrations:** `php artisan migrate:fresh` then `php artisan db:seed`

## License

Open source.

## Author

Technical assessment following Laravel best practices.

---

See `docs/`, `STRUCTURE.md`, and `GENERATE_TOKEN.md` for more.
