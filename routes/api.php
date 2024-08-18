<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AboutController;


// Protected routes
Route::middleware(['customAuthenticate'])->prefix('v1/dashboard')->group(function () {
    Route::apiResource('artikels', ArtikelController::class);
    Route::apiResource('categories', CategoryController::class);

    Route::get('/about', [AboutController::class, 'edit']);
    Route::put('/about', [AboutController::class, 'update']);
});

Route::prefix('v1')->group(function () {
    Route::prefix('public/artikels')->controller(ArtikelController::class)->group(function () {
        Route::get('/all', 'getAllArtikel');
        Route::get('/{slug}', 'getArtikelBySlug');
        Route::get('/category/{category}', 'getArtikelByCategory');
        Route::get('/query/{query}', 'getArtikelByQuery');
        Route::get('/year/{year}', 'getArtikelByYear');
    });

    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/logout', 'logout');
        Route::get('/verify/token', 'verifyToken');
    });
});
