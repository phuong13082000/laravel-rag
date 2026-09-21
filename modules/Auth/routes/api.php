<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthApiController;

Route::prefix('v1/auth')->group(function () {
    Route::post('register', [AuthApiController::class, 'register']);
    Route::post('login', [AuthApiController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthApiController::class, 'me']);
        Route::post('logout', [AuthApiController::class, 'logout']);
    });
});