<?php

use Illuminate\Support\Facades\Route;
use Modules\Document\Http\Controllers\DocumentApiController;

Route::prefix('v1')->group(function () {
    Route::prefix('knowledge-bases/{knowledgeBaseId}')
        ->group(function () {
            Route::get(
                'documents',
                [DocumentApiController::class, 'index'],
            );

            Route::post(
                'documents',
                [DocumentApiController::class, 'store'],
            );

            Route::get(
                'documents/{document}',
                [DocumentApiController::class, 'show'],
            );

            Route::put(
                'documents/{document}',
                [DocumentApiController::class, 'update'],
            );

            Route::delete(
                'documents/{document}',
                [DocumentApiController::class, 'destroy'],
            );

            Route::post(
                'documents/{document}/reprocess',
                [DocumentApiController::class, 'reprocess'],
            );
        });
});
