<?php

namespace App\Interfaces;

use App\Models\User;

interface FinancialReportInterface
{

    public function getFinancialReport(User $user, string $from, string $to): array;
}
