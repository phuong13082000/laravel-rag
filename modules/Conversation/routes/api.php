<?php

use Illuminate\Support\Facades\Route;
use Modules\Conversation\Http\Controllers\ConversationApiController;

Route::prefix('v1/conversations')->middleware('auth:sanctum')->group(function () {
    Route::get('', [ConversationApiController::class, 'index']);
    Route::get('{conversation}', [ConversationApiController::class, 'show']);
    Route::get('{conversation}/messages', [ConversationApiController::class, 'messages']);
    Route::delete('{conversation}', [ConversationApiController::class, 'destroy']);
});
