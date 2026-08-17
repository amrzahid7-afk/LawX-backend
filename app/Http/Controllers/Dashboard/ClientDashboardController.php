<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use App\Models\Invoice;
use App\Models\Legal_request;
use App\Models\Session;
use App\Models\SignatureRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $caseIds = $user->clientCases()->pluck('cases.id');

        $statistics = [
            // القضايا (open, close, pending)
            'total_cases'     => $caseIds->count(),
            'active_cases'    => $this->countCasesByStatus($caseIds, 'open'),
            'completed_cases' => $this->countCasesByStatus($caseIds, 'close'),
            'pending_cases'   => $this->countCasesByStatus($caseIds, 'pending'),

            // الطلبات القانونية
            'total_requests'   => Legal_request::where('client_id', $user->id)->count(),
            'pending_requests' => Legal_request::where('client_id', $user->id)->where('status', 'pending')->count(),

            // الفواتير (unpaid, partial, paid, overdue)
            'total_invoices'       => Invoice::where('client_id', $user->id)->count(),
            'paid_invoices'        => Invoice::where('client_id', $user->id)->where('status', 'paid')->count(),
            'unpaid_invoices'      => Invoice::where('client_id', $user->id)->whereIn('status', ['unpaid', 'partial'])->count(),
            'overdue_invoices'     => Invoice::where('client_id', $user->id)->where('status', 'overdue')->count(),
            'total_paid_amount'    => Invoice::where('client_id', $user->id)->where('status', 'paid')->sum('total_amount'),
            'total_unpaid_amount'  => Invoice::where('client_id', $user->id)->whereIn('status', ['unpaid', 'partial'])->sum('total_amount'),

            // الجلسات (حساب القادمة ديناميكياً لأنه لا يوجد scheduled)
            'upcoming_sessions'  => $this->countUpcomingSessions($caseIds),
            'completed_sessions' => $this->countSessionsByStatus($caseIds, 'completed'),

            // التوقيعات
            'pending_signatures' => $this->countPendingSignatures($caseIds),
        ];

        $recentCases = CaseModel::whereIn('id', $caseIds)
            ->select('id', 'case_number', 'title', 'status', 'progress_percentage', 'importance_stars', 'created_at')
            ->with('leadLawyer:id,full_name,avatar_path')
            ->latest()->take(5)->get();

        $recentInvoices = Invoice::where('client_id', $user->id)
            ->select('id', 'total_amount', 'status', 'type', 'due_date', 'created_at')
            ->with('case:id,case_number,title')
            ->latest()->take(5)->get();

        $upcomingSessions = Session::whereIn('case_id', $caseIds)
            ->select('id', 'case_id', 'date', 'location', 'status', 'nots')
            ->where('date', '>=', now())
            ->where('status', 'pending') // الجلسات القادمة هي التي حالتها pending وتاريخها لم يحن بعد
            ->with('case:id,case_number,title')
            ->orderBy('date')->take(5)->get();

        $pendingSignatures = SignatureRequest::whereHas('document', function ($q) use ($caseIds) {
            $q->whereIn('case_id', $caseIds);
        })->whereIn('status', ['pending', 'in_progress'])
            ->select('id', 'document_id', 'status', 'created_at')
            ->with('document:id,name')
            ->latest()->take(5)->get();

        $alerts = $this->getClientAlerts($user, $caseIds);

        return response()->json(['success' => true, 'data' => [
            'statistics'         => $statistics,
            'recent_cases'       => $recentCases,
            'recent_invoices'    => $recentInvoices,
            'upcoming_sessions'  => $upcomingSessions,
            'pending_signatures' => $pendingSignatures,
            'alerts'             => $alerts,
        ]]);
    }

    private function countCasesByStatus($caseIds, string $status): int
    {
        return $caseIds->isEmpty() ? 0 : CaseModel::whereIn('id', $caseIds)->where('status', $status)->count();
    }

    private function countUpcomingSessions($caseIds): int
    {
        return $caseIds->isEmpty() ? 0 : Session::whereIn('case_id', $caseIds)->where('date', '>=', now())->where('status', 'pending')->count();
    }

    private function countSessionsByStatus($caseIds, string $status): int
    {
        return $caseIds->isEmpty() ? 0 : Session::whereIn('case_id', $caseIds)->where('status', $status)->count();
    }

    private function countPendingSignatures($caseIds): int
    {
        return $caseIds->isEmpty() ? 0 : SignatureRequest::whereHas('document', fn($q) => $q->whereIn('case_id', $caseIds))->whereIn('status', ['pending', 'in_progress'])->count();
    }

    private function getClientAlerts($user, $caseIds): array
    {
        $alerts = [];
        $overdueCount = Invoice::where('client_id', $user->id)->where('status', 'overdue')->count();
        if ($overdueCount > 0) $alerts[] = ['type' => 'warning', 'message' => "لديك {$overdueCount} فاتورة متأخرة الدفع", 'key' => 'overdue_invoices', 'count' => $overdueCount];

        if ($caseIds->isNotEmpty()) {
            $soonSessions = Session::whereIn('case_id', $caseIds)->where('date', '>=', now())->where('date', '<=', now()->addDay())->where('status', 'pending')->count();
            if ($soonSessions > 0) $alerts[] = ['type' => 'info', 'message' => "لديك {$soonSessions} جلسة خلال الـ 24 ساعة القادمة", 'key' => 'upcoming_sessions_soon', 'count' => $soonSessions];
        }

        $sigCount = $this->countPendingSignatures($caseIds);
        if ($sigCount > 0) $alerts[] = ['type' => 'info', 'message' => "لديك {$sigCount} ملف ينتظر توقيعك", 'key' => 'pending_signatures', 'count' => $sigCount];

        return $alerts;
    }
}
