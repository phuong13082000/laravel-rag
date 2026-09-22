<?php

namespace Modules\AI\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\KnowledgeBase\Models\KnowledgeBase;
use Modules\Message\Models\Message;
use Modules\User\Models\User;

#[Fillable([
    'user_id',
    'knowledge_base_id',
    'title',
])]
class Conversation extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
