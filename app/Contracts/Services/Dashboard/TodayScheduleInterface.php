<?php

namespace App\Contracts\Services\Dashboard;

interface TodayScheduleInterface
{
      /**
     * جدول اليوم: الجلسات القادمة (حد أقصى 5)
     * @return array
     */
    public function getTodaySchedule($user, $caseIds = null): array;
}
