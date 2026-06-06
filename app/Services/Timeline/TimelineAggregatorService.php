<?php

namespace App\Services\Timeline;

use Illuminate\Support\Collection;

class TimelineAggregatorService
{
    protected $providers = [];

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    /**
     * جلب جميع الأحداث مجمعة ومُرتبة زمنياً تلقائياً
     * @param mixed $case
     * @return array
     */
    public function getTimeline($case) // ✅ تم تصحيح الحرف المكتوب (getTimeline)
    {
        $events = collect();

        foreach ($this->providers as $provider) {
            $events = $events->concat($provider->getEvents($case));
        }

        // ترتيب الأحداث من الأقدم إلى الأحدث أو العكس حسب الرغبة
        $sorted = $events->sortBy(function ($event) {
            return $event['date'] instanceof \DateTime ? $event['date'] : new \DateTime($event['date']);
        })->values();

        return $sorted->toArray();
    }
}
