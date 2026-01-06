<?php

use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\WishlistController;
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

        Route::prefix('wishlist')->group(function () {

            Route::get('/count', [WishlistController::class, 'count']);
            Route::get('/', [WishlistController::class, 'show']);
            Route::post('/', [WishlistController::class, 'store']);
            Route::delete('/items/{itemId}', [WishlistController::class, 'destroyItem']);
        });

        Route::prefix('orders')->group(function () {
            Route::post('/checkout', [OrderController::class, 'checkout']);
            Route::get('/', [OrderController::class, 'index']);           // List Orders
            Route::get('/{id}', [OrderController::class, 'show']);        // Detail Order
            Route::post('/{id}/cancel', [OrderController::class, 'cancel']); // Cancel
            Route::post('/{id}/complete', [OrderController::class, 'complete']); // Complete
        });

        Route::prefix('addresses')->group(function () {
            Route::get('/', [AddressController::class, 'index']);
            Route::post('/', [AddressController::class, 'store']);
            Route::patch('/{id}', [AddressController::class, 'update']);
            Route::delete('/{id}', [AddressController::class, 'destroy']);
            Route::patch('/{id}/set-primary', [AddressController::class, 'setPrimary']);
        });
    });
});
