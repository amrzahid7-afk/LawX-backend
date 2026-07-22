<?php

namespace App\Traits;

use App\Models\SignatureRequest;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Model\Document as DocusignDocument;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\EventNotification;
use DocuSign\eSign\Model\Recipients;
use DocuSign\eSign\Model\RecipientViewRequest;
use DocuSign\eSign\Model\SealSign;
use DocuSign\eSign\Model\Signer as DocusignSigner;
use DocuSign\eSign\Model\SignHere;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use DocuSign\eSign\Model\Tabs;

trait DocuSignAuthentication
{
    protected $apiClient;
    protected $accessToken;
    protected $accountId;

    private function authenticate()
    {
        try {
            // get the keys From .env File 
            $clientId = env('DOCUSIGN_INTEGRATION_KEY');
            $userId = env('DOCUSIGN_USER_ID');
            $rsaKeyId = env('DOCUSIGN_RSA_KEY_ID');
            $privateKeyPath = storage_path(env('DOCUSIGN_PRIVATE_KEY_PATH'));
            // validate the config data For Docusign integration
            if (!$clientId || !$userId) {
                throw new \Exception('  Missing DocuSign credentials in .env');
            }

            // check if the private key file exists
            if (!file_exists($privateKeyPath)) {
                throw new \Exception(" Private key for Encryption Of Connection File not found : " . $privateKeyPath);
            }

            // get the private key value From  private key file
            $privateKey = file_get_contents($privateKeyPath);

            // Make  New Socket with DocuSign
            $this->apiClient = new ApiClient();
            // Set DocuSign Base Path
            $this->apiClient->getOAuth()->setOAuthBasePath('account-d.docusign.com');

            // provide the socket With  out credentials
            $response = $this->apiClient->requestJWTUserToken(
                $clientId,
                $userId,
                $privateKey,
                'signature impersonation',
                3600,
                $rsaKeyId
            );

            // get the access token from the socket response
            $this->accessToken = $response[0]['access_token'];

            // set the access token in each request with Docu sign  
            $this->apiClient->getConfig()->addDefaultHeader(
                'Authorization',
                'Bearer ' . $this->accessToken
            );

            // Set DocuSign Base Path
            $this->apiClient->getConfig()->setHost('https://demo.docusign.net/restapi');

            Log::info(' Authenticated with DocuSign Successfully  ');
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
    public function createEnvelope(SignatureRequest $signatureRequest)
    {
        $this->ensureAuthenticated();

        $filePath = $signatureRequest->document->file_path;

        if (!Storage::exists($filePath)) {
            throw new \Exception(" File Not Found In LawX Server : {$filePath}");
        }
        // this model Is for put the document that will be signed 
        $document = new DocusignDocument([
            'document_id' => '1',
            'document_base64' => base64_encode(Storage::get($filePath)),
            'name' => $signatureRequest->document->type,
            'file_extension' => pathinfo($filePath, PATHINFO_EXTENSION)
        ]);

        $signersArray = [];
        foreach ($signatureRequest->signers as $index => $signer) {
            $recipientId = (string)($index + 1);

            // if ($signer->sign_type === 'stamp') {
            //     $tab = new SealSign([
            //         'document_id' => '1',
            //         'page_number' => $signer->page_number ?? 1,
            //         'x_position' => $signer->x_position,
            //         'y_position' => $signer->y_position,
            //         'width' => $signer->width,
            //         'height' => $signer->height,
            //         'locked' => true,
            //         'required' => true
            //     ]);
            //     $tabsArray = ['seal_signs' => [$tab]];
            // } else {
            //     $tab = new SignHere([
            //         'document_id' => '1',
            //         'page_number' => $signer->page_number ?? 1,
            //         'x_position' => $signer->x_position,
            //         'y_position' => $signer->y_position,
            //         'width' => $signer->width,
            //         'height' => $signer->height,
            //         'locked' => true,
            //         'required' => true
            //     ]);
            //     $tabsArray = ['sign_here' => [$tab]];
            // }
            // $docusignSigner = new DocusignSigner([
            //     'email' => $signer->user->email,
            //     'name' => $signer->user->full_name,
            //     'recipient_id' => $recipientId,
            //     'client_user_id' => (string) $signer->user->id,
            //     'can_create_tabs' => false,
            //     'tabs' => $tabsArray
            // ]);
            if ($signer->sign_type === 'seal') {
                $tab = new SealSign([
                    'document_id' => '1',
                    'page_number' => (int) ($signer->page_number ?? 1),
                    'x_position' => (int) $signer->x_position,
                    'y_position' => (int) $signer->y_position,
                    'width' => (int) $signer->width,
                    'height' => (int) $signer->height,
                    'locked' => true,
                    'required' => true
                ]);
                $tabsArray = ['seal_signs' => [$tab]];
            } else {
                $tab = new SignHere([
                    'document_id' => '1',
                    'page_number' => (int) ($signer->page_number ?? 1),
                    'x_position' => (int) $signer->x_position,
                    'y_position' => (int) $signer->y_position,
                    'width' => (int) $signer->width,
                    'height' => (int) $signer->height,
                    'locked' => true,
                    'required' => true
                ]);
                $tabsArray = ['sign_here' => [$tab]];
            }

            $docusignSigner = new DocusignSigner([
                'email' => $signer->user->email,
                'name' => $signer->user->full_name,
                'recipient_id' => $recipientId,
                'client_user_id' => (string) $signer->user->id,
                'can_create_tabs' => false,
                'tabs' => $tabsArray
            ]);
            $signer->update(['docusign_recipient_id' => $recipientId]);
            $signersArray[] = $docusignSigner;
        }
        // send notification To ( Ngrok -> Laravel)  Tool when the document is signed
        $eventNotification = new EventNotification([
            'url' => 'https://stowing-cleft-slinging.ngrok-free.dev/api/docusign-webhook',
            'logging_enabled' => 'true',
            'envelope_events' => [
                ['envelope_event_status_code' => 'signed'],
                ['envelope_event_status_code' => 'completed']
            ],
            'require_acknowledgment' => 'true'
        ]);

        // Create Envelope (Put All Data in one Record For Send  To DocuSign Platform )
        $envelopeDefinition = new EnvelopeDefinition([
            'email_subject' => 'يرجى توقيع المستند: ' . ($signatureRequest->document->title ?? 'مستند'),
            'documents' => [$document],
            'recipients' => new Recipients(['signers' => $signersArray]),
            'status' => 'sent',
            'event_notification' => $eventNotification
        ]);
        $envelopeApi = new EnvelopesApi($this->apiClient);

        try {
            // send the Request to DocuSign Servers 
            \Log::info('DocuSign Payload: ' . $envelopeDefinition->__toString());
            $result = $envelopeApi->createEnvelope($this->accountId, $envelopeDefinition);
            return $result->getEnvelopeId();
        } catch (\Exception $e) {
            // If docu sign refuse the request because Authentication in valid retry Authenticate and resend the request
            if (strpos($e->getMessage(), '401') !== false) {
                $this->authenticate();
                $result = $envelopeApi->createEnvelope($this->accountId, $envelopeDefinition);
                return $result->getEnvelopeId();
            }
            throw $e;
        }
    }

    public function getRecipientViewUrl($envelopeId, $signer, $returnUrl)
    {
        // Ensure that the user is authenticated
        $this->ensureAuthenticated();
        // Create Recipient View Request
        $recipientViewRequest = new RecipientViewRequest([
            'authentication_method' => 'none',
            'email' => $signer->user->email,
            'user_name' => $signer->user->full_name,
            'recipient_id' => $signer->docusign_recipient_id,
            'return_url' => $returnUrl,
            'client_user_id' => (string) $signer->user->id,
        ]);
        // initialize the  Request to send it to DocuSign
        $envelopeApi = new EnvelopesApi($this->apiClient);
        // send the Request to DocuSign Servers
        $viewUrl = $envelopeApi->createRecipientView(
            $this->accountId,
            $envelopeId,
            $recipientViewRequest
        );
        // get the View URL response from DocuSign
        return $viewUrl->getUrl();
    }
    // Function For get the document after Signing Completing 
    public function downloadSignedDocument($envelopeId, $localSavePath)
    {
        // validate Authentication with docusign 
        $this->ensureAuthenticated();
        // make  new Connection with docusign server 
        $envelopeApi = new EnvelopesApi($this->apiClient);
        // get the document from DocuSign
        $documentBytes = $envelopeApi->getDocument($this->accountId, $envelopeId, '1');
        // store the signed document in local storage
        Storage::put($localSavePath, $documentBytes);
        // return the local save path
        return $localSavePath;
    }
}
