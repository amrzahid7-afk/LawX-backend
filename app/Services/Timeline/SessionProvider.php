<?php
namespace App\Services\Timeline;
use App\Contracts\Services\Timeline\TimelineEventProviderInterface;
use carbon\Carbon;
class SessionProvider implements TimelineEventProviderInterface
{
    /**
     * Summary of getEvents
     * @param mixed $case
     * @return array{date: mixed, description: string, title: string, type: string, user: mixed[]}
     */
    public function getEvents($case): array
    {
        $events = [];

        if (!$case->sessions) {
            return $events;
        }

        foreach ($case->sessions as $session) {
            // 1. تأمين تحويل التاريخ إلى كائن Carbon
            $sessionDate = $session->date instanceof Carbon ? $session->date : Carbon::parse($session->date);

            $events[] = [
                'type'        => 'session',
                'title'       => 'جلسة محكمة',

                'description' => "تمت جدولة جلسة في: \"{$session->location}\"" ,
                'date'        => $sessionDate->toDateTimeString(),

                'user'        => 'النظام',


                'meta'        => [
                    'location' => $session->location,
                    'notes'    => $session->nots,
                    'result'   => $session->result ?? 'بانتظار الجلسة',
                ]
            ];
        }

        return $events;

    }
}
