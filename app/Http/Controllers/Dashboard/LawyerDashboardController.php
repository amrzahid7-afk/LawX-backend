<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CaseModel;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Office;
use App\Models\Session;
use App\Models\Task;
use App\Traits\DashboardChartTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LawyerDashboardController extends Controller
{
    use DashboardChartTrait;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $lawyerProfile = $user->lawyerProfile;

        if (!$lawyerProfile) {
            return response()->json(['success' => false, 'message' => 'لم يتم العثور على ملف المحامي'], 404);
        }

        $office = Office::where('owner_lawyer_profile_id', $lawyerProfile->id)->first();

        if ($office && $office->status === 'active') {
            return $this->officeManagerDashboard($request, $user, $lawyerProfile, $office);
        }

        if ($lawyerProfile->isTrainee()) { // استخدام الميثود الموجودة في الموديل
            return $this->traineeDashboard($user, $lawyerProfile);
        }

        return $this->licensedLawyerDashboard($request, $user, $lawyerProfile);
    }

    // =====================================================
    //  المحامي المرخص
    // =====================================================
    private function licensedLawyerDashboard(Request $request, $user, $lawyerProfile): JsonResponse
    {
        $caseIds = $this->getLawyerCaseIds($user->id);

        $statistics = [
            'total_cases'       => $caseIds->count(),
            'active_cases'      => $this->countCasesByStatus($caseIds, 'open'),
            'completed_cases'   => $this->countCasesByStatus($caseIds, 'close'),
            'pending_cases'     => $this->countCasesByStatus($caseIds, 'pending'),

            'total_tasks'       => $this->countLawyerTasks($user->id, $caseIds),
            'pending_tasks'     => $this->countTasksByStatus($user->id, 'pending', $caseIds),
            'in_progress_tasks' => $this->countTasksByStatus($user->id, 'in_progress', $caseIds),
            'completed_tasks'   => $this->countTasksByStatus($user->id, 'completed', $caseIds),
            'overdue_tasks'     => $this->countOverdueTasks($user->id, $caseIds), // محسوب ديناميكياً

            'upcoming_sessions'  => $this->countUpcomingSessions($caseIds),
            'completed_sessions' => $this->countSessionsByStatus($caseIds, 'completed'),

            'unpaid_invoices' => Invoice::where('lawyer_id', $user->id)->whereIn('status', ['unpaid', 'partial'])->count(),
            'paid_invoices'   => Invoice::where('lawyer_id', $user->id)->where('status', 'paid')->count(),
            'total_earned'    => Invoice::where('lawyer_id', $user->id)->where('status', 'paid')->sum('total_amount'),
        ];

        $months = (int) ($request->query('months') ?? 6);
        $financialChart = $this->getLicensedLawyerFinancialChart($user->id, $caseIds, $months);

        $recentCases = CaseModel::whereIn('id', $caseIds)
            ->select('id', 'case_number', 'title', 'status', 'progress_percentage', 'importance_stars', 'created_at')
            ->with('clients:id,full_name,avatar_path')
            ->latest()->take(5)->get();

        $pendingTasks = Task::where('assigned_to', $user->id)
            ->when($caseIds->isNotEmpty(), fn($q) => $q->whereIn('case_id', $caseIds))
            ->whereIn('status', ['pending', 'in_progress'])
            ->select('id', 'title', 'due_date', 'status', 'case_id')
            ->with('case:id,case_number,title')
            ->orderBy('due_date')->take(10)->get();

        $upcomingSessions = Session::whereIn('case_id', $caseIds)
            ->select('id', 'case_id', 'date', 'location', 'status')
            ->where('date', '>=', now())->where('status', 'pending')
            ->with('case:id,case_number,title')
            ->orderBy('date')->take(5)->get();

        return response()->json([
            'success'     => true,
            'lawyer_type' => 'licensed',
            'data'        => [
                'statistics'        => $statistics,
                'financial_chart'   => $financialChart,
                'recent_cases'      => $recentCases,
                'pending_tasks'     => $pendingTasks,
                'upcoming_sessions' => $upcomingSessions,
                'alerts'            => $this->getLawyerAlerts($user, $caseIds),
            ],
        ]);
    }

    // =====================================================
    //  مدير المكتب
    // =====================================================
    private function officeManagerDashboard(Request $request, $user, $lawyerProfile, Office $office): JsonResponse
    {
        $officeCaseIds = CaseModel::where('office_id', $office->id)->pluck('id');
        $teamCount = $office->employees()->count();

        $statistics = [
            'total_office_cases' => $officeCaseIds->count(),
            'active_cases'       => $this->countCasesByStatus($officeCaseIds, 'open'),
            'completed_cases'    => $this->countCasesByStatus($officeCaseIds, 'close'),
            'pending_cases'      => $this->countCasesByStatus($officeCaseIds, 'pending'),

            'team_members'      => $teamCount,
            'total_tasks'       => Task::whereIn('case_id', $officeCaseIds)->count(),
            'pending_tasks'     => Task::whereIn('case_id', $officeCaseIds)->where('status', 'pending')->count(),
            'in_progress_tasks' => Task::whereIn('case_id', $officeCaseIds)->where('status', 'in_progress')->count(),
            'completed_tasks'   => Task::whereIn('case_id', $officeCaseIds)->where('status', 'completed')->count(),
            'overdue_tasks'     => $this->countOfficeOverdueTasks($officeCaseIds),

            'upcoming_sessions'  => $this->countUpcomingSessions($officeCaseIds),
            'completed_sessions' => $this->countSessionsByStatus($officeCaseIds, 'completed'),

            'total_invoices'  => Invoice::whereIn('case_id', $officeCaseIds)->where('type', 'For_case')->count(),
            'paid_invoices'   => Invoice::whereIn('case_id', $officeCaseIds)->where('type', 'For_case')->where('status', 'paid')->count(),
            'total_revenue'   => Invoice::whereIn('case_id', $officeCaseIds)->where('type', 'For_case')->where('status', 'paid')->sum('total_amount'),

            'pending_salary_payments'    => Invoice::where('lawyer_id', $user->id)->where('type', 'salary')->whereIn('status', ['unpaid', 'partial'])->count(),
            'pending_expense_approvals'  => Expense::whereHas('task', fn($q) => $q->whereIn('case_id', $officeCaseIds))->where('status', 'pending')->count(),
        ];

        $months = (int) ($request->query('months') ?? 6);
        $financialChart = $this->getOfficeManagerFinancialChart($user->id, $officeCaseIds, $months);

        $taskDistribution = Task::whereIn('case_id', $officeCaseIds)
            ->select('assigned_to', 'status')
            ->with('assignedTo:id,full_name,avatar_path')
            ->get()->groupBy('assigned_to')
            ->map(function ($tasks) {
                $user = $tasks->first()->assignedTo;
                return [
                    'lawyer'      => $user ? $user->only('id', 'full_name', 'avatar_path') : null,
                    'total_tasks' => $tasks->count(),
                    'pending'     => $tasks->where('status', 'pending')->count(),
                    'in_progress' => $tasks->where('status', 'in_progress')->count(),
                    'completed'   => $tasks->where('status', 'completed')->count(),
                ];
            })->values();

        $pendingTasks = Task::whereIn('case_id', $officeCaseIds)
            ->whereIn('status', ['pending', 'in_progress'])
            ->select('id', 'title', 'due_date', 'status', 'assigned_to', 'case_id')
            ->with(['assignedTo:id,full_name,avatar_path', 'case:id,case_number,title'])
            ->orderBy('due_date')->take(10)->get();

        return response()->json([
            'success'     => true,
            'lawyer_type' => 'office_manager',
            'office'      => ['id' => $office->id, 'name' => $office->name],
            'data'        => [
                'statistics'        => $statistics,
                'financial_chart'   => $financialChart,
                'task_distribution' => $taskDistribution,
                'pending_tasks'     => $pendingTasks,
                'alerts'            => $this->getOfficeManagerAlerts($user, $office, $officeCaseIds),
            ],
        ]);
    }

    // =====================================================
    //  المتدرب
    // =====================================================
    private function traineeDashboard($user, $lawyerProfile): JsonResponse
    {
        $tasksQuery = Task::where('assigned_to', $user->id);

        $statistics = [
            'total_tasks'       => $tasksQuery->count(),
            'pending_tasks'     => (clone $tasksQuery)->where('status', 'pending')->count(),
            'in_progress_tasks' => (clone $tasksQuery)->where('status', 'in_progress')->count(),
            'completed_tasks'   => (clone $tasksQuery)->where('status', 'completed')->count(),
            'overdue_tasks'     => (clone $tasksQuery)->where('status', '!=', 'completed')->where('due_date', '<', now())->count(),
            'upcoming_deadlines' => (clone $tasksQuery)->where('status', '!=', 'completed')->where('due_date', '>=', now())->where('due_date', '<=', now()->addDays(7))->count(),
        ];

        $tasks = $tasksQuery->select('id', 'title', 'description', 'due_date', 'status', 'case_id')
            ->with('case:id,case_number,title')
            ->orderByRaw("FIELD(status, 'in_progress', 'pending', 'completed')")
            ->orderBy('due_date')->get();

        $upcomingDeadlines = Task::where('assigned_to', $user->id)
            ->where('status', '!=', 'completed')->where('due_date', '>=', now())->where('due_date', '<=', now()->addDays(7))
            ->select('id', 'title', 'due_date', 'status', 'case_id')
            ->with('case:id,case_number,title')->orderBy('due_date')->get();

        $alerts = [];
        if ($statistics['overdue_tasks'] > 0) $alerts[] = ['type' => 'danger', 'message' => "لديك {$statistics['overdue_tasks']} مهمة متأخرة عن موعدها", 'key' => 'overdue_tasks', 'count' => $statistics['overdue_tasks']];
        if ($statistics['upcoming_deadlines'] > 0) $alerts[] = ['type' => 'warning', 'message' => "لديك {$statistics['upcoming_deadlines']} مهمة موعدها خلال الأيام السبعة القادمة", 'key' => 'upcoming_deadlines', 'count' => $statistics['upcoming_deadlines']];

        return response()->json([
            'success'     => true,
            'lawyer_type' => 'trainee',
            'data'        => [
                'statistics'         => $statistics,
                'tasks'              => $tasks,
                'upcoming_deadlines' => $upcomingDeadlines,
                'alerts'             => $alerts,
            ],
        ]);
    }

    // =====================================================
    //  الرسوم البيانية المالية
    // =====================================================
    private function getLicensedLawyerFinancialChart(string $lawyerId, $caseIds, int $months): array
    {
        // الدخل: الفواتير المدفوعة للقضايا (نوعها For_case)
        $inputQuery = Invoice::where('lawyer_id', $lawyerId)->where('status', 'paid')->where('type', 'For_case');
        $input = $this->getMonthlyChartData($inputQuery, 'created_at', 'total_amount', $months);

        // المصروفات: مصروفات المهام المعتمدة (approved)
        $taskIds = Task::whereIn('case_id', $caseIds)->pluck('id');
        $expenseQuery = Expense::whereIn('task_id', $taskIds)->where('status', 'approved');
        $output = $this->getMonthlyChartData($expenseQuery, 'expense_date', 'amount', $months);

        return [
            'labels'       => $input['labels'],
            'input_money'  => $input['values'],
            'output_money' => $output['values'],
            'profit'       => $this->calculateProfit($input['values'], $output['values']),
        ];
    }

    private function getOfficeManagerFinancialChart(string $managerId, $officeCaseIds, int $months): array
    {
        // الدخل: فواتير العملاء المدفوعة للقضايا (For_case)
        $inputQuery = Invoice::whereIn('case_id', $officeCaseIds)->where('status', 'paid')->where('type', 'For_case');
        $input = $this->getMonthlyChartData($inputQuery, 'created_at', 'total_amount', $months);

        // المصروفات 1: الرواتب المدفوعة
        $salaryQuery = Invoice::where('lawyer_id', $managerId)->where('type', 'salary')->where('status', 'paid');
        $salaryOutput = $this->getMonthlyChartData($salaryQuery, 'created_at', 'total_amount', $months);

        // المصروفات 2: مصروفات القضايا المعتمدة
        $officeTaskIds = Task::whereIn('case_id', $officeCaseIds)->pluck('id');
        $expenseQuery = Expense::whereIn('task_id', $officeTaskIds)->where('status', 'approved');
        $expenseOutput = $this->getMonthlyChartData($expenseQuery, 'expense_date', 'amount', $months);

        // دمج المصروفات
        $totalOutput = $this->mergeChartValues($salaryOutput['values'], $expenseOutput['values']);

        return [
            'labels'          => $input['labels'],
            'input_money'     => $input['values'],
            'output_money'    => $totalOutput,
            'salary_expenses' => $salaryOutput['values'],
            'case_expenses'   => $expenseOutput['values'],
            'profit'          => $this->calculateProfit($input['values'], $totalOutput),
        ];
    }

    // =====================================================
    //  Helper Methods
    // =====================================================
    private function getLawyerCaseIds(string $lawyerId)
    {
        return CaseModel::where('lead_lawyer_id', $lawyerId)
            ->orWhereHas('team', fn($q) => $q->where('user_id', $lawyerId))
            ->pluck('id');
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

    private function countLawyerTasks(string $lawyerId, $caseIds): int
    {
        $query = Task::where('assigned_to', $lawyerId);
        if ($caseIds->isNotEmpty()) $query->whereIn('case_id', $caseIds);
        return $query->count();
    }

    private function countTasksByStatus(string $lawyerId, string $status, $caseIds): int
    {
        $query = Task::where('assigned_to', $lawyerId)->where('status', $status);
        if ($caseIds->isNotEmpty()) $query->whereIn('case_id', $caseIds);
        return $query->count();
    }

    private function countOverdueTasks(string $lawyerId, $caseIds): int
    {
        $query = Task::where('assigned_to', $lawyerId)->where('status', '!=', 'completed')->where('due_date', '<', now());
        if ($caseIds->isNotEmpty()) $query->whereIn('case_id', $caseIds);
        return $query->count();
    }

    private function countOfficeOverdueTasks($officeCaseIds): int
    {
        return $officeCaseIds->isEmpty() ? 0 : Task::whereIn('case_id', $officeCaseIds)->where('status', '!=', 'completed')->where('due_date', '<', now())->count();
    }

    private function getLawyerAlerts($user, $caseIds): array
    {
        $alerts = [];
        $overdueCount = $this->countOverdueTasks($user->id, $caseIds);
        if ($overdueCount > 0) $alerts[] = ['type' => 'danger', 'message' => "لديك {$overdueCount} مهمة متأخرة", 'key' => 'overdue_tasks', 'count' => $overdueCount];

        if ($caseIds->isNotEmpty()) {
            $soonSessions = Session::whereIn('case_id', $caseIds)->where('date', '>=', now())->where('date', '<=', now()->addDay())->where('status', 'pending')->count();
            if ($soonSessions > 0) $alerts[] = ['type' => 'info', 'message' => "لديك {$soonSessions} جلسة خلال الـ 24 ساعة", 'key' => 'upcoming_sessions_soon', 'count' => $soonSessions];
        }

        $unpaidCount = Invoice::where('lawyer_id', $user->id)->whereIn('status', ['unpaid', 'partial'])->count();
        if ($unpaidCount > 0) $alerts[] = ['type' => 'warning', 'message' => "لديك {$unpaidCount} فاتورة معلّقة", 'key' => 'pending_invoices', 'count' => $unpaidCount];

        return $alerts;
    }

    private function getOfficeManagerAlerts($user, Office $office, $officeCaseIds): array
    {
        $alerts = [];
        $pendingExpenses = Expense::whereHas('task', fn($q) => $q->whereIn('case_id', $officeCaseIds))->where('status', 'pending')->count();
        if ($pendingExpenses > 0) $alerts[] = ['type' => 'warning', 'message' => "لديك {$pendingExpenses} مصروفات تنتظر موافقتك", 'key' => 'pending_expense_approvals', 'count' => $pendingExpenses];

        $pendingSalaries = Invoice::where('lawyer_id', $user->id)->where('type', 'salary')->whereIn('status', ['unpaid', 'partial'])->count();
        if ($pendingSalaries > 0) $alerts[] = ['type' => 'warning', 'message' => "لديك {$pendingSalaries} راتب معلّق", 'key' => 'pending_salaries', 'count' => $pendingSalaries];

        $overdueTasks = $this->countOfficeOverdueTasks($officeCaseIds);
        if ($overdueTasks > 0) $alerts[] = ['type' => 'danger', 'message' => "يوجد {$overdueTasks} مهمة متأخرة في المكتب", 'key' => 'office_overdue_tasks', 'count' => $overdueTasks];

        return $alerts;
    }
}
