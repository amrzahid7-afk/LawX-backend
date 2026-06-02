<?php

namespace App\Http\Controllers;

use App\Services\TasksService;
use Illuminate\Http\Request;
use App\Http\Requests\AddTaskRequest;
use App\Http\Requests\EditTaskRequest;

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
                    'code' => '400'
                ]);
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
    public function get_my_tasks()
    {
        try {
            $tasks = $this->tasks_service->get_my_tasks();
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
