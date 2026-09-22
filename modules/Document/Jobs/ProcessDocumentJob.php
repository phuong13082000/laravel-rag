<?php

namespace Modules\Document\Jobs;

use Modules\Document\Enums\DocumentStatus;
use Modules\Document\Models\Document;
use Modules\Document\Repositories\DocumentRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Modules\Document\Services\DocumentTextExtractorService;
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
        DocumentRepository $repository,
    ): void {
        $document = Document::find($this->documentId);

        if (!$document) {
            return;
        }

        $repository->updateStatus(
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

            /*
             * Tạm thời chỉ kiểm tra extraction.
             *
             * Bước tiếp theo:
             *
             * text
             * ↓
             * ChunkService
             * ↓
             * document_chunks
             */
            Log::info('Document text extracted.', [
                'document_id' => $document->id,
                'characters' => mb_strlen($text),
            ]);

            $repository->updateStatus(
                $document,
                DocumentStatus::COMPLETED,
            );
        } catch (Throwable $e) {
            $repository->updateStatus(
                $document,
                DocumentStatus::FAILED,
                $e->getMessage(),
            );

            throw $e;
        }
    }
}
