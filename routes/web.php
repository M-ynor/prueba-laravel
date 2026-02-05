<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/docs');
});

Route::get('/login', function () {
    return redirect('/docs');
})->name('login');

Route::get('/docs/postman', function () {
    return response()->download(
        storage_path('app/private/scribe/collection.json'),
        'Products_API_Postman.json'
    );
});

Route::get('/docs/openapi', function () {
    return response()->download(
        storage_path('app/private/scribe/openapi.yaml'),
        'Products_API_OpenAPI.yaml'
    );
});
