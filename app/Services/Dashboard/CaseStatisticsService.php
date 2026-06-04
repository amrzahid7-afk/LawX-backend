<?php

namespace App\Services\Dashboard;

use App\Contracts\Services\Dashboard\CaseStatisticsInterface;
use App\Models\CaseModel;
use App\Models\Session;
use Carbon\Carbon;

class CaseStatisticsService implements CaseStatisticsInterface
 {
public function getClosedCasesCount($user, $caseIds = null): int
{
$query=CaseModel::where('status','close');
if ($caseIds) {
    $query->whereIn('id', $caseIds);
}
return $query->count();
}


public function getInProgressCasesCount($user, $caseIds = null): int
{ $query=CaseModel::where('status','pending');
if ($caseIds) {
    $query->whereIn('id', $caseIds);

}
return $query->count();
}



public function getUpcomingSessionsCount($user, $caseIds = null): int
{

$query=Session::where('date','>',Carbon::today());
if ($caseIds) {
    $query->whereIn('case_id', $caseIds);

}

return $query->count();
 }



 public function getOpenCasesCount($user, $caseIds = null): int
 {
    $query=CaseModel::where('status','open');
    if ($caseIds) {
        $query->whereIn('id', $caseIds);
    }
    return $query->count();


 }
 }
