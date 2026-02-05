# API Documentation - Products API

## Información General

Esta API RESTful permite gestionar productos con soporte para múltiples divisas. Implementa autenticación mediante tokens, validación robusta y sigue las mejores prácticas de desarrollo con Laravel.

**Base URL:** `http://localhost:8000/api/v1`

**Formato de respuesta:** JSON

**Autenticación:** Bearer Token (Laravel Sanctum)

---

## Autenticación

### Obtener un Token

Para usar la API, primero necesitas un token de autenticación. Puedes generarlo mediante Tinker:

```bash
php artisan tinker
>>> $user = \App\Models\User::first()
>>> $token = $user->createToken('api-token')->plainTextToken
>>> echo $token
```

### Usar el Token

Incluye el token en el header `Authorization` de todas las peticiones:

```
Authorization: Bearer {your-token-here}
```

---

## Formato de Respuestas

### Respuesta Exitosa

```json
{
  "success": true,
  "data": { ... },
  "message": "Operación exitosa",
  "meta": {
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7
  }
}
```

### Respuesta de Error

```json
{
  "success": false,
  "message": "Descripción del error",
  "errors": {
    "field": ["Error de validación"]
  }
}
```

### Códigos de Estado HTTP

| Código | Significado |
|--------|-------------|
| 200 | OK - Petición exitosa |
| 201 | Created - Recurso creado exitosamente |
| 204 | No Content - Eliminación exitosa |
| 400 | Bad Request - Petición mal formada |
| 401 | Unauthorized - No autenticado |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Error de validación |
| 500 | Internal Server Error - Error del servidor |

---

## Endpoints

### 1. Currencies (Divisas)

#### GET /currencies

Obtiene lista de todas las divisas disponibles.

**Autenticación:** Requerida

**Response 200:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "US Dollar",
      "symbol": "USD",
      "exchange_rate": 1.000000,
      "formatted_name": "US Dollar (USD)",
      "created_at": "2026-02-05T00:00:00Z",
      "updated_at": "2026-02-05T00:00:00Z"
    }
  ],
  "message": "Divisas obtenidas exitosamente"
}
```

#### GET /currencies/{id}

Obtiene una divisa específica por ID.

**Autenticación:** Requerida

**Parámetros de URL:**
- `id` (integer, required): ID de la divisa

**Response 200:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "US Dollar",
    "symbol": "USD",
    "exchange_rate": 1.000000,
    "formatted_name": "US Dollar (USD)",
    "created_at": "2026-02-05T00:00:00Z",
    "updated_at": "2026-02-05T00:00:00Z"
  },
  "message": "Divisa obtenida exitosamente"
}
```

**Response 404:**

```json
{
  "success": false,
  "message": "Divisa no encontrada"
}
```

---

### 2. Products (Productos)

#### GET /products

Obtiene lista paginada de productos con filtros opcionales.

**Autenticación:** Requerida

**Query Parameters:**
- `name` (string, optional): Filtrar por nombre (búsqueda parcial)
- `currency_id` (integer, optional): Filtrar por ID de divisa
- `min_price` (float, optional): Precio mínimo
- `max_price` (float, optional): Precio máximo
- `per_page` (integer, optional): Items por página (default: 15)
- `sort_by` (string, optional): Campo para ordenar (default: created_at)
- `sort_order` (string, optional): Orden (asc/desc, default: desc)

**Ejemplo:**

```bash
GET /products?name=laptop&min_price=500&max_price=2000&per_page=10
```

**Response 200:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Laptop Dell XPS 13",
      "description": "High-performance laptop",
      "price": 999.99,
      "currency": {
        "id": 1,
        "name": "US Dollar",
        "symbol": "USD",
        "exchange_rate": 1.000000
      },
      "tax_cost": 150.00,
      "manufacturing_cost": 500.00,
      "total_cost": 1649.99,
      "prices": [],
      "created_at": "2026-02-05T10:00:00Z",
      "updated_at": "2026-02-05T10:00:00Z"
    }
  ],
  "meta": {
    "total": 1,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  },
  "message": "Productos obtenidos exitosamente"
}
```

#### POST /products

Crea un nuevo producto.

**Autenticación:** Requerida

**Request Body:**

```json
{
  "name": "Laptop Dell XPS 13",
  "description": "High-performance laptop with 16GB RAM",
  "price": 999.99,
  "currency_id": 1,
  "tax_cost": 150.00,
  "manufacturing_cost": 500.00
}
```

**Validación:**
- `name`: required, string, max:255
- `description`: nullable, string
- `price`: required, numeric, min:0, 2 decimales máximo
- `currency_id`: required, integer, debe existir en tabla currencies
- `tax_cost`: required, numeric, min:0, 2 decimales máximo
- `manufacturing_cost`: required, numeric, min:0, 2 decimales máximo

**Response 201:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Laptop Dell XPS 13",
    "description": "High-performance laptop with 16GB RAM",
    "price": 999.99,
    "currency": {
      "id": 1,
      "name": "US Dollar",
      "symbol": "USD"
    },
    "tax_cost": 150.00,
    "manufacturing_cost": 500.00,
    "total_cost": 1649.99,
    "created_at": "2026-02-05T10:00:00Z",
    "updated_at": "2026-02-05T10:00:00Z"
  },
  "message": "Producto creado exitosamente"
}
```

**Response 422 (Validation Error):**

```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "name": ["El nombre del producto es obligatorio."],
    "price": ["El precio debe ser un número."]
  }
}
```

#### GET /products/{id}

Obtiene un producto específico con todas sus relaciones.

**Autenticación:** Requerida

**Parámetros de URL:**
- `id` (integer, required): ID del producto

**Response 200:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Laptop Dell XPS 13",
    "description": "High-performance laptop",
    "price": 999.99,
    "currency": {
      "id": 1,
      "name": "US Dollar",
      "symbol": "USD",
      "exchange_rate": 1.000000
    },
    "tax_cost": 150.00,
    "manufacturing_cost": 500.00,
    "total_cost": 1649.99,
    "prices": [
      {
        "id": 1,
        "product_id": 1,
        "currency": {
          "id": 2,
          "name": "Euro",
          "symbol": "EUR"
        },
        "price": 919.99,
        "created_at": "2026-02-05T10:30:00Z",
        "updated_at": "2026-02-05T10:30:00Z"
      }
    ],
    "created_at": "2026-02-05T10:00:00Z",
    "updated_at": "2026-02-05T10:00:00Z"
  },
  "message": "Producto obtenido exitosamente"
}
```

**Response 404:**

```json
{
  "success": false,
  "message": "Producto no encontrado"
}
```

#### PUT /products/{id}

Actualiza un producto existente (actualización parcial permitida).

**Autenticación:** Requerida

**Parámetros de URL:**
- `id` (integer, required): ID del producto

**Request Body (todos los campos son opcionales):**

```json
{
  "name": "Laptop Dell XPS 13 - Updated",
  "price": 1099.99,
  "tax_cost": 165.00
}
```

**Response 200:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Laptop Dell XPS 13 - Updated",
    "price": 1099.99,
    "tax_cost": 165.00,
    "manufacturing_cost": 500.00,
    "total_cost": 1764.99,
    "created_at": "2026-02-05T10:00:00Z",
    "updated_at": "2026-02-05T11:00:00Z"
  },
  "message": "Producto actualizado exitosamente"
}
```

#### DELETE /products/{id}

Elimina un producto (soft delete).

**Autenticación:** Requerida

**Parámetros de URL:**
- `id` (integer, required): ID del producto

**Response 200:**

```json
{
  "success": true,
  "message": "Producto eliminado exitosamente"
}
```

**Response 404:**

```json
{
  "success": false,
  "message": "Producto no encontrado"
}
```

---

### 3. Product Prices (Precios de Productos)

#### GET /products/{productId}/prices

Obtiene todos los precios de un producto en diferentes divisas.

**Autenticación:** Requerida

**Parámetros de URL:**
- `productId` (integer, required): ID del producto

**Response 200:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "currency": {
        "id": 2,
        "name": "Euro",
        "symbol": "EUR",
        "exchange_rate": 0.920000
      },
      "price": 919.99,
      "created_at": "2026-02-05T10:30:00Z",
      "updated_at": "2026-02-05T10:30:00Z"
    },
    {
      "id": 2,
      "product_id": 1,
      "currency": {
        "id": 3,
        "name": "Guatemalan Quetzal",
        "symbol": "GTQ",
        "exchange_rate": 7.850000
      },
      "price": 7849.42,
      "created_at": "2026-02-05T10:35:00Z",
      "updated_at": "2026-02-05T10:35:00Z"
    }
  ],
  "message": "Precios obtenidos exitosamente"
}
```

#### POST /products/{productId}/prices

Crea o actualiza el precio de un producto en una divisa específica.

**Autenticación:** Requerida

**Parámetros de URL:**
- `productId` (integer, required): ID del producto

**Request Body:**

```json
{
  "currency_id": 2,
  "price": 850.50
}
```

**Validación:**
- `currency_id`: required, integer, debe existir en tabla currencies
- `price`: required, numeric, min:0, 2 decimales máximo

**Response 201:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "product_id": 1,
    "currency": {
      "id": 2,
      "name": "Euro",
      "symbol": "EUR",
      "exchange_rate": 0.920000
    },
    "price": 850.50,
    "created_at": "2026-02-05T10:30:00Z",
    "updated_at": "2026-02-05T10:30:00Z"
  },
  "message": "Precio creado/actualizado exitosamente"
}
```

---

## Ejemplos de uso con cURL

### Obtener todas las divisas

```bash
curl -X GET "http://localhost:8000/api/v1/currencies" \
  -H "Authorization: Bearer {your-token}" \
  -H "Accept: application/json"
```

### Crear un producto

```bash
curl -X POST "http://localhost:8000/api/v1/products" \
  -H "Authorization: Bearer {your-token}" \
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

### Filtrar productos

```bash
curl -X GET "http://localhost:8000/api/v1/products?name=laptop&min_price=500" \
  -H "Authorization: Bearer {your-token}" \
  -H "Accept: application/json"
```

### Actualizar un producto

```bash
curl -X PUT "http://localhost:8000/api/v1/products/1" \
  -H "Authorization: Bearer {your-token}" \
  -H "Content-Type: application/json" \
  -d '{
    "price": 1099.99
  }'
```

### Agregar precio en otra divisa

```bash
curl -X POST "http://localhost:8000/api/v1/products/1/prices" \
  -H "Authorization: Bearer {your-token}" \
  -H "Content-Type: application/json" \
  -d '{
    "currency_id": 2,
    "price": 850.50
  }'
```

---

## Rate Limiting

La API implementa rate limiting para prevenir abuso:

- **Límite:** 60 peticiones por minuto por usuario autenticado
- **Header de respuesta:** `X-RateLimit-Limit`, `X-RateLimit-Remaining`

Cuando se excede el límite, recibirás un error 429 (Too Many Requests).

---

## Seguridad

### Buenas Prácticas

1. **Nunca compartas tu token** de autenticación
2. **Usa HTTPS** en producción
3. **Rota tokens** periódicamente
4. **Valida siempre** los datos de entrada
5. **Maneja errores** apropiadamente

### Protecciones Implementadas

- ✅ Autenticación con tokens (Sanctum)
- ✅ Validación de entrada con Form Requests
- ✅ Protección SQL Injection (Eloquent ORM)
- ✅ Protección Mass Assignment ($fillable)
- ✅ Rate Limiting
- ✅ CORS configurado
- ✅ Soft Deletes para auditoría

---

## Contacto y Soporte

Para reportar problemas o sugerencias, consulta el repositorio del proyecto.

**Documentación adicional:**
- README.md - Guía de instalación y arquitectura
- Postman Collection - `docs/postman_collection.json`
- Insomnia Collection - `docs/insomnia_collection.json`
