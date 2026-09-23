<?php

namespace Modules\AI\Services;

use Modules\AI\Contracts\LLMService;
use Modules\AI\Exceptions\LLMException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class OllamaLLMService implements LLMService
{
    public function generate(string $prompt, ?string $systemPrompt = null): string
    {
        $payload = [
            'model' => config('ai.ollama.model_llm'),
            'prompt' => $prompt,
            'stream' => false,
        ];

        if ($systemPrompt !== null) {
            $payload['system'] = $systemPrompt;
        }

        try {
            $response = Http::baseUrl(config('ai.ollama.base_url'))
                ->timeout(config('ai.ollama.timeout', 600))
                ->acceptJson()
                ->post('/api/generate', $payload);
        } catch (ConnectionException $e) {
            throw new LLMException('Unable to connect to Ollama.', previous: $e);
        }

        if ($response->failed()) {
            throw new LLMException('Ollama generation failed: ' . $response->body());
        }

        $result = $response->json('response');

        if (!is_string($result) || trim($result) === '') {
            throw new LLMException('Ollama returned an empty response.');
        }

        return trim($result);
    }
}
