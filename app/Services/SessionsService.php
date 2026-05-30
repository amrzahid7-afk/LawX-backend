<?php

namespace App\Services;

use App\Models\Session;

class SessionsService
{
    public function add_new_session($data)
    {
        $session = Session::create([
            'case_id' => $data['case_id'],
            'date' => $data['date'],
            'location' => $data['location'],
            'nots' => $data['nots'],
            'result' => "null",
        ]);
        return [
            'status' => 'success',
            'data' => [
                'message' => 'create session successfully',
                'data' => $session
            ],
            'code' => '200'
        ];
    }
    public function getSessions($case_id)
    {
        $sessions = Session::where('case_id', $case_id)->get();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Fetch session successfully',
                'data' => $sessions
            ],
            'code' => '200'
        ];
    }
    public function get_sessions_for_lawer()
    {
        // get lawer object
        $lawer = auth('api')->user();
        // get sessions just for this lawer (Authenticated)
        $sessions = Session::with('case.lawer')->whereHas('case', function ($query) use ($lawer) {
            $query->where('lawer_id', $lawer->id);
        })->get();
        // returning resposne data
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Fetch sessions For lawer successfully',
                'data' => $sessions
            ],
            'code' => '200'
        ];
    }
    public function get_sessions_for_client()
    {
        // get client object
        $client = auth('api')->user();
        // get sessions just for this client (Authenticated)
        $sessions = Session::with('case.client')->whereHas('case', function ($query) use ($client) {
            $query->where('client_id', $client->id);
        })->get();
        // returning resposne data
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Fetch sessions For client successfully',
                'data' => $sessions
            ],
            'code' => '200'
        ];
    }
    public function delete_session($session_id)
    {
        Session::where('id', $session_id)->delete();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'delete session successfully'
            ],
            'code' => '200'
        ];
    }
    public function update_session($data)
    {
        $session = Session::where('id', $data['session_id'])->first();
        $session->update([
            'date' => $data['date'] ?? $session->date,
            'location' => $data['location'] ?? $session->location,
            'nots'  => $data['nots'] ?? $session->nots,
            'result'  => $data['result'] ?? $session->result,
        ]);
        if ($data['file']) {
            $documentService = app(DocumentService::class);
            $documentData = [
                'case_id' => $session['case_id'],
                'file'    => $data['file'],
                'type'    => $data['file_type'] ?? 'session_document',
                'nots'    => $data['file_notes'] ?? null,
            ];
            $documentService->add_new_document($documentData);
        }
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Update session successfully',
                'data' => $session
            ],
            'code' => '200'
        ];
    }
}
