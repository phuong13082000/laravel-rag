<?php

namespace Modules\AI\Services;

use Modules\AI\Models\Conversation;
use Modules\AI\Repositories\MessageRepository;
use Modules\Search\DTOs\SearchDTO;
use Modules\Search\Services\SearchService;

class RagContextService
{
    public function __construct(
        private readonly SearchService $searchService,
        private readonly MessageRepository $messageRepository,
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

        $history = $this->messageRepository->getRecent(
            conversation: $conversation,
            limit: 10,
        );

        $prompt = $this->promptBuilder->build(
            question: $searchDTO->query,
            chunks: $chunks,
            history: $history,
        );

        return [
            'prompt' => $prompt,
            'chunks' => $chunks,
            'history' => $history,
        ];
    }
}