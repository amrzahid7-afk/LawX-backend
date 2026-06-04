<?php
namespace App\Services\Timeline;
use App\Contracts\Services\Timeline\TimelineEventProviderInterface;
class TaskProvider implements TimelineEventProviderInterface
{
    /**
     * Summary of getEvents
     * @param mixed $case
     * @return array{date: mixed, description: string, title: string, type: string, user: mixed[]}
     */
    public function getEvents($case): array
    {
        $events = [];
        foreach ($case->tasks as $task) {
            $events[] = [
                'type' => 'task',
                'title' => "تم إضافة مهمة جديدة: {$task->title}",
                'description' => "تمت إضافة المهمة \"{$task->title}\" إلى القضية \"{$case->title}\"",
                'date' => $task->created_at,
                'user' => $task->assignedTo?->full_name ?? 'غير محدد',
            ];
        }
        return $events;
    }
}
