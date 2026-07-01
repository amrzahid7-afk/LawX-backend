<?php

namespace App\Jobs;

use App\Ai\Agents\DocumentAnalyzerAgent;
use App\Models\Document as DocumentModel;
use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Files\Document as AiDocument;
use Throwable;
use Laravel\Ai\Ai;
use Laravel\Ai\Exceptions\ProviderOverloadedException;
use Laravel\Ai\Exceptions\RateLimitedException;

class ProcessDocumentUploadJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;
    public $tries = 10;
    public function __construct(public string $documentId) {}
    public function handle()
    {
        $providers = [
            'zai',
            'openai',
            'gemini',
            'groq',
            // 'anthropic',
            // 'openrouter',
        ];
        $document = Document::findOrFail($this->documentId);
        $user = User::find($document->uploaded_by);
        $agent = DocumentAnalyzerAgent::make($user);

        foreach ($providers as $provider) {
            try {
                Log::info("Trying Analysis the file with AI Provider  : " . $provider);
                $response =   DocumentAnalyzerAgent::make($user) //Ai::textProviderFor($agent, $provider)
                    ->prompt(
                        'Read the document...',
                        attachments: [
                            AiDocument::fromPath(
                                Storage::disk('public')->path($document->file_path)
                            ),
                        ],
                        provider: $provider
                    );
                $document->update([
                    'ai_tags' => $response['keywords'] ?? null,
                    'ai_summary' => $response['summary'] ?? null,
                    'extracted_text' => $response['full_text'] ?? null,
                ]);
                Log::info("Success using  AI Provider : " . $provider);
                return;
            } catch (RateLimitedException $e) {

                Log::warning("$provider rate limited");

                continue;
            } catch (ProviderOverloadedException $e) {

                Log::warning("$provider overloaded");

                continue;
            } catch (Throwable $e) {

                if ($this->isPermanentFailure($e)) {

                    $this->fail($e);

                    return;
                }

                Log::warning("$provider failed : " . $e->getMessage());

                continue;
            }
        }
        throw new \Exception("All AI providers failed.");
    }
    // try {

    //     $response = DocumentAnalyzerAgent::make($user)
    //         ->prompt(
    //             'Read the document...',
    //             attachments: [
    //                 AiDocument::fromPath(
    //                     Storage::disk('public')->path($document->file_path)
    //                 ),
    //             ],
    //         );
    //     $data = $response;
    //     $document->update([
    //         'ai_tags' => $data['keywords'] ?? null,
    //         'ai_summary' => $data['summary'] ?? null,
    //         'extracted_text' => $data['full_text'] ?? null,
    //     ]);
    // } catch (\Throwable $e) {
    //     if ($this->isPermanentFailure($e)) {
    //         $this->fail($e);
    //         return;
    //     }
    //     throw $e;
    // }

    public function backoff(): array
    {
        $delays = [];

        for ($i = 0; $i < 10; $i++) {
            $delays[] = 86400 * (2 ** $i);
        }

        return $delays;
    }
    public function failed(\Throwable $exception): void
    {
        Log::error('Document AI analysis failed', [
            'document_id' => $this->documentId,
            'error' => $exception->getMessage(),
        ]);
    }
    private function isPermanentFailure(Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'unsupported') ||
            str_contains($message, 'invalid file') ||
            str_contains($message, 'corrupted') ||
            str_contains($message, 'mime type');
    }
}
