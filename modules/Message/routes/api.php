<?php

use Illuminate\Support\Facades\Route;
use Modules\Message\Http\Controllers\ChatApiController;
use Modules\Message\Http\Controllers\ChatStreamApiController;

Route::prefix('v1/knowledge-bases')->middleware('auth:sanctum')->group(function () {
    Route::post('{knowledgeBase}/chat/stream', [ChatStreamApiController::class, 'stream']);
    Route::post('{knowledgeBase}/chat', [ChatApiController::class, 'chat']);
});
