<?php

namespace App\Services;

use App\Models\Task;
use App\Models\CaseModel;

class TasksService
{
    public function addTask(array $data)
    {
        $task = Task::create([
            'case_id' => $data['case_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'assigned_to' => $data['assigned_to'],
        ]);
        return [
            'status' => 'success',
            'data' => [
                'message' => 'create task successfully',
                'data' => $task
            ],
            'code' => '200'
        ];
    }
    // public function get_tasks_for_case($case_id)
    // {
    //     $tasks_for_case = Task::with('case')->get();
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'get tasks successfully',
    //             'data' => $tasks_for_case
    //         ],
    //         'code' => '200'
    //     ];
    // }
    public function get_tasks_for_case($case_id)
    {
        $case = CaseModel::with(['lawer', 'client'])->find($case_id);
        $tasks = Task::with(['assignedTo.role'])
            ->where('case_id', $case_id)
            ->get();
        $formattedCase = [
            'id' => $case->id,
            'title' => $case->title,
            'description' => $case->description,
            'lawyr_name' => $case->lawer->full_name ?? null,
            'client_name' => $case->client->full_name ?? null,
            'status' => $case->status,
            'created_at' => $case->created_at->format('Y-m-d h'), // السنة-الشهر-اليوم الساعة (24)
        ];
        $formattedTasks = $tasks->map(function ($task) {
            return [
                'title' => $task->title,
                'description' => $task->description,
                'assigned_to' => [
                    'name' => $task->assignedTo->full_name ?? 'Unknown',
                    'role' => $task->assignedTo->role->name ?? 'Unknown'
                ],
                'status' => $task->status,
                'created_at' => $task->created_at->format('Y-m-d h'),
            ];
        });
        return [
            'status' => 'success',
            'data' => [
                'message' => 'fetch tasks successfully',
                'data' => [
                    'case' => $formattedCase,
                    'tasks' => $formattedTasks
                ]
            ],
            'code' => '200'
        ];
    }
}
