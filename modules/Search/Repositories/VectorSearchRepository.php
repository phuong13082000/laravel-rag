<?php

namespace Modules\Search\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VectorSearchRepository
{
    public function search(
        int $knowledgeBaseId,
        array $embedding,
        int $limit,
        float $minSimilarity = 0.0,
    ): Collection {
        $vector = '[' . implode(',', $embedding) . ']';

        return DB::table('document_chunks')
            ->join(
                'documents',
                'documents.id',
                '=',
                'document_chunks.document_id',
            )
            ->where(
                'documents.knowledge_base_id',
                $knowledgeBaseId,
            )
            ->whereNotNull('document_chunks.embedding')
            ->select([
                'document_chunks.id',
                'document_chunks.document_id',
                'document_chunks.content',
                'document_chunks.chunk_index',
                'document_chunks.token_count',
                'document_chunks.metadata',
                'documents.title as document_title',
                'documents.original_name',
            ])
            ->selectRaw(
                '1 - (document_chunks.embedding <=> ?::vector) AS similarity',
                [$vector],
            )
            ->whereRaw(
                '1 - (document_chunks.embedding <=> ?::vector) >= ?',
                [
                    $vector,
                    $minSimilarity,
                ],
            )
            ->orderByDesc('similarity')
            ->limit($limit)
            ->get();
    }
}
