<?php

use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
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

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('user/authenticated', [UserController::class, 'authenticated'])
        ->name('user.authenticated');

    Route::get('/users', [UserController::class, 'index'])
        ->name('user.index');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('user.show');

    Route::get('/messages/{user}', [MessageController::class, 'list'])
        ->name('message.list');

    Route::post('/messages/store', [MessageController::class, 'store'])
        ->name('message.store');
});