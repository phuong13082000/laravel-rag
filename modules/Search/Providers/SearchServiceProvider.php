<?php

namespace Modules\Search\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Embedding\Contracts\EmbeddingService;
use Modules\KnowledgeBase\Services\KnowledgeBaseService;
use Modules\Search\Repositories\VectorSearchRepository;
use Modules\Search\Services\SearchService;

class SearchServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Search';

    protected $moduleNameLower = 'search';

    public function register()
    {
        $this->app->singleton(VectorSearchRepository::class);

        $this->app->singleton(SearchService::class, function ($app) {
            return new SearchService(
                embeddingService: $app->make(EmbeddingService::class),
                vectorSearchRepository: $app->make(VectorSearchRepository::class),
                knowledgeBaseService: $app->make(KnowledgeBaseService::class),
            );
        });
    }

    public function boot()
    {
        // config
        $this->mergeConfigFrom(
            __DIR__ . '/../Configs/SearchConfig.php',
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
