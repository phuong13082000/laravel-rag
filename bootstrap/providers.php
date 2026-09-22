<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    Modules\KnowledgeBase\Providers\KnowledgeBaseServiceProvider::class,
    Modules\Auth\Providers\AuthServiceProvider::class,
    Modules\Document\Providers\DocumentServiceProvider::class,
    Modules\Chunk\Providers\ChunkServiceProvider::class,
    Modules\Embedding\Providers\EmbeddingServiceProvider::class,
    Modules\Search\Providers\SearchServiceProvider::class,
    Modules\AI\Providers\AIServiceProvider::class,
    Modules\Citation\Providers\CitationServiceProvider::class,
    Modules\Conversation\Providers\ConversationServiceProvider::class,
    Modules\Message\Providers\MessageServiceProvider::class,
];
