<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardAggregatorService;
use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardAggregator;

    public function __construct(DashboardAggregatorService $dashboardAggregator)
    {
        $this->dashboardAggregator = $dashboardAggregator;
    }

    public function getStatistics(Request $request)
    {
        // جلب المستخدم المؤمن الحالي (يدعم Sanctum وطلبات الـ API بكل سلاسة)
        $user = $request->user() ?? auth::user();

         if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'المستخدم غير مسجل في النظام',
            ], 401);
        }

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'المستخدم غير مسجل في النظام',
            ], 401);
        }

        $data = $this->dashboardAggregator->getDashboardData($user);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ], 200);
    }
}
