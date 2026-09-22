<?php

namespace Modules\Conversation\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Conversation\Models\Conversation;
use Modules\Conversation\Repositories\ConversationRepository;
use Modules\Message\Repositories\MessageRepository;

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

    public function create(
        int $userId,
        int $knowledgeBaseId,
        string $title,
    ): Conversation {
        return $this->repository->create([
            'user_id' => $userId,
            'knowledge_base_id' => $knowledgeBaseId,
            'title' => \Illuminate\Support\Str::limit(
                $title,
                255,
                '',
            ),
        ]);
    }
}
