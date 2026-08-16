<?php

namespace App\Services\SyriaLaw;

use Illuminate\Support\Carbon;

class LegislationMapper
{
    public function __construct(private ContentParser $parser) {}

    /**
     * Map WordPress post payload into a structured law database array.
     *
     * @param int $lawssId Parent law taxonomy ID
     * @param string $lawTitle Parent law title
     * @param array $posts Collection of law articles sorted in order
     */
    public function map(int $lawssId, string $lawTitle, array $posts): array
    {
        $articles = [];
        $fullText = [];
        $context = ['book' => null, 'part' => null, 'chapter' => null];
        $numberYear = null;
        $lastModified = null;

        foreach ($posts as $post) {
            $title = trim($post['title']['rendered']);
            $body = $this->parser->clean($post['content']['rendered']);

            $numberYear ??= $this->parser->lawNumberYear($post['class_list'] ?? []);
            $modified = Carbon::parse($post['modified_gmt']);
            $lastModified = $lastModified?->max($modified) ?? $modified;

            // Handle structural headers (Book/Part/Chapter) to assign contextual breadcrumbs to articles
            if ($level = $this->parser->headingLevel($title)) {
                $context[$level] = $title;
                if ($level === 'book') {
                    $context['part'] = $context['chapter'] = null;
                }
                if ($level === 'part') {
                    $context['chapter'] = null;
                }
                continue;
            }

            $articles[] = [
                'wp_id'    => $post['id'],
                'number'   => $this->parser->articleNumber($title),
                'title'    => $title,
                'content'  => $body,
                'book'     => $context['book'],
                'part'     => $context['part'],
                'chapter'  => $context['chapter'],
                'modified' => $modified->toIso8601String(),
            ];
            $fullText[] = $body;
        }

        $content = implode("\n\n", $fullText);
        $lawNumber = $numberYear
            ? "{$numberYear['number']}/{$numberYear['year']}"
            : "lawss-{$lawssId}"; // Fallback if law number cannot be parsed

        return [
            'external_id'          => $lawssId,
            'external_source'      => 'syria-law.com',
            'external_modified_at' => $lastModified,
            'title'                => $lawTitle,
            'content'              => $content,
            'content_checksum'     => hash('sha256', $content),
            'law_number'           => $lawNumber,
            'status'               => 'active',
            'source'               => 'external_api',
            'country'              => 'Syria',
            'effective_date'       => $numberYear ? "{$numberYear['year']}-01-01" : null,
            'articles'             => $articles,
            'book'                 => $context['book'],
            'part'                 => $context['part'],
            'chapter'              => $context['chapter'],
        ];
    }
}
