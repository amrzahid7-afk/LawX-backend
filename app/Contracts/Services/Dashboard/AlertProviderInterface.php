<?php
// App\Contracts\Services\Dashboard\AlertProviderInterface.php

namespace App\Contracts\Services\Dashboard;

interface AlertProviderInterface
{
    /**
     * يجلب التنبيهات من مصدر واحد فقط (مثل: فواتير متأخرة، إشعارات غير مقروءة)
     *
     * @param mixed $user المستخدم الحالي
     * @param array|null $caseIds معرفات القضايا المسموح بها
     * @return array قائمة التنبيهات من هذا المصدر فقط
     */
    public function getAlerts($user, $caseIds = null): array;
}
