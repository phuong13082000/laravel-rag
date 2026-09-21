<?php

namespace Modules\KnowledgeBase\Dtos;

class UpdateKnowledgeBaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
