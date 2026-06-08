<?php

namespace App\Contracts\Services\Timeline;

interface TimelineEventProviderInterface
{
        /**
        * الحصول على أحداث الخط الزمني لقضية معينة
        * @param int $case
        * @return array
        */
 public function getEvents($case): array;
}
