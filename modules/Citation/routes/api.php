<?php

use Illuminate\Support\Facades\Route;
use Modules\Citation\Http\Controllers\CitationApiController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('messages/{message}/citations', [CitationApiController::class, 'getByMessage']);
    Route::get('citations/{citation}', [CitationApiController::class, 'find']);
});
