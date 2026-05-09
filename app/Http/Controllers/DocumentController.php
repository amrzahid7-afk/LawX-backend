<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadDocumentRequest;
use App\Services\DocumentService;

class DocumentController extends Controller
{
    protected DocumentService $documentService;
    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function upload_Document(UploadDocumentRequest $request)
    {
        $result = $this->documentService->add_new_document($request->validated());
        return response()->json([
            'status' => $result['status'],
            'data' => [
                'message' => $result['data']['message'],
                'data' => $result['data']['data'],
            ]
        ], $result['code']);
    }
    public function get_sessions_for_case($case_id)
    {
        $result = $this->documentService->get_documents_for_case($case_id);
        return response()->json([
            'status' => $result['status'],
            'data' => [
                'message' => $result['data']['message'],
                'data' => $result['data']['data'],
            ]
        ], $result['code']);
    }
}
