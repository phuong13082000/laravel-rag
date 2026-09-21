<?php

namespace Modules\KnowledgeBase\Repositories;

use Illuminate\Support\Collection;
use Modules\KnowledgeBase\Models\KnowledgeBase;

class KnowledgeBaseRepository
{
    public function findById(int $id): ?KnowledgeBase
    {
        return KnowledgeBase::find($id);
    }

    public function getByUserId(int $userId): Collection
    {
        return KnowledgeBase::where('user_id', $userId)->latest()->get();
    }

    public function findByIdForUser(int $id, int $userId): ?KnowledgeBase
    {
        return KnowledgeBase::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function create(array $data): KnowledgeBase
    {
        return KnowledgeBase::create($data);
    }

    public function update(KnowledgeBase $knowledgeBase, array $data): KnowledgeBase
    {
        $knowledgeBase->update($data);

        return $knowledgeBase->refresh();
    }

    public function delete(KnowledgeBase $knowledgeBase): bool
    {
        return $knowledgeBase->delete();
    }
}
