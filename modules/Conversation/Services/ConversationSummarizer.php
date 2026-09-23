<?php

namespace Modules\Conversation\Services;

use Illuminate\Support\Facades\DB;
use Modules\Conversation\Models\Conversation;
use Modules\Message\Repositories\MessageRepository;
use Modules\Conversation\Services\ConversationSummaryService;

class ConversationSummarizer
{
    private const int MESSAGE_THRESHOLD = 20;

    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly ConversationSummaryService $summaryService,
    ) {}

    public function summarizeIfNeeded(
        Conversation $conversation,
    ): void {
        $messageCount = $conversation
            ->messages()
            ->count();

        if ($messageCount < self::MESSAGE_THRESHOLD) {
            return;
        }

        $messages = $this->messageRepository->getRecent(
            conversation: $conversation,
            limit: 20,
        );

        $history = $messages
            ->map(function ($message) {
                return sprintf(
                    '%s: %s',
                    strtoupper($message->role->value),
                    $message->content,
                );
            })
            ->implode("\n");

        $summary = $this->summaryService->summarize(
            conversation: $conversation,
            existingSummary: $conversation->summary ?? '',
            history: $history,
        );

        DB::transaction(function () use (
            $conversation,
            $summary,
        ) {
            $conversation->update([
                'summary' => $summary,
            ]);
        });
    }
}
