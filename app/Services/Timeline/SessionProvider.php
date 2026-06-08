<?php

namespace App\Services\Timeline;

use App\Contracts\Services\Timeline\TimelineEventProviderInterface;
use Carbon\Carbon;

class SessionProvider implements TimelineEventProviderInterface
{
    /**
     * الحصول على أحداث الجلسات
     * @param mixed $case
     * @return array
     */
    public function getEvents($case): array
    {
        $events = [];

        if (!$case->sessions) {
            return $events;
        }

        foreach ($case->sessions as $session) {
            $sessionDate = $session->date instanceof Carbon ? $session->date : Carbon::parse($session->date);

            $events[] = [
                'type'        => 'session',
                'title'       => 'جلسة محكمة جديد',
                'description' => "تمت جدولة جلسة جديدة في: \"{$session->location}\"",
                'date'        => $sessionDate->toDateTimeString(),
                'user'        => 'النظام',
               'meta' => [
    'location' => $session->location,
    'notes'    => $session->nots, // ✅ تم تعديلها لتطابق عمود قاعدة البيانات حرفياً
    'result'   => $session->result ?? 'بانتظار الجلسة',
]
            ];
        }

        return $events;
    }
}
