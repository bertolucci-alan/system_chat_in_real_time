<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Api\UserController;


Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    Route::get('/dashboard', [PageController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/chat', [PageController::class, 'chat'])
        ->name('chat');
});