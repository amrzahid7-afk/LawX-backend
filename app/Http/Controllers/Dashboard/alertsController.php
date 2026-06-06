<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Contracts\Services\Dashboard\AlertsAggregatorInterface;  // 🔄 التغيير هنا
use Illuminate\Http\Request;

class alertsController extends Controller
{
    protected $alertsAggregator;  // 🔄 تغيير اسم الخاصية (اختياري ولكن مستحب)

    // 🔄 تغيير نوع الـ parameter في الكونستركتور
    public function __construct(AlertsAggregatorInterface $alertsAggregator)
    {
        $this->alertsAggregator = $alertsAggregator;
    }

    public function index(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير مسجل دخوله أو انتهت صلاحية الجلسة',
            ], 401);
        }


        $alerts = $this->alertsAggregator->getAlerts($user);

        return response()->json([
            'status' => true,
            'data' => [
                'alerts_count' => count($alerts),
                'alerts'       => $alerts,
            ],
        ], 200);
    }
}
