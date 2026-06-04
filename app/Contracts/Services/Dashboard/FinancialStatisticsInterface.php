<?php

namespace App\Contracts\Services\Dashboard;

interface FinancialStatisticsInterface
{
    /**
     * الإحصائيات المالية: إجمالي مدفوع، غير مدفوع، فواتير متأخرة، رسم بياني شهري
     * @return array
     */
    public function getFinancialStats($user, $caseIds = null): array;
}
