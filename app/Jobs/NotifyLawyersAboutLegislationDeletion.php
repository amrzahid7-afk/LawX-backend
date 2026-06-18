<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Notification;

class NotifyLawyersAboutLegislationDeletion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public string $lawTitle,
        public string $lawNumber,
        public ?string $categoryId
    ) {}

    public function handle(): void
    {
        // البحث عن المحامين الذين لديهم قضايا نشطة في هذا التصنيف
        $query = User::where('role', 'lawyer');

        if ($this->categoryId) {
            $query->whereHas('cases', function ($q) {
                $q->where('category_id', $this->categoryId)
                    ->where('status', 'active');
            });
        }

        $lawyers = $query->get();

        foreach ($lawyers as $lawyer) {
            Notification::create([
                'user_id' => $lawyer->id,
                'type' => 'legislation_deletion',
                'title' => 'حذف تشريع',
                'message' => "تم حذف القانون: {$this->lawTitle} (رقم: {$this->lawNumber})",
                'data' => json_encode([
                    'law_number' => $this->lawNumber,
                    'category_id' => $this->categoryId,
                ]),
            ]);
        }
    }
}
