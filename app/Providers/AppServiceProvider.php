<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Services\Dashboard\CaseStatisticsInterface;
use App\Services\Dashboard\CaseStatisticsService;
use App\Contracts\Services\Dashboard\FinancialStatisticsInterface;
use App\Services\Dashboard\FinancialStatisticsService;
use App\Contracts\Services\Dashboard\TeamPerformanceInterface;
use App\Services\Dashboard\TeamPerformanceService;
use App\Contracts\Services\Dashboard\TodayScheduleInterface;
use App\Services\Dashboard\TodayScheduleService;
use App\Contracts\Services\Dashboard\AlertsInterface;
use App\Services\Dashboard\AlertsService;
use App\Contracts\Services\Dashboard\NotificationsInterface;
use App\Services\Dashboard\NotificationsService;
use App\Contracts\Services\Timeline\TimelineEventProviderInterface;
use App\Services\Timeline\CaseCreatedProvider;
use App\Services\Timeline\SessionProvider;
use App\Services\Timeline\TaskProvider;
use App\Services\Timeline\DocumentProvider;
use App\Services\Timeline\InvoiceProvider;
use App\Services\Timeline\TimelineAggregatorService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
            $this->app->bind(CaseStatisticsInterface::class, CaseStatisticsService::class);
    // $this->app->bind(FinancialStatisticsInterface::class, FinancialStatisticsService::class);
     $this->app->bind(TeamPerformanceInterface::class, TeamPerformanceService::class);
     $this->app->bind(TodayScheduleInterface::class, TodayScheduleService::class);
    // $this->app->bind(AlertsInterface::class, AlertsService::class);
    // $this->app->bind(NotificationsInterface::class, NotificationsService::class);


        $this->app->when(TimelineAggregatorService::class)
        ->needs('$providers')
        ->give(function ($app) {
            return [
                $app->make(CaseCreatedProvider::class),
                $app->make(SessionProvider::class),
                $app->make(TaskProvider::class),
                $app->make(DocumentProvider::class),
              //  $app->make(InvoiceProvider::class),
            ];
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
