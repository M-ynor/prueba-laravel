# Project Structure - Products API

## Simplified and Clear Structure

```
prueba-laravel/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/V1/    ← API CONTROLLERS
│   │   │   ├── ProductController.php
│   │   │   ├── CurrencyController.php
│   │   │   └── ProductPriceController.php
│   │   │
│   │   ├── Requests/              ← VALIDATION
│   │   │   ├── StoreProductRequest.php
│   │   │   ├── UpdateProductRequest.php
│   │   │   └── StoreProductPriceRequest.php
│   │   │
│   │   └── Resources/              ← RESPONSE TRANSFORMATION
│   │       ├── ProductResource.php
│   │       ├── ProductCollection.php
│   │       ├── CurrencyResource.php
│   │       └── ProductPriceResource.php
│   │
│   ├── Models/                     ← ELOQUENT MODELS
│   │   ├── Product.php
│   │   ├── Currency.php
│   │   ├── ProductPrice.php
│   │   └── User.php
│   │
│   ├── Services/                   ← BUSINESS LOGIC
│   │   ├── ProductService.php
│   │   ├── ProductPriceService.php
│   │   └── CurrencyService.php
│   │
│   └── Repositories/               ← DATA ACCESS
│       ├── Contracts/              ← Interfaces
│       │   ├── ProductRepositoryInterface.php
│       │   ├── CurrencyRepositoryInterface.php
│       │   └── ProductPriceRepositoryInterface.php
│       │
│       ├── ProductRepository.php
│       ├── CurrencyRepository.php
│       └── ProductPriceRepository.php
│
├── database/
│   ├── migrations/                 ← DATABASE SCHEMA
│   ├── seeders/                    ← INITIAL DATA
│   ├── factories/                  ← TEST DATA
│   └── database.sqlite            ← DATABASE FILE
│
├── routes/
│   ├── api.php                     ← API ROUTES
│   └── web.php                     ← Web routes
│
├── tests/
│   └── Feature/                    ← API TESTS
│       └── ProductApiTest.php
│
├── docs/                           ← DOCUMENTATION
│   ├── postman_collection.json
│   ├── insomnia_collection.json
│   └── API_DOCUMENTATION.md
│
├── .env                            ← CONFIGURATION
├── README.md                       ← MAIN GUIDE
└── STRUCTURE.md                    ← This file
```

---

## What Each Folder Does

### app/Http/Controllers/Api/V1/
**CONTROLLERS** handle HTTP requests and responses.
- HTTP only
- No business logic
- Call Services

### app/Http/Requests/
**VALIDATION** for input data.
- Validate data before processing
- Custom error messages
- Reusable rules

### app/Http/Resources/
**TRANSFORMATION** of JSON responses.
- Consistent format
- Hide/show fields by context
- Include relations

### app/Models/
**Eloquent MODELS** for the database.
- Table relations
- Data casts
- Query scopes

### app/Services/
**BUSINESS LOGIC** of the application.
- Orchestrate repositories
- Database transactions
- Complex calculations and validation

### app/Repositories/
**DATA ACCESS** (database queries).
- Queries only
- No business logic
- Implement interfaces

### database/migrations/
**DATABASE SCHEMA**.
- Create tables
- Define columns and relations
- Version database changes

### database/seeders/
**INITIAL DATA** to populate the database.
- Predefined currencies (USD, EUR, GTQ)
- Test users

### routes/
**ROUTES** of the application.
- `api.php`: API endpoints
- `web.php`: Web routes

### tests/
**AUTOMATED TESTS**.
- Feature: full endpoint tests
- Unit: individual service tests

### docs/
**DOCUMENTATION**.
- Postman/Insomnia collections
- API documentation in Markdown

---

## Request Flow

```
1. HTTP Client
   ↓
2. Route (routes/api.php)
   ↓
3. Middleware (Sanctum Auth)
   ↓
4. Controller (Http/Controllers/Api/V1/)
   ↓
5. Form Request (Http/Requests/) - Validation
   ↓
6. Service (Services/) - Business logic
   ↓
7. Repository (Repositories/) - Database query
   ↓
8. Model (Models/) - Eloquent ORM
   ↓
9. Database (SQLite)
   ↓
10. Response Resource (Http/Resources/)
    ↓
11. JSON Response to Client
```

---

## Important Files

| File | Description |
|------|-------------|
| `.env` | Project configuration |
| `README.md` | Full installation and usage guide |
| `routes/api.php` | Endpoint definitions |
| `database/database.sqlite` | Database file |
| `docs/postman_collection.json` | Test API in Postman |
| `docs/API_DOCUMENTATION.md` | Technical documentation |

---

## Useful Commands

```bash
# List API routes
php artisan route:list --path=api

# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed

# Clear caches
php artisan config:clear
php artisan cache:clear

# Run tests
php artisan test

# Start server
php artisan serve
```

---

## Principles Applied

1. **Separation of Concerns** - Each class does one thing
2. **Dependency Injection** - Dependencies injected via constructor
3. **Repository Pattern** - Data access abstraction
4. **Service Layer** - Centralized business logic
5. **SOLID Principles** - Maintainable and scalable code
