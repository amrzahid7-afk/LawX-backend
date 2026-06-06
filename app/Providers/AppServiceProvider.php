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
use App\Services\Dashboard\Alerts\AlertsService;
use App\Services\Dashboard\Alerts\OverdueInvoiceAlertProvider;
use App\Services\Dashboard\Alerts\UnreadNotificationAlertProvider;

// ✅ استورد الإنترفيسات الجديدة بدلاً من القديمة
use App\Contracts\Services\Dashboard\AlertsAggregatorInterface;
use App\Contracts\Services\Dashboard\AlertProviderInterface;  // قد لا تحتاجه في الـ bind مباشرة

// ❌ يمكنك حذف أو تعليق السطر التالي (لن تستخدمه بعد الآن)
// use App\Contracts\Services\Dashboard\AlertsInterface;

use App\Contracts\Services\Dashboard\NotificationsInterface;
use App\Services\Dashboard\NotificationsService;
use App\Contracts\Services\Timeline\TimelineEventProviderInterface;
use App\Services\Timeline\CaseCreatedProvider;
use App\Services\Timeline\SessionProvider;
use App\Services\Timeline\TaskProvider;
use App\Services\Timeline\DocumentProvider;
use App\Services\Timeline\InvoiceProvider;
use App\Services\Timeline\TimelineAggregatorService;

use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(AlertsAggregatorInterface::class, AlertsService::class);


        $this->app->bind(CaseStatisticsInterface::class, CaseStatisticsService::class);
        $this->app->bind(FinancialStatisticsInterface::class, FinancialStatisticsService::class);
        // $this->app->bind(TeamPerformanceInterface::class, TeamPerformanceService::class);
        $this->app->bind(TodayScheduleInterface::class, TodayScheduleService::class);


        // $this->app->bind(NotificationsInterface::class, NotificationsService::class);

        // ✅ تسجيل مزودات الـ Timeline (موجود وصحيح)
        $this->app->when(TimelineAggregatorService::class)
            ->needs('$providers')
            ->give(function ($app) {
                return [
                    $app->make(CaseCreatedProvider::class),
                    $app->make(SessionProvider::class),
                    $app->make(TaskProvider::class),
                    $app->make(DocumentProvider::class),
                    $app->make(InvoiceProvider::class),
                ];
            });

        $this->app->when(AlertsService::class)
            ->needs('$providers')
            ->give(function ($app) {
                return [
                    $app->make(UnreadNotificationAlertProvider::class),
                    $app->make(OverdueInvoiceAlertProvider::class),
                ];
            });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
