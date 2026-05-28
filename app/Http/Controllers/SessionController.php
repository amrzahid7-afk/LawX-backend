<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Services\SessionsService;
use Faker\Core\Uuid;

class SessionController extends Controller
{
    private SessionsService $session_service;
    public function __construct(SessionsService $session_service)
    {
        $this->session_service = $session_service;
    }

    public function addSession(AddSessionRequest $request)
    {
        $session = $this->session_service->add_new_session($request->validated());
        return response()->json([
            'status' => $session['status'],
            'data' => $session['data'],
        ], $session['code']);
    }
    public function get_sessions_for_case($case_id)
    {
        $sessions = $this->session_service->getSessions($case_id);
        return response()->json([
            'status' => $sessions['status'],
            'data' => $sessions['data'],
        ], $sessions['code']);
    }
    public function get_sessions_for_lawer()
    {
        $sessions = $this->session_service->get_sessions_for_lawer();
        return response()->json([
            'status' => $sessions['status'],
            'data' => $sessions['data'],
        ], $sessions['code']);
    }
    public function get_sessions_for_client()
    {
        $sessions = $this->session_service->get_sessions_for_client();
        return response()->json([
            'status' => $sessions['status'],
            'data' => $sessions['data'],
        ], $sessions['code']);
    }
    public function delete_session($session_id)
    {
        $sessions = $this->session_service->delete_session($session_id);
        return response()->json([
            'status' => $sessions['status'],
            'message' => $sessions['message'],
        ], $sessions['code']);
    }
    public function update_session(UpdateSessionRequest $request)
    {
        $sessions = $this->session_service->update_session($request);
        return response()->json([
            'status' => $sessions['status'],
            'message' => $sessions['message'],
        ], $sessions['code']);
    }
}
