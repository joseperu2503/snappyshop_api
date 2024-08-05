<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SeedController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login-google', [AuthController::class, 'loginGoogle']);

    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index');
    });

    Route::controller(StoreController::class)->group(function () {
        Route::get('stores', 'index');
    });

    Route::middleware('auth:api')->group(function () {

        Route::controller(AddressController::class)->group(function () {
            Route::get('addresses/my-addresses', 'myAddresses');
            Route::post('addresses', 'store');
            Route::put('addresses/mark-as-primary/{address}', 'markAsPrimary');
            Route::delete('addresses/{address}', 'destroy');
            Route::get('addresses/{address}', 'show');
        });

        Route::controller(CartController::class)->group(function () {
            Route::post('cart', 'store');
            Route::get('cart/my-cart', 'myCart');
        });

        Route::controller(FavoriteController::class)->group(function () {
            Route::get('products/my-favorite-products', 'myFavoriteProducts');
            Route::post('products/toggle-favorite-product', 'toggleFavorite');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('products/my-products', 'myProducts');
            Route::get('products/form-data', 'formData');
            Route::post('products', 'store');
            Route::put('products/{product}', 'update');
            Route::delete('products/{product}', 'destroy');
        });

        Route::controller(UserController::class)->group(function () {
            Route::post('user/change-password-internal', 'changePasswordInternal');
            Route::post('user/change-personal-data', 'changePersonalData');
            Route::get('user/me', 'me');
        });

        Route::controller(NotificationController::class)->group(function () {
            Route::get('notification/get-firebase-token', 'getFirebaseToken');
            Route::post('notification/send-notifications', 'sendNotifications');
            Route::post('notification/save-device-fcm-token',  'saveDeviceFcmToken');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::get('orders/order-statuses', 'orderStatuses');
            Route::get('orders/my-orders', 'myOrders');
            Route::post('orders', 'store');
            Route::get('orders/{order}', 'show');
        });

        Route::controller(CommandController::class)->group(function () {
            Route::get(env('MIGRATION_URL'), 'migration');
            Route::get(env('MIGRATION_URL') . '_rollback', 'migration_rollback');
            Route::post(env('MIGRATION_URL') . '_command', 'command');
        });

        Route::controller(SeedController::class)->group(function () {
            Route::get('export-data', 'exportData');
            Route::post('import-data', 'importData');
        });
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index');
        Route::get('products/filter-data', 'filterData');
        Route::get('products/{product}', 'show');
    });

    Route::controller(UserController::class)->group(function () {
        Route::post('user/change-password-external', 'changePasswordExternal');
        Route::post('user/send-verify-code', 'sendVerifyCode');
        Route::post('user/validate-verify-code', 'validateVerifyCode');
    });
});
