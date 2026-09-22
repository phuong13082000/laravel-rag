<?php

use Illuminate\Support\Facades\Route;
use Modules\Message\Http\Controllers\ChatApiController;
use Modules\Message\Http\Controllers\ChatStreamApiController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post(
        'knowledge-bases/{knowledgeBase}/chat/stream',
        [ChatStreamApiController::class, 'stream'],
    );

    Route::post(
        'knowledge-bases/{knowledgeBase}/chat',
        [ChatApiController::class, 'chat'],
    );
});
