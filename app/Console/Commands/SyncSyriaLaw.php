<?php

namespace App\Console\Commands;

use App\Services\SyriaLaw\SyncService;
use Illuminate\Console\Command;
use Throwable;

class SyncSyriaLaw extends Command
{
    protected $signature = 'syria-law:sync {--full : Perform full sync} {--limit=10 : Limit number of laws to fetch for testing}';
    protected $description = 'Sync Syrian legislations from syria-law.com';

    public function handle(SyncService $service): int
    {
        $isFull = $this->option('full');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        $this->info("Starting Sync (Limit: " . ($limit ?? 'All') . " laws)...");

        try {
            $service->sync(
                full: $isFull,
                logger: fn($msg) => $this->line($msg),
                limit: $limit
            );

            $this->info('SyriaLaw sync finished successfully!');
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
