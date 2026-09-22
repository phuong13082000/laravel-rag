<?php

use Illuminate\Support\Facades\Route;
use Modules\KnowledgeBase\Http\Controllers\KnowledgeBaseApiController;
use Modules\Document\Http\Controllers\DocumentApiController;

Route::prefix('v1/knowledge-bases')->middleware('auth:sanctum')->group(function () {
    Route::get('', [KnowledgeBaseApiController::class, 'index']);
    Route::post('', [KnowledgeBaseApiController::class, 'store']);
    Route::get('{id}', [KnowledgeBaseApiController::class, 'show']);
    Route::put('{id}', [KnowledgeBaseApiController::class, 'update']);
    Route::delete('{id}', [KnowledgeBaseApiController::class, 'destroy']);

    Route::prefix('{knowledgeBaseId}/documents')->group(function () {
        Route::get('', [DocumentApiController::class, 'index']);
        Route::post('', [DocumentApiController::class, 'store']);
        Route::get('{document}', [DocumentApiController::class, 'show']);
        Route::put('{document}', [DocumentApiController::class, 'update']);
        Route::delete('{document}', [DocumentApiController::class, 'destroy']);
        Route::post('{document}/reprocess', [DocumentApiController::class, 'reprocess']);
    });
});
