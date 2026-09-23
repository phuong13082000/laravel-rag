<?php

namespace Modules\Message\Services;

use Illuminate\Support\Facades\DB;
use Modules\AI\Contracts\StreamingLLMService;
use Modules\Message\DTOs\ChatDTO;
use Modules\Message\Enums\MessageRole;
use Modules\Conversation\Jobs\SummarizeConversationJob;
use Modules\Conversation\Models\Conversation;
use Modules\Citation\Repositories\CitationRepository;
use Modules\Message\Repositories\MessageRepository;
use Modules\KnowledgeBase\Services\KnowledgeBaseService;
use Modules\AI\Services\RagContextService;
use Modules\Search\DTOs\SearchDTO;
use Modules\Conversation\Services\ConversationService;

class StreamingChatService
{
    private const string SYSTEM_PROMPT = <<<'PROMPT'
Bạn là một AI assistant dùng để trả lời câu hỏi dựa trên
các tài liệu được cung cấp.

Quy tắc:

1. Chỉ sử dụng thông tin trong CONTEXT.
2. Có thể sử dụng CONVERSATION HISTORY để hiểu ngữ cảnh.
3. Không tự bịa thông tin.
4. Nếu CONTEXT không đủ thông tin, hãy nói rõ rằng
   không tìm thấy thông tin phù hợp trong tài liệu.
5. Trả lời bằng ngôn ngữ phù hợp với câu hỏi.
6. Trả lời ngắn gọn nhưng đầy đủ.
PROMPT;

    public function __construct(
        private readonly KnowledgeBaseService $knowledgeBaseService,
        private readonly ConversationService $conversationService,
        private readonly MessageRepository $messageRepository,
        private readonly CitationRepository $citationRepository,
        private readonly RagContextService $ragContextService,
        private readonly StreamingLLMService $llmService,
    ) {}

    public function prepare(
        ChatDTO $dto,
        int $userId,
    ): array {
        $this->knowledgeBaseService->find(
            id: $dto->knowledgeBaseId,
            userId: $userId,
        );

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

        $context = $this->ragContextService->build(
            searchDTO: new SearchDTO(
                knowledgeBaseId: $dto->knowledgeBaseId,
                query: $dto->message,
                limit: $dto->limit,
                minSimilarity: $dto->minSimilarity,
            ),
            userId: $userId,
            conversation: $conversation,
        );

        return [
            'conversation' => $conversation,
            'user_message' => $userMessage,
            'prompt' => $context['prompt'],
            'chunks' => $context['chunks'],
        ];
    }

    public function stream(
        array $context,
        callable $onToken,
    ): string {
        $answer = '';

        $this->llmService->stream(
            prompt: $context['prompt'],
            systemPrompt: self::SYSTEM_PROMPT,
            onToken: function (string $token) use (
                &$answer,
                $onToken,
            ): void {
                $answer .= $token;

                $onToken($token);
            },
        );

        return $answer;
    }

    public function complete(
        array $context,
        string $answer,
    ): array {
        $assistantMessage = DB::transaction(
            function () use ($context, $answer) {
                $message = $this->messageRepository->create(
                    conversation: $context['conversation'],
                    role: MessageRole::ASSISTANT->value,
                    content: $answer,
                );

                $this->citationRepository->createMany(
                    message: $message,
                    chunks: $context['chunks'],
                );

                return $message;
            },
        );

        $assistantMessage->load([
            'citations.document',
            'citations.chunk',
        ]);

        SummarizeConversationJob::dispatch(
            $context['conversation']->id,
        );

        return [
            'conversation' => $context['conversation'],
            'user_message' => $context['user_message'],
            'assistant_message' => $assistantMessage,
        ];
    }

    private function resolveConversation(ChatDTO $dto, int $userId): Conversation
    {
        if ($dto->conversationId !== null) {
            $conversation = $this->conversationService->find(
                conversationId: $dto->conversationId,
                userId: $userId,
            );

            if ($conversation->knowledge_base_id !== $dto->knowledgeBaseId) {
                abort(422, 'Conversation does not belong to this knowledge base.');
            }

            return $conversation;
        }

        return $this->conversationService->create(
            userId: $userId,
            knowledgeBaseId: $dto->knowledgeBaseId,
            title: $dto->message,
        );
    }
}
