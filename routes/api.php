<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\ProjectController;


Route::middleware(['customAuthenticate'])->prefix('v1/dashboard')->group(function () {
    Route::apiResource('artikels', ArtikelController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('features', FeatureController::class);
    Route::apiResource('designs', DesignController::class);

    Route::get('/about', [AboutController::class, 'edit']);
    Route::put('/about', [AboutController::class, 'update']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::prefix('v1')->group(function () {
    Route::prefix('public/artikels')->controller(ArtikelController::class)->group(function () {
        Route::get('/all', 'getAllArtikel');
        Route::get('/{slug}', 'getArtikelBySlug');
        Route::get('/category/{category}', 'getArtikelByCategory');
        Route::get('/query/{query}', 'getArtikelByQuery');
        Route::get('/year/{year}', 'getArtikelByYear');
    });

    Route::prefix('public/projects')->controller(ProjectController::class)->group(function () {
        Route::get('/all', 'getAllProject');
        Route::get('/{slug}', 'getProjectBySlug');
    });

    Route::prefix('public/features')->controller(FeatureController::class)->group(function () {
        Route::get('/all', 'getAllFeature');
        Route::get('/{slug}', 'getFeatureBySlug');
    });

    Route::prefix('public/designs')->controller(DesignController::class)->group(function () {
        Route::get('/all', 'getAllDesign');
    });

    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::get('/verify/token', 'verifyToken');
    });
});
