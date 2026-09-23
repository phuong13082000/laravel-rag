<?php

namespace Modules\Citation\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Citation\Repositories\CitationRepository;
use Modules\Citation\Http\Resources\CitationResource;

class CitationApiController
{
    public function __construct(
        private readonly CitationRepository $repository,
    ) {}

    public function getByMessage(Request $request, int $messageId): array
    {
        $user = $request->user();
        $citations = $this->repository->getByMessageForUser(
            messageId: $messageId,
            userId: $user->id,
        );

        return CitationResource::collection(citations: $citations);
    }

    public function find(Request $request, int $citationId): array
    {
        $user = $request->user();
        $citation = $this->repository->findByIdForUser(
            citationId: $citationId,
            userId: $user->id,
        );

        if (!$citation) {
            abort(404, 'Citation not found.');
        }

        return CitationResource::make($citation);
    }
}
