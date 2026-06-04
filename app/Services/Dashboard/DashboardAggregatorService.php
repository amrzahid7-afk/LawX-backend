<?php

namespace App\Services\Dashboard;
use App\Contracts\Services\Dashboard\CaseStatisticsInterface;
use App\Contracts\Services\Dashboard\FinancialStatisticsInterface;
use App\Contracts\Services\Dashboard\TeamPerformanceInterface;
use App\Contracts\Services\Dashboard\TodayScheduleInterface;
use App\Models\CaseModel;

class DashboardAggregatorService
{
    protected $caseStatsService;
    protected $financialStatsService;
    protected $teamPerformanceService;
    protected $todayScheduleService;

    public function __construct(
        CaseStatisticsInterface $caseStatsService,
      //  FinancialStatisticsInterface $financialStatsService,
      //  TeamPerformanceInterface $teamPerformanceService,
        TodayScheduleInterface $todayScheduleService
    ) {
        $this->caseStatsService = $caseStatsService;
        // $this->financialStatsService = $financialStatsService;
        // $this->teamPerformanceService = $teamPerformanceService;
         $this->todayScheduleService = $todayScheduleService;
    }

    public function getDashboardData($user, $caseIds = null)
    {
        $roleName=$user->role->name;

         if ($roleName === 'lawyer') {
            $caseIds = CaseModel::where('lawer_id', $user->id)->pluck('id');
        } elseif ($roleName === 'client') {
            $caseIds = CaseModel::where('client_id', $user->id)->pluck('id');
        }


        return [
            'case_statistics' =>[
                'upcoming_sessions_count'=>$this->caseStatsService->getUpcomingSessionsCount($user, $caseIds),
                   'closed_case_count'=>$this->caseStatsService->getClosedCasesCount($user, $caseIds) ,
                    'in_progress_case_count'=>$this->caseStatsService->getInProgressCasesCount($user, $caseIds),
                    'in_open_count'=>$this->caseStatsService->getOpenCasesCount($user, $caseIds),
                    ],
                // 'financial_statistics' => $this->financialStatsService->getFinancialStats($user, $caseIds),
                // 'team_performance' => $this->teamPerformanceService->getTeamPerformance($user),
                 'today_schedule' => $this->todayScheduleService->getTodaySchedule($user, $caseIds),

                ];
    }
}
