<?php

namespace Modules\KnowledgeBase\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

#[Fillable(['user_id', 'name', 'description'])]
class KnowledgeBase extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
