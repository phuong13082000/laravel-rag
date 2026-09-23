<?php

namespace Modules\Document\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Document\Services\DocumentService;
use Modules\Document\Services\DocumentTextExtractorService;
use Modules\KnowledgeBase\Services\KnowledgeBaseService;
use Modules\Document\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Route;

class DocumentServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Document';

    protected string $moduleNameLower = 'document';

    public function register()
    {
        $this->app->singleton(DocumentRepository::class);
        $this->app->singleton(KnowledgeBaseService::class);

        $this->app->singleton(DocumentService::class, function ($app) {
            return new DocumentService(
                repository: $app->make(DocumentRepository::class),
                knowledgeBaseService: $app->make(KnowledgeBaseService::class)
            );
        });

        $this->app->singleton(DocumentTextExtractorService::class);
    }

    public function boot()
    {
        // config
        $this->mergeConfigFrom(
            __DIR__ . '/../Configs/DocumentConfig.php',
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
