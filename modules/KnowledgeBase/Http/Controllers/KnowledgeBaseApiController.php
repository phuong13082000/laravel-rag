<?php

namespace Modules\KnowledgeBase\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\KnowledgeBase\Dtos\UpdateKnowledgeBaseDTO;
use Modules\KnowledgeBase\Dtos\CreateKnowledgeBaseDTO;
use Modules\KnowledgeBase\Services\KnowledgeBaseService;
use Modules\KnowledgeBase\Http\Requests\StoreKnowledgeBaseRequest;
use Modules\KnowledgeBase\Http\Requests\UpdateKnowledgeBaseRequest;

class KnowledgeBaseApiController extends Controller
{
    public function __construct(
        private readonly KnowledgeBaseService $knowledgeBaseService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $data = $this->knowledgeBaseService->list($userId);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Knowledge base list retrieved successfully',
        ]);
    }

    public function store(StoreKnowledgeBaseRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $dto = new CreateKnowledgeBaseDTO(
            userId: $userId,
            name: $request->string('name')->toString(),
            description: $request->input('description'),
        );
        $knowledgeBase = $this->knowledgeBaseService->create($dto);

        return response()->json([
            'success' => true,
            'data' => $knowledgeBase,
            'message' => 'Knowledge base created successfully',
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id;
        $knowledgeBase = $this->knowledgeBaseService->find($id, $userId);

        return response()->json([
            'success' => true,
            'data' => $knowledgeBase,
            'message' => 'Knowledge base retrieved successfully',
        ]);
    }

    public function update(UpdateKnowledgeBaseRequest $request, int $id): JsonResponse
    {
        $userId = $request->user()->id;
        $dto = new UpdateKnowledgeBaseDTO(
            name: $request->string('name')->toString(),
            description: $request->input('description'),
        );
        $knowledgeBase = $this->knowledgeBaseService->update($id, $userId, $dto);

        return response()->json([
            'success' => true,
            'data' => $knowledgeBase,
            'message' => 'Knowledge base updated successfully',
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id;
        $this->knowledgeBaseService->delete($id, $userId);

        return response()->json([
            'success' => true,
            'message' => 'Knowledge base deleted successfully.',
        ]);
    }
}
