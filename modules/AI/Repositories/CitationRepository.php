<?php

namespace Modules\AI\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\AI\Models\Citation;
use Modules\AI\Models\Message;

class CitationRepository
{
    public function createMany(
        Message $message,
        iterable $chunks,
    ): void {
        foreach ($chunks as $chunk) {
            Citation::create([
                'message_id' => $message->id,
                'document_id' => $chunk->document_id,
                'document_chunk_id' => $chunk->id,
                'similarity' => (float) $chunk->similarity,
            ]);
        }
    }

    public function getByMessageForUser(
        int $messageId,
        int $userId,
    ): Collection {
        return Citation::query()
            ->where('message_id', $messageId)
            ->whereHas(
                'message.conversation',
                function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
            )
            ->with([
                'document:id,title,original_name',
                'chunk:id,content,chunk_index',
            ])
            ->orderByDesc('similarity')
            ->get();
    }

    public function findByIdForUser(
        int $citationId,
        int $userId,
    ): ?Citation {
        return Citation::query()
            ->whereKey($citationId)
            ->whereHas(
                'message.conversation',
                function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
            )
            ->with([
                'message:id,conversation_id,role,content',
                'document:id,title,original_name',
                'chunk:id,document_id,content,chunk_index',
            ])
            ->first();
    }
}
