<?php

namespace App\Http\Controllers;

use App\Services\TasksService;
use Illuminate\Http\Request;
use App\Http\Requests\AddTaskRequest;

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
}
