<?php

// New Code 

namespace App\Services;

use App\Models\Document;
use App\Models\SignatureRequest;
use App\Models\Signer;
use App\Notifications\DocumentFullySignedNotification;
use App\Notifications\RequestSignatureNotification;
use App\Notifications\SignerSignedNotification;
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
use App\Jobs\ProcessDocumentUploadJob;

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
            // call job to generate the keywords and summary 
            ProcessDocumentUploadJob::dispatch($document->id);
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
    // this method is work and send notification complete 
    public function handleDocuSignWebhook($request)
    {
        Log::info('DocuSign Webhook Received', $request->all());

        // Support both formats: legacy (top-level envelopeId) and new (data.envelopeId)
        $envelopeId = $request->input('data.envelopeId') ?? $request->input('envelopeId');
        if (!$envelopeId) {
            Log::warning('DocuSign Webhook: envelopeId missing', $request->all());
            return;
        }

        $signatureRequest = SignatureRequest::where('envelope_id', $envelopeId)->first();
        if (!$signatureRequest) {
            Log::warning("No SignatureRequest for envelope {$envelopeId}");
            return;
        }

        // Read recipients & status straight from the webhook payload (no extra API calls,
        // avoids the 400 INVALID_REQUEST_PARAMETER caused by a malformed stored envelopeId)
        $signers        = $request->input('recipients.signers', []);
        $envelopeStatus = strtolower((string) $request->input('status'));

        // (1) Sync each signer and notify the lawyer per individual signature
        foreach ($signers as $recipient) {
            if (strtolower($recipient['status'] ?? '') !== 'completed') {
                continue;
            }

            // Only the signer who just moved from pending -> signed
            $signer = $signatureRequest->signers()
                ->where('docusign_recipient_id', $recipient['recipientId'] ?? null)
                ->where('status', '!=', 'signed')
                ->first();

            if (!$signer) {
                continue;
            }

            $signer->update(['status' => 'signed', 'signed_at' => now()]);

            // Notify the lawyer that this specific signer has signed
            try {
                $signatureRequest->lawyer->notify(
                    new SignerSignedNotification($signatureRequest, $signer)
                );
            } catch (\Throwable $e) {
                Log::error('SignerSigned notification failed: ' . $e->getMessage());
            }
        }

        // (2) When the whole envelope is completed
        if ($envelopeStatus === 'completed' && $signatureRequest->status !== 'completed') {
            $signatureRequest->update(['status' => 'completed']);

            // (a) Notification first — before anything that could fail
            try {
                $signatureRequest->lawyer->notify(
                    new DocumentFullySignedNotification(
                        $signatureRequest->document,
                        $signatureRequest->lawyer->id
                    )
                );
                Log::info('DocumentFullySigned notification sent OK');
            } catch (\Throwable $e) {
                Log::error('DocumentFullySigned notification failed: ' . $e->getMessage());
            }

            // (b) Download the signed file and store it as a NEW document (version) linked to the original
            try {
                $document  = $signatureRequest->document; // الملف الأصلي (بدون توقيع)
                $directory = 'signed_documents';
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }

                // "combined" returns the full signed PDF (all pages)
                $signedPath = $directory . '/' . pathinfo($document->file_path, PATHINFO_FILENAME) . '_signed.pdf';
                $this->downloadSignedDocument($envelopeId, $signedPath);

                // Create the signed file as a new child document (a version of the base one)
                $signedDocument = Document::create([
                    'file_path'      => $signedPath,
                    'type'           => 'signed_document',
                    'parent_id'      => $document->id,                 // ← الربط بالملف الأصلي
                    'version_number' => (int) ($document->version_number ?? 1) + 1,
                    'has_versions'   => false,
                    'uploaded_by'    => $document->uploaded_by,
                    'case_id'        => $document->case_id,
                    'file_size'      => Storage::size($signedPath),
                ]);

                // Flag the base document as having versions + point to the signed copy
                $document->update([
                    'status'           => 'fully_signed',
                    'signed_file_path' => $signedPath,
                    'has_versions'     => true,
                ]);
                // call job to generate key words and summary for the signed document
                ProcessDocumentUploadJob::dispatch($signedDocument->id);

                Log::info('Signed document version created', [
                    'parent_id'   => $document->id,
                    'signed_doc'  => $signedDocument->id,
                ]);
            } catch (\Throwable $e) {
                Log::error('Signed document version failed: ' . $e->getMessage());
            }
        }
    }
    // public function handleDocuSignWebhook($request)
    // {
    //     Log::info('DocuSign Webhook Received', $request->all());

    //     // يدعم الصيغتين: القديمة (envelopeId بالأعلى) والحديثة (data.envelopeId)
    //     $envelopeId = $request->input('data.envelopeId') ?? $request->input('envelopeId');
    //     if (!$envelopeId) {
    //         Log::warning('DocuSign Webhook: envelopeId missing', $request->all());
    //         return;
    //     }

    //     $signatureRequest = SignatureRequest::where('envelope_id', $envelopeId)->first();
    //     if (!$signatureRequest) {
    //         Log::warning("No SignatureRequest for envelope {$envelopeId}");
    //         return;
    //     }

    //     // (1) زامن حالة كل موقّع من DocuSign مباشرة — مصدر الحقيقة
    //     foreach ($this->getEnvelopeRecipients($envelopeId) as $recipient) {
    //         if (strtolower($recipient->getStatus()) === 'completed') {
    //             $signatureRequest->signers()
    //                 ->where('docusign_recipient_id', $recipient->getRecipientId())
    //                 ->where('status', '!=', 'signed')
    //                 ->update(['status' => 'signed', 'signed_at' => now()]);
    //         }
    //     }

    //     // (2) لما الـ envelope كلها تكتمل
    //     if (
    //         strtolower($this->getEnvelopeStatus($envelopeId)) === 'completed'
    //         && $signatureRequest->status !== 'completed'
    //     ) {
    //         try {
    //             $signatureRequest->update(['status' => 'completed']);

    //             $document  = $signatureRequest->document;
    //             $directory = 'signed_documents';
    //             if (!Storage::exists($directory)) {
    //                 Storage::makeDirectory($directory);
    //             }
    //             $signedPath = $directory . '/' . basename($document->file_path);

    //             $this->downloadSignedDocument($envelopeId, $signedPath);
    //             $document->update(['status' => 'fully_signed', 'signed_file_path' => $signedPath]);

    //             Notification::send(
    //                 $signatureRequest->lawyer,
    //                 new DocumentFullySignedNotification($document, $signatureRequest->lawyer->id)
    //             );
    //         } catch (\Exception $e) {
    //             Log::error('envelope-completed handling failed: ' . $e->getMessage());
    //         }
    //     }
    // }
    // new code recieve web hhok when all users sign $ own user sign 
    // public function handleDocuSignWebhook($request)
    // {
    //     // Log خام — مصدر الحقيقة الوحيد لشكل الـ payload
    //     Log::info('DocuSign Webhook Received', $request->all());

    //     $event      = $request->input('event');
    //     $envelopeId = $request->input('data.envelopeId') ?? $request->input('envelopeId');

    //     if (!$envelopeId) {
    //         Log::warning('DocuSign Webhook: envelopeId missing', $request->all());
    //         return;
    //     }

    //     $signatureRequest = SignatureRequest::where('envelope_id', $envelopeId)->first();
    //     if (!$signatureRequest) {
    //         Log::warning("DocuSign Webhook: no SignatureRequest for envelope {$envelopeId}");
    //         return;
    //     }

    //     if ($event === 'recipient-completed') {
    //         $recipientId = $request->input('data.recipientId'); // = docusign_recipient_id

    //         $signer = $signatureRequest->signers()
    //             ->where('docusign_recipient_id', $recipientId)
    //             ->first();

    //         if ($signer && $signer->status !== 'signed') {
    //             $signer->update(['status' => 'signed', 'signed_at' => now()]);
    //             $signatureRequest->lawyer->notify(
    //                 new SignerSignedNotification($signatureRequest, $signer)
    //             );
    //         }
    //         return;
    //     }

    //     if ($event === 'envelope-completed') {
    //         try {
    //             $signatureRequest->signers()->update([
    //                 'status'    => 'signed',
    //                 'signed_at' => now(),
    //             ]);
    //             $signatureRequest->update(['status' => 'completed']);

    //             $document  = $signatureRequest->document;
    //             $directory = 'signed_documents';
    //             if (!Storage::exists($directory)) {
    //                 Storage::makeDirectory($directory);
    //             }
    //             $signedPath = $directory . '/' . basename($document->file_path);

    //             $this->downloadSignedDocument($envelopeId, $signedPath);

    //             $document->update([
    //                 'status'           => 'fully_signed',
    //                 'signed_file_path' => $signedPath,
    //             ]);

    //             Notification::send(
    //                 $signatureRequest->lawyer,
    //                 new DocumentFullySignedNotification($document, $signatureRequest->lawyer->id)
    //             );
    //         } catch (\Exception $e) {
    //             Log::error('DocuSign envelope-completed handling failed: ' . $e->getMessage());
    //         }
    //         return;
    //     }
    // }
    // old code recive web hook when all users sign 
    // public function handleDocuSignWebhook($request)
    // {
    //     // getting envelope id
    //     $envelopeId = $request->envelopeId ?? null;
    //     // if envelope id not found ignore the notification
    //     if (!$envelopeId) return;
    //     // getting the signature request that is change status
    //     $signatureRequest = SignatureRequest::where('envelope_id', $envelopeId)->first();
    //     // if signature request not found ignore the notification 
    //     if (!$signatureRequest) return;
    //     // if document is completed signed from all signers
    //     if ($request->event === 'envelope-completed') {
    //         try {
    //             // update db values
    //             $signatureRequest->signers()->update(['status' => 'signed', 'signed_at' => now()]);
    //             $signatureRequest->update(['status' => 'completed']);
    //             $document = $signatureRequest->document;
    //             $originalPath = $document->file_path;
    //             $fileName = basename($originalPath);
    //             $directory = 'signed_documents';
    //             if (!Storage::exists($directory)) {
    //                 Storage::makeDirectory($directory);
    //             }
    //             $signedPath = $directory . '/' . $fileName;
    //             // call traint Function 
    //             $this->downloadSignedDocument($envelopeId, $signedPath);
    //             // update data 
    //             $document->update([
    //                 'status' => 'fully_signed',
    //                 'signed_file_path' => $signedPath
    //             ]);
    //             // send notification For lawer who make the request signature
    //             Notification::send($signatureRequest->lawyer, new DocumentFullySignedNotification($document, $signatureRequest->lawyer->id));
    //         } catch (\Exception $e) {
    //             Log::error('Failed To Download the Signed File From DocuSign Platform : ' . $e->getMessage());
    //         }
    //     }
    // }
}
