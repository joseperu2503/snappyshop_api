<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SnappyTokenController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api')->group(function () {
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
            Route::get('products/filter-data', 'filterData');
            Route::post('products', 'store');
            Route::put('products/{product}', 'update');
            Route::delete('products/{product}', 'destroy');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::get('my-orders', 'myOrders');
            Route::post('orders', 'store');
        });

        Route::controller(CartController::class)->group(function () {
            Route::post('cart', 'store');
            Route::get('my-cart', 'myCart');
        });

        Route::controller(UserController::class)->group(function () {
            Route::post('change-password-internal', 'changePasswordInternal');
            Route::post('change-personal-data', 'changePersonalData');
        });


        Route::controller(FavoriteController::class)->group(function () {
            Route::get('my-favorite-products', 'myFavoriteProducts');
            Route::post('toggle-favorite-product', 'toggleFavorite');
        });

        Route::controller(NotificationController::class)->group(function () {
            Route::get('get-firebase-token', 'getFirebaseToken');
            Route::post('send-notifications', 'sendNotifications');
        });

        Route::controller(SnappyTokenController::class)->group(function () {
            Route::post('snappytoken', 'store');
        });

        Route::controller(CommandController::class)->group(function () {
            Route::get(env('MIGRATION_URL'), 'migration');
        });
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index');
        Route::get('products/{product}', 'show');
    });

    Route::controller(UserController::class)->group(function () {
        Route::post('change-password-external', 'changePasswordExternal');
        Route::post('send-verify-code', 'sendVerifyCode');
        Route::post('validate-verify-code', 'validateVerifyCode');
    });
});
