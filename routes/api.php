<?php

use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{slug}', [BookController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}/books', [CategoryController::class, 'showWithBooks']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('cart')->group(function () {

            Route::get('/count', [CartController::class, 'count']);
            Route::get('/', [CartController::class, 'show']);
            Route::post('/', [CartController::class, 'store']);
            Route::patch('/items/{itemId}', [CartController::class, 'updateItem']);
            Route::post('/items/{itemId}/decrement', [CartController::class, 'decrement']);
            Route::delete('/items/{itemId}', [CartController::class, 'destroyItem']);
        });
    });
});
