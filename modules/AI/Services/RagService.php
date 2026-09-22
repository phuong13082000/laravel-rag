<?php

namespace Modules\AI\Services;

use Modules\AI\Contracts\LLMService;
use Modules\AI\Repositories\MessageRepository;
use Modules\Search\DTOs\SearchDTO;
use Modules\Search\Services\SearchService;
use Modules\AI\Models\Conversation;


class RagService
{
    private const string SYSTEM_PROMPT = <<<'PROMPT'
Bạn là một AI assistant dùng để trả lời câu hỏi dựa trên
các tài liệu được cung cấp.

Quy tắc:

1. Chỉ sử dụng thông tin trong CONTEXT.
2. Không tự bịa thông tin.
3. Nếu CONTEXT không đủ thông tin, hãy nói rõ rằng
   không tìm thấy thông tin phù hợp.
4. Trả lời bằng ngôn ngữ phù hợp với câu hỏi.
5. Trả lời ngắn gọn nhưng đầy đủ.
PROMPT;

    public function __construct(
        private readonly SearchService $searchService,
        private readonly LLMService $llmService,
        private readonly RagPromptBuilder $promptBuilder,
        private readonly MessageRepository $messageRepository,
    ) {}

    public function answer(
        SearchDTO $searchDTO,
        int $userId,
        Conversation $conversation,
    ): array {
        $chunks = $this->searchService->search(
            dto: $searchDTO,
            userId: $userId,
        );

        $history = $this->messageRepository->getRecent(
            conversation: $conversation,
            limit: 10,
        );

        $prompt = $this->promptBuilder->build(
            question: $searchDTO->query,
            chunks: $chunks,
            history: $history,
        );

        $answer = $this->llmService->generate(
            prompt: $prompt,
            systemPrompt: self::SYSTEM_PROMPT,
        );

        return [
            'answer' => $answer,
            'chunks' => $chunks,
        ];
    }
}
