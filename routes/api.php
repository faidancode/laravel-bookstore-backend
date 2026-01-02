<?php

use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{slug}', [BookController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}/books', [CategoryController::class, 'showWithBooks']);

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Add your protected routes here
        // Example: POST, PUT, DELETE operations
    });
});
