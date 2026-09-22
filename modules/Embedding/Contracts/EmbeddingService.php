<?php

namespace Modules\Embedding\Contracts;

interface EmbeddingService
{
    /**
     * @return array<float>
     */
    public function embed(string $text): array;

    /**
     * @param array<string> $texts
     * @return array<int, array<float>>
     */
    public function embedMany(array $texts): array;
}
