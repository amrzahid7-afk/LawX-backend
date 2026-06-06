<?php

namespace App\Services\Dashboard;

use App\Contracts\Services\Dashboard\FinancialStatisticsInterface;
use App\Models\Invoice;
use Carbon\Carbon;

class FinancialStatisticsService implements FinancialStatisticsInterface
{
    public function getFinancialStats($user, $caseIds = null): array
    {
        $query = Invoice::query();


        if ($caseIds) {
            $query->whereIn('case_id', $caseIds);
        }

        $totalPaid = (clone $query)->where('status', 'paid')->sum('total_amount');

        $totalUnpaid = (clone $query)->whereIn('status', ['unpaid', 'partial'])->sum('total_amount');

        $overdueInvoices = (clone $query)
            ->where(function ($q) {
                $q->where('status', 'overdue')
                  ->orWhere(function ($subQ) {
                      $subQ->whereIn('status', ['unpaid', 'partial'])
                           ->whereDate('due_date', '<', Carbon::today());
                  });
            })
            ->count();

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = (clone $query)
                ->where('status', 'paid')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', $m)
                ->sum('total_amount');
        }

        return [
            'total_paid'             => round($totalPaid, 2),
            'total_unpaid'           => round($totalUnpaid, 2),
            'overdue_invoices_count' => $overdueInvoices,
            'monthly_paid'           => $monthlyData,
        ];
    }
}
