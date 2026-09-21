<?php

namespace Modules\KnowledgeBase\Repositories;

use Modules\KnowledgeBase\Models\KnowledgeBase;

class KnowledgeBaseRepository
{
    public function create(array $data): KnowledgeBase
    {
        return KnowledgeBase::create($data);
    }

    public function findById(int $id): ?KnowledgeBase
    {
        return KnowledgeBase::find($id);
    }

    public function delete(KnowledgeBase $knowledgeBase): bool
    {
        return $knowledgeBase->delete();
    }
}