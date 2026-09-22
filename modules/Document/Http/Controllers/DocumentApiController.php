<?php

namespace Modules\Document\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Document\Http\Requests\UploadDocumentRequest;
use Modules\Document\DTOs\UpdateDocumentDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Document\Services\DocumentService;

class DocumentApiController extends Controller
{
    public function __construct(private readonly DocumentService $service) {}

    public function index(Request $request, int $knowledgeBaseId): JsonResponse
    {
        $userId = $request->user()->id;
        $documents = $this->service->list($knowledgeBaseId, $userId);

        return response()->json([
            'success' => true,
            'data' => $documents,
            'message' => 'Documents retrieved successfully',
        ]);
    }

    public function store(UploadDocumentRequest $request, int $knowledgeBaseId): JsonResponse
    {
        $document = $this->service->upload(
            knowledgeBaseId: $knowledgeBaseId,
            userId: $request->user()->id,
            file: $request->file('file'),
            title: $request->input('title'),
        );

        return response()->json([
            'success' => true,
            'data' => $document,
            'message' => 'Document uploaded successfully.',
        ], 201);
    }

    public function show(
        Request $request,
        int $knowledgeBaseId,
        int $document,
    ): JsonResponse {
        $result = $this->service->find(
            $document,
            $knowledgeBaseId,
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function update(
        Request $request,
        int $knowledgeBaseId,
        int $document,
    ): JsonResponse {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $result = $this->service->update(
            id: $document,
            knowledgeBaseId: $knowledgeBaseId,
            userId: $request->user()->id,
            dto: new UpdateDocumentDTO(
                title: $validated['title'],
            ),
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function destroy(
        Request $request,
        int $knowledgeBaseId,
        int $document,
    ): JsonResponse {
        $this->service->delete(
            $document,
            $knowledgeBaseId,
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.',
        ]);
    }

    public function reprocess(
        Request $request,
        int $knowledgeBaseId,
        int $document,
    ): JsonResponse {
        $result = $this->service->reprocess(
            $document,
            $knowledgeBaseId,
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => 'Document processing restarted.',
        ]);
    }
}
