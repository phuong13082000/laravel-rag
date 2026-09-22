<?php

namespace Modules\Document\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\KnowledgeBase\Models\KnowledgeBase;

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
}
