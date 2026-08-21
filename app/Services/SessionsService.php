<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Session;
use Illuminate\Support\Facades\Storage;
use App\Notifications\Add_Session_Notification;

class SessionsService
{
    public function add_new_session($data)
    {
        // create session 
        $session = Session::create([
            'case_id' => $data['case_id'],
            'date' => $data['date'],
            'location' => $data['location'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'nots' => $data['nots'],
            'result' => null,
        ]);
        // send notification
        $clients = $session->case->clients;
        foreach ($clients as $client) {
            $client->notify(new Add_Session_Notification($session->id, $client->id));
        }
        return [
            'status' => 'success',
            'data' => [
                'message' => 'create session successfully',
                'session_id' => $session->id
            ],
            'code' => '200'
        ];
    }
    public function getSessions($case_id)
    {
        $sessions = Session::where('case_id', $case_id)->with(['case', 'case.documents'])->get();
        $sessionFiles = Document::where('case_id', $case_id)
            ->where('type', 'session_document') // تصفية حسب النوع
            ->get()
            ->map(function ($doc) {
                return [
                    'file_name' => $doc->type,
                    'file_url' => $doc->file_path,
                    // 'file_size' => $this->formatBytes(Storage::size($doc->file_path)), // دالة تحويل الحجم
                    'file_size' => "1 MB",
                ];
            });
        $formattedSessions = $sessions->map(function ($session) {
            return [
                'session_id' => $session->id,
                'case_number' => $session->case->case_number,
                'date' => $session->date->format('Y-m-d H:i'),
                'location' => $session->location,
                'latitude' => $session->latitude,
                'longitude' => $session->longitude,
                'nots' => $session->nots,
                'status' => $session->status,
                'result' => $session->result,
                'created_at' => $session->created_at->format('Y-m-d H:i'),
            ];
        });

        return [
            'status' => 'success',
            'data' => [
                'message' => 'Fetch session successfully',
                'sessions' => $formattedSessions,
                'session_files' => $sessionFiles
            ],
            'code' => '200'
        ];
    }
    public function get_sessions_for_lawer()
    {
        $lawer = auth('sanctum')->user();
        $profile = $lawer->lawyerProfile;
        // get sessions for lawer    
        $sessions = Session::with('case')->whereHas('case', function ($query) use ($lawer, $profile) {
            // sessions for personal work 
            $query->where('lead_lawyer_id', $lawer->id)
                // sessions for my office
                ->orWhereHas('office', function ($q) use ($profile) {
                    $q->where('owner_lawyer_profile_id', $profile->id);
                })
                // sessions for my cases teams (Employee)
                ->orWhereHas('team', function ($q) use ($lawer) {
                    $q->where('users.id', $lawer->id);
                });
        })->get();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Fetch sessions For lawyer successfully',
                'data' => $sessions
            ],
            'code' => '200'
        ];
    }

    public function get_sessions_for_client()
    {
        $client = auth('sanctum')->user();

        $sessions = Session::with('case.clients')
            ->whereHas('case', function ($query) use ($client) {
                $query->whereHas('clients', function ($q) use ($client) {
                    $q->where('users.id', $client->id);
                });
            })
            ->get();
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
            'latitude' => $data['latitude'] ?? $session?->latitude,
            'longitude' => $data['longitude'] ?? $session?->longitude,
            'nots'  => $data['nots'] ?? $session->nots,
            'result'  => $data['result'] ?? $session->result,
            'status'  => $data['status'] ?? $session->status,
        ]);
        if (isset($data['file']) && $data['file']) {
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
                'session_id' => $session->id
            ],
            'code' => '200'
        ];
    }
}
