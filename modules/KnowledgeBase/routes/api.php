<?php

use Illuminate\Support\Facades\Route;
use Modules\KnowledgeBase\Http\Controllers\KnowledgeBaseController;

Route::prefix('v1/knowledge-bases')->group(function () {
    Route::post('', [KnowledgeBaseController::class, 'store']);
    Route::get('', [KnowledgeBaseController::class, 'index']);
    Route::get('{id}', [KnowledgeBaseController::class, 'show']);
    Route::put('{id}', [KnowledgeBaseController::class, 'update']);
    Route::delete('{id}', [KnowledgeBaseController::class, 'destroy']);
});
