<?php

namespace Modules\Embedding\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Modules\Embedding\Contracts\EmbeddingService;
use Modules\Embedding\Exceptions\EmbeddingException;

class OllamaEmbeddingService implements EmbeddingService
{
    private const int DIMENSIONS = 1024; // BGE-M3 (vector size)

    public function embed(string $text): array
    {
        $embeddings = $this->request([$text]);

        if (!isset($embeddings[0])) {
            throw new EmbeddingException(
                'Ollama did not return an embedding.',
            );
        }

        return $embeddings[0];
    }

    public function embedMany(array $texts): array
    {
        if ($texts === []) {
            return [];
        }

        return $this->request($texts);
    }

    private function request(array $texts): array
    {
        try {
            $response = Http::baseUrl(
                config('services.ollama.base_url'),
            )
                ->timeout(300)
                ->acceptJson()
                ->post('/api/embed', [
                    'model' => config(
                        'services.ollama.embedding_model',
                    ),
                    'input' => array_values($texts),
                ]);
        } catch (ConnectionException $e) {
            throw new EmbeddingException(
                'Unable to connect to Ollama.',
                previous: $e,
            );
        }

        if ($response->failed()) {
            throw new EmbeddingException(
                'Ollama embedding request failed: '
                    . $response->body(),
            );
        }

        $embeddings = $response->json('embeddings');

        if (!is_array($embeddings)) {
            throw new EmbeddingException(
                'Invalid embedding response from Ollama.',
            );
        }

        foreach ($embeddings as $embedding) {
            if (!is_array($embedding)) {
                throw new EmbeddingException(
                    'Invalid embedding vector.',
                );
            }

            if (count($embedding) !== self::DIMENSIONS) {
                throw new EmbeddingException(
                    sprintf(
                        'Invalid embedding dimension. Expected %d, got %d.',
                        self::DIMENSIONS,
                        count($embedding),
                    ),
                );
            }
        }

        return $embeddings;
    }
}
