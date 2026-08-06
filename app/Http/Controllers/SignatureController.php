<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignatureRequest as SignatureRequestRequest;
use App\Models\Document;
use App\Models\SignatureRequest;
use App\Models\Signer;
use App\Notifications\RequestSignatureNotification;
use App\Notifications\Send_Request_Notification;
use App\Services\DocuSignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SignatureController extends Controller
{
    private $docuSignService;

    public function __construct(DocuSignService $docuSignService)
    {
        $this->docuSignService = $docuSignService;
    }
    // updload File To sign it 
    public function lawyerRequestSignature(SignatureRequestRequest $request)
    {
        // validate data for Request 
        $validated_data = $request->validated();
        // validate permissions 
        if (Gate::denies('lawyerRequestSignature', SignatureRequest::class)) {
            return sendError(" Unauthorized , You do not have permission to create signature requests ", [], 403);
        }
        try {
            // call service
            $signatureRequest_id = $this->docuSignService->request_signature($validated_data);
            // send response 
            return sendResponse(['request_id' => $signatureRequest_id], ' Signature request created successfully.', 201);
        } catch (\Exception $e) {
            return sendError('DocuSign Error', ['message' => $e->getMessage()], 500);
        };
    }
    // get the File To Sign it
    public function getSigningUrl(Request $request, $signatureRequestId)
    {
        try {
            // // validate data 
            $validator = Validator::make(['signature_request_id' => $signatureRequestId], [
                'signature_request_id' => 'required|uuid|exists:signature_requests,id',
            ], [
                'signature_request_id.required' => 'Signature request ID is required',
                'signature_request_id.uuid' => 'Signature request ID must be a valid UUID',
                'signature_request_id.exists' => 'Signature request ID does not exist',
            ]);
            if ($validator->fails()) {
                return sendError('Validation failed', $validator->errors()->toArray(), 422);
            }

            // validate permission 
            $signer = Signer::where('signature_request_id', $signatureRequestId)
                ->where('user_id', auth('sanctum')->user()->id)
                ->first();
            // dd($signer);
            $this->authorize('getSigningUrl', [SignatureRequest::class, $signer]);
            // dd('success');
            // call service
            $url = $this->docuSignService->getSigningUrl($request, $signatureRequestId);
            // return response
            return sendResponse(['signing_url' => $url], 'Signing URL retrieved successfully.', 200);
        } catch (\Exception $e) {
            return sendError('DocuSign Error', ['message' => $e->getMessage()], 500);
        }
    }
    // recive the notification from DocuSign platform that the signature is completed
    public function handleDocuSignWebhook(Request $request)
    {
        // call Service Function 
        $this->docuSignService->handleDocuSignWebhook($request);
        // send response
        return response('OK', 200);
    }
    public function getLawyerFiles()
    {
        $this->authorize('getLawyerFiles', SignatureRequest::class);
        $requests = SignatureRequest::where('lawyer_id', auth('sanctum')->user()->id)
            ->with(['document', 'signers.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return sendResponse($requests, 'Lawyer files retrieved successfully.');
    }
    public function get_files_require_sign()
    {
        // validate permissions
        // $this->authorize('get_files_require_sign', SignatureRequest::class);
        // getting files 
        $signers = Signer::where('user_id', auth('sanctum')->user()->id)
            ->where('role', 'lawyer')
            ->with(['request.document', 'request.lawyer'])
            ->get();
        // sending response
        return sendResponse($signers, 'Files require sign by lawyer retrieved successfully.');
    }
    public function getClientFiles()
    {
        $this->authorize('getClientFiles', SignatureRequest::class);
        $signers = Signer::where('user_id', auth('sanctum')->user()->id)
            ->where('role', 'client')
            ->with(['request.document', 'request.lawyer'])
            ->get();
        return sendResponse($signers, 'Client pending files retrieved successfully.');
    }
    public function getSyndicateFiles()
    {
        $this->authorize('getSyndicateFiles', SignatureRequest::class);
        $signers = Signer::where('user_id', auth('sanctum')->user()->id)
            ->where('role', 'syndicate')
            ->with(['request.document', 'request.lawyer'])
            ->get();

        return sendResponse($signers, 'Syndicate pending files retrieved successfully.');
    }
    public function reviewFile($signatureRequestId)
    {
        // validate data 
        $validator = Validator::make(
            ['signature_request_id' => $signatureRequestId],
            [
                'signature_request_id' => 'required|uuid|exists:signature_requests,id',
            ],
            [
                'signature_request_id.required' => 'Signature request ID is required',
                'signature_request_id.uuid' => 'Signature request ID must be a valid UUID',
                'signature_request_id.exists' => 'Signature request ID does not exist',
            ]
        );
        if ($validator->fails()) {
            return sendError('Validation failed', $validator->errors()->toArray(), 422);
        }
        // get signer
        $signer = Signer::where('signature_request_id', $signatureRequestId)
            ->where('user_id', auth('sanctum')->user()->id)
            ->first();
        // dd($signer);
        // validate permission 
        $this->authorize('reviewFile', [SignatureRequest::class, $signer]);
        // Get File 
        $filePath = storage_path('app/public/' . $signer->request->document->file_path);
        if (!file_exists($filePath)) {
            return sendError('File not found', [], 404);
        }
        return response()->file($filePath);
    }
}
