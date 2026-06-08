<?php

namespace App\Services\Dashboard\Alerts;

use App\Contracts\Services\Dashboard\AlertsAggregatorInterface;
use App\Models\CaseModel;

class AlertsService implements AlertsAggregatorInterface
{
    protected $providers = [];

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function getAlerts($user, $caseIds = null): array
    {
        $allAlerts = [];
        $roleName = $user->role->name ?? '';

        // 🛡️ بناء استعلام ذكي لمعرفة القضايا المسموح للمستخدم رؤية تنبيهاتها
        // نتحقق أولاً أنه ليس أدمن، وأن المصفوفة لم يتم تمريرها مسبقاً
        if ($roleName !== 'admin' && empty($caseIds)) {
            if ($roleName === 'lawyer') {
                // المحامي يرى تنبيهات قضاياه الرئيسية أو التي يشارك في فريقها
                $caseIds = CaseModel::where('lead_lawyer_id', $user->id)
                    ->orWhereHas('team', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    })
                    ->pluck('id')
                    ->toArray();
            } elseif ($roleName === 'client') {
                // العميل يرى تنبيهات القضايا المرتبطة به
                $caseIds = CaseModel::whereHas('clients', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->pluck('id')
                ->toArray();
            }
        }

        // تدوير الطلب على الـ Providers وتمرير معرفات القضايا المفلترة
        foreach ($this->providers as $provider) {
            $allAlerts = array_merge($allAlerts, $provider->getAlerts($user, $caseIds));
        }

        // ترتيب التنبيهات من الأحدث إلى الأقدم
        usort($allAlerts, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $allAlerts;
    }
}
