<?php
// app/Jobs/GenerateEmbeddingJob.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Legislation;
use App\Services\EmbeddingService;

class GenerateEmbeddingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // عدد المحاولات في حالة الفشل
    public $timeout = 120; // المهلة بالثواني

    public function __construct(
        public Legislation $legislation
    ) {}

    public function handle(EmbeddingService $embeddingService): void
    {
        $text = $this->legislation->title . ' ' . $this->legislation->content;
        $embedding = $embeddingService->generateEmbedding($text);

        if ($embedding) {
            $this->legislation->update(['embedding' => $embedding]);
        }
    }
}
