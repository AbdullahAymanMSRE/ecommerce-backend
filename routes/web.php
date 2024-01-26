<?php

use App\Http\Controllers\FacebookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth/facebook'], function () {
    Route::get('', [FacebookController::class, 'facebookRedirect']);
    // Route::get('callback', [FacebookController::class, 'loginWithFacebook']);
});

Route::get('/', function () {
    return view('welcome');
});
