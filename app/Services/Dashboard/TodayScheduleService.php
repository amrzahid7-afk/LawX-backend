<?php
namespace App\Services\Dashboard;
use App\Contracts\Services\Dashboard\TodayScheduleInterface;
use App\Models\Session;
use Carbon\Carbon;

class TodayScheduleService implements TodayScheduleInterface
{
    public function getTodaySchedule($user, $caseIds = null): array
    {

    $query=Session::with('case');
    if ($caseIds) {
        $query->whereIn('case_id', $caseIds);
    }
    $sessions = $query->where('date', '>=', Carbon::today())
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

       return $sessions->map(function ($session) {
            return [
                'title' => $session->case->title ?? 'Unknown Case',
                'date' => $session->date,
                'location' => $session->location,
                'notes' => $session->nots,
                'type' => 'session',
            ];
        })->toArray();
}
}
