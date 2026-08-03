<?php

namespace App\Services;

use App\Models\CaseModel;
use App\Models\Expense;
use App\Models\Task;
use App\Models\User;
use App\Notifications\Add_Task_Notification;

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
        // send Notification 
        $user = User::find($task->assigned_to);
        $user->notify(new Add_Task_Notification($task->id, $user->id));
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
        $case = CaseModel::with(['leadLawyer', 'clients', 'team'])->find($case_id);
        $tasks = Task::with(['assignedTo.role', 'expenses'])
            ->where('case_id', $case_id)
            ->get();
        $formattedCase = [
            'id' => $case->id,
            'title' => $case->title,
            'description' => $case->description,
            'lead_lawyer' => [
                'name' => $case->leadLawyer->full_name ?? 'لم يُحدد بعد',
            ],
            'clients' => $case->clients->map(function ($client) {
                return [
                    'id'         => $client->id,
                    'name'       => $client->full_name,
                    'is_primary' => $client->pivot->is_primary ? true : false // ميزة قادمة من جدول case_clients
                ];
            }),
            'team' => $case->team->map(function ($member) {
                return [
                    'id'   => $member->id,
                    'name' => $member->full_name,
                    'role_in_case' => $member->pivot->role_in_case // ميزة قادمة من جدول case_teams
                ];
            }),

            // 'lawyr_name' => $case->lawer->full_name ?? null,
            // 'client_name' => $case->client->full_name ?? null,
            'status' => $case->status,
            'created_at' => $case->created_at->format('Y-m-d h'), // السنة-الشهر-اليوم الساعة (24)
        ];
        $formattedTasks = $tasks->map(function ($task) {
            return [
                'task_id' => $task->id,
                'title' => $task->title,
                'Due_date' => $task->due_date ? $task->due_date->format('Y-m-d h') : null,
                'description' => $task->description,
                'assigned_to' => [
                    'name' => $task->assignedTo->full_name ?? 'Unknown',
                    'role' => $task->assignedTo->role->name ?? 'Unknown'
                ],
                'status' => $task->status,
                'created_at' => $task->created_at->format('Y-m-d h'),
                // expenses for this task
                'expenses' => $task->expenses->map(function ($expense) {
                    return [
                        'expense_id' => $expense->id,
                        'amount' => $expense->amount,
                        'description' => $expense->description,
                        'status' => $expense->status,
                        'expense_date' => $expense->expense_date ? $expense->expense_date->format('Y-m-d') : null,
                    ];
                }),
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
    public function get_my_tasks($request)
    {
        $user = auth('sanctum')->user();
        $scope = $request->input('scope', 'all');

        $tasksQuery = $user->tasks()->with(['case', 'expenses']);

        if ($scope === 'office') {
            $tasksQuery->whereHas('case', function ($query) {
                $query->whereNotNull('office_id');
            });
        } elseif ($scope === 'personal') {
            $tasksQuery->whereHas('case', function ($query) {
                $query->whereNull('office_id');
            });
        }

        $tasks = $tasksQuery->get();

        $formattedTasks = $tasks->map(function ($task) {
            return [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'task_description' => $task->description,
                'task_due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                'task_status' => $task->status,
                'case_number' => $task->case->case_number,
                'case_title' => $task->case->title,
                'case_category' => $task->case->case_category,
                'is_personal_case' => is_null($task->case->office_id),
                'expenses' => $task->expenses->map(function ($expense) {
                    return [
                        'expense_id' => $expense->id,
                        'amount' => $expense->amount,
                        'description' => $expense->description,
                        'status' => $expense->status,
                        'expense_date' => $expense->expense_date ? $expense->expense_date->format('Y-m-d') : null,
                    ];
                }),
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
    public function add_Expense_for_task(array $data)
    {
        $task = Task::find($data['task_id']);
        $expense = $task->expenses()->create([
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'status' => 'pending',
            // 'added_by' => auth('sanctum')->id(),
            'expense_date' => now()->format('Y-m-d'),
        ]);
        /**
         *  'task_id',
        'amount',
        'description',
        'status',
        'expense_date',
         */

        return [
            'status' => 'success',
            'data' => [
                'message' => 'Expense added successfully',
                'expense' => $expense
            ],
            'code' => 200
        ];
    }
    public function aprove_expensive_for_task($expense_id)
    {
        $expense = Expense::find($expense_id);
        if (!$expense) {
            return [
                'status' => 'error',
                'data' => [
                    'message' => 'Expense not found'
                ],
                'code' => 404
            ];
        }
        $expense->update([
            'status' => 'approved'
        ]);
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Expense approved successfully',
                'expense' => $expense
            ],
            'code' => 200
        ];
    }
    public function get_expenses_for_task($task_id)
    {
        $task = Task::with('expenses')->find($task_id);
        $expenses = $task->expenses->map(function ($expense) {
            return [
                'expense_id' => $expense->id,
                'amount' => $expense->amount,
                'description' => $expense->description,
                'status' => $expense->status,
                'expense_date' => $expense->expense_date ? $expense->expense_date->format('Y-m-d') : null,
            ];
        });
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Expenses fetched successfully',
                'expenses' => $expenses
            ],
            'code' => 200
        ];
    }
}
