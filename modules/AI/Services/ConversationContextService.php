<?php

namespace Modules\AI\Services;

use Illuminate\Support\Collection;
use Modules\AI\Models\Conversation;
use Modules\AI\Repositories\MessageRepository;

class ConversationContextService
{
    private const RECENT_MESSAGE_LIMIT = 10;

    public function __construct(
        private readonly MessageRepository $messageRepository,
    ) {}

    public function build(
        Conversation $conversation,
    ): array {
        $history = $this->messageRepository->getRecent(
            conversation: $conversation,
            limit: self::RECENT_MESSAGE_LIMIT,
        );

        return [
            'summary' => $conversation->summary,
            'messages' => $history,
        ];
    }
}