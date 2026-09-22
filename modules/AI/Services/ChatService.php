<?php

namespace Modules\AI\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\AI\DTOs\ChatDTO;
use Modules\AI\Enums\MessageRole;
use Modules\AI\Models\Conversation;
use Modules\AI\Repositories\CitationRepository;
use Modules\AI\Repositories\ConversationRepository;
use Modules\AI\Repositories\MessageRepository;
use Modules\KnowledgeBase\Services\KnowledgeBaseService;
use Modules\Search\DTOs\SearchDTO;

class ChatService
{
    public function __construct(
        private readonly KnowledgeBaseService $knowledgeBaseService,
        private readonly ConversationRepository $conversationRepository,
        private readonly MessageRepository $messageRepository,
        private readonly CitationRepository $citationRepository,
        private readonly RagService $ragService,
    ) {}

    public function chat(ChatDTO $dto, int $userId): array
    {
        // Verify knowledge base ownership.
        $this->knowledgeBaseService->find($dto->knowledgeBaseId, $userId);

        /*
         * Database transaction only for the initial
         * conversation + user message.
         *
         * Do NOT keep a transaction open while calling Ollama.
         */
        [$conversation, $userMessage] = DB::transaction(
            function () use ($dto, $userId) {
                $conversation = $this->resolveConversation(
                    dto: $dto,
                    userId: $userId,
                );

                $userMessage = $this->messageRepository->create(
                    conversation: $conversation,
                    role: MessageRole::USER->value,
                    content: $dto->message,
                );

                return [
                    $conversation,
                    $userMessage,
                ];
            },
        );

        /*
         * RAG + Ollama.
         *
         * This happens outside the transaction because
         * the LLM request can take several seconds.
         */
        $result = $this->ragService->answer(
            searchDTO: new SearchDTO(
                knowledgeBaseId: $dto->knowledgeBaseId,
                query: $dto->message,
                limit: $dto->limit,
                minSimilarity: $dto->minSimilarity,
            ),
            userId: $userId,
            conversation: $conversation,
        );

        /*
         * Store assistant message + citations.
         */
        $assistantMessage = DB::transaction(
            function () use ($conversation, $result) {
                $message = $this->messageRepository->create(
                    conversation: $conversation,
                    role: MessageRole::ASSISTANT->value,
                    content: $result['answer'],
                );

                $this->citationRepository->createMany(
                    message: $message,
                    chunks: $result['chunks'],
                );

                return $message;
            },
        );

        $assistantMessage->load([
            'citations.document',
            'citations.chunk',
        ]);

        return [
            'conversation' => $conversation,
            'user_message' => $userMessage,
            'assistant_message' => $assistantMessage,
        ];
    }

    private function resolveConversation(ChatDTO $dto, int $userId): Conversation
    {
        if ($dto->conversationId !== null) {
            $conversation = $this->conversationRepository
                ->findByIdForUser(
                    id: $dto->conversationId,
                    userId: $userId,
                );

            if (!$conversation) {
                abort(404, 'Conversation not found.');
            }

            if ($conversation->knowledge_base_id !== $dto->knowledgeBaseId) {
                abort(422, 'Conversation does not belong to this knowledge base.');
            }

            return $conversation;
        }

        return $this->conversationRepository->create([
            'user_id' => $userId,
            'knowledge_base_id' => $dto->knowledgeBaseId,
            'title' => Str::limit($dto->message, 255, ''),
        ]);
    }
}
