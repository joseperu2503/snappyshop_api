<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
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
    Route::post('login', [AuthController::class,'login']);
    Route::post('register', [AuthController::class,'register']);

    Route::middleware('auth:api')->group(function () {
        Route::get('logout', [AuthController::class,'logout']);
        Route::get('refresh', [AuthController::class,'refresh']);
        Route::get('me', [AuthController::class,'me']);

        Route::get('products',[ProductController::class,'index']);
        Route::post('products',[ProductController::class,'store']);
        Route::get('products/{product}',[ProductController::class,'show']);
        Route::put('products/{product}',[ProductController::class,'update']);
        Route::delete('products/{product}',[ProductController::class,'destroy']);
    });
});
