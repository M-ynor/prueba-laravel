# How to Generate an API Token

## Option 1: Using Tinker Step by Step

### 1. Open Tinker
```bash
php artisan tinker
```

### 2. Run this line inside Tinker:
```php
echo \App\Models\User::first()->createToken('api-token')->plainTextToken;
```

**IMPORTANT:** Copy and paste EXACTLY as above. Do not forget the `$` before variables if you assign to one.

### 3. Copy the token
The token will look like: `1|AbC123XyZ...`

### 4. Exit Tinker
```
exit
```

---

## Option 2: One-liner from terminal

```bash
php artisan tinker --execute="echo \App\Models\User::first()->createToken('api-token')->plainTextToken;"
```

---

## Using the Token

Once you have the token, use it like this:

### With cURL:
```bash
curl -X GET "http://localhost:8000/api/v1/currencies" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### In Postman:
1. Import the collection `docs/postman_collection.json` or download from `/docs/postman`
2. Go to Collection variables
3. Paste the token in the `token` variable
4. All endpoints will work

### In Insomnia:
1. Import the collection `docs/insomnia_collection.json`
2. Go to Environment
3. Paste the token in the `token` variable

---

## Important Note

Whenever you run `php artisan migrate:fresh`, the database will be wiped and you will need to:

1. Run seeders: `php artisan db:seed`
2. Generate a new token using any of the options above
