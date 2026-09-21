<?php

namespace Modules\KnowledgeBase\Services;

use Modules\KnowledgeBase\Dtos\CreateKnowledgeBaseDTO;
use Modules\KnowledgeBase\Models\KnowledgeBase;
use Modules\KnowledgeBase\Repositories\KnowledgeBaseRepository;

class KnowledgeBaseService
{
    public function __construct(
        private readonly KnowledgeBaseRepository $knowledgeBaseRepository,
    ) {}

    public function create(
        CreateKnowledgeBaseDTO $dto
    ): KnowledgeBase {
        return $this->knowledgeBaseRepository->create(
            $dto->toArray()
        );
    }
}
