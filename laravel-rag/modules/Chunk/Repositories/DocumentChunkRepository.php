<?php

namespace Modules\Chunk\Repositories;

use Modules\Chunk\Models\DocumentChunk;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class DocumentChunkRepository
{
    public function getByDocument(int $documentId): Collection
    {
        return DocumentChunk::query()
            ->where('document_id', $documentId)
            ->orderBy('chunk_index')
            ->get();
    }

    public function create(array $data): DocumentChunk
    {
        return DocumentChunk::create($data);
    }

    public function createMany(array $chunks): void
    {
        if ($chunks === []) return;

        foreach ($chunks as $chunk) {
            DocumentChunk::create($chunk);
        }
    }

    public function deleteByDocument(int $documentId): int
    {
        return DocumentChunk::query()
            ->where('document_id', $documentId)
            ->delete();
    }

    public function updateEmbedding(int $chunkId, array $embedding): void
    {
        $vector = '[' . implode(',', $embedding) . ']';

        DB::statement(
            'UPDATE document_chunks
         SET embedding = ?::vector,
             updated_at = NOW()
         WHERE id = ?',
            [
                $vector,
                $chunkId,
            ],
        );
    }
}
