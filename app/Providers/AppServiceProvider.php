<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Contracts\Services\Dashboard\AlertProviderInterface;  // قد لا تحتاجه في الـ bind مباشرة
use App\Contracts\Services\Dashboard\AlertsAggregatorInterface;
use App\Contracts\Services\Dashboard\CaseStatisticsInterface;
use App\Contracts\Services\Dashboard\FinancialStatisticsInterface;
use App\Contracts\Services\Dashboard\NotificationsInterface;
use App\Contracts\Services\Dashboard\TeamPerformanceInterface;
use App\Contracts\Services\Dashboard\TodayScheduleInterface;
use App\Contracts\Services\Timeline\TimelineEventProviderInterface;
use App\Interfaces\FinancialReportInterface;
use App\Models\PersonalAccessToken;
use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Services\Dashboard\Alerts\AlertsService;
use App\Services\Dashboard\Alerts\OverdueInvoiceAlertProvider;
use App\Services\Dashboard\Alerts\UnreadNotificationAlertProvider;
use App\Services\Dashboard\CaseStatisticsService;
use App\Services\Dashboard\FinancialStatisticsService;
use App\Services\Dashboard\NotificationsService;
use App\Services\Dashboard\TeamPerformanceService;
use App\Services\Dashboard\TodayScheduleService;
use App\Services\LawyerFinancialService;
use App\Services\OfficeManagerFinancialService;
use App\Services\StripePaymentService;
use App\Services\Timeline\CaseCreatedProvider;
use App\Services\Timeline\DocumentProvider;
use App\Services\Timeline\InvoiceProvider;
use App\Services\Timeline\SessionProvider;
use App\Services\Timeline\TaskProvider;
use App\Services\Timeline\TimelineAggregatorService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // My Services and Bindings
        if (class_exists(\Laravel\Ai\AiServiceProvider::class)) {
            $this->app->register(\Laravel\Ai\AiServiceProvider::class);
        }
        $this->app->bind(PaymentGatewayInterface::class, StripePaymentService::class);

        // $this->app->bind(FinancialReportInterface::class, function ($app) {
        //     $user = $app->make('request')->user(); // نحصل على المستخدم المسجل

        //     // إذا كان يملك مكتب (مدير مكتب) - نفذ سيرفس المدير
        //     if ($user && $user->lawyerProfile && $user->lawyerProfile->ownedOffice) {
        //         return new OfficeManagerFinancialService();
        //     }

        //     // غير ذلك (محامي عادي/مستقل/موظف) - نفذ سيرvfs المحامي
        //     return new LawyerFinancialService();
        // });
        // ---------------------------------------------------------------------------------------
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
        // 1. جيت لعرض التقرير المالي: يسمح للمحامين ومدراء المكاتب فقط
        Gate::define('view-financial-report', function ($user) {
            // تأكد أن المستخدم محامي، أو أنه مدير مكتب (يملك مكتب)
            $isLawyer = $user->isLawyer(); // دالتك الموجودة في موديل User
            $isOfficeManager = $user->lawyerProfile && $user->lawyerProfile->ownedOffice;

            return $isLawyer || $isOfficeManager;
        });

        // 2. جيت لعرض كشف الراتب: يسمح للمحامين فقط (لأن الموظفين هم محامين)
        Gate::define('view-salary-slip', function ($user) {
            return $user->isLawyer();
        });
        // Gate::policy(Task::class, TaskPolicy::class);
    }
}
