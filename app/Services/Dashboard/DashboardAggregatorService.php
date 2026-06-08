<?php

namespace App\Services\Dashboard;

use App\Contracts\Services\Dashboard\CaseStatisticsInterface;
use App\Contracts\Services\Dashboard\FinancialStatisticsInterface;
use App\Contracts\Services\Dashboard\TodayScheduleInterface;
use App\Models\CaseModel;

class DashboardAggregatorService
{
    protected $caseStatsService;
    protected $financialStatsService;
    protected $todayScheduleService;

    public function __construct(
        CaseStatisticsInterface $caseStatsService,
        FinancialStatisticsInterface $financialStatsService,
        TodayScheduleInterface $todayScheduleService
    ) {
        $this->caseStatsService = $caseStatsService;
        $this->financialStatsService = $financialStatsService;
        $this->todayScheduleService = $todayScheduleService;
    }

    public function getDashboardData($user, $caseIds = null)
    {
        $roleName = $user->role->name ?? '';


        if ($roleName === 'lawyer') {

            $caseIds = CaseModel::where('lead_lawyer_id', $user->id)
                ->orWhereHas('team', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->pluck('id')
                ->toArray();
        } elseif ($roleName === 'client') {
            // العميل يرى القضايا المرتبطة به عبر الجدول الوسيط case_clients
            $caseIds = CaseModel::whereHas('clients', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->pluck('id')
            ->toArray();
        }


        return [
            'case_statistics' => [
                'upcoming_sessions_count' => $this->caseStatsService->getUpcomingSessionsCount($user, $caseIds),
                'closed_case_count'       => $this->caseStatsService->getClosedCasesCount($user, $caseIds),
                'in_progress_case_count'  => $this->caseStatsService->getInProgressCasesCount($user, $caseIds),
                'in_open_count'           => $this->caseStatsService->getOpenCasesCount($user, $caseIds),
            ],
            'financial_statistics' => $this->financialStatsService->getFinancialStats($user, $caseIds),
            'today_schedule'       => $this->todayScheduleService->getTodaySchedule($user, $caseIds),
        ];
    }
}
