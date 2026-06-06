<?php

namespace App\Services\Timeline;

use App\Contracts\Services\Timeline\TimelineEventProviderInterface;

class CaseCreatedProvider implements TimelineEventProviderInterface
{
    /**
     * الحصول على حدث إنشاء القضية
     * @param mixed $case
     * @return array
     */
    public function getEvents($case): array
    {

        $primaryClient = $case->clients->where('pivot.is_primary', true)->first() ?? $case->clients->first();
        $clientName = $primaryClient?->full_name ?? 'العميل المشترك';


        $lawyerName = $case->leadLawyer?->full_name ?? 'النظام';

        return [[
            'type'        => 'case_created',
            'title'       => 'تم إنشاء القضية',
            'description' => "تم إنشاء القضية \"{$case->title}\" وتعيين المحامي الرئيسي {$lawyerName}",
            'date'        => $case->created_at,
            'user'        => $clientName,
        ]];
    }
}
