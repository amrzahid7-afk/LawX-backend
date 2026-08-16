<?php

namespace App\Services\SyriaLaw;

use Illuminate\Support\Str;

class ContentParser
{
    /**
     * Clean rendered HTML content into plain text while preserving meaningful line breaks.
     */
    public function clean(string $html): string
    {
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/p>/i', "\n\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/u', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    /**
     * Identify structural headers (Book, Part, Chapter) from article titles.
     */
    public function headingLevel(string $title): ?string
    {
        return match (true) {
            Str::startsWith($title, 'الكتاب') => 'book',
            Str::startsWith($title, 'الباب')   => 'part',
            Str::startsWith($title, 'الفصل')  => 'chapter',
            default => null,
        };
    }

    /**
     * Extract article number from title string (supports numbers with "مكرر").
     */
    public function articleNumber(string $title): ?string
    {
        if (preg_match('/^\s*(?:ال)?مادة\s+(\d+(?:\s*مكرر\s*\d*)?)/u', $title, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    /**
     * Extract law number and year from WP class list (e.g., lawss------61--1950).
     */
    public function lawNumberYear(array $classList): ?array
    {
        foreach ($classList as $class) {
            if (preg_match('/lawss-+(\d+)-+(\d{4})/', $class, $m)) {
                return ['number' => $m[1], 'year' => $m[2]];
            }
        }
        return null;
    }
}
