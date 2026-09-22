<?php

namespace Modules\Conversation\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ConversationServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Conversation';

    protected $moduleNameLower = 'conversation';

    public function register()
    {

    }

    public function boot()
    {
        // config
        $this->mergeConfigFrom(
            __DIR__ . '/../Configs/ConversationConfig.php',
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
