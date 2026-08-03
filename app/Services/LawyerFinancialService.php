<?php

namespace App\Services;

use App\Interfaces\FinancialReportInterface;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Salary;
use App\Models\Expense;

class LawyerFinancialService implements FinancialReportInterface
{
    public function getFinancialReport(User $user, string $from, string $to): array
    {
        dd("here");

        // 1. الإيرادات (Inflow Part A): فواتير قضاياه الخاصة المدفوعة
        $inflowFromOwnCases = Invoice::where('lawyer_id', $user->id)
            ->where('type', 'For_case')
            ->where('status', 'paid')
            ->whereBetween('updated_at', [$from, $to])
            ->sum('total_amount');

        // 2. الإيرادات (Inflow Part B): صافي راتبه إذا كان موظفاً في مكتب (Basic + Expenses)
        $inflowFromSalary = Salary::where('lawyer_id', $user->id)
            ->where('status', 'paid')
            ->whereBetween('updated_at', [$from, $to])
            ->sum('net_salary'); // صافي الراتب يشمل المصروفات كما تم حسابها في الكوماند

        $totalInflow = $inflowFromOwnCases + $inflowFromSalary;

        // 3. المصروفات (Outflow Part A): مصروفات قضاياه هو (حيث أنه lead_lawyer)
        $outflowOwnCaseExpenses = Expense::where('status', 'approved')
            ->whereHas('task.case', function ($query) use ($user) {
                $query->where('lead_lawyer_id', $user->id);
            })
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        // 4. المصروفات (Outflow Part B): مصروفات المهام الموكلة إليه من المكتب الذي يعمل فيه
        $outflowOfficeTasks = Expense::where('status', 'approved')
            ->whereHas('task', function ($query) use ($user) {
                // المهمة موكلة له
                $query->where('assigned_to', $user->id)
                    // والقضية تابعة لمكتب (ليست قضية شخصية)
                    ->whereHas('case', function ($q2) {
                        $q2->whereNotNull('office_id');
                    });
            })
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        $totalOutflow = $outflowOwnCaseExpenses + $outflowOfficeTasks;

        return [
            'period' => ['from' => $from, 'to' => $to],
            'inflow' => [
                'own_case_invoices' => $inflowFromOwnCases,
                'received_salaries' => $inflowFromSalary,
                'total_inflow' => $totalInflow,
            ],
            'outflow' => [
                'own_case_expenses' => $outflowOwnCaseExpenses,
                'office_assigned_tasks_expenses' => $outflowOfficeTasks,
                'total_outflow' => $totalOutflow,
            ],
            'net_profit' => $totalInflow - $totalOutflow,
        ];
    }
}
