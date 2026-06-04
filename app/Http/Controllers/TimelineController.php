<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\CaseModel;
use App\Services\Timeline\TimelineAggregatorService;
use App\Contracts\Services\Timeline\TimelineEventProviderInterface;

class TimelineController extends Controller
{
        protected $timelineAggregator;

    public function __construct(TimelineAggregatorService $timelineAggregator)
    {
        $this->timelineAggregator = $timelineAggregator;
    }
    public function timeline(Request $request, $caseId)
    {

         $case = CaseModel::with(['client', 'lawer', 'sessions', 'tasks.assignedTo', 'documents.uploadedBy', 'invoices'])
            ->find($caseId);
              if (!$case) {
            return response()->json([
                'status' => false,
                'message' => 'القضية غير موجودة',
            ], 404);

if ($roleName !== 'admin' && $case->client_id !== $user->id && $case->lawer_id !== $user->id) {
    return response()->json([
        'status' => false,
        'message' => 'غير مصرح لك بمشاهدة هذه القضية',
    ], 403);
}

        }


        $timeline = $this->timelineAggregator->getTimeline($case);


        return response()->json([
            'status' => true,
            'data' => [
                'case_id' => $case->id,
                'case_title' => $case->title,
                'timeline' => $timeline,
            ],
        ], 200);

    }}
