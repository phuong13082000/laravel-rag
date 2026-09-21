<?php

namespace Modules\KnowledgeBase\Http\Controllers;
    
use App\Http\Controllers\Controller;
use Modules\KnowledgeBase\Dtos\CreateKnowledgeBaseDTO;
use Modules\KnowledgeBase\Services\KnowledgeBaseService;
use Modules\KnowledgeBase\Http\Requests\StoreKnowledgeBaseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function __construct(
        private readonly KnowledgeBaseService $service,
    ) {}

    public function store(
        StoreKnowledgeBaseRequest $request
    ): JsonResponse {
        $knowledgeBase = $this->service->create(
            new CreateKnowledgeBaseDTO(
                userId: $request->user()->id,
                name: $request->string('name')->toString(),
                description: $request->input('description'),
            )
        );

        return response()->json([
            'success' => true,
            'data' => $knowledgeBase,
        ], 201);
    }
}