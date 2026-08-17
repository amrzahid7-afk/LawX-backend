<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait DashboardChartTrait
{

    protected function getMonthlyChartData($query, string $dateColumn, string $sumColumn, int $months = 6): array
    {
        $startDate = now()->subMonths($months - 1)->startOfMonth();

        $results = (clone $query)
            ->where($dateColumn, '>=', $startDate)
            ->selectRaw("DATE_FORMAT({$dateColumn}, '%Y-%m') as month, SUM({$sumColumn}) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $values = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $labels[] = $date->format('M Y');
            $values[] = (float) ($results[$monthKey] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * دمج مصفوفتين من القيم (لجمع الرواتب + المصروفات مثلاً)
     */
    protected function mergeChartValues(array $a, array $b): array
    {
        return array_map(function ($va, $vb) {
            return round($va + $vb, 2);
        }, $a, $b);
    }

    /**
     * حساب الربح = الدخل - المصروفات
     */
    protected function calculateProfit(array $input, array $output): array
    {
        return array_map(function ($in, $out) {
            return round($in - $out, 2);
        }, $input, $output);
    }
}
