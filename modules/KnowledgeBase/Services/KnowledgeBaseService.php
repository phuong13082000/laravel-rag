<?php

namespace Modules\KnowledgeBase\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\KnowledgeBase\Dtos\CreateKnowledgeBaseDTO;
use Modules\KnowledgeBase\Dtos\UpdateKnowledgeBaseDTO;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\KnowledgeBase\Models\KnowledgeBase;
use Modules\KnowledgeBase\Repositories\KnowledgeBaseRepository;

class KnowledgeBaseService
{
    public function __construct(
        private readonly KnowledgeBaseRepository $knowledgeBaseRepository,
    ) {}

    public function list(int $userId): Collection
    {
        return $this->knowledgeBaseRepository->getByUserId($userId);
    }

    public function create(CreateKnowledgeBaseDTO $dto): KnowledgeBase
    {
        return $this->knowledgeBaseRepository->create($dto->toArray());
    }

    public function find(int $id, int $userId): KnowledgeBase
    {
        $knowledgeBase = $this->knowledgeBaseRepository->findByIdForUser($id, $userId);

        if (!$knowledgeBase) {
            throw new ModelNotFoundException();
        }

        return $knowledgeBase;
    }

    public function update(int $id, int $userId, UpdateKnowledgeBaseDTO $dto): KnowledgeBase
    {
        $knowledgeBase = $this->find($id, $userId);

        return $this->knowledgeBaseRepository->update(
            $knowledgeBase,
            $dto->toArray(),
        );
    }

    public function delete(int $id, int $userId): void
    {
        $knowledgeBase = $this->find($id, $userId);

        $this->knowledgeBaseRepository->delete($knowledgeBase);
    }
}
