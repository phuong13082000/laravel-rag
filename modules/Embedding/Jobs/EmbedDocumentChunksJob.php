<?php

namespace Modules\Embedding\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Chunk\Repositories\DocumentChunkRepository;
use Modules\Document\Enums\DocumentStatus;
use Modules\Document\Models\Document;
use Modules\Document\Repositories\DocumentRepository;
use Modules\Embedding\Contracts\EmbeddingService;
use Throwable;

class EmbedDocumentChunksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 600;

    public function __construct(
        private readonly int $documentId,
    ) {}

    public function handle(
        DocumentChunkRepository $chunkRepository,
        DocumentRepository $documentRepository,
        EmbeddingService $embeddingService,
    ): void {
        $document = Document::find($this->documentId);

        if (!$document) {
            return;
        }

        $documentRepository->updateStatus(
            $document,
            DocumentStatus::EMBEDDING,
        );

        try {
            $chunks = $chunkRepository->getByDocument(
                $document->id,
            );

            if ($chunks->isEmpty()) {
                throw new \RuntimeException(
                    'Document has no chunks to embed.',
                );
            }

            $batchSize = 8;

            foreach ($chunks->chunk($batchSize) as $batch) {
                $texts = $batch
                    ->map(fn ($chunk) => $chunk->content)
                    ->values()
                    ->all();

                $embeddings = $embeddingService->embedMany(
                    $texts,
                );

                if (count($embeddings) !== count($batch)) {
                    throw new \RuntimeException(
                        'Embedding count does not match chunk count.',
                    );
                }

                foreach ($batch->values() as $index => $chunk) {
                    $chunkRepository->updateEmbedding(
                        $chunk->id,
                        $embeddings[$index],
                    );
                }
            }

            $documentRepository->updateStatus(
                $document,
                DocumentStatus::COMPLETED,
            );
        } catch (Throwable $e) {
            $documentRepository->updateStatus(
                $document,
                DocumentStatus::FAILED,
                $e->getMessage(),
            );

            throw $e;
        }
    }
}