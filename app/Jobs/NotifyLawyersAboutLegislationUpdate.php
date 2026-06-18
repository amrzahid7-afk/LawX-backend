<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Legislation;
use App\Models\User;
use App\Models\Notification;

class NotifyLawyersAboutLegislationUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public Legislation $legislation
    ) {}

    public function handle(): void
    {
        // البحث عن المحامين الذين لديهم قضايا نشطة في هذا التصنيف
        $lawyers = User::where('role', 'lawyer')
            ->whereHas('cases', function ($q) {
                $q->where('category_id', $this->legislation->category_id)
                    ->where('status', 'active');
            })
            ->get();

        foreach ($lawyers as $lawyer) {
            Notification::create([
                'user_id' => $lawyer->id,
                'type' => 'legislation_update',
                'title' => 'تحديث تشريعي',
                'message' => "تم تحديث القانون: {$this->legislation->title}",
                'data' => json_encode([
                    'legislation_id' => $this->legislation->id,
                    'category_id' => $this->legislation->category_id,
                ]),
            ]);
        }
    }
}
