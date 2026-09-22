<?php

namespace Modules\Message\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Modules\Conversation\Models\Conversation;
use Modules\Citation\Models\Citation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Message\Enums\MessageRole;

#[Fillable([
    'conversation_id',
    'role',
    'content',
])]
class Message extends Model
{
    protected $casts = [
        'role' => MessageRole::class,
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function citations(): HasMany
    {
        return $this->hasMany(Citation::class);
    }
}
