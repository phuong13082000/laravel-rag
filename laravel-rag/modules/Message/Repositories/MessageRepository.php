<?php

namespace Modules\Message\Repositories;

use Modules\Conversation\Models\Conversation;
use Modules\Message\Models\Message;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MessageRepository
{
    public function create(
        Conversation $conversation,
        string $role,
        string $content,
    ): Message {
        return $conversation->messages()->create([
            'role' => $role,
            'content' => $content,
        ]);
    }

    public function getByConversation(
        Conversation $conversation,
        int $perPage = 50,
    ): LengthAwarePaginator {
        return $conversation
            ->messages()
            ->with([
                'citations.document:id,title,original_name',
                'citations.chunk:id,content,chunk_index',
            ])
            ->oldest('created_at')
            ->paginate($perPage);
    }

    public function getRecent(
        Conversation $conversation,
        int $limit = 10,
    ): Collection {
        return $conversation
            ->messages()
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }
}
