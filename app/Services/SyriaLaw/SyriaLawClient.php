<?php

namespace App\Services\SyriaLaw;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class SyriaLawClient
{
    private string $base;
    private int $perPage;

    public function __construct()
    {
        $this->base = config('services.syria_law.base_url');
        $this->perPage = config('services.syria_law.per_page', 20);
    }

    /**
     * Fetch a paginated chunk of resources from the WordPress REST API.
     */
    private function fetch(string $type, int $page, ?Carbon $modifiedAfter = null, array $extraParams = []): array
    {
        // Build base params
        $query = array_merge([
            'per_page' => $type === 'lawss' ? 50 : $this->perPage, // Smaller batch size to prevent server timeout
            'page'     => $page,
        ], $extraParams);

        // Apply menu_order only for articles/laws, not for categories/lawss
        if ($type === 'laws' && !isset($query['orderby'])) {
            $query['orderby'] = 'menu_order';
            $query['order']   = 'asc';
        }

        if ($modifiedAfter) {
            $query['modified_after'] = $modifiedAfter->toIso8601String();
            $query['orderby']        = 'modified';
            $query['order']          = 'asc';
        }

        $res = Http::withoutVerifying() // Avoid cURL SSL handshake timeouts in Windows/Laragon environments
            ->connectTimeout(30)       // Increased connect timeout for slow networks
            ->timeout(120)             // Extended response timeout
            ->retry(3, 5000, throw: false) // Retry 3 times with 5s delay
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept'     => 'application/json',
            ])
            ->get("{$this->base}/{$type}", $query);

        if ($res->status() === 400) {
            return [[], 0];
        }

        $res->throw();

        return [$res->json(), (int) $res->header('X-WP-TotalPages', 1)];
    }

    /**
     * Yield items page by page using a Generator to manage memory consumption.
     */
    public function all(string $type, ?Carbon $modifiedAfter = null, array $extraParams = []): \Generator
    {
        $page = 1;
        do {
            [$items, $totalPages] = $this->fetch($type, $page, $modifiedAfter, $extraParams);
            foreach ($items as $item) {
                yield $item;
            }
            $page++;
        } while ($page <= $totalPages);
    }

    /**
     * Fetch legislation articles.
     */
    public function laws(?Carbon $modifiedAfter = null, array $extraParams = []): \Generator
    {
        return $this->all('laws', $modifiedAfter, $extraParams);
    }

    /**
     * Fetch parent law taxonomies (lawss).
     */
    public function lawss(): \Generator
    {
        return $this->all('lawss');
    }
}
