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
            'nots' => $data['nots'],
            'result' => "null",
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
                'data' => $session
            ],
            'code' => '200'
        ];
    }
    // public function getSessions($case_id)
    // {
    //     $sessions = Session::where('case_id', $case_id)->with(['case', 'case.documents'])->get();
    //     $formattedSessions = $sessions->map(function ($session) {
    //         return [
    //             'session_id' => $session->id,
    //             'case_number' => $session->case->case_number,
    //             'date' => $session->date->format('Y-m-d H:i'),
    //             'location' => $session->location,
    //             'nots' => $session->nots,
    //             'status' => $session->status,
    //             'result' => $session->result,
    //             'created_at' => $session->created_at->format('Y-m-d H:i'),
    //         ];
    //     });
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'Fetch session successfully',
    //             'data' => $formattedSessions
    //         ],
    //         'code' => '200'
    //     ];
    // }
    public function getSessions($case_id)
    {
        $sessions = Session::where('case_id', $case_id)->with(['case', 'case.documents'])->get();
        $sessionFiles = Document::where('case_id', $case_id)
            ->where('type', 'session_document') // تصفية حسب النوع
            ->get()
            ->map(function ($doc) {
                return [
                    'file_name' => $doc->type,
                    'file_url' => Storage::url($doc->file_path),
                    // 'file_size' => $this->formatBytes(Storage::size($doc->file_path)), // دالة تحويل الحجم
                    'file_size' => Storage::size($doc->file_path),
                ];
            });
        $formattedSessions = $sessions->map(function ($session) {
            return [
                'session_id' => $session->id,
                'case_number' => $session->case->case_number,
                'date' => $session->date->format('Y-m-d H:i'),
                'location' => $session->location,
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
        // get lawer object
        $lawer = auth('sanctum')->user();
        // get sessions just for this lawer (Authenticated)
        $sessions = Session::with('case.leadLawyer')->whereHas('case', function ($query) use ($lawer) {
            $query->where('lead_lawyer_id', $lawer->id);
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
        // جلب كائن العميل المسجل حالياً
        $client = auth('sanctum')->user();

        // جلب الجلسات
        $sessions = Session::with('case.clients') // تم التعديل هنا: case.clients بدلاً من case.client
            ->whereHas('case', function ($query) use ($client) {
                // البحث داخل القضايا المرتبطة بهذا العميل
                $query->whereHas('clients', function ($q) use ($client) {
                    // التحقق من أن العميل موجود في جدول case_clients المرتبط بالقضية
                    $q->where('users.id', $client->id);
                });
            })
            ->get();

        // إرجاع البيانات
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
                'data' => $session
            ],
            'code' => '200'
        ];
    }
}
