<?php

namespace Modules\Conversation\Services;

use Modules\Conversation\Models\Conversation;
use Modules\Message\Repositories\MessageRepository;

class ConversationContextService
{
    private const int RECENT_MESSAGE_LIMIT = 10;

    public function __construct(
        private readonly MessageRepository $messageRepository,
    ) {}

    public function build(Conversation $conversation): array
    {
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
