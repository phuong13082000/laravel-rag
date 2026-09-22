<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    Modules\KnowledgeBase\Providers\KnowledgeBaseServiceProvider::class,
    Modules\Auth\Providers\AuthServiceProvider::class,
    Modules\Document\Providers\DocumentServiceProvider::class,
    Modules\Chunk\Providers\ChunkServiceProvider::class,
];
