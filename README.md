# Products API - Laravel 11

API RESTful robusta para gestión de productos con soporte multi-divisa, implementada con Laravel 11 siguiendo las mejores prácticas de arquitectura y desarrollo.

## 📋 Tabla de Contenidos

- [Características](#características)
- [Requisitos del Sistema](#requisitos-del-sistema)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Arquitectura del Proyecto](#arquitectura-del-proyecto)
- [Buenas Prácticas Implementadas](#buenas-prácticas-implementadas)
- [Endpoints de la API](#endpoints-de-la-api)
- [Documentación](#documentación)
- [Testing](#testing)
- [Seguridad](#seguridad)
- [Troubleshooting](#troubleshooting)

## ✨ Características

- ✅ **CRUD completo** de productos con soft deletes
- ✅ **Soporte multi-divisa** con gestión de precios por producto
- ✅ **Autenticación** con Laravel Sanctum (tokens de API)
- ✅ **Validación robusta** mediante Form Requests
- ✅ **Arquitectura en capas** (Controller → Service → Repository → Model)
- ✅ **API Resources** para transformación consistente de respuestas
- ✅ **Manejo de errores** centralizado y consistente
- ✅ **Documentación completa** (Laravel Scribe, Postman, OpenAPI)
- ✅ **Testing** con PHPUnit (Feature y Unit tests)

## 🔧 Requisitos del Sistema

- PHP >= 8.2
- Composer >= 2.0
- SQLite 3
- Extensions: PDO, SQLite, OpenSSL, Mbstring

## 📦 Instalación

### 1. Clonar el repositorio

```bash
git clone <repository-url>
cd prueba-laravel
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Ejecutar migraciones y seeders

```bash
php artisan migrate
php artisan db:seed
```

### 5. Iniciar el servidor

```bash
php artisan serve
```

La API estará disponible en `http://localhost:8000`

## ⚙️ Configuración

### Base de Datos

El proyecto usa SQLite por defecto. La configuración en `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/absoluta/a/database/database.sqlite
```

### Sanctum (Autenticación)

Para generar tokens de API:

```bash
php artisan tinker
>>> $user = User::first()
>>> $token = $user->createToken('api-token')->plainTextToken
```

Usa este token en el header `Authorization: Bearer {token}` para todas las peticiones.

## 🏗️ Arquitectura del Proyecto

La aplicación sigue una **arquitectura en capas** para separar responsabilidades y mantener el código limpio y escalable:

```
┌─────────────────┐
│   HTTP Request  │
└────────┬────────┘
         │
┌────────▼────────┐
│   Controller    │  ← Maneja requests/responses HTTP
└────────┬────────┘
         │
┌────────▼────────┐
│     Service     │  ← Lógica de negocio y orquestación
└────────┬────────┘
         │
┌────────▼────────┐
│   Repository    │  ← Acceso a datos (queries)
└────────┬────────┘
         │
┌────────▼────────┐
│ Eloquent Model  │  ← ORM y relaciones
└────────┬────────┘
         │
┌────────▼────────┐
│    Database     │
└─────────────────┘
```

### Estructura de Directorios

```
app/
├── Http/
│   ├── Controllers/Api/V1/    # Controllers versionados
│   ├── Requests/               # Form Requests (validación)
│   └── Resources/              # API Resources (transformación)
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

## 🎯 Buenas Prácticas Implementadas

### 1. **Repository Pattern**

Separa la lógica de acceso a datos del resto de la aplicación.

**Ventajas:**
- Desacoplamiento de la capa de datos
- Facilita testing con mocks
- Centraliza queries complejas
- Permite cambiar el ORM sin afectar la lógica de negocio

**Ejemplo:**

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

Contiene toda la lógica de negocio de la aplicación.

**Ventajas:**
- Controllers delgados y enfocados en HTTP
- Lógica reutilizable entre diferentes controllers
- Facilita testing unitario
- Manejo centralizado de transacciones

**Ejemplo:**

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

Validación separada en clases dedicadas.

**Ventajas:**
- Controllers más limpios
- Validaciones reutilizables
- Mensajes de error personalizados
- Fácil mantenimiento

**Ejemplo:**

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
            'name.required' => 'El nombre del producto es obligatorio.',
            // ...
        ];
    }
}
```

### 4. **API Resources**

Transformación consistente de datos para respuestas.

**Ventajas:**
- Formato de respuesta consistente
- Oculta/expone datos según contexto
- Facilita versionado de API
- Incluye relaciones condicionales

**Ejemplo:**

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

Inyección de dependencias en constructores.

**Ventajas:**
- Código testeable
- Bajo acoplamiento
- Facilita mocking en tests
- Principio de inversión de dependencias (SOLID)

**Binding en AppServiceProvider:**

```php
$this->app->bind(
    ProductRepositoryInterface::class,
    ProductRepository::class
);
```

### 6. **Scopes en Models**

Queries reutilizables y legibles.

**Ejemplo:**

```php
class Product extends Model
{
    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', "%{$name}%");
    }
}

// Uso:
Product::byName('laptop')->get();
```

### 7. **Soft Deletes**

Los productos eliminados se marcan en vez de borrarse.

**Ventajas:**
- Auditoría completa
- Recuperación de datos
- Integridad referencial

### 8. **Exception Handling Centralizado**

Manejo consistente de errores en `bootstrap/app.php`:

```php
$exceptions->render(function (ValidationException $e, $request) {
    if ($request->is('api/*')) {
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
    }
});
```

### 9. **Eager Loading**

Previene el problema N+1 queries:

```php
Product::with(['currency', 'productPrices.currency'])->get();
```

### 10. **Type Hinting y Return Types**

Código fuertemente tipado:

```php
public function getProduct(int $id): ?Product
{
    return $this->productRepository->findWithRelations($id);
}
```

## 🔌 Endpoints de la API

### Base URL

```
http://localhost:8000/api/v1
```

### Autenticación

Todas las rutas requieren autenticación con Bearer token:

```
Authorization: Bearer {your-token}
```

### Currencies

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/currencies` | Lista todas las divisas |
| GET | `/currencies/{id}` | Obtiene una divisa por ID |

### Products

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/products` | Lista paginada de productos con filtros |
| POST | `/products` | Crear producto |
| GET | `/products/{id}` | Obtener producto por ID |
| PUT | `/products/{id}` | Actualizar producto |
| DELETE | `/products/{id}` | Eliminar producto (soft delete) |

**Filtros disponibles en GET /products:**
- `name`: Filtrar por nombre (búsqueda parcial)
- `currency_id`: Filtrar por divisa
- `min_price`: Precio mínimo
- `max_price`: Precio máximo
- `per_page`: Items por página (default: 15)

### Product Prices

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/products/{id}/prices` | Lista precios del producto |
| POST | `/products/{id}/prices` | Crear/actualizar precio en divisa |

### Ejemplo de Request

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

### Formato de Respuestas

**Éxito:**

```json
{
  "success": true,
  "data": { ... },
  "message": "Operación exitosa",
  "meta": {
    "pagination": { ... }
  }
}
```

**Error:**

```json
{
  "success": false,
  "message": "Descripción del error",
  "errors": { ... }
}
```

## 📚 Documentación

El proyecto incluye múltiples formatos de documentación:

### 1. Documentación Web (Laravel Scribe)

Interfaz interactiva tipo MkDocs con ejemplos de código:

```bash
php artisan serve
# Visita: http://localhost:8000/docs
```

Incluye:
- ✅ Descripción detallada de cada endpoint
- ✅ Ejemplos de código en múltiples lenguajes
- ✅ Respuestas de ejemplo
- ✅ Pruebas interactivas

### 2. Postman Collection

Descarga la colección generada automáticamente:

```bash
# Visita: http://localhost:8000/docs/postman
```

O manualmente desde: `storage/app/private/scribe/collection.json`

### 3. OpenAPI/Swagger Specification

Descarga la especificación OpenAPI:

```bash
# Visita: http://localhost:8000/docs/openapi
```

O manualmente desde: `storage/app/private/scribe/openapi.yaml`

### 4. Regenerar Documentación

Después de cambios en los endpoints:

```bash
php artisan scribe:generate
```

## 🧪 Testing

### Ejecutar todos los tests

```bash
php artisan test
```

### Tests por tipo

```bash
# Feature tests
php artisan test --testsuite=Feature

# Unit tests
php artisan test --testsuite=Unit
```

### Coverage

```bash
php artisan test --coverage
```

## 🔒 Seguridad

### Medidas Implementadas

1. **Laravel Sanctum**: Autenticación basada en tokens
2. **Rate Limiting**: 60 requests por minuto por defecto
3. **CORS**: Configurado para entornos permitidos
4. **Mass Assignment Protection**: `$fillable` en todos los models
5. **SQL Injection Prevention**: Uso exclusivo de Eloquent ORM
6. **Input Validation**: Form Requests con reglas estrictas
7. **Soft Deletes**: Auditoría de eliminaciones

### Configuración de Seguridad

```env
# .env
APP_DEBUG=false  # En producción
APP_ENV=production
```

## 🐛 Troubleshooting

### Error: "Unauthenticated"

**Solución:** Asegúrate de incluir el token en el header:

```
Authorization: Bearer {your-token}
```

### Error: "SQLSTATE[HY000]: database is locked"

**Solución:** SQLite no soporta múltiples escrituras concurrentes. Considera usar MySQL/PostgreSQL para producción.

### Error: "Class 'Repository' not found"

**Solución:** Ejecuta:

```bash
composer dump-autoload
```

### Migraciones fallan

**Solución:**

```bash
php artisan migrate:fresh
php artisan db:seed
```

## 📄 Licencia

Este proyecto es de código abierto.

## 👤 Autor

Desarrollado como prueba técnica siguiendo las mejores prácticas de Laravel.

---

## 🎓 Aprendizajes Clave

Este proyecto demuestra:

1. **Arquitectura limpia** con separación de responsabilidades
2. **Código mantenible** y escalable
3. **Testing** comprehensivo
4. **Documentación** completa y profesional
5. **Seguridad** como prioridad
6. **Buenas prácticas** de la industria

**¿Preguntas?** Consulta la documentación adicional en la carpeta `docs/`
