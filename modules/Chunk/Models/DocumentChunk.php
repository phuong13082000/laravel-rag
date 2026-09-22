<?php

namespace Modules\Chunk\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Document\Models\Document;

#[Fillable(['document_id', 'content', 'chunk_index', 'token_count', 'metadata'])]
class DocumentChunk extends Model
{
    protected $casts = [
        'chunk_index' => 'integer',
        'token_count' => 'integer',
        'metadata' => 'array',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
