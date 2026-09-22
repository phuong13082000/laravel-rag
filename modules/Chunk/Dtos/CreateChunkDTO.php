<?php

namespace Modules\Chunk\Dtos;

class CreateChunkDTO
{
    public function __construct(
        public readonly int $documentId,
        public readonly string $content,
        public readonly int $chunkIndex,
        public readonly int $tokenCount,
        public readonly ?array $metadata = null,
    ) {}

    public function toArray(): array
    {
        return [
            'document_id' => $this->documentId,
            'content' => $this->content,
            'chunk_index' => $this->chunkIndex,
            'token_count' => $this->tokenCount,
            'metadata' => $this->metadata,
        ];
    }
}