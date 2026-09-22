<?php

namespace Modules\AI\Repositories;

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
}
