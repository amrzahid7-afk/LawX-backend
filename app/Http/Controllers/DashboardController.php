<?php

namespace App\Http\Controllers;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;

use App\Services\Dashboard\DashboardAggregatorService;

class DashboardController extends Controller
{

    protected $dashboardAggregator;

    public function __construct(DashboardAggregatorService $dashboardAggregator)
    {
        $this->dashboardAggregator = $dashboardAggregator;
    }

    public function getStatistics()
    {
        $user = auth('api')->user();
        $data = $this->dashboardAggregator->getDashboardData($user);
        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }
}
