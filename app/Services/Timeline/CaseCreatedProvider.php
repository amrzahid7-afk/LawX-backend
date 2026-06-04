<?php
namespace App\Services\Timeline;
use App\Contracts\Services\Timeline\TimelineEventProviderInterface;

class CaseCreatedProvider implements TimelineEventProviderInterface
{
    /**
     * Summary of getEvents
     * @param mixed $case
     * @return array{date: mixed, description: string, title: string, type: string, user: mixed[]}
     */
    public function getEvents($case): array
    {

    $clientName=$case->client?->full_name ?? 'العميل';

       return [[
            'type' => 'case_created',
            'title' => 'تم إنشاء القضية',
            'description' => "تم إنشاء القضية \"{$case->title}\" بواسطة {$clientName}",
            'date' => $case->created_at,
            'user' =>$clientName,
        ]];
    }
}
