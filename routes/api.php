<?php

use App\Http\Controllers\Post\IndexController;
use App\Http\Controllers\Post\StoreController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'posts', 'namespace' => 'Post'], function () {
    Route::group(['prefix' => 'images', 'namespace' => 'Image'], function () {
        Route::post('/', [App\Http\Controllers\Post\Image\StoreController::class, '__invoke']);
    });

    Route::post('/', [StoreController::class, '__invoke']);
    Route::get('/', [IndexController::class, '__invoke']);
});
