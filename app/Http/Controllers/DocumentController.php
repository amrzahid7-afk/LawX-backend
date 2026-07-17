<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadDocumentRequest;
use App\Models\CaseModel;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Gate;
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
        if (Gate::denies('upload_Document', CaseModel::find($request->case_id))) {
            return sendError('Unauthorized , You are not allowed to upload documents for this case', [], 403);
        }
        $result = $this->documentService->add_new_document($request->validated());
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
        // validate permissiosn for the user to access the case
        if (Gate::denies('get_documents_for_case', CaseModel::find($case_id))) {
            return sendError('Unauthorized , You are not allowed to view documents for this case', [], 403);
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
                return sendError('Validation Error : ', [$validator->errors()], 422);
            }
            // validate permissions for the user to delete the document
            if (Gate::denies('delete_document', Document::find($document_id))) {
                return sendError('Unauthorized , You are not allowed to delete this document', [], 403);
            }
            // calling Service function 
            $result = $this->documentService->delete_document($document_id);
            // sending response
            return sendResponse($result['data'], $result['status'], $result['code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "failed",
                'Message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], $e->getCode());
        }
    }
}
