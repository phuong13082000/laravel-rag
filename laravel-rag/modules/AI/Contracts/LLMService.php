<?php

namespace Modules\AI\Contracts;

interface LLMService
{
    public function generate(string $prompt, ?string $systemPrompt = null): string;
}