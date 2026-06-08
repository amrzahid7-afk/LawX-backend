<?php

namespace App\Contracts\Services\Dashboard;

interface CaseStatisticsInterface
{
   // عدد الجلسات القادمة (من اليوم فصاعداً)
    public function getUpcomingSessionsCount($user, $caseIds = null): int;

    // عدد القضايا المغلقة

    public function getClosedCasesCount($user, $caseIds = null): int;

    // عدد القضايا قيد التنفيذ (open أو pending)
    public function getInProgressCasesCount($user, $caseIds = null): int;
    public function getOpenCasesCount($user, $caseIds = null): int;

}
