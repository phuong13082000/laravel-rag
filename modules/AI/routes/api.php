<?php

use Illuminate\Support\Facades\Route;
use Modules\AI\Http\Controllers\ChatApiController;
use Modules\AI\Http\Controllers\ChatStreamApiController;
use Modules\AI\Http\Controllers\ConversationApiController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post(
        'knowledge-bases/{knowledgeBase}/chat/stream',
        [ChatStreamApiController::class, 'stream'],
    );

    Route::post(
        'knowledge-bases/{knowledgeBase}/chat',
        [ChatApiController::class, 'chat'],
    );

    Route::get(
        'conversations',
        [ConversationApiController::class, 'index'],
    );

    Route::get(
        'conversations/{conversation}',
        [ConversationApiController::class, 'show'],
    );

    Route::get(
        'conversations/{conversation}/messages',
        [ConversationApiController::class, 'messages'],
    );

    Route::delete(
        'conversations/{conversation}',
        [ConversationApiController::class, 'destroy'],
    );
});
