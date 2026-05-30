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
            'due_date' => $data['due_date'] ?? null,
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
    public function edit_task(array $data)
    {
        $task = Task::find($data['task_id']);
        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'due_date' => $data['due_date'] ?? $task->due_date,
            'assigned_to' => $data['assigned_to'] ?? $task->assigned_to,
            'status' => $data['status'] ?? $task->status,
        ]);
        $formattedTask = [
            'id' => $task->id,
            'case_id' => $task->case_id,
            'title' => $task->title,
            'description' => $task->description,
            'due_date' => $task->due_date ? $task->due_date->format('Y-m-d h') : null,
            'assigned_to' => [
                'id' => $task->assignedTo->id ?? null,
                'name' => $task->assignedTo->full_name ?? null,
                'role' => $task->assignedTo->role->name ?? null
            ],
            'status' => $task->status,
            'created_at' => $task->created_at->format('Y-m-d h'),
        ];
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Task updated successfully',
                'data' => $formattedTask
            ],
            'code' => '200'
        ];
    }
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
    public function delete_task($task_id)
    {
        $task = Task::find($task_id);
        $task->delete();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Task deleted successfully'
            ],
            'code' => '200'
        ];
    }
    public function get_my_tasks()
    {
        $user = auth('sanctum')->user();
        $tasks = $user->tasks()->with(['case.client'])->get();
        $formattedTasks = $tasks->map(function ($task) {
            return [
                'task_title' => $task->title,
                'task_description' => $task->description,
                'task_due_date' => $task->due_date->format('Y-m-d'),
                'task_status' => $task->status,
                'case_number' => $task->case->case_number,
                'case_title' => $task->case->title,
                'case_category' => $task->case->case_category,
                'case_description' => $task->case->description,
                'client' => $task->case->client
            ];
        });
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Task Fetching successfully',
                'tasks' => $formattedTasks
            ],
            'code' => '200'
        ];
    }
}
