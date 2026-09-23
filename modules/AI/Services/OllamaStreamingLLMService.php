<?php

namespace Modules\AI\Services;

use Illuminate\Support\Facades\Http;
use Modules\AI\Contracts\StreamingLLMService;
use Modules\AI\Exceptions\LLMException;

class OllamaStreamingLLMService implements StreamingLLMService
{
    public function stream(
        string $prompt,
        ?string $systemPrompt,
        callable $onToken,
    ): void {
        $payload = [
            'model' => config('ai.ollama.model_llm'),
            'prompt' => $prompt,
            'stream' => true,
        ];

        if ($systemPrompt !== null) {
            $payload['system'] = $systemPrompt;
        }

        $response = Http::baseUrl(
            config('ai.ollama.base_url'),
        )
            ->timeout(config('ai.ollama.timeout', 600))
            ->withOptions(['stream' => true])
            ->post('/api/generate', $payload);

        if ($response->failed()) {
            throw new LLMException('Ollama streaming request failed: ' . $response->body());
        }

        $body = $response->toPsrResponse()->getBody();

        $buffer = '';

        while (!$body->eof()) {
            $buffer .= $body->read(8192);

            while (($position = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $position);

                $buffer = substr($buffer, $position + 1);

                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                $data = json_decode(
                    $line,
                    true,
                );

                if (!is_array($data)) {
                    continue;
                }

                if (isset($data['error'])) {
                    throw new LLMException($data['error']);
                }

                $token = $data['response'] ?? '';

                if ($token !== '') {
                    $onToken($token);
                }

                if (($data['done'] ?? false) === true) {
                    return;
                }
            }
        }
    }
}
