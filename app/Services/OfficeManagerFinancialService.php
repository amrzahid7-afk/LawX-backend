<?php

namespace App\Services;

use App\Interfaces\FinancialReportInterface;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Salary;
use App\Models\Expense;

class OfficeManagerFinancialService implements FinancialReportInterface
{
    public function getFinancialReport(User $user, string $from, string $to): array
    {
        dd("here");

        // جلب المكتب الذي يملكه هذا المستخدم
        $office = $user->lawyerProfile?->ownedOffice;
        if (!$office) {
            return ['error' => 'User does not own an office'];
        }
        dd("here");
        // 1. الإيرادات (Inflow): فواتير الخدمات المدفوعة الخاصة بقضايا هذا المكتب
        $inflowFromClients = Invoice::where('type', 'For_case')
            ->where('status', 'paid')
            ->whereHas('case', function ($query) use ($office) {
                $query->where('office_id', $office->id);
            })
            ->whereBetween('updated_at', [$from, $to])
            ->sum('total_amount');

        // 2. المصروفات (Outflow Part A): الرواتب الأساسية المدفوعة لموظفي هذا المكتب فقط
        $outflowBaseSalaries = Salary::where('status', 'paid')
            ->whereHas('lawyer.lawyerProfile', function ($query) use ($office) {
                $query->where('employed_In_office_id', $office->id); // تأكد من الاسم في DB
            })
            ->whereBetween('updated_at', [$from, $to])
            ->sum('base_salary'); // أخذنا الأساسي فقط كما طلبت

        // 3. المصروفات (Outflow Part B): كل مصروفات المهام لكل القضايا في هذا المكتب
        $outflowCaseExpenses = Expense::where('status', 'approved')
            ->whereHas('task.case', function ($query) use ($office) {
                $query->where('office_id', $office->id);
            })
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        $totalOutflow = $outflowBaseSalaries + $outflowCaseExpenses;

        return [
            'period' => ['from' => $from, 'to' => $to],
            'inflow' => [
                'total_client_payments' => $inflowFromClients,
            ],
            'outflow' => [
                'total_base_salaries_paid' => $outflowBaseSalaries,
                'total_office_case_expenses' => $outflowCaseExpenses,
                'total_outflow' => $totalOutflow,
            ],
            'net_profit' => $inflowFromClients - $totalOutflow,
        ];
    }
}
