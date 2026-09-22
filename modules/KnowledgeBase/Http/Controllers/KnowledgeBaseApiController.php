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
        $user = $request->user();
        $data = $this->knowledgeBaseService->list($user->id);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Knowledge base list retrieved successfully',
        ]);
    }

    public function store(StoreKnowledgeBaseRequest $request): JsonResponse
    {
        $user = $request->user();
        $dto = new CreateKnowledgeBaseDTO(
            userId: $user->id,
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
        $user = $request->user();
        $knowledgeBase = $this->knowledgeBaseService->find($id, $user->id);

        return response()->json([
            'success' => true,
            'data' => $knowledgeBase,
            'message' => 'Knowledge base retrieved successfully',
        ]);
    }

    public function update(UpdateKnowledgeBaseRequest $request, int $id): JsonResponse
    {
        $user = $request->user();
        $dto = new UpdateKnowledgeBaseDTO(
            name: $request->string('name')->toString(),
            description: $request->input('description'),
        );
        $knowledgeBase = $this->knowledgeBaseService->update($id, $user->id, $dto);

        return response()->json([
            'success' => true,
            'data' => $knowledgeBase,
            'message' => 'Knowledge base updated successfully',
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $this->knowledgeBaseService->delete($id, $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Knowledge base deleted successfully.',
        ]);
    }
}
