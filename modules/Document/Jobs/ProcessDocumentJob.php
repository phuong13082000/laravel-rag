<?php

namespace Modules\Document\Jobs;

use Modules\Document\Enums\DocumentStatus;
use Modules\Document\Models\Document;
use Modules\Document\Repositories\DocumentRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Document\Services\DocumentTextExtractorService;
use Modules\Chunk\Services\ChunkService;
use Throwable;

class ProcessDocumentJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        public readonly int $documentId,
    ) {}

    public function handle(
        DocumentTextExtractorService $extractor,
        DocumentRepository $documentRepository,
        ChunkService $chunkService,
    ): void {
        $document = Document::find($this->documentId);

        if (!$document) {
            return;
        }

        $documentRepository->updateStatus(
            $document,
            DocumentStatus::PROCESSING,
        );

        try {
            $text = $extractor->extract($document);

            if (trim($text) === '') {
                throw new \RuntimeException(
                    'Document contains no readable text.',
                );
            }

            $chunkService->chunk($document, $text);
            
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