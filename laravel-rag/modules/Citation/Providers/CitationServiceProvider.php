<?php

namespace Modules\Citation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Citation\Services\CitationService;
use Modules\Citation\Repositories\CitationRepository;
use Illuminate\Support\Facades\Route;

class CitationServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Citation';

    protected $moduleNameLower = 'citation';

    public function register()
    {
        $this->app->singleton(CitationRepository::class);

        $this->app->singleton(
            CitationService::class,
            function ($app) {
                return new CitationService(
                    citationRepository: $app->make(CitationRepository::class),
                );
            }
        );
    }

    public function boot()
    {
        // config
        $this->mergeConfigFrom(
            __DIR__ . '/../Configs/CitationConfig.php',
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
