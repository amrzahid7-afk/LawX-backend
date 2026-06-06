<?php
// App\Contracts\Services\Dashboard\AlertsAggregatorInterface.php

namespace App\Contracts\Services\Dashboard;

interface AlertsAggregatorInterface
{
    /**
     * يجمع التنبيهات من جميع المزودات (Providers)،
     * يدمجها، ويرتبها حسب التاريخ
     *
     * @param mixed $user المستخدم الحالي
     * @param array|null $caseIds معرفات القضايا المسموح بها
     * @return array قائمة التنبيهات النهائية المرتبة
     */
    public function getAlerts($user, $caseIds = null): array;
}
