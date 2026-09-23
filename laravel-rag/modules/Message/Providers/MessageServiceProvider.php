<?php

namespace Modules\Message\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\AI\Contracts\LLMService;
use Modules\AI\Services\RagContextService;
use Modules\Conversation\Repositories\ConversationRepository;
use Modules\KnowledgeBase\Services\KnowledgeBaseService;
use Modules\Message\Repositories\MessageRepository;
use Modules\Message\Services\ChatService;
use Modules\Citation\Repositories\CitationRepository;
use Modules\AI\Services\RagService;
use Modules\Conversation\Services\ConversationService;
use Modules\Message\Services\StreamingChatService;

class MessageServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Message';

    protected $moduleNameLower = 'message';

    public function register()
    {
        $this->app->singleton(MessageRepository::class);

        $this->app->singleton(ChatService::class, function ($app) {
            return new ChatService(
                knowledgeBaseService: $app->make(KnowledgeBaseService::class),
                conversationRepository: $app->make(ConversationRepository::class),
                messageRepository: $app->make(MessageRepository::class),
                citationRepository: $app->make(CitationRepository::class),
                ragService: $app->make(RagService::class),
            );
        });

        $this->app->singleton(StreamingChatService::class, function ($app) {
            return new StreamingChatService(
                knowledgeBaseService: $app->make(KnowledgeBaseService::class),
                conversationService: $app->make(ConversationService::class),
                messageRepository: $app->make(MessageRepository::class),
                citationRepository: $app->make(CitationRepository::class),
                ragContextService: $app->make(RagContextService::class),
                llmService: $app->make(LLMService::class),
            );
        });
    }

    public function boot()
    {
        // config
        $this->mergeConfigFrom(
            __DIR__ . '/../Configs/MessageConfig.php',
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
