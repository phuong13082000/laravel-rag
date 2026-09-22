<?php

use Illuminate\Support\Facades\Route;
use Modules\Conversation\Http\Controllers\ConversationApiController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
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
