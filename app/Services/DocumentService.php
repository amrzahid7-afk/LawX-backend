<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use App\Models\CaseModel;
use Illuminate\Support\Number;

class DocumentService
{
    public function add_new_document($data)
    {

        // $document = Document::create($data);
        $case = CaseModel::find($data['case_id']);
        $safe_name_case = str_replace(' ', '_', $case->title);
        $path = $data['file']->store('Documents/Case_files/' . "{$safe_name_case}", 'public');
        $data['file'] = $path;
        $document = Document::create([
            'case_id' => $data['case_id'],
            'file_path' => $data['file'],
            'type' => $data['type'],
            'nots' => $data['nots'] ?? null,
            'uploaded_by' => auth('api')->user()->id,
        ]);
        // $formattedData = [
        //     'id' => $document->id,
        //     'nots' => $document->nots,
        //     'file_url' => Storage::url($document->file_path),
        //     'file_size' => Number::fileSize(Storage::size($document->file_path), 2),  // Storage::size($document->file_path),
        //     'case' => [
        //         'id' => $document->case->id,
        //         'title' => $document->case->title,
        //     ],
        //     'uploaded_by' => [
        //         'id' => $document->uploadedBy->id,
        //         'name' => $document->uploadedBy->full_name,
        //         'role' => $document->uploadedBy->role->name,
        //     ],

        //     'File_name' => $document->type,
        //     'uploaded_AT' => $document->created_at->format('Y-m-d h:i A'),
        // ];
        return [
            'status' => 'success',
            'data' => [
                'message' => 'create document successfully',
                'data' => ['document_id' =>  $document->id]
            ],
            'code' => '200'
        ];
    }
    public function get_documents_for_case($case_id)
    {
        $documents = Document::with(['case', 'uploadedBy'])->where('case_id', $case_id)->get();
        $formattedData = $documents->map(function ($document) {
            return [
                'id' => $document->id,
                'nots' => $document->nots,
                'file_url' => Storage::disk('public')->url($document->file_path),
                'file_size' => Number::fileSize(Storage::disk('public')->size($document->file_path), 2),  // Storage::size($document->file_path),
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
