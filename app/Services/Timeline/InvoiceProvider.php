<?php

namespace App\Services\Timeline;

use App\Contracts\Services\Timeline\TimelineEventProviderInterface;

class InvoiceProvider implements TimelineEventProviderInterface
{
    /**
     * الحصول على أحداث الفواتير
     * @param mixed $case
     * @return array
     */
    public function getEvents($case): array
    {
        $events = [];

        // التأكد من وجود فواتير مرتبطة بالقضية
        if (!$case->invoices) {
            return $events;
        }

        foreach ($case->invoices as $invoice) {
            $events[] = [
                'type'        => 'invoice',
                'title'       => 'فاتورة جديدة',
                'description' => "تم إصدار فاتورة للنظام بقيمة {$invoice->total_amount}",
                'date'        => $invoice->created_at,
                'user'        => 'النظام المالي',
                'meta'        => [
                    'status' => $invoice->status,
                    'due_date' => $invoice->due_date,
                ]
            ];
        }

        return $events;
    }
}
