<?php

use Illuminate\Support\Facades\Route;
use Modules\Search\Http\Controllers\SearchApiController;

Route::prefix('v1/knowledge-bases')->middleware('auth:sanctum')->group(function () {
    Route::post('{knowledgeBaseId}/search', [SearchApiController::class, 'search']);
});
