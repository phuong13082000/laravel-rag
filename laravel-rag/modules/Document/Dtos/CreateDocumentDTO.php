<?php

namespace Modules\Document\Dtos;

class CreateDocumentDTO
{
    public function __construct(
        public readonly int $knowledgeBaseId,
        public readonly string $title,
        public readonly string $originalName,
        public readonly string $disk,
        public readonly string $path,
        public readonly ?string $mimeType,
        public readonly int $size,
    ) {}

    public function toArray(): array
    {
        return [
            'knowledge_base_id' => $this->knowledgeBaseId,
            'title' => $this->title,
            'original_name' => $this->originalName,
            'disk' => $this->disk,
            'path' => $this->path,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
        ];
    }
}
