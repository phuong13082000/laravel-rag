<?php

namespace Modules\AI\Contracts;

interface StreamingLLMService
{
    public function stream(
        string $prompt,
        ?string $systemPrompt,
        callable $onToken,
    ): void;
}
