<?php

namespace App\Services\Dashboard\Alerts;

use App\Contracts\Services\Dashboard\AlertProviderInterface;
use App\Models\Invoice;
use Carbon\Carbon;

class OverdueInvoiceAlertProvider implements AlertProviderInterface
{
    public function getAlerts($user, $caseIds = null): array
    {
        $alerts = [];
        $query = Invoice::query();

        if ($caseIds) {
            $query->whereIn('case_id', $caseIds);
        }

        $overdueInvoices = $query->where(function ($q) {
            $q->where('status', 'overdue')
              ->orWhere(function ($subQ) {
                  $subQ->whereIn('status', ['unpaid', 'partial'])
                       ->whereDate('due_date', '<', Carbon::today());
              });
        })->limit(3)->get();

        foreach ($overdueInvoices as $invoice) {
            $alerts[] = [
                'type' => 'overdue_invoice',
                'title' => 'فاتورة متأخرة',
                'message' => "الفاتورة رقم {$invoice->id} بقيمة {$invoice->total_amount} غير مدفوعة بالكامل",
                'date' => $invoice->created_at,
            ];
        }

        return $alerts;
    }
}
