<?php

namespace App\Services\SyriaLaw;

use App\Jobs\EmbedLegislation;
use App\Models\Legislation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SyncService
{
    private const CURSOR = 'syria_law:last_synced_at';

    public function __construct(
        private SyriaLawClient $client,
        private LegislationMapper $mapper,
    ) {}

    /**
     * @param bool $full
     * @param callable|null $logger
     * @param int|null $limit Maximum number of parent laws to fetch for quick testing
     */
    public function sync(bool $full = false, ?callable $logger = null, ?int $limit = 10): void
    {
        $since = $full ? null : Cache::get(self::CURSOR);
        $startedAt = now();
        $processedCount = 0;

        // Iterate over parent law taxonomies
        foreach ($this->client->lawss() as $term) {
            // Stop if we reached the testing limit
            if ($limit !== null && $processedCount >= $limit) {
                if ($logger) {
                    $logger("Test limit of {$limit} laws reached. Stopping sync.");
                }
                break;
            }

            $lawssId = $term['id'];
            $lawTitle = trim($term['name']);

            if ($logger) {
                $logger("Processing Law [" . ($processedCount + 1) . "/{$limit}]: {$lawTitle} (ID: {$lawssId})...");
            }

            // Fetch all articles assigned to this specific law
            $posts = [];
            foreach ($this->client->laws(modifiedAfter: $since ? Carbon::parse($since) : null, extraParams: ['lawss' => $lawssId]) as $post) {
                $posts[] = $post;
            }

            if (empty($posts)) {
                if ($logger) {
                    $logger("No articles found for: {$lawTitle}. Skipping.");
                }
                continue;
            }

            $row = $this->mapper->map($lawssId, $lawTitle, $posts);
            $this->upsert($row);

            $processedCount++;

            if ($logger) {
                $logger("Saved successfully: {$lawTitle} with " . count($posts) . " articles.");
            }
        }

        // Cache cursor only if full sync was performed without artificially limiting records
        if ($limit === null) {
            Cache::put(self::CURSOR, $startedAt->toIso8601String());
        }
    }

    private function upsert(array $row): void
    {
        $existing = Legislation::where('external_source', $row['external_source'])
            ->where('external_id', $row['external_id'])
            ->first();

        if ($existing && $existing->content_checksum === $row['content_checksum']) {
            return;
        }

        DB::transaction(function () use ($row, $existing) {
            $isAmended = $existing && $existing->content_checksum !== $row['content_checksum'];

            $attributes = [
                'external_source' => $row['external_source'],
                'external_id'     => $row['external_id'],
            ];

            $values = array_merge($row, [
                'status'    => $isAmended ? 'amended' : ($existing->status ?? 'active'),
                'embedding' => null,
            ]);

            $model = Legislation::updateOrCreate($attributes, $values);

            EmbedLegislation::dispatch($model->id);
        });
    }
}
