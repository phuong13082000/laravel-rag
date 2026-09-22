<?php

namespace Modules\Search\Services;

use Modules\Embedding\Contracts\EmbeddingService;
use Modules\KnowledgeBase\Services\KnowledgeBaseService;
use Modules\Search\DTOs\SearchDTO;
use Modules\Search\Repositories\VectorSearchRepository;

class SearchService
{
    public function __construct(
        private readonly EmbeddingService $embeddingService,
        private readonly VectorSearchRepository $vectorSearchRepository,
        private readonly KnowledgeBaseService $knowledgeBaseService,
    ) {}

    public function search(SearchDTO $dto, int $userId)
    {
        $this->knowledgeBaseService->find(
            $dto->knowledgeBaseId,
            $userId,
        );

        $embedding = $this->embeddingService->embed(
            $dto->query,
        );

        return $this->vectorSearchRepository->search(
            knowledgeBaseId: $dto->knowledgeBaseId,
            embedding: $embedding,
            limit: $dto->limit,
            minSimilarity: $dto->minSimilarity,
        );
    }
}
