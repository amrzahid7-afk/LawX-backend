<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaseModel;
use App\Services\Timeline\TimelineAggregatorService;

class TimelineController extends Controller
{
    protected $timelineAggregator;

    public function __construct(TimelineAggregatorService $timelineAggregator)
    {
        $this->timelineAggregator = $timelineAggregator;
    }

    public function timeline(Request $request, $caseId)
    {

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير مسجل دخوله',
            ], 401);
        }

        $roleName = $user->role->name ?? '';


        $case = CaseModel::with([
            'clients',
            'leadLawyer',
            'sessions',
            'tasks',
            'documents',
            'invoices',
            'team'
        ])->find($caseId);


        if (!$case) {
            return response()->json([
                'status' => false,
                'message' => 'القضية غير موجودة',
            ], 404);
        }

        // 4. 🛡️ جدار الحماية الذكي: فحص الصلاحيات بناء على العلاقات الجديدة
        if ($roleName !== 'admin') {
            $isLeadLawyer   = $case->lead_lawyer_id === $user->id; // هل هو المحامي الرئيسي؟
            $isClientInCase = $case->clients->contains($user->id);  // هل هو أحد العملاء المرتبطين بالقضية؟
            $isTeamMember   = $case->team->contains($user->id);     // هل هو عضو في الفريق المكلف بالقضية؟

            // إذا لم يتوفر أي شرط، يتم حظره فوراً حمايةً للخصوصية
            if (!$isLeadLawyer && !$isClientInCase && !$isTeamMember) {
                return response()->json([
                    'status' => false,
                    'message' => 'غير مصرح لك بمشاهدة تايملاين هذه القضية',
                ], 403);
            }
        }

        // 5. استدعاء مجمع أحداث الخط الزمني النظيف
        $timeline = $this->timelineAggregator->getTimeline($case);

        return response()->json([
            'status' => true,
            'data' => [
                'case_id'    => $case->id,
                'case_title' => $case->title,
                'timeline'   => $timeline,
            ],
        ], 200);
    }
}
