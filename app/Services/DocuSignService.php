<?php

// New Code 

namespace App\Services;

use App\Models\Document;
use App\Models\SignatureRequest;
use App\Models\Signer;
use App\Notifications\DocumentFullySignedNotification;
use App\Notifications\RequestSignatureNotification;
use App\Traits\DocuSignAuthentication;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Model\Document as DocusignDocument;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\EventNotification;
use DocuSign\eSign\Model\Recipients;
use DocuSign\eSign\Model\RecipientViewRequest;
use DocuSign\eSign\Model\Signer as DocusignSigner;
use DocuSign\eSign\Model\SignHere;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class DocuSignService
{
    use DocuSignAuthentication;

    public function __construct()
    {
        $this->accountId = env('DOCUSIGN_ACCOUNT_ID');
        $this->authenticate();
    }
    public function request_signature($request)
    {
        return DB::transaction(function () use ($request) {
            // save Document
            $filePath = $request['file']->store('documents');
            // create document record 
            $document = Document::create([
                'file_path' => $filePath,
                'type' => 'sign_request_document',
                'uploaded_by' => auth('sanctum')->user()->id
            ]);
            // create sign request record 
            $signatureRequest = SignatureRequest::create([
                'document_id' => $document->id,
                'lawyer_id' => auth('sanctum')->user()->id,
                'status' => 'pending'
            ]);
            // create signers records 
            foreach ($request['signers'] as $signerData) {
                Signer::create([
                    'signature_request_id' => $signatureRequest->id,
                    'user_id' => $signerData['user_id'],
                    'role' => $signerData['role'],
                    'is_seal' => $signerData['is_seal'] ?? false,
                    //  'anchor_text' => $signerData['anchor_text'],
                    'page_number' => $signerData['page_number'] ?? 1,
                    'x_position' => $signerData['x_position'],
                    'y_position' => $signerData['y_position'],
                    'width' => $signerData['width'],
                    'height' => $signerData['height'],
                    'sign_type' => $signerData['sign_type'],
                ]);
            }
            // send the File To DocuSign platform 
            $envelopeId = $this->createEnvelope($signatureRequest);
            $signatureRequest->update(['envelope_id' => $envelopeId, 'status' => 'in_progress']);

            // Send Notifications For signers 
            foreach ($signatureRequest->signers as $signer) {
                $signer->user->notify(new RequestSignatureNotification($signatureRequest->id, $document->id, $signer->user->id));
            }
            // send response 
            return $signatureRequest->id;
        });
    }
    public function getSigningUrl($request, $signatureRequestId)
    {
        // getting signer from db 
        $signer = Signer::where('signature_request_id', $signatureRequestId)
            ->where('user_id', auth('sanctum')->user()->id)
            ->where('status', 'pending')
            ->firstOrFail();
        //  Here we will save the route for page that the client will return to it after finish signing
        $returnUrl = $request->query('return_url', 'https://your-react-app.com/signing-done');
        // call service
        $url = $this->getRecipientViewUrl(
            $signer->request->envelope_id,
            $signer,
            $returnUrl
        );
        return $url;
    }
    public function handleDocuSignWebhook($request)
    {
        // getting envelope id
        $envelopeId = $request->envelopeId ?? null;
        // if envelope id not found ignore the notification
        if (!$envelopeId) return;
        // getting the signature request that is change status
        $signatureRequest = SignatureRequest::where('envelope_id', $envelopeId)->first();
        // if signature request not found ignore the notification 
        if (!$signatureRequest) return;
        // if document is completed signed from all signers
        if ($request->event === 'envelope-completed') {
            try {
                // update db values
                $signatureRequest->signers()->update(['status' => 'signed', 'signed_at' => now()]);
                $signatureRequest->update(['status' => 'completed']);
                $document = $signatureRequest->document;
                $originalPath = $document->file_path;
                $fileName = basename($originalPath);
                $directory = 'signed_documents';
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }
                $signedPath = $directory . '/' . $fileName;
                // call traint Function 
                $this->downloadSignedDocument($envelopeId, $signedPath);
                // update data 
                $document->update([
                    'status' => 'fully_signed',
                    'signed_file_path' => $signedPath
                ]);
                // send notification For lawer who make the request signature
                Notification::send($signatureRequest->lawyer, new DocumentFullySignedNotification($document, $signatureRequest->lawyer->id));
            } catch (\Exception $e) {
                Log::error('Failed To Download the Signed File From DocuSign Platform : ' . $e->getMessage());
            }
        }
    }
}
