# 🔑 Cómo Generar un Token de API

## 📝 Opción 1: Usando Tinker Paso a Paso

### 1. Abrir Tinker
```bash
php artisan tinker
```

### 2. Ejecutar esta línea dentro de Tinker:
```php
echo \App\Models\User::first()->createToken('api-token')->plainTextToken;
```

**⚠️IMPORTANTE:** Copia y pega EXACTAMENTE como está arriba. No olvides el `$` antes de las variables.

### 3. Copiar el token
El token se verá así: `1|AbC123XyZ...`

### 4. Salir de Tinker
```
exit
```

---

## Opción 2: Crear un Comando Artisan

Ejecuta en terminal:
```bash
php artisan make:command GenerateApiToken
```

Luego edita el archivo creado, pero por ahora usa la **Opción 2** que es más rápida.

---

## 🎯 Usar el Token

Una vez que tengas el token, úsalo así:

### En cURL:
```bash
curl -X GET "http://localhost:8000/api/v1/currencies" \
  -H "Authorization: Bearer TU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

### En Postman:
1. Import la colección `docs/postman_collection.json`
2. Ve a Variables de la colección
3. Pega el token en la variable `token`
4. ¡Listo! Todos los endpoints funcionarán

### En Insomnia:
1. Import la colección `docs/insomnia_collection.json`
2. Ve a Environment
3. Pega el token en la variable `token`

---

## Nota Importante

Cada vez que ejecutes `php artisan migrate:fresh`, se borrará la base de datos y tendrás que:

1. Ejecutar seeders: `php artisan db:seed`
2. Generar un nuevo token usando cualquiera de las opciones de arriba
