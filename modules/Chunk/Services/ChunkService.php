<?php

namespace Modules\Chunk\Services;

use Modules\Chunk\Dtos\CreateChunkDTO;
use Modules\Chunk\Repositories\DocumentChunkRepository;
use Modules\Document\Models\Document;

class ChunkService
{
    private const int CHUNK_SIZE = 1000;

    private const int CHUNK_OVERLAP = 150;

    public function __construct(
        private readonly DocumentChunkRepository $repository,
    ) {}

    public function chunk(Document $document, string $text): void
    {
        $this->repository->deleteByDocument($document->id);

        $chunks = $this->split($text);

        $data = [];

        foreach ($chunks as $index => $content) {
            $content = trim($content);

            if ($content === '') {
                continue;
            }

            $dto = new CreateChunkDTO(
                documentId: $document->id,
                content: $content,
                chunkIndex: $index,
                tokenCount: $this->estimateTokenCount($content),
                metadata: [
                    'character_count' => mb_strlen($content),
                ],
            );

            $data[] = $dto->toArray();
        }

        $this->repository->createMany($data);
    }

    /**
     * @return array<int, string>
     */
    private function split(string $text): array
    {
        $paragraphs = preg_split(
            '/\R{2,}/u',
            trim($text),
            -1,
            PREG_SPLIT_NO_EMPTY,
        );

        if (!$paragraphs) {
            return [];
        }

        $chunks = [];

        $current = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            $candidate = $current === ''
                ? $paragraph
                : $current . "\n\n" . $paragraph;

            if (mb_strlen($candidate) <= self::CHUNK_SIZE) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $chunks[] = $current;
            }

            $current = $paragraph;
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }

    private function estimateTokenCount(string $content): int
    {
        return max(1, (int) ceil(mb_strlen($content) / 4));
    }
}
