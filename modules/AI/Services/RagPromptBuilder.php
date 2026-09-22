<?php

namespace Modules\AI\Services;

use Illuminate\Support\Collection;

class RagPromptBuilder
{
    public function build(
        string $question,
        Collection $chunks,
        Collection $history,
    ): string {
        $context = $this->buildContext($chunks);
        $historyText = $this->buildHistory($history);

        return <<<PROMPT
Dựa ONLY trên CONTEXT bên dưới để trả lời câu hỏi.

Bạn có thể sử dụng CONVERSATION HISTORY để hiểu
ngữ cảnh của câu hỏi hiện tại.

Nếu CONTEXT không chứa đủ thông tin để trả lời, hãy nói rõ:
"Không tìm thấy thông tin phù hợp trong tài liệu."

Không được tự bịa thông tin.

CONVERSATION HISTORY:
{$historyText}

CONTEXT:
{$context}

QUESTION:
{$question}

ANSWER:
PROMPT;
    }

    private function buildContext(Collection $chunks): string
    {
        if ($chunks->isEmpty()) {
            return '[No relevant context found]';
        }

        $parts = [];

        foreach ($chunks as $index => $chunk) {
            $source = $chunk->original_name ?? 'Unknown document';

            $similarity = isset($chunk->similarity)
                ? number_format((float) $chunk->similarity, 4)
                : 'unknown';

            $parts[] = sprintf(
                "[SOURCE %d]\nDocument: %s\nSimilarity: %s\nContent:\n%s",
                $index + 1,
                $source,
                $similarity,
                $chunk->content,
            );
        }

        return implode("\n\n---\n\n", $parts);
    }

    private function buildHistory(
        Collection $history,
    ): string {
        if ($history->isEmpty()) {
            return '[No conversation history]';
        }

        $parts = [];

        foreach ($history as $message) {
            $parts[] = sprintf(
                '%s: %s',
                strtoupper($message->role->value),
                $message->content,
            );
        }

        return implode("\n", $parts);
    }
}
