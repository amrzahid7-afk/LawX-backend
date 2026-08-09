<?php

namespace App\Jobs;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// use Laravel\Ai\Facades\AI; // استدعاء الـ Facade من حزمة laravel/ai
use Laravel\Ai\Ai;
// use Laravel\Ai
class FilterReviewWithAiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(
        public Review $review
    ) {}

    public function handle(): void
    {
        // لا يوجد تعليق → أظهر التقييم مباشرة
        if (empty($this->review->comment)) {
            $this->review->update(['is_hidden' => false]);
            return;
        }

        $providers = ['zai', 'openai', 'gemini', 'groq'];

        foreach ($providers as $provider) {
            try {
                $response = \App\Ai\Agents\BadWordsDetectorAgent::make()
                    ->prompt(
                        "افحص التعليق التالي وحدّد هل يحتوي محتوى مسيء.\n\nالتعليق: {$this->review->comment}",
                        provider: $provider
                    );

                // structured output → مصفوفة فيها is_offensive
                $isOffensive = (bool) ($response['is_offensive'] ?? true);

                // نظيف → أظهره | مسيء → يبقى مخفي
                $this->review->update(['is_hidden' => $isOffensive]);

                \Log::info("Review {$this->review->id} filtered", [
                    'provider'     => $provider,
                    'is_offensive' => $isOffensive,
                    'reason'       => $response['reason'] ?? null,
                ]);

                return; // نجح مزوّد → اخرج
            } catch (\Throwable $e) {
                \Log::warning("Review filter [{$provider}] failed: " . $e->getMessage());
                continue; // جرّب التالي
            }
        }

        // فشل كل المزوّدين → قرار احتياطي آمن: يبقى مخفي (is_hidden = true أصلاً)
        \Log::error("AI filter failed for review {$this->review->id} on all providers");
    }
}
