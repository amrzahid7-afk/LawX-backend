<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LawerProfile;
use App\Models\Legislation;
use App\Models\Office;
use App\Models\SmartTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyndicateDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // ===== طلبات النظام بناءً على هيكل DB الفعلي =====
        // المحامين المعلقين = من لم يتم إثبات حسابه بعد (is_claimed = false)
        $pendingLawyers = LawerProfile::where('is_claimed', false)->count();
        $urgentLawyers = LawerProfile::where('is_claimed', false)->where('created_at', '<', now()->subDays(7))->count();

        // المكاتب المعلقة = التي حالتها suspended (تنتظر تفعيل النقابة)
        $pendingOffices = Office::where('status', 'suspended')->count();
        $urgentOffices = Office::where('status', 'suspended')->where('created_at', '<', now()->subDays(7))->count();

        $totalPending = $pendingLawyers + $pendingOffices;
        $totalUrgent  = $urgentLawyers + $urgentOffices;
        $totalTasks   = $totalPending + $totalUrgent; // إجمالي ما يحتاج تدخل

        // ===== إحصائيات المحامين =====
        $totalLawyers    = LawerProfile::where('is_claimed', true)->where('is_suspended', false)->count();
        $licensedLawyers = LawerProfile::where('is_claimed', true)->where('is_suspended', false)->where('lawyer_state', 'licensed')->count();
        $traineeLawyers  = LawerProfile::where('is_claimed', true)->where('is_suspended', false)->where('lawyer_state', 'trainee')->count();
        $suspendedLawyers = LawerProfile::where('is_suspended', true)->count();

        // ===== إحصائيات المحتوى =====
        $totalLaws     = Legislation::count();
        $totalTemplates = SmartTemplate::count();

        $statistics = [
            'pending_requests'    => $totalPending,
            'urgent_requests'     => $totalUrgent,
            'total_tasks'         => $totalTasks,

            'pending_lawyer_registrations' => $pendingLawyers,
            'pending_office_activations'   => $pendingOffices,
            'urgent_lawyer_registrations'  => $urgentLawyers,
            'urgent_office_activations'    => $urgentOffices,

            'total_lawyers'     => $totalLawyers,
            'licensed_lawyers'  => $licensedLawyers,
            'trainee_lawyers'   => $traineeLawyers,
            'suspended_lawyers' => $suspendedLawyers,

            'total_laws'      => $totalLaws,
            'total_templates' => $totalTemplates,
        ];

        // ===== قوائم الطلبات =====
        $recentLawyerRegistrations = LawerProfile::where('is_claimed', false)
            ->select('id', 'user_id', 'lawyer_state', 'created_at')
            ->with('user:id,full_name,email,phone')
            ->latest()->take(5)->get()
            ->map(function ($profile) {
                return [
                    'id'           => $profile->id,
                    'lawyer_name'  => $profile->user?->full_name,
                    'lawyer_email' => $profile->user?->email,
                    'type'         => $profile->lawyer_state,
                    'request_type' => 'lawyer_registration',
                    'created_at'   => $profile->created_at->format('Y-m-d'),
                    'is_urgent'    => $profile->created_at->lt(now()->subDays(7)),
                ];
            });

        $recentOfficeRequests = Office::where('status', 'suspended')
            ->select('id', 'name', 'owner_lawyer_profile_id', 'created_at')
            ->with('owner.user:id,full_name,email,phone')
            ->latest()->take(5)->get()
            ->map(function ($office) {
                return [
                    'id'           => $office->id,
                    'office_name'  => $office->name,
                    'owner_name'   => $office->owner?->user?->full_name,
                    'owner_email'  => $office->owner?->user?->email,
                    'request_type' => 'office_activation',
                    'created_at'   => $office->created_at->format('Y-m-d'),
                    'is_urgent'    => $office->created_at->lt(now()->subDays(7)),
                ];
            });

        // دمج وترتيب كل الطلبات
        $allRequests = collect()
            ->merge($recentLawyerRegistrations)
            ->merge($recentOfficeRequests)
            ->sortByDesc('created_at')->take(10)->values();

        // ===== التنبيهات =====
        $alerts = $this->getSyndicateAlerts($totalUrgent, $totalPending);

        return response()->json([
            'success' => true,
            'data'    => [
                'statistics'                  => $statistics,
                'recent_requests'             => $allRequests,
                'recent_lawyer_registrations' => $recentLawyerRegistrations,
                'recent_office_requests'      => $recentOfficeRequests,
                'alerts'                      => $alerts,
            ],
        ]);
    }

    private function getSyndicateAlerts(int $urgentCount, int $pendingCount): array
    {
        $alerts = [];
        if ($urgentCount > 0) {
            $alerts[] = ['type' => 'danger', 'message' => "يوجد {$urgentCount} طلب عاجل ينتظر المراجعة (معلّق أكثر من 7 أيام)", 'key' => 'urgent_requests', 'count' => $urgentCount];
        }
        if ($pendingCount > 0) {
            $alerts[] = ['type' => 'warning', 'message' => "يوجد {$pendingCount} طلب معلّق يحتاج مراجعة", 'key' => 'pending_requests', 'count' => $pendingCount];
        }
        return $alerts;
    }
}
