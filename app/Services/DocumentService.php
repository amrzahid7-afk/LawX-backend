<?php

namespace App\Services;

use App\Ai\Agents\DocumentAnalyzerAgent;
use App\Jobs\ProcessDocumentUploadJob;
use App\Models\CaseModel;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Laravel\Ai\Files\Document as AiDocument;

class DocumentService
{
    // public function add_new_document($data)
    // {

    //     // $document = Document::create($data);
    //     $case = CaseModel::find($data['case_id']);
    //     $safe_name_case = str_replace(' ', '_', $case->title);
    //     $path = $data['file']->store('Documents/Case_files/' . "{$safe_name_case}", 'public');
    //     $data['file'] = $path;
    //     $document = Document::create([
    //         'case_id' => $data['case_id'],
    //         'file_path' => $data['file'],
    //         'type' => $data['type'],
    //         'nots' => $data['nots'] ?? null,
    //         'uploaded_by' => auth('sanctum')->user()->id,
    //     ]);
    //     // $formattedData = [
    //     //     'id' => $document->id,
    //     //     'nots' => $document->nots,
    //     //     'file_url' => Storage::url($document->file_path),
    //     //     'file_size' => Number::fileSize(Storage::size($document->file_path), 2),  // Storage::size($document->file_path),
    //     //     'case' => [
    //     //         'id' => $document->case->id,
    //     //         'title' => $document->case->title,
    //     //     ],
    //     //     'uploaded_by' => [
    //     //         'id' => $document->uploadedBy->id,
    //     //         'name' => $document->uploadedBy->full_name,
    //     //         'role' => $document->uploadedBy->role->name,
    //     //     ],

    //     //     'File_name' => $document->type,
    //     //     'uploaded_AT' => $document->created_at->format('Y-m-d h:i A'),
    //     // ];
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'create document successfully',
    //             'data' => ['document_id' =>  $document->id]
    //         ],
    //         'code' => '200'
    //     ];
    // }
    public function add_new_document($request, $data)
    {
        try {
            $case = CaseModel::find($data['case_id']);
            $safe_name_case = str_replace(' ', '_', $case->title);
            // $path = $data['file']->store('Documents/Case_files/' . "{$safe_name_case}", 'public');
            $path = $data['file']->store('Documents/Case_files/' . $case->id, 'public');
            $documentData = [
                'case_id' => $data['case_id'],
                'file_size' =>  Storage::disk('public')->size($path), //  Number::fileSize(Storage::disk('public')->size($path), 2),
                'file_path' => $path,
                'type' => $data['type'],
                'has_versions' => $data['parent_id'] ? true : false,
                'parent_id' => $data['parent_id'] ?? null,
                'uploaded_by' => auth('sanctum')->user()->id,
                'version_number' => $data['parent_id'] ? Document::find($data['parent_id'])->version_number + 1 : 1,
            ];
            $document = Document::create($documentData);

            // Call Job To generate FullText , KeyWords , Summary in the File By AI Service
            //  ProcessDocumentUploadJob::dispatch($request, $document->id);
            $user = auth('sanctum')->user();
            $response = DocumentAnalyzerAgent::make($user)
                // ->model('gemini-2.5-flash')
                ->prompt(
                    'read the file completely . then extract the text.then generate a summary . then generate key words.',
                    attachments: [
                        AiDocument::fromPath(Storage::disk('public')->path($document->file_path)),
                    ],
                );
            $data = $response;
            $document->update([
                'ai_tags' => $data['keywords'] ?? null,
                'ai_summary' => $data['summary'] ?? null,
                'extracted_text' => $data['full_text'] ?? null,
            ]);
            return [
                'status' => 'success',
                'data' => [
                    'message' => 'تم رفع الملف بنجاح، جاري معالجته...',
                    'data' => [
                        'document_id' => $document->id,
                        'version_info' => [
                            'version_number' => $document->version_number,
                            'has_parent' => $document->parent_id !== null,
                        ]
                    ]
                ],
                'code' => '201'
            ];
        } catch (\Throwable $e) {

            dd($e->getMessage());
        }
    }
    public function get_documents_for_case($case_id)
    {
        $documents = Document::with(['case', 'uploadedBy'])->where('case_id', $case_id)->get();
        $formattedData = $documents->map(function ($document) {
            return [
                'id' => $document->id,
                'file_url' => Storage::disk('public')->url($document->file_path),
                'file_size' =>  Number::fileSize($document->file_size, 2),  // Storage::size($document->file_path),
                'case' => [
                    'id' => $document->case->id,
                    'title' => $document->case->title,
                ],
                'uploaded_by' => [
                    'id' => $document->uploadedBy->id,
                    'name' => $document->uploadedBy->full_name,
                    'role' => $document->uploadedBy->role->name,
                ],
                'File_name' => $document->type,
                'uploaded_AT' => $document->created_at->format('Y-m-d h:i A'),
                'ai_summary' => $document->ai_summary ?? null,
                'ai_tags' => $document->ai_tags ?? null,
                'versions_files' => $document->versions()->get(),
            ];
        });
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Fetch documents successfully',
                'data' => $formattedData
            ],
            'code' => '200'
        ];
    }
    public function delete_document($document_id)
    {

        $document = Document::where('id', $document_id)->first();
        Storage::delete($document->file_path);
        $document->delete();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Document deleted successfully',
                'data' => null
            ],
            'code' => '200'
        ];
    }
}
