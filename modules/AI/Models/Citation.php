<?php

namespace Modules\AI\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Chunk\Models\DocumentChunk;
use Modules\Document\Models\Document;

#[Fillable([
    'message_id',
    'document_id',
    'document_chunk_id',
    'similarity',
])]
class Citation extends Model
{
    protected $casts = [
        'similarity' => 'float',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(
            Message::class,
        );
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(
            Document::class,
        );
    }

    public function chunk(): BelongsTo
    {
        return $this->belongsTo(
            DocumentChunk::class,
            'document_chunk_id',
        );
    }
}
