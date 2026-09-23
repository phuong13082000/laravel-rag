<?php

namespace Modules\KnowledgeBase\Dtos;

class CreateKnowledgeBaseDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $name,
        public readonly ?string $description = null,
    ) {}
    
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}