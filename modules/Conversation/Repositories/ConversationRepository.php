<?php

namespace Modules\Conversation\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

use Modules\Conversation\Models\Conversation;

class ConversationRepository
{
    public function findByIdForUser(
        int $id,
        int $userId,
    ): ?Conversation {
        return Conversation::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function getByUser(
        int $userId,
        int $perPage = 20,
    ): LengthAwarePaginator {
        return Conversation::query()
            ->where('user_id', $userId)
            ->with('knowledgeBase:id,name')
            ->withCount('messages')
            ->latest('updated_at')
            ->paginate($perPage);
    }

    public function create(array $data): Conversation
    {
        return Conversation::create($data);
    }

    public function delete(Conversation $conversation): bool
    {
        return $conversation->delete();
    }
}
