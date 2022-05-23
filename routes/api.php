<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('/login',  'login')->name('api.login');
    Route::post('/register',  'register')->name('api.register');
});


//Auth routes
Route::middleware('auth:sanctum')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::get('/me', 'me')->name("api.me");
        Route::get('/logout', 'logout')->name("api.logout");
    });
});

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to API']);
});
