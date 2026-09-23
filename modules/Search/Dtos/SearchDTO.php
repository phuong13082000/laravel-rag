<?php

namespace Modules\Search\Dtos;

class SearchDTO
{
    public function __construct(
        public readonly int $knowledgeBaseId,
        public readonly string $query,
        public readonly int $limit = 5,
        public readonly float $minSimilarity = 0.0,
    ) {}
}
