<?php

namespace Modules\Document\Repositories;

use Modules\Document\Models\Document;
use Modules\Document\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Collection;

class DocumentRepository
{
    public function getByKnowledgeBase(int $knowledgeBaseId): Collection
    {
        return Document::query()
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->latest()
            ->get();
    }

    public function findById(int $id, int $knowledgeBaseId): ?Document
    {
        return Document::query()
            ->where('id', $id)
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->first();
    }

    public function create(array $data): Document
    {
        return Document::create($data);
    }

    public function update(Document $document, array $data): Document
    {
        $document->update($data);
        return $document->refresh();
    }

    public function updateStatus(
        Document $document,
        DocumentStatus $status,
        ?string $errorMessage = null,
    ): Document {
        $document->update([
            'status' => $status,
            'error_message' => $errorMessage,
        ]);

        return $document->refresh();
    }

    public function delete(Document $document): bool
    {
        return $document->delete();
    }
}
