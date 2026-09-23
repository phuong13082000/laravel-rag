<?php

namespace Modules\Citation\Http\Resources;

use Modules\Citation\Models\Citation;

class CitationResource
{
    public static function make(Citation $citation): array
    {
        $citation->loadMissing([
            'document:id,title,original_name',
            'chunk:id,content,chunk_index',
        ]);

        return [
            'id' => $citation->id,

            'document' => [
                'id' => $citation->document->id,
                'title' => $citation->document->title,
                'original_name' => $citation->document->original_name,
            ],

            'chunk' => [
                'id' => $citation->chunk->id,
                'index' => $citation->chunk->chunk_index,
            ],

            'similarity' => round(
                (float) $citation->similarity,
                4,
            ),

            'content_preview' => mb_substr(
                $citation->chunk->content,
                0,
                500,
            ),
        ];
    }

    /**
     * @param iterable<Citation> $citations
     */
    public static function collection(iterable $citations): array
    {
        $result = [];

        foreach ($citations as $citation) {
            $result[] = self::make($citation);
        }

        return $result;
    }
}
