<?php

namespace App\Services\Dashboard\Alerts;

use App\Contracts\Services\Dashboard\AlertProviderInterface;
use Illuminate\Support\Facades\DB;

class UnreadNotificationAlertProvider implements AlertProviderInterface
{
    public function getAlerts($user, $caseIds = null): array
    {
        $alerts = [];

        if (!$user) {
            return $alerts;
        }

        $notifications = DB::table('notifications')
            ->where('notifiable_type', get_class($user)) // يحضر مثلاً 'App\Models\User'
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($notifications as $notif) {
            // في جدول لارافيل، البيانات النصية وعنوان الإشعار يتم تخزينها داخل حقل الـ JSON المسمى 'data'
            $data = json_decode($notif->data, true);

            $alerts[] = [
                'type' => 'notification',
                'title' => $data['title'] ?? 'إشعار جديد',
                'message' => $data['message'] ?? ($data['text'] ?? 'لديك تحديث جديد في النظام'),
                'date' => $notif->created_at,
            ];
        }

        return $alerts;
    }
}
