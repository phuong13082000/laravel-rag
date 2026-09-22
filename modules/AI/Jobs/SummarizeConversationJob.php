<?php

namespace Modules\AI\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\AI\Models\Conversation;
use Modules\AI\Services\ConversationSummarizer;

class SummarizeConversationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 600;

    public function __construct(
        private readonly int $conversationId,
    ) {}

    public function handle(
        ConversationSummarizer $summarizer,
    ): void {
        $conversation = Conversation::find(
            $this->conversationId,
        );

        if (!$conversation) {
            return;
        }

        $summarizer->summarizeIfNeeded(
            conversation: $conversation,
        );
    }
}
