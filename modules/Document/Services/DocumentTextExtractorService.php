<?php

namespace Modules\Document\Services;

use Modules\Document\Exceptions\DocumentProcessingException;
use Modules\Document\Models\Document;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class DocumentTextExtractorService
{
    public function extract(Document $document): string
    {
        $disk = Storage::disk($document->disk);

        if (!$disk->exists($document->path)) {
            throw new DocumentProcessingException('Document file does not exist.');
        }

        return match ($document->mime_type) {
            'application/pdf' => $this->extractPdf(
                $disk->path($document->path),
            ),

            'text/plain',
            'text/markdown' => $this->extractText(
                $disk->path($document->path),
            ),

            default => throw new DocumentProcessingException('Unsupported document type.'),
        };
    }

    private function extractText(string $path): string
    {
        $content = file_get_contents($path);

        if ($content === false) {
            throw new DocumentProcessingException('Unable to read document.');
        }

        return trim($content);
    }

    private function extractPdf(string $path): string
    {
        try {
            $parser = new Parser();

            $pdf = $parser->parseFile($path);

            return trim($pdf->getText());
        } catch (\Throwable $e) {
            throw new DocumentProcessingException('Unable to extract PDF text.', previous: $e);
        }
    }
}
