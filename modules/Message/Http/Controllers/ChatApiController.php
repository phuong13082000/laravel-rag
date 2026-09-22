<?php

namespace Modules\Message\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Message\DTOs\ChatDTO;
use Modules\Message\Http\Requests\ChatRequest;
use Modules\Message\Services\ChatService;

class ChatApiController
{
    public function __construct(
        private readonly ChatService $chatService,
    ) {}

    public function chat(
        ChatRequest $request,
        int $knowledgeBase,
    ): JsonResponse {
        $data = $request->validated();

        $dto = new ChatDTO(
            knowledgeBaseId: $knowledgeBase,
            conversationId: $data['conversation_id'] ?? null,
            message: $data['message'],
            limit: $data['limit'] ?? 5,
            minSimilarity: (float) (
                $data['min_similarity'] ?? 0.3
            ),
        );

        $result = $this->chatService->chat(
            dto: $dto,
            userId: $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => [
                    'id' => $result['conversation']->id,
                    'knowledge_base_id' =>
                    $result['conversation']->knowledge_base_id,
                    'title' => $result['conversation']->title,
                ],

                'user_message' => [
                    'id' => $result['user_message']->id,
                    'role' => $result['user_message']->role->value,
                    'content' => $result['user_message']->content,
                ],

                'assistant_message' => [
                    'id' => $result['assistant_message']->id,
                    'role' =>
                    $result['assistant_message']->role->value,
                    'content' =>
                    $result['assistant_message']->content,

                    'citations' => $result['assistant_message']
                        ->citations
                        ->map(fn($citation) => [
                            'id' => $citation->id,
                            'document_id' =>
                            $citation->document_id,
                            'chunk_id' =>
                            $citation->document_chunk_id,
                            'similarity' =>
                            $citation->similarity,
                            'document_name' =>
                            $citation->document?->original_name,
                            'content' =>
                            $citation->chunk?->content,
                        ])
                        ->values(),
                ],
            ],

            'message' => 'Chat completed successfully.',
        ]);
    }
}
