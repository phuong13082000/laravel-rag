<?php

use Illuminate\Support\Facades\Route;
use Modules\KnowledgeBase\Http\Controllers\KnowledgeBaseApiController;

Route::prefix('v1/knowledge-bases')->middleware('auth:sanctum')->group(function () {
    Route::get('', [KnowledgeBaseApiController::class, 'index']);
    Route::post('', [KnowledgeBaseApiController::class, 'store']);
    Route::get('{id}', [KnowledgeBaseApiController::class, 'show']);
    Route::put('{id}', [KnowledgeBaseApiController::class, 'update']);
    Route::delete('{id}', [KnowledgeBaseApiController::class, 'destroy']);
});
