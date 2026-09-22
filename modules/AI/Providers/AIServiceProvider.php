<?php

namespace Modules\AI\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\AI\Contracts\LLMService;
use Modules\AI\Contracts\StreamingLLMService;
use Modules\AI\Services\OllamaLLMService;
use Modules\AI\Services\OllamaStreamingLLMService;
use Modules\AI\Services\RagPromptBuilder;
use Modules\AI\Services\RagContextService;
use Modules\AI\Services\RagService;

class AIServiceProvider extends ServiceProvider
{
    protected $moduleName = 'AI';

    protected $moduleNameLower = 'ai';

    public function register()
    {
        $this->app->singleton(RagService::class, function ($app) {
            return new RagService(
                contextService: $app->make(RagContextService::class),
                llmService: $app->make(LLMService::class),
            );
        });
        
        $this->app->singleton(RagPromptBuilder::class);
        $this->app->singleton(
            LLMService::class,
            OllamaLLMService::class,
        );
        $this->app->singleton(
            StreamingLLMService::class,
            OllamaStreamingLLMService::class,
        );
    }

    public function boot()
    {
        // config
        $this->mergeConfigFrom(
            __DIR__ . '/../Configs/AIConfig.php',
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
