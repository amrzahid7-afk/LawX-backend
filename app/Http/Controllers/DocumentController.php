<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadDocumentRequest;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    protected DocumentService $documentService;
    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function upload_Document(UploadDocumentRequest $request)
    {
        $result = $this->documentService->add_new_document($request, $request->validated());
        return response()->json([
            'status' => $result['status'],
            'data' => [
                'message' => $result['data']['message'],
                'data' => $result['data']['data'],
            ]
        ], $result['code']);
    }
    public function get_documents_for_case($case_id)
    {
        // validate case Id 
        $validate = Validator::make(
            [
                'case_id' => $case_id,
                'tags'    => request()->tags
            ],
            [
                'case_id' => ['required', 'uuid', Rule::exists('cases', 'id')],
                'tags' => ['nullable', 'array'],
                'tags.*' => ['string']
            ],
            [
                'case_id.required' => 'Case ID is required',
                'case_id.uuid' => 'Case ID must be a valid UUID',
                'case_id.exists' => 'Case not found',
                'tags.array'       => 'Tags must be an array of strings'
            ]
        );
        if ($validate->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid case ID',
                'error'   => $validate->errors()
            ], 422);
        }
        // calling Service function 
        $result = $this->documentService->get_documents_for_case($case_id);
        // sending response 
        return response()->json([
            'status' => $result['status'],
            'data' => [
                'message' => $result['data']['message'],
                'data' => $result['data']['data'],
            ]
        ], $result['code']);
    }
    public function delete_document($document_id)
    {
        try {
            // dd(1);
            // validate Document ID
            $validator = Validator::make(
                ['document_id' => $document_id],
                ['document_id' => 'required|exists:documents,id'],
                [
                    'document_id.required' => "document Id is required",
                    'document_id.exists' => "document Id must Be Exist in the Files Table"
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'status' => "failed",
                    'Message' => $validator->errors(),
                ], 422);
            }
            // dd(1);

            // calling Service function 
            $result = $this->documentService->delete_document($document_id);
            // sending response 
            return response()->json([
                'status' =>  $result['status'],
                'data' => [
                    'message' => $result['data']['message'],
                    'data' => $result['data']['data'],
                ]
            ], $result['code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "failed",
                'Message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], $e->getCode());
        }
    }
}
