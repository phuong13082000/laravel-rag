<?php

use Illuminate\Support\Facades\Route;
use Modules\KnowledgeBase\Http\Controllers\KnowledgeBaseController;

Route::prefix('v1/knowledge-bases')->group(function () {
    Route::post('', [KnowledgeBaseController::class, 'store']);
});
