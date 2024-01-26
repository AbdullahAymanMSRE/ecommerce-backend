<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTagController;
use Google\Service\Connectors\AuthConfig;
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


Route::get('', function () {
    return '';
})->name('password.reset');


Route::apiResource('products', ProductController::class)->only('index', 'show');
Route::apiResource('tags', ProductTagController::class)->only('index', 'show');

// Will be moved to auth

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('forgot-password', [AuthController::class, 'forgot_password']);
    Route::post('change-password', [AuthController::class, 'change_password'])
        ->middleware('auth:sanctum');

    // Google Auth
    Route::group(['prefix' => 'google/login'], function () {
        Route::get('url', [GoogleController::class, 'getAuthUrl']);
        Route::post('', [GoogleController::class, 'postLogin']);
    });

    // Facebook Auth
    Route::get('facebook/login', [FacebookController::class, 'loginWithFacebook'])->middleware('web');
});


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('auth/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('auth/user-data', [AuthController::class, 'user_data']);

    Route::group(['middleware' => 'admin'], function () {
        Route::apiResource('products', ProductController::class)->only('store', 'destroy',);
        Route::apiResource('tags', ProductTagController::class)->only('store', 'destroy');
    });

    Route::group(['prefix' => 'carts'], function () {
        Route::get('my-cart', [CartController::class, 'myCart']);
        Route::post('add-to-cart', [CartController::class, 'addToCart']);
        Route::post('remove-from-cart', [CartController::class, 'removeFromCart']);
        Route::post('', [CartController::class, 'setCart']);
    });
});
