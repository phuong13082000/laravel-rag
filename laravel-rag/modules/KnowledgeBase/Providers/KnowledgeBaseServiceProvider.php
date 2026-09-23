<?php

namespace Modules\KnowledgeBase\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\KnowledgeBase\Services\KnowledgeBaseService;
use Modules\KnowledgeBase\Repositories\KnowledgeBaseRepository;

class KnowledgeBaseServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'KnowledgeBase';

    protected string $moduleNameLower = 'knowledgebase';

    public function register(): void
    {
        $this->app->singleton(
            KnowledgeBaseService::class,
            fn($app) => new KnowledgeBaseService(
                $app->make(KnowledgeBaseRepository::class)
            )
        );
    }

    public function boot(): void
    {
        // config
        $this->mergeConfigFrom(
            __DIR__ . '/../Configs/KnowledgeBaseConfig.php',
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
