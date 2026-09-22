<?php

namespace Modules\Document\Dtos;

class UpdateDocumentDTO
{
    public function __construct(
        public readonly string $title,
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
        ];
    }
}
