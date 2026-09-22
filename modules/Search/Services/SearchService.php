<?php

namespace Modules\Search\Services;

use Modules\Embedding\Contracts\EmbeddingService;
use Modules\Search\DTOs\SearchDTO;
use Modules\Search\Repositories\VectorSearchRepository;

class SearchService
{
    public function __construct(
        private readonly EmbeddingService $embeddingService,
        private readonly VectorSearchRepository $repository,
    ) {}

    public function search(SearchDTO $dto)
    {
        $embedding = $this->embeddingService->embed(
            $dto->query,
        );

        return $this->repository->search(
            knowledgeBaseId: $dto->knowledgeBaseId,
            embedding: $embedding,
            limit: $dto->limit,
            minSimilarity: $dto->minSimilarity,
        );
    }
}
