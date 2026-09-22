<?php

namespace Modules\Chunk\Repositories;

use Modules\Chunk\Models\DocumentChunk;

class DocumentChunkRepository
{
    public function create(array $data): DocumentChunk
    {
        return DocumentChunk::create($data);
    }

    public function createMany(array $chunks): void
    {
        if ($chunks === []) return;

        DocumentChunk::query()->insert($chunks);
    }

    public function deleteByDocument(int $documentId): int
    {
        return DocumentChunk::query()
            ->where('document_id', $documentId)
            ->delete();
    }
}
