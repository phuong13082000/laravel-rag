<?php

namespace Modules\AI\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\AI\Models\Conversation;
use Modules\AI\Repositories\ConversationRepository;
use Modules\AI\Repositories\MessageRepository;

class ConversationService
{
    public function __construct(
        private readonly ConversationRepository $repository,
        private readonly MessageRepository $messageRepository,
    ) {}

    public function list(
        int $userId,
        int $perPage = 20,
    ): LengthAwarePaginator {
        return $this->repository->getByUser(
            userId: $userId,
            perPage: $perPage,
        );
    }

    public function find(
        int $conversationId,
        int $userId,
    ): Conversation {
        $conversation = $this->repository
            ->findByIdForUser(
                id: $conversationId,
                userId: $userId,
            );

        if (!$conversation) {
            abort(
                404,
                'Conversation not found.',
            );
        }

        return $conversation;
    }

    public function messages(
        int $conversationId,
        int $userId,
        int $perPage = 50,
    ): LengthAwarePaginator {
        $conversation = $this->find(
            conversationId: $conversationId,
            userId: $userId,
        );

        return $this->messageRepository
            ->getByConversation(
                conversation: $conversation,
                perPage: $perPage,
            );
    }

    public function delete(
        int $conversationId,
        int $userId,
    ): void {
        $conversation = $this->find(
            conversationId: $conversationId,
            userId: $userId,
        );

        $this->repository->delete($conversation);
    }
}
