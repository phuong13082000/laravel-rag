<?php

namespace Modules\Conversation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Conversation\Services\ConversationService;

class ConversationApiController
{
    public function __construct(
        private readonly ConversationService $service,
    ) {}

    public function index(
        Request $request,
    ): JsonResponse {
        $perPage = min(
            (int) $request->input('per_page', 20),
            100,
        );

        $conversations = $this->service->list(
            userId: $request->user()->id,
            perPage: $perPage,
        );

        return response()->json([
            'success' => true,
            'data' => $conversations,
            'message' => 'Conversations retrieved successfully.',
        ]);
    }

    public function show(
        Request $request,
        int $conversation,
    ): JsonResponse {
        $item = $this->service->find(
            conversationId: $conversation,
            userId: $request->user()->id,
        );

        $item->load([
            'knowledgeBase:id,name',
        ]);

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Conversation retrieved successfully.',
        ]);
    }

    public function messages(
        Request $request,
        int $conversation,
    ): JsonResponse {
        $perPage = min(
            (int) $request->input('per_page', 50),
            100,
        );

        $messages = $this->service->messages(
            conversationId: $conversation,
            userId: $request->user()->id,
            perPage: $perPage,
        );

        return response()->json([
            'success' => true,
            'data' => $messages,
            'message' => 'Messages retrieved successfully.',
        ]);
    }

    public function destroy(
        Request $request,
        int $conversation,
    ): JsonResponse {
        $this->service->delete(
            conversationId: $conversation,
            userId: $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Conversation deleted successfully.',
        ]);
    }
}
