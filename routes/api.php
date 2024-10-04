<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;

Route::middleware(['customAuthenticate'])->prefix('v1/dashboard')->group(function () {
    Route::apiResource('artikels', ArtikelController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('designs', DesignController::class);

    Route::prefix('/about')->controller(AboutController::class)->group(function () {
        Route::get('/', 'edit');
        Route::put('/', 'update');
    });

    Route::prefix('/profile')->controller(ProfileController::class)->group(function () {
        Route::get('/{email}', 'profile');
        Route::put('/{email}/update', 'updateProfile');
        Route::put('/{email}/update/password', 'updatePassword');
    });

    Route::prefix('/setting')->controller(SettingsController::class)->group(function () {
        Route::get('/whatsapp-number', 'getWhatsAppNumber');
        Route::post('/whatsapp-number', 'saveWhatsAppNumber');
        Route::get('/featured-image', 'getFeaturedImage');
        Route::post('/featured-image', 'saveFeaturedImage');
    });

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

    Route::prefix('public/services')->controller(ServiceController::class)->group(function () {
        Route::get('/all', 'getAllService');
        Route::get('/{slug}', 'getServiceBySlug');
    });

    Route::prefix('public/designs')->controller(DesignController::class)->group(function () {
        Route::get('/all', 'getAllDesign');
    });

    Route::prefix('setting')->controller(SettingsController::class)->group(function () {
        Route::get('/whatsapp-number', 'getWhatsAppNumber');
        Route::get('/featured-image', 'getFeaturedImage');
    });

    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::get('/verify/token', 'verifyToken');
    });
});
