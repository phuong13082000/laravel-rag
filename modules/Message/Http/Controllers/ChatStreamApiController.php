<?php

namespace Modules\Message\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Message\DTOs\ChatDTO;
use Modules\Message\Services\StreamingChatService;

class ChatStreamApiController
{
    public function __construct(
        private readonly StreamingChatService $chatService,
    ) {}

    public function stream(Request $request, int $knowledgeBase)
    {
        $data = $request->validate([
            'conversation_id' => [
                'nullable',
                'integer',
            ],

            'message' => [
                'required',
                'string',
                'min:1',
                'max:5000',
            ],

            'limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],

            'min_similarity' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1',
            ],
        ]);

        $dto = new ChatDTO(
            knowledgeBaseId: $knowledgeBase,
            conversationId: $data['conversation_id'] ?? null,
            message: $data['message'],
            limit: $data['limit'] ?? 5,
            minSimilarity: (float) (
                $data['min_similarity'] ?? 0.3
            ),
        );

        $context = $this->chatService->prepare(
            dto: $dto,
            userId: $request->user()->id,
        );

        return response()->stream(
            function () use ($context) {
                $send = function (
                    string $event,
                    array $data,
                ): void {
                    echo "event: {$event}\n";
                    echo 'data: '
                        . json_encode(
                            $data,
                            JSON_UNESCAPED_UNICODE,
                        )
                        . "\n\n";

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }

                    flush();
                };

                $send('conversation', [
                    'id' =>
                    $context['conversation']->id,
                ]);

                $send('message.start', [
                    'user_message_id' =>
                    $context['user_message']->id,
                ]);

                $answer = '';

                try {
                    $answer = $this->chatService->stream(
                        context: $context,
                        onToken: function (
                            string $token,
                        ) use ($send): void {
                            $send('token', [
                                'content' => $token,
                            ]);
                        },
                    );

                    $result = $this->chatService->complete(
                        context: $context,
                        answer: $answer,
                    );

                    $send('message.complete', [
                        'assistant_message_id' =>
                        $result['assistant_message']->id,
                    ]);

                    $send('done', [
                        'success' => true,
                    ]);
                } catch (\Throwable $e) {
                    $send('error', [
                        'message' => $e->getMessage(),
                    ]);
                }
            },
            200,
            [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ],
        );
    }
}
