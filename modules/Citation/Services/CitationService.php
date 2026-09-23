<?php

namespace Modules\Citation\Services;

use Modules\Citation\Repositories\CitationRepository;
use Modules\Citation\Http\Resources\CitationResource;

class CitationService
{
    public function __construct(
        private readonly CitationRepository $citationRepository,
    ) {}

    public function getByMessage(int $messageId, int $userId): array
    {
        $citations = $this->citationRepository->getByMessageForUser(
            messageId: $messageId,
            userId: $userId,
        );

        return CitationResource::collection(citations: $citations);
    }

    public function find(int $citationId, int $userId): array
    {
        $citation = $this->citationRepository->findByIdForUser(
            citationId: $citationId,
            userId: $userId,
        );

        if (!$citation) {
            abort(404, 'Citation not found.');
        }

        return CitationResource::make($citation);
    }
}
