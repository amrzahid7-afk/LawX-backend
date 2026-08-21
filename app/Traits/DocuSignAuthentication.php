<?php
// Edit Kimi K3 Code - النسخة النهائية المُصلّحة
// 1. الكل Signer عادي (بدون Seal)
// 2. توقيع متوازي (routing_order = 1 للكل) - إصلاح REcipient_NOT_IN_SEQUENCE
// 3. اسم المستخدم أسفل التوقيع

namespace App\Traits;

use DocuSign\eSign\Model\EnvelopeEvent;
use DocuSign\eSign\Model\RecipientEvent;
use DocuSign\eSign\Model\ConnectEventData;

use App\Models\SignatureRequest;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Model\Document as DocusignDocument;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\EventNotification;
use DocuSign\eSign\Model\Recipients;
use DocuSign\eSign\Model\RecipientViewRequest;
use DocuSign\eSign\Model\Signer as DocusignSigner;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\Text;
use DocuSign\eSign\Model\Tabs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use DocuSign\eSign\Model\RecipientViewRequestOptions;

trait DocuSignAuthentication
{
    protected $apiClient;
    protected $accessToken;
    protected $accountId;

    private function authenticate()
    {
        try {
            $clientId = env('DOCUSIGN_INTEGRATION_KEY');
            $userId = env('DOCUSIGN_USER_ID');
            $rsaKeyId = env('DOCUSIGN_RSA_KEY_ID');
            $privateKeyPath = storage_path(env('DOCUSIGN_PRIVATE_KEY_PATH'));

            if (!$clientId || !$userId) {
                throw new \Exception('Missing DocuSign credentials in .env');
            }

            if (!file_exists($privateKeyPath)) {
                throw new \Exception("Private key file not found: " . $privateKeyPath);
            }

            $privateKey = file_get_contents($privateKeyPath);

            $this->apiClient = new ApiClient();
            $this->apiClient->getOAuth()->setOAuthBasePath('account-d.docusign.com');

            $response = $this->apiClient->requestJWTUserToken(
                $clientId,
                $userId,
                $privateKey,
                'signature impersonation',
                3600,
                $rsaKeyId
            );

            $this->accessToken = $response[0]['access_token'];

            $this->apiClient->getConfig()->addDefaultHeader(
                'Authorization',
                'Bearer ' . $this->accessToken
            );

            $this->apiClient->getConfig()->setHost('https://demo.docusign.net/restapi');

            Log::info('Authenticated with DocuSign Successfully');
        } catch (\Exception $e) {
            Log::error('DocuSign Auth Error: ' . $e->getMessage());
            throw new \Exception('فشلت المصادقة مع DocuSign: ' . $e->getMessage());
        }
    }

    private function ensureAuthenticated()
    {
        if (!$this->accessToken) {
            $this->authenticate();
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════════════════
     * ║  الدالة النهائية: createEnvelope                                    ║
     * ║  1. الكل Signer عادي (بدون Seal)                                   ║
     * ║  2. توقيع متوازي (routing_order = 1 للكل)                          ║
     *     ← هذا يُصلّح خطأ REcipient_NOT_IN_SEQUENCE                        ║
     *  3. اسم المستخدم أسفل التوقيع                                       ║
     * ═══════════════════════════════════════════════════════════════════════
     */
    public function createEnvelope(SignatureRequest $signatureRequest)
    {
        $this->ensureAuthenticated();

        // ===== OLD =====
        // $filePath = $signatureRequest->document->file_path;
        // if (!Storage::exists($filePath)) {
        //     throw new \Exception("File Not Found: {$filePath}");
        // }
        // $document = new DocusignDocument([
        //     'document_id' => '1',
        //     'document_base64' => base64_encode(Storage::get($filePath)),
        //     'name' => $signatureRequest->document->type ?? 'Document',
        //     'file_extension' => pathinfo($filePath, PATHINFO_EXTENSION)
        // ]);

        // ===== NEW (Cloudinary) =====
        $filePath = $signatureRequest->document->file_path; // URL كامل

        $fileContent = @file_get_contents($filePath);
        if ($fileContent === false) {
            throw new \Exception("File Not Found: {$filePath}");
        }

        $document = new DocusignDocument([
            'document_id' => '1',
            'document_base64' => base64_encode($fileContent),
            'name' => $signatureRequest->document->type ?? 'Document',
            'file_extension' => pathinfo(parse_url($filePath, PHP_URL_PATH), PATHINFO_EXTENSION),
        ]);

        $signersArray = [];

        foreach ($signatureRequest->signers as $index => $signer) {
            $recipientId = (string)($index + 1);
            $userName = $signer->user->full_name ?? 'Unknown';

            $tabs = new Tabs();
            $signHereTabs = [];
            $textTabs = [];

            // ──── موضع التوقيع الأساسي ────
            $baseX = (float)$signer->x_position;
            $baseY = (float)$signer->y_position;

            /*
             * ╔════════════════════════════════════════════════════════════════════╗
             * ║  1. SignHere Tab (التوقيع)                                        ║
             * ╚════════════════════════════════════════════════════════════════════╝
             */
            $signHere = new SignHere([
                'document_id' => '1',
                'page_number' => (string)($signer->page_number ?? 1),
                'x_position' => (string)$baseX,
                'y_position' => (string)$baseY,
                'width' => (int)($signer->width ?? 100),
                'height' => (int)($signer->height ?? 50),
                'optional' => 'false',
                'tab_label' => 'Sign_Here_' . $recipientId,
            ]);
            $signHereTabs[] = $signHere;

            /*
             * ╔════════════════════════════════════════════════════════════════════╗
             * ║  2. Text Tab (اسم المستخدم أسفل التوقيع)                          ║
             * ╚════════════════════════════════════════════════════════════════════╝
             */
            $nameText = new Text([
                'document_id' => '1',
                'page_number' => (string)($signer->page_number ?? 1),
                'x_position' => (string)$baseX,
                'y_position' => (string)($baseY + ($signer->height ?? 50) + 15),
                'width' => (int)($signer->width ?? 150),
                'height' => 20,
                'value' => $userName,
                'locked' => 'true',
                'tab_label' => 'Name_' . $recipientId,
                'font_size' => 'Size12',
                'bold' => 'true',
            ]);
            $textTabs[] = $nameText;

            $tabs->setSignHereTabs($signHereTabs);
            $tabs->setTextTabs($textTabs);

            /*
             * ╔════════════════════════════════════════════════════════════════════╗
             * ║  إنشاء Signer (للجميع - عادي)                                    ║
             * ║  routing_order = '1' للكل = توقيع متوازي                         ║
             * ║  هذا يُصلّح: REcipient_NOT_IN_SEQUENCE                              ║
             * ╚════════════════════════════════════════════════════════════════════╝
             */
            $docusignSigner = new DocusignSigner([
                'email' => $signer->user->email,
                'name' => $userName,
                'recipient_id' => $recipientId,
                'routing_order' => '1',  // ← الكل = 1 = متوازي = لا يوجد ترتيب
                'client_user_id' => $recipientId,
                'tabs' => $tabs,
                'can_create_tabs' => false,
            ]);

            $signer->update(['docusign_recipient_id' => $recipientId]);
            $signersArray[] = $docusignSigner;
        }

        $eventNotification = new EventNotification([
            'url'                    => env('DOCUSIGN_WEBHOOK_URL'),
            'logging_enabled'        => 'true',
            'require_acknowledgment' => 'true',
            'envelope_events' => [
                new EnvelopeEvent(['envelope_event_status_code' => 'Completed']),
            ],
            'recipient_events' => [
                new RecipientEvent(['recipient_event_status_code' => 'Completed']),
            ],
            'event_data' => new ConnectEventData([
                'version'      => 'restv2.1',
                'format'       => 'json',
                'include_data' => ['recipients'],
            ]),
        ]);

        $envelopeDefinition = new EnvelopeDefinition([
            'email_subject' => 'يرجى توقيع المستند: ' . ($signatureRequest->document->title ?? 'مستند'),
            'documents' => [$document],
            'recipients' => new Recipients(['signers' => $signersArray]),
            'status' => 'sent',
            'event_notification' => $eventNotification,
            'envelope_id_stamping' => 'false',
            'use_disclosure' => 'false'
        ]);

        $envelopeApi = new EnvelopesApi($this->apiClient);

        try {
            Log::info('DocuSign Payload: ' . $envelopeDefinition->__toString());
            $result = $envelopeApi->createEnvelope($this->accountId, $envelopeDefinition);
            return $result->getEnvelopeId();
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), '401') !== false) {
                $this->authenticate();
                $result = $envelopeApi->createEnvelope($this->accountId, $envelopeDefinition);
                return $result->getEnvelopeId();
            }
            throw $e;
        }
    }
    // new code 

    // احذف هذا السطر نهائياً من أعلى الملف
    public function getRecipientViewUrl($envelopeId, $signer, $returnUrl)
    {
        $this->ensureAuthenticated();

        $clientUserId = $signer->docusign_recipient_id ?? (string) $signer->user->id;

        // origin تبع صفحة الفرونت اند (origin فقط: بدون path وبدون / بالنهاية)
        $appOrigin = rtrim(env('FRONTEND_ORIGIN', 'http://localhost:3000'), '/');

        $recipientViewRequest = new RecipientViewRequest([
            'authentication_method' => 'none',
            'email'                 => $signer->user->email,
            'user_name'             => $signer->user->full_name,
            'recipient_id'          => $signer->docusign_recipient_id,
            'return_url'            => $returnUrl,
            'client_user_id'        => $clientUserId,

            // مفتاح الـ Focused View
            'frame_ancestors' => [
                $appOrigin,
                'https://apps-d.docusign.com', // بالـ production: https://apps.docusign.com
            ],
            'message_origins' => [
                'https://apps-d.docusign.com', // بالـ production: https://apps.docusign.com
            ],
        ]);

        $envelopeApi = new EnvelopesApi($this->apiClient);

        $viewUrl = $envelopeApi->createRecipientView(
            $this->accountId,
            $envelopeId,
            $recipientViewRequest
        );

        return $viewUrl->getUrl();
    }
    public function downloadSignedDocument($envelopeId, $localSavePath = null)
    {
        $this->ensureAuthenticated();

        $host = 'https://demo.docusign.net/restapi';
        $url  = "{$host}/v2.1/accounts/{$this->accountId}/envelopes/{$envelopeId}/documents/combined";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->accessToken,
                'Accept: application/pdf',
            ],
        ]);
        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $body === false) {
            throw new \Exception("Download failed [{$httpCode}]");
        }
        // ===== OLD (Local storage) — علّق ده =====
        // Storage::disk('public')->put($localSavePath, $body);
        // return $localSavePath;

        // ===== NEW (Cloudinary) =====
        $tempPath = tempnam(sys_get_temp_dir(), 'lawx_signed_') . '.pdf';
        file_put_contents($tempPath, $body);

        $upload = app(\Cloudinary\Cloudinary::class)
            ->uploadApi()
            ->upload($tempPath, [
                'folder' => 'lawx/signed_documents',
                'resource_type' => 'auto',
            ]);
        @unlink($tempPath);
        return $upload['secure_url'];
    }
    // public function downloadSignedDocument($envelopeId, $localSavePath)
    // {
    //     $this->ensureAuthenticated();
    //     $envelopeApi = new EnvelopesApi($this->apiClient);

    //     // Options: get the combined signed PDF WITHOUT the certificate of completion page
    //     $options = new \DocuSign\eSign\Api\EnvelopesApi\GetDocumentOptions();
    //     $options->setCertificate('false');
    //     $options->setWatermark('false');

    //     // "combined" = full signed document (all pages) as one PDF
    //     $tempFile = $envelopeApi->getDocument(
    //         $this->accountId,
    //         $envelopeId,
    //         'combined',
    //         $options
    //     );

    //     // getDocument returns an SplFileObject (temp file), not raw bytes
    //     $contents = file_get_contents($tempFile->getRealPath());
    //     Storage::put($localSavePath, $contents);

    //     return $localSavePath;
    // }
    // public function downloadSignedDocument($envelopeId, $localSavePath)
    // {
    //     $this->ensureAuthenticated();
    //     $envelopeApi = new EnvelopesApi($this->apiClient);

    //     // "combined" returns the full signed document (all pages) as one PDF
    //     $documentBytes = $envelopeApi->getDocument($this->accountId, $envelopeId, 'combined');

    //     Storage::put($localSavePath, $documentBytes);
    //     return $localSavePath;
    // }
    // get wrong invelop ID 
    // public function downloadSignedDocument($envelopeId, $localSavePath)
    // {
    //     $this->ensureAuthenticated();
    //     $envelopeApi = new EnvelopesApi($this->apiClient);
    //     $documentBytes = $envelopeApi->getDocument($this->accountId, $envelopeId, '1');
    //     Storage::put($localSavePath, $documentBytes);
    //     return $localSavePath;
    // }
    public function getEnvelopeRecipients($envelopeId)
    {
        $this->ensureAuthenticated();
        $envelopeApi = new EnvelopesApi($this->apiClient);
        $recipients  = $envelopeApi->listRecipients($this->accountId, $envelopeId);
        return $recipients->getSigners() ?? [];
    }

    public function getEnvelopeStatus($envelopeId)
    {
        $this->ensureAuthenticated();
        $envelopeApi = new EnvelopesApi($this->apiClient);
        return $envelopeApi->getEnvelope($this->accountId, $envelopeId)->getStatus();
    }
}
// Old Code it is Work 

// namespace App\Traits;

// use App\Models\SignatureRequest;
// use DocuSign\eSign\Api\EnvelopesApi;
// use DocuSign\eSign\Client\ApiClient;
// use DocuSign\eSign\Model\Document as DocusignDocument;
// use DocuSign\eSign\Model\EnvelopeDefinition;
// use DocuSign\eSign\Model\EventNotification;
// use DocuSign\eSign\Model\Recipients;
// use DocuSign\eSign\Model\RecipientViewRequest;
// use DocuSign\eSign\Model\SealSign;
// use DocuSign\eSign\Model\Signer as DocusignSigner;
// use DocuSign\eSign\Model\SignHere;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Storage;
// use DocuSign\eSign\Model\Tabs;

// trait DocuSignAuthentication
// {
//     protected $apiClient;
//     protected $accessToken;
//     protected $accountId;

//     private function authenticate()
//     {
//         try {
//             // get the keys From .env File 
//             $clientId = env('DOCUSIGN_INTEGRATION_KEY');
//             $userId = env('DOCUSIGN_USER_ID');
//             $rsaKeyId = env('DOCUSIGN_RSA_KEY_ID');
//             $privateKeyPath = storage_path(env('DOCUSIGN_PRIVATE_KEY_PATH'));
//             // validate the config data For Docusign integration
//             if (!$clientId || !$userId) {
//                 throw new \Exception('  Missing DocuSign credentials in .env');
//             }

//             // check if the private key file exists
//             if (!file_exists($privateKeyPath)) {
//                 throw new \Exception(" Private key for Encryption Of Connection File not found : " . $privateKeyPath);
//             }

//             // get the private key value From  private key file
//             $privateKey = file_get_contents($privateKeyPath);

//             // Make  New Socket with DocuSign
//             $this->apiClient = new ApiClient();
//             // Set DocuSign Base Path
//             $this->apiClient->getOAuth()->setOAuthBasePath('account-d.docusign.com');

//             // provide the socket With  out credentials
//             $response = $this->apiClient->requestJWTUserToken(
//                 $clientId,
//                 $userId,
//                 $privateKey,
//                 'signature impersonation',
//                 3600,
//                 $rsaKeyId
//             );

//             // get the access token from the socket response
//             $this->accessToken = $response[0]['access_token'];

//             // set the access token in each request with Docu sign  
//             $this->apiClient->getConfig()->addDefaultHeader(
//                 'Authorization',
//                 'Bearer ' . $this->accessToken
//             );

//             // Set DocuSign Base Path
//             $this->apiClient->getConfig()->setHost('https://demo.docusign.net/restapi');

//             Log::info(' Authenticated with DocuSign Successfully  ');
//         } catch (\Exception $e) {
//             Log::error('DocuSign Auth Error: ' . $e->getMessage());
//             throw new \Exception('فشلت المصادقة مع DocuSign: ' . $e->getMessage());
//         }
//     }

//     private function ensureAuthenticated()
//     {
//         if (!$this->accessToken) {
//             $this->authenticate();
//         }
//     }
//     public function createEnvelope(SignatureRequest $signatureRequest)
//     {
//         $this->ensureAuthenticated();

//         $filePath = $signatureRequest->document->file_path;

//         if (!Storage::exists($filePath)) {
//             throw new \Exception(" File Not Found In LawX Server : {$filePath}");
//         }
//         // this model Is for put the document that will be signed 
//         $document = new DocusignDocument([
//             'document_id' => '1',
//             'document_base64' => base64_encode(Storage::get($filePath)),
//             'name' => $signatureRequest->document->type,
//             'file_extension' => pathinfo($filePath, PATHINFO_EXTENSION)
//         ]);

//         $signersArray = [];
//         foreach ($signatureRequest->signers as $index => $signer) {
//             $recipientId = (string)($index + 1);

//             // if ($signer->sign_type === 'stamp') {
//             //     $tab = new SealSign([
//             //         'document_id' => '1',
//             //         'page_number' => $signer->page_number ?? 1,
//             //         'x_position' => $signer->x_position,
//             //         'y_position' => $signer->y_position,
//             //         'width' => $signer->width,
//             //         'height' => $signer->height,
//             //         'locked' => true,
//             //         'required' => true
//             //     ]);
//             //     $tabsArray = ['seal_signs' => [$tab]];
//             // } else {
//             //     $tab = new SignHere([
//             //         'document_id' => '1',
//             //         'page_number' => $signer->page_number ?? 1,
//             //         'x_position' => $signer->x_position,
//             //         'y_position' => $signer->y_position,
//             //         'width' => $signer->width,
//             //         'height' => $signer->height,
//             //         'locked' => true,
//             //         'required' => true
//             //     ]);
//             //     $tabsArray = ['sign_here' => [$tab]];
//             // }
//             // $docusignSigner = new DocusignSigner([
//             //     'email' => $signer->user->email,
//             //     'name' => $signer->user->full_name,
//             //     'recipient_id' => $recipientId,
//             //     'client_user_id' => (string) $signer->user->id,
//             //     'can_create_tabs' => false,
//             //     'tabs' => $tabsArray
//             // ]);
//             if ($signer->sign_type === 'seal') {
//                 $tab = new SealSign([
//                     'document_id' => '1',
//                     'page_number' => (int) ($signer->page_number ?? 1),
//                     'x_position' => (int) $signer->x_position,
//                     'y_position' => (int) $signer->y_position,
//                     'width' => (int) $signer->width,
//                     'height' => (int) $signer->height,
//                     'locked' => true,
//                     'required' => true
//                 ]);
//                 $tabsArray = ['seal_signs' => [$tab]];
//             } else {
//                 $tab = new SignHere([
//                     'document_id' => '1',
//                     'page_number' => (int) ($signer->page_number ?? 1),
//                     'x_position' => (int) $signer->x_position,
//                     'y_position' => (int) $signer->y_position,
//                     'width' => (int) $signer->width,
//                     'height' => (int) $signer->height,
//                     'locked' => true,
//                     'required' => true
//                 ]);
//                 $tabsArray = ['sign_here' => [$tab]];
//             }

//             $docusignSigner = new DocusignSigner([
//                 'email' => $signer->user->email,
//                 'name' => $signer->user->full_name,
//                 'recipient_id' => $recipientId,
//                 'client_user_id' => (string) $signer->user->id,
//                 'can_create_tabs' => false,
//                 'tabs' => $tabsArray
//             ]);
//             $signer->update(['docusign_recipient_id' => $recipientId]);
//             $signersArray[] = $docusignSigner;
//         }
//         // send notification To ( Ngrok -> Laravel)  Tool when the document is signed
//         $eventNotification = new EventNotification([
//             'url' => 'https://stowing-cleft-slinging.ngrok-free.dev/api/docusign-webhook',
//             'logging_enabled' => 'true',
//             'envelope_events' => [
//                 ['envelope_event_status_code' => 'signed'],
//                 ['envelope_event_status_code' => 'completed']
//             ],
//             'require_acknowledgment' => 'true'
//         ]);

//         // Create Envelope (Put All Data in one Record For Send  To DocuSign Platform )
//         $envelopeDefinition = new EnvelopeDefinition([
//             'email_subject' => 'يرجى توقيع المستند: ' . ($signatureRequest->document->title ?? 'مستند'),
//             'documents' => [$document],
//             'recipients' => new Recipients(['signers' => $signersArray]),
//             'status' => 'sent',
//             'event_notification' => $eventNotification
//         ]);
//         $envelopeApi = new EnvelopesApi($this->apiClient);

//         try {
//             // send the Request to DocuSign Servers 
//             \Log::info('DocuSign Payload: ' . $envelopeDefinition->__toString());
//             $result = $envelopeApi->createEnvelope($this->accountId, $envelopeDefinition);
//             return $result->getEnvelopeId();
//         } catch (\Exception $e) {
//             // If docu sign refuse the request because Authentication in valid retry Authenticate and resend the request
//             if (strpos($e->getMessage(), '401') !== false) {
//                 $this->authenticate();
//                 $result = $envelopeApi->createEnvelope($this->accountId, $envelopeDefinition);
//                 return $result->getEnvelopeId();
//             }
//             throw $e;
//         }
//     }

//     public function getRecipientViewUrl($envelopeId, $signer, $returnUrl)
//     {
//         // Ensure that the user is authenticated
//         $this->ensureAuthenticated();
//         // Create Recipient View Request
//         $recipientViewRequest = new RecipientViewRequest([
//             'authentication_method' => 'none',
//             'email' => $signer->user->email,
//             'user_name' => $signer->user->full_name,
//             'recipient_id' => $signer->docusign_recipient_id,
//             'return_url' => $returnUrl,
//             'client_user_id' => (string) $signer->user->id,
//         ]);
//         // initialize the  Request to send it to DocuSign
//         $envelopeApi = new EnvelopesApi($this->apiClient);
//         // send the Request to DocuSign Servers
//         $viewUrl = $envelopeApi->createRecipientView(
//             $this->accountId,
//             $envelopeId,
//             $recipientViewRequest
//         );
//         // get the View URL response from DocuSign
//         return $viewUrl->getUrl();
//     }
//     // Function For get the document after Signing Completing 
//     public function downloadSignedDocument($envelopeId, $localSavePath)
//     {
//         // validate Authentication with docusign 
//         $this->ensureAuthenticated();
//         // make  new Connection with docusign server 
//         $envelopeApi = new EnvelopesApi($this->apiClient);
//         // get the document from DocuSign
//         $documentBytes = $envelopeApi->getDocument($this->accountId, $envelopeId, '1');
//         // store the signed document in local storage
//         Storage::put($localSavePath, $documentBytes);
//         // return the local save path
//         return $localSavePath;
//     }
// }
