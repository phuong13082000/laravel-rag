<?php

namespace Modules\Chunk\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Chunk\Services\ChunkService;
use Modules\Chunk\Repositories\DocumentChunkRepository;
use Illuminate\Support\Facades\Route;

class ChunkServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Chunk';

    protected string $moduleNameLower = 'chunk';

    public function register()
    {
        $this->app->singleton(DocumentChunkRepository::class);

        $this->app->singleton(ChunkService::class, function ($app) {
            return new ChunkService(
                repository: $app->make(DocumentChunkRepository::class)
            );
        });
    }

    public function boot()
    {
        // config
        $this->mergeConfigFrom(
            __DIR__ . '/../Configs/ChunkConfig.php',
            $this->moduleNameLower
        );

        // migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // routes
        Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/../routes/api.php');
    }
}
