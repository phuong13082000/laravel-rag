<?php

use Illuminate\Support\Facades\Route;
use Modules\Document\Http\Controllers\DocumentApiController;

Route::prefix('v1/knowledge-bases')->middleware('auth:sanctum')->group(function () {
    Route::get('{knowledgeBaseId}/documents', [DocumentApiController::class, 'index']);
    Route::post('{knowledgeBaseId}/documents', [DocumentApiController::class, 'store']);
    Route::get('{knowledgeBaseId}/documents/{document}', [DocumentApiController::class, 'show']);
    Route::put('{knowledgeBaseId}/documents/{document}', [DocumentApiController::class, 'update']);
    Route::delete('{knowledgeBaseId}/documents/{document}', [DocumentApiController::class, 'destroy']);
    Route::post('{knowledgeBaseId}/documents/{document}/reprocess', [DocumentApiController::class, 'reprocess']);
});
