<?php

namespace Modules\AI\Http\Controllers;

use Modules\AI\Repositories\CitationRepository;
use Modules\AI\Http\Resources\CitationResource;

class CitationApiController
{
    public function __construct(
        private readonly CitationRepository $repository,
    ) {}

    public function getByMessage(
        int $messageId,
        int $userId,
    ): array {
        $citations = $this->repository->getByMessageForUser(
            messageId: $messageId,
            userId: $userId,
        );

        return CitationResource::collection(
            citations: $citations,
        );
    }

    public function find(
        int $citationId,
        int $userId,
    ): array {
        $citation = $this->repository->findByIdForUser(
            citationId: $citationId,
            userId: $userId,
        );

        if (!$citation) {
            abort(404, 'Citation not found.');
        }

        return CitationResource::make($citation);
    }
}
