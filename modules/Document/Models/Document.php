<?php

namespace Modules\Document\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\KnowledgeBase\Models\KnowledgeBase;
use Modules\Chunk\Models\DocumentChunk;

#[Fillable([
    'knowledge_base_id',
    'title',
    'original_name',
    'disk',
    'path',
    'mime_type',
    'size',
    'status',
    'error_message',
])]
class Document extends Model
{
    protected $casts = [
        'size' => 'integer',
    ];

    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(DocumentChunk::class);
    }
}
