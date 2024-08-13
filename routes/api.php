<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CustomAuthenticate;


// Protected routes
Route::middleware([CustomAuthenticate::class])->prefix('v1/dashboard')->group(function () {
    Route::apiResource('artikels', ArtikelController::class);
    Route::apiResource('categories', CategoryController::class);
});

Route::prefix('v1/public')->group(function () {
    Route::controller(ArtikelController::class)->group(function () {
        Route::get('artikels/all', 'getAllArtikel');
        Route::get('artikels/{slug}', 'getArtikelBySlug');
        Route::get('artikels/category/{category}', 'getArtikelByCategory');
        Route::get('artikels/query/{query}', 'getArtikelByQuery');
        Route::get('artikels/year/{year}', 'getArtikelByYear');
    });
});

Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('auth/login', 'login');
        Route::get('auth/verify/token', 'verifyToken');
    });
});

// Route::apiResource('artikels', ArtikelController::class);
// Route::apiResource('categories', CategoryController::class);

// Route::post('/login', [AuthController::class, 'login']);
// Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Route::middleware(['customAuthenticate'])->group(function () {
//     Route::resource('artikels', ArtikelController::class);
//     Route::resource('categories', CategoryController::class);

// });

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
