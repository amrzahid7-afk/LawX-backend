<?php

namespace App\Jobs;

use App\Models\Legislation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Embeddings;

class GenerateEmbeddingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Legislation $legislation) {}


    public function handle(): void
    {
        try {
            // $text = trim($this->legislation->title . "\n" . $this->legislation->content);
            $text = trim($this->legislation->title);

            if ($text === '') {
                return;
            }
            $response = Embeddings::for([$text])
                ->generate('gemini', 'gemini-embedding-001');

            $vector = $response->embeddings[0] ?? null;

            if ($vector) {
                $this->legislation->update(['embedding' => $vector]);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to generate embedding for Legislation ID: {$this->legislation->id}", [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            throw $e;
        }
    }
}
