<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Models\CaseModel;
use App\Models\Session;
use App\Services\SessionsService;
use Faker\Core\Uuid;
use Illuminate\Support\Facades\Gate;

class SessionController extends Controller
{
    private SessionsService $session_service;
    public function __construct(SessionsService $session_service)
    {
        $this->session_service = $session_service;
    }

    public function addSession(AddSessionRequest $request)
    {
        try {
            // validate data
            $data = $request->validated();
            // validate permissions 
            $case = CaseModel::find($data['case_id']);
            if (Gate::denies('addSession', [Session::class, $case])) {
                return  sendError("Unauthorized , You do not have permission to add a session for this case.", [], 403);
            }
            // call service
            $session = $this->session_service->add_new_session($data);
            // return response 
            return sendResponse($session['data'], $session['status'], $session['code']);
        } catch (\Exception $e) {
            return sendError('Internal server Error ', [$e->getMessage()], 500);
        }
    }
    public function get_sessions_for_case($case_id)
    {
        // validate case_id
        $validate = validator()->make(
            ['case_id' => $case_id],
            ['case_id' => 'required|uuid|exists:cases,id'],
            [
                'case_id.required' => 'Case ID is required',
                'case_id.uuid' => 'Case ID must be a valid UUID',
                'case_id.exists' => 'Case not found'
            ]
        );
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors()->first(),
            ], 422);
        }
        // validate permission
        $case = CaseModel::find($case_id);
        if (Gate::denies('get_sessions_for_case', [Session::class, $case])) {
            return sendError("Unauthorized , You do not have permission to view sessions for this case.", [], 403);
        }
        try {
            $sessions = $this->session_service->getSessions($case_id);
            return response()->json([
                'status' => $sessions['status'],
                'data' => $sessions['data'],
            ], $sessions['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function get_sessions_for_lawer()
    {
        try {
            // validate permission
            if (Gate::denies('get_sessions_for_lawer', Session::class)) {
                return sendError("Unauthorized , You do not have permission to view sessions ", [], 403);
            }
            $sessions = $this->session_service->get_sessions_for_lawer();
            return response()->json([
                'status' => $sessions['status'],
                'data' => $sessions['data'],
            ], $sessions['code']);
        } catch (\Exception $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'Line' => $th->getLine()
            ], 500);
        }
    }
    public function get_sessions_for_client()
    {
        try {
            // validate permission
            if (Gate::denies('get_sessions_for_client', Session::class)) {
                return sendError("Unauthorized , You do not have permission to view sessions ", [], 403);
            }
            // call service
            $sessions = $this->session_service->get_sessions_for_client();
            // send response
            return sendResponse($sessions['data'], $sessions['status'], $sessions['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function delete_session($session_id)
    {
        // validate session_id is uuid and exists in sessions table
        $validate = validator()->make(
            ['session_id' => $session_id],
            ['session_id' => 'required|uuid|exists:sessions,id'],
            [
                'session_id.required' => 'Session ID is required',
                'session_id.uuid' => 'Session ID must be a valid UUID',
                'session_id.exists' => 'Session not found'
            ]
        );
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validate->errors()->first(),
            ], 422);
        }
        // validate permission
        $session = Session::find($session_id);
        if (Gate::denies('delete_session', [Session::class, $session])) {
            return sendError("Unauthorized , You do not have permission to delete this session.", [], 403);
        }
        // calling Service function
        $sessions = $this->session_service->delete_session($session_id);
        // sending response
        return response()->json([
            'status' => $sessions['status'],
            'data' => $sessions['data'],
        ], $sessions['code']);
    }
    public function update_session(UpdateSessionRequest $request)
    {
        try {
            // validate data 
            $data = $request->validated();
            // validate permission
            if (Gate::denies('update_session', Session::find($data['session_id']))) {
                return sendError("Unauthorized , You do not have permission to update this session.", [], 403);
            }
            // logic in service class 
            $sessions = $this->session_service->update_session($data);
            // sending response 
            return response()->json([
                'status' => $sessions['status'],
                'data' => $sessions['data'],
            ], $sessions['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
}
