<?php

namespace Modules\AI\Services;

use Modules\Conversation\Models\Conversation;
use Modules\Conversation\Services\ConversationContextService;
use Modules\AI\Services\RagPromptBuilder;
use Modules\Search\DTOs\SearchDTO;
use Modules\Search\Services\SearchService;

class RagContextService
{
    public function __construct(
        private readonly SearchService $searchService,
        private readonly ConversationContextService $conversationContextService,
        private readonly RagPromptBuilder $promptBuilder,
    ) {}

    public function build(
        SearchDTO $searchDTO,
        int $userId,
        Conversation $conversation,
    ): array {
        $chunks = $this->searchService->search(
            dto: $searchDTO,
            userId: $userId,
        );

        $conversationContext = $this
            ->conversationContextService
            ->build($conversation);

        $prompt = $this->promptBuilder->build(
            question: $searchDTO->query,
            chunks: $chunks,
            history: $conversationContext['messages'],
            summary: $conversationContext['summary'],
        );

        return [
            'prompt' => $prompt,
            'chunks' => $chunks,
            'history' => $conversationContext['messages'],
            'summary' => $conversationContext['summary'],
        ];
    }
}
