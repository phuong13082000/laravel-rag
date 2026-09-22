<?php

namespace Modules\Conversation\Services;

use Modules\AI\Contracts\LLMService;
use Modules\Conversation\Models\Conversation;

class ConversationSummaryService
{
    public function __construct(
        private readonly LLMService $llmService,
    ) {}

    public function summarize(
        Conversation $conversation,
        string $existingSummary,
        string $history,
    ): string {
        $prompt = <<<PROMPT
Hãy tóm tắt cuộc hội thoại dưới đây.

Mục tiêu:
- Giữ lại các thông tin quan trọng.
- Giữ lại những quyết định hoặc yêu cầu của người dùng.
- Giữ lại các chủ đề đang được thảo luận.
- Không tự thêm thông tin.
- Viết ngắn gọn.
- Không cần ghi tên User/Assistant.

SUMMARY HIỆN TẠI:
{$existingSummary}

HỘI THOẠI:
{$history}

TÓM TẮT:
PROMPT;

        return $this->llmService->generate(
            prompt: $prompt,
            systemPrompt: <<<SYSTEM
Bạn là conversation summarizer.

Chỉ tóm tắt thông tin đã xuất hiện trong
conversation.

Không được suy diễn hoặc thêm thông tin mới.
SYSTEM,
        );
    }
}
