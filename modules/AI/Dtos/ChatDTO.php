<?php

namespace Modules\AI\DTOs;

class ChatDTO
{
    public function __construct(
        public readonly int $knowledgeBaseId,
        public readonly ?int $conversationId,
        public readonly string $message,
        public readonly int $limit = 5,
        public readonly float $minSimilarity = 0.3,
    ) {}
}
