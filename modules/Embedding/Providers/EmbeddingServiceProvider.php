<?php

namespace Modules\Embedding\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Embedding\Contracts\EmbeddingService;
use Modules\Embedding\Services\OllamaEmbeddingService;

class EmbeddingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmbeddingService::class, OllamaEmbeddingService::class);
    }

    public function boot(): void
    {
        //
    }
}
