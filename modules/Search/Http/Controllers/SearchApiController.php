<?php

namespace Modules\Search\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Search\Dtos\SearchDTO;
use Modules\Search\Http\Requests\SearchRequest;
use Modules\Search\Services\SearchService;

class SearchApiController
{
    public function __construct(
        private readonly SearchService $service,
    ) {}

    public function search(SearchRequest $request, int $knowledgeBaseId): JsonResponse
    {
        $data = $request->validated();

        $dto = new SearchDTO(
            knowledgeBaseId: $knowledgeBaseId,
            query: $data['query'],
            limit: $data['limit'] ?? 5,
            minSimilarity: (float) (
                $data['min_similarity'] ?? 0
            ),
        );

        $user = $request->user();
        $results = $this->service->search($dto, $user->id);

        return response()->json([
            'success' => true,
            'data' => $results,
            'message' => 'Search completed successfully.',
        ]);
    }
}
