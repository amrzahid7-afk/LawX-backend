<?php
namespace App\Services\Timeline;
use App\Contracts\Services\Timeline\TimelineEventProviderInterface;
class DocumentProvider implements TimelineEventProviderInterface
{
    /**
     * Summary of getEvents
     * @param mixed $case
     * @return array{date: mixed, description: string, title: string, type: string, user: mixed[]}
     */
    public function getEvents($case): array
    {
        $events = [];

        foreach ($case->documents as $doc) {
            $uploader = $doc->uploadedBy->full_name ?? 'مجهول';
            $events[] = [
                'type' => 'document_uploaded',
                'title' => 'مستند مرفوع',
                'description' => "تم رفع مستند من نوع \"{$doc->type}\" بواسطة {$uploader}",
                'date' => $doc->created_at,
                'user' => $uploader,
            ];
        }
        return $events;
    }
}
