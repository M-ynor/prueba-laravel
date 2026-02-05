# 📁 Estructura del Proyecto - Products API

## 🎯 Estructura Simplificada y Clara

```
prueba-laravel/
│
├── 📂 app/
│   ├── 📂 Http/
│   │   ├── 📂 Controllers/Api/V1/    ← CONTROLLERS de la API
│   │   │   ├── ProductController.php
│   │   │   ├── CurrencyController.php
│   │   │   └── ProductPriceController.php
│   │   │
│   │   ├── 📂 Requests/              ← VALIDACIÓN de datos
│   │   │   ├── StoreProductRequest.php
│   │   │   ├── UpdateProductRequest.php
│   │   │   └── StoreProductPriceRequest.php
│   │   │
│   │   └── 📂 Resources/             ← TRANSFORMACIÓN de respuestas
│   │       ├── ProductResource.php
│   │       ├── ProductCollection.php
│   │       ├── CurrencyResource.php
│   │       └── ProductPriceResource.php
│   │
│   ├── 📂 Models/                    ← MODELOS Eloquent
│   │   ├── Product.php
│   │   ├── Currency.php
│   │   ├── ProductPrice.php
│   │   └── User.php
│   │
│   ├── 📂 Services/                  ← LÓGICA DE NEGOCIO
│   │   ├── ProductService.php
│   │   ├── ProductPriceService.php
│   │   └── CurrencyService.php
│   │
│   └── 📂 Repositories/              ← ACCESO A DATOS
│       ├── 📂 Contracts/             ← Interfaces
│       │   ├── ProductRepositoryInterface.php
│       │   ├── CurrencyRepositoryInterface.php
│       │   └── ProductPriceRepositoryInterface.php
│       │
│       ├── ProductRepository.php
│       ├── CurrencyRepository.php
│       └── ProductPriceRepository.php
│
├── 📂 database/
│   ├── 📂 migrations/                ← ESTRUCTURA de BD
│   │   ├── create_currencies_table.php
│   │   ├── create_products_table.php
│   │   └── create_product_prices_table.php
│   │
│   ├── 📂 seeders/                   ← DATOS INICIALES
│   │   ├── DatabaseSeeder.php
│   │   └── CurrencySeeder.php
│   │
│   ├── 📂 factories/                 ← DATOS DE PRUEBA
│   │   └── ProductFactory.php
│   │
│   └── database.sqlite               ← BASE DE DATOS
│
├── 📂 routes/
│   ├── api.php                       ← RUTAS de la API
│   └── web.php                       ← Página principal
│
├── 📂 tests/
│   ├── 📂 Feature/                   ← TESTS de API
│   │   └── ProductApiTest.php
│   │
│   └── 📂 Unit/                      ← TESTS de servicios
│       └── ProductServiceTest.php
│
├── 📂 docs/                          ← DOCUMENTACIÓN
│   ├── postman_collection.json
│   ├── insomnia_collection.json
│   └── API_DOCUMENTATION.md
│
├── 📄 .env                           ← CONFIGURACIÓN
├── 📄 README.md                      ← GUÍA PRINCIPAL
└── 📄 ESTRUCTURA.md                  ← Este archivo
```

---

## 🔍 ¿Qué hace cada carpeta?

### **app/Http/Controllers/Api/V1/**
Los **CONTROLLERS** reciben las peticiones HTTP y devuelven respuestas.
- Solo manejan HTTP
- No tienen lógica de negocio
- Llaman a los Services

### **app/Http/Requests/**
Las **VALIDACIONES** de datos de entrada.
- Validan datos antes de procesarlos
- Mensajes de error personalizados
- Reglas reutilizables

### **app/Http/Resources/**
Las **TRANSFORMACIONES** de respuestas JSON.
- Formato consistente
- Ocultan/muestran campos según contexto
- Incluyen relaciones

### **app/Models/**
Los **MODELOS** Eloquent de base de datos.
- Relaciones entre tablas
- Casts de datos
- Scopes para queries

### **app/Services/**
La **LÓGICA DE NEGOCIO** de la aplicación.
- Orquestan repositorios
- Transacciones de BD
- Cálculos y validaciones complejas

### **app/Repositories/**
El **ACCESO A DATOS** (queries a BD).
- Solo queries
- Sin lógica de negocio
- Implementan interfaces

### **database/migrations/**
La **ESTRUCTURA** de la base de datos.
- Crean tablas
- Definen columnas y relaciones
- Versionan cambios en BD

### **database/seeders/**
Los **DATOS INICIALES** para poblar BD.
- Divisas predefinidas (USD, EUR, GTQ)
- Usuarios de prueba

### **routes/**
Las **RUTAS** de la aplicación.
- `api.php`: Endpoints de la API
- `web.php`: Página principal

### **tests/**
Los **TESTS** automáticos.
- Feature: Prueban endpoints completos
- Unit: Prueban servicios individuales

### **docs/**
La **DOCUMENTACIÓN**.
- Colecciones Postman/Insomnia
- Documentación de API en Markdown

---

## 🎯 Flujo de una Petición

```
1. Cliente HTTP
   ↓
2. Route (routes/api.php)
   ↓
3. Middleware (Sanctum Auth)
   ↓
4. Controller (Http/Controllers/Api/V1/)
   ↓
5. Form Request (Http/Requests/) - Validación
   ↓
6. Service (Services/) - Lógica de negocio
   ↓
7. Repository (Repositories/) - Query a BD
   ↓
8. Model (Models/) - Eloquent ORM
   ↓
9. Database (SQLite)
   ↓
10. Response Resource (Http/Resources/)
    ↓
11. JSON Response al Cliente
```

---

## 📝 Archivos Importantes

| Archivo | Descripción |
|---------|-------------|
| `.env` | Configuración del proyecto |
| `README.md` | Guía completa de instalación |
| `routes/api.php` | Definición de endpoints |
| `database/database.sqlite` | Base de datos |
| `docs/postman_collection.json` | Probar API en Postman |
| `docs/API_DOCUMENTATION.md` | Documentación técnica |

---

## 🚀 Comandos Útiles

```bash
# Ver rutas de la API
php artisan route:list --path=api

# Ejecutar migraciones
php artisan migrate

# Poblar datos iniciales
php artisan db:seed

# Limpiar cachés
php artisan config:clear
php artisan cache:clear

# Ejecutar tests
php artisan test

# Iniciar servidor
php artisan serve
```

---

## 💡 Principios Aplicados

1. **Separación de Responsabilidades** - Cada clase hace UNA cosa
2. **Dependency Injection** - Inyección de dependencias
3. **Repository Pattern** - Abstracción de datos
4. **Service Layer** - Lógica de negocio centralizada
5. **SOLID Principles** - Código mantenible y escalable

---

¿Necesitas modificar algo? Todo está organizado de forma clara y lógica.
