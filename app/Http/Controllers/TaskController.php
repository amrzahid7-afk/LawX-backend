<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTaskRequest;
use App\Http\Requests\EditTaskRequest;
use App\Models\CaseModel;
use App\Models\Task;
use App\Models\User;
use App\Services\TasksService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    //create_task
    private TasksService $tasks_service;
    public function __construct(TasksService $tasks_service)
    {
        $this->tasks_service = $tasks_service;
    }
    public function create_task(AddTaskRequest $request)
    {
        try {
            // validate data
            $data = $request->validated();
            //validate permissions 
            $case = CaseModel::find($data['case_id']);
            $assigned_To_lawer = User::find($data['assigned_to']);
            if (Gate::denies('create_task', [$case, $assigned_To_lawer])) {
                return sendError('Un authorized , you dont have permission to create task For this case or this employee', [], 403);
            }
            // logic In service class 
            $result  = $this->tasks_service->addTask($data);
            // sending response
            return response()->json([
                'status' => $result['status'],
                'data' => $result['data'],
                'ID' => $result['data']['data']->id,
            ], $result['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function get_tasks_for_case($case_id)
    {
        try {
            // validate ID
            $validator = validator()->make(['case_id' => $case_id], [
                'case_id' => 'required|exists:cases,id'
            ], [
                'case_id.required' => "Case ID is required",
                'case_id.exists' => "Case ID must exist in the cases table"
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'data' => $validator->errors(),
                    'code' => '400'
                ]);
            }
            // validate permissions
            $case = CaseModel::find($case_id);
            if (Gate::denies('get_tasks_for_case',  $case)) {
                return sendError('Un authorized , you dont have permission to show tasks for this case ', [], 403);
            }
            // logic In Service Class 
            $result  = $this->tasks_service->get_tasks_for_case($case_id);
            // sending response
            return response()->json([
                'status' => $result['status'],
                'data' => $result['data'],
            ], $result['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function edit_task(EditTaskRequest $request)
    {
        try {
            // validate data 
            $data = $request->validated();
            // validate permissions
            $task = Task::find($data['task_id']);
            if (Gate::denies('edit_task', $task)) {
                return sendError('Un authorized , you dont have permission to edit this task ', [], 403);
            }
            // logic in service class 
            $result = $this->tasks_service->edit_task($data);
            // sending response 
            return response()->json([
                'status' => $result['status'],
                'data' => $result['data'],
            ], $result['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function delete_task($task_id)
    {
        try {
            // validate Task ID
            $validator = validator()->make(['task_id' => $task_id], [
                'task_id' => 'required|exists:tasks,id'
            ], [
                'task_id.required' => "Task ID is required",
                'task_id.exists' => "Task ID must exist in the tasks table"
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'data' => $validator->errors(),
                ], 400);
            }
            // validate permissions
            $task = Task::find($task_id);
            if (Gate::denies('delete_task', $task)) {
                return sendError('Un authorized , you dont have permission to delete this task ', [], 403);
            }
            // Call Service function
            $result = $this->tasks_service->delete_task($task_id);
            // sending response
            return response()->json([
                'status' => $result['status'],
                'data' => $result['data'],
            ], $result['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function get_my_tasks(Request $request)
    {
        try {
            // validate permissions
            if (Gate::denies('get_my_tasks', Task::class)) {
                return sendError('Un authorized , you dont have permission to show your tasks ', [], 403);
            }
            $tasks = $this->tasks_service->get_my_tasks($request);
            return response()->json([
                'status' => $tasks['status'],
                'data' => $tasks['data'],
            ], $tasks['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
}
