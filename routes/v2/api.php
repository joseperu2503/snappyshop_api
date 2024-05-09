<?php

use App\Http\Controllers\V1\BrandController;
use App\Http\Controllers\V2\AuthController;
use App\Http\Controllers\V1\CartController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\FavoriteController;
use App\Http\Controllers\V2\OrderController;
use App\Http\Controllers\V2\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::prefix('v2')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);


        Route::controller(CategoryController::class)->group(function () {
            Route::get('categories', 'index');
        });

        Route::controller(BrandController::class)->group(function () {
            Route::get('brands', 'index');
        });

        Route::middleware('auth:api')->group(function () {
            Route::get('logout', [AuthController::class, 'logout']);
            Route::get('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);

            Route::controller(ProductController::class)->group(function () {
                Route::get('my-products', 'myProducts');
                Route::get('products/form-data', 'formData');
                Route::post('products', 'store');
                Route::put('products/{product}', 'update');
                Route::delete('products/{product}', 'destroy');
            });

            Route::controller(CartController::class)->group(function () {
                Route::post('cart', 'store');
                Route::get('my-cart', 'myCart');
            });

            Route::controller(FavoriteController::class)->group(function () {
                Route::get('my-favorite-products', 'myFavoriteProducts');
                Route::post('toggle-favorite-product', 'toggleFavorite');
            });

            Route::controller(OrderController::class)->group(function () {
                Route::get('my-orders', 'myOrders');
                Route::post('orders', 'store');
                Route::get('orders/{order}', 'show');
            });
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('products', 'index');
            Route::get('products/filter-data', 'filterData');
            Route::get('products/{product}', 'show');
        });
    });
});
