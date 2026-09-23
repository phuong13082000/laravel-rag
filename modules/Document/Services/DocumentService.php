<?php

namespace Modules\Document\Services;

use Modules\Document\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Document\DTOs\CreateDocumentDTO;
use Modules\Document\DTOs\UpdateDocumentDTO;
use Modules\Document\Jobs\ProcessDocumentJob;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Modules\Document\Repositories\DocumentRepository;
use Modules\KnowledgeBase\Services\KnowledgeBaseService;
use Illuminate\Database\Eloquent\Collection;

class DocumentService
{
    public function __construct(
        private readonly DocumentRepository $repository,
        private readonly KnowledgeBaseService $knowledgeBaseService,
    ) {}

    public function list(int $knowledgeBaseId, int $userId): Collection
    {
        $this->knowledgeBaseService->find($knowledgeBaseId, $userId);

        return $this->repository->getByKnowledgeBase($knowledgeBaseId);
    }

    public function find(int $id, int $knowledgeBaseId, int $userId): Document
    {
        $this->knowledgeBaseService->find($knowledgeBaseId, $userId);

        $document = $this->repository->findById($id, $knowledgeBaseId);

        if (!$document) {
            throw new NotFoundHttpException('Document not found.');
        }

        return $document;
    }

    public function upload(
        int $knowledgeBaseId,
        int $userId,
        UploadedFile $file,
        ?string $title = null,
    ): Document {
        $this->ensureKnowledgeBaseOwnership(
            $knowledgeBaseId,
            $userId,
        );

        $disk = 'local';

        $originalName = $file->getClientOriginalName();

        $documentTitle = $title
            ?: pathinfo(
                $originalName,
                PATHINFO_FILENAME,
            );

        $directory = "knowledge-bases/{$knowledgeBaseId}/documents";

        $filename = Str::uuid()
            . '.'
            . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            $directory,
            $filename,
            $disk,
        );

        try {
            $dto = new CreateDocumentDTO(
                knowledgeBaseId: $knowledgeBaseId,
                title: $documentTitle,
                originalName: $originalName,
                disk: $disk,
                path: $path,
                mimeType: $file->getMimeType(),
                size: $file->getSize(),
            );
            $document = $this->repository->create($dto->toArray());

            ProcessDocumentJob::dispatch($document->id);

            return $document;
        } catch (\Throwable $e) {
            Storage::disk($disk)->delete($path);
            throw $e;
        }
    }

    public function update(
        int $id,
        int $knowledgeBaseId,
        int $userId,
        UpdateDocumentDTO $dto,
    ): Document {
        $document = $this->find(
            $id,
            $knowledgeBaseId,
            $userId,
        );

        return $this->repository->update(
            $document,
            $dto->toArray(),
        );
    }

    public function delete(
        int $id,
        int $knowledgeBaseId,
        int $userId,
    ): void {
        $document = $this->find(
            $id,
            $knowledgeBaseId,
            $userId,
        );

        Storage::disk($document->disk)
            ->delete($document->path);

        $this->repository->delete($document);
    }

    public function reprocess(
        int $id,
        int $knowledgeBaseId,
        int $userId,
    ): Document {
        $document = $this->find(
            $id,
            $knowledgeBaseId,
            $userId,
        );

        $document->update([
            'status' => 'pending',
            'error_message' => null,
        ]);

        ProcessDocumentJob::dispatch(
            $document->id,
        );

        return $document->refresh();
    }

    private function ensureKnowledgeBaseOwnership(
        int $knowledgeBaseId,
        int $userId,
    ): void {
        $this->knowledgeBaseService->find(
            $knowledgeBaseId,
            $userId,
        );
    }
}
