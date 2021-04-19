<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->middleware('api')->namespace('App\Http\Controllers\Api\v1')->group(function() {
    Route::prefix('auth')->group(function () {
        Route::post('auth.login', 'AuthController@login')->name('auth.login');

        Route::middleware('auth:api')->group(function() {
            Route::put('auth.create', 'AuthController@create')->name('auth.create');
        });
    });
});
