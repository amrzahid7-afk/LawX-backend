<?php

namespace App\Contracts\Services\Dashboard;

interface TeamPerformanceInterface
{
     /**
     * أداء الفريق: قائمة الموظفين مع عدد المهام المسندة لكل منهم
     * @return array
     */
    public function getTeamPerformance($user): array;
}
