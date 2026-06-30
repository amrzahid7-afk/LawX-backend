<?php

namespace App\Jobs;

use App\Ai\Agents\DocumentAnalyzerAgent;
use App\Models\Document as DocumentModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Files\Document as AiDocument; // 💡 الكلاس الصحيح المذكور في فقرة "إرسال الملف كمرفق"

class ProcessDocumentUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $document_id;
    protected $request;


    public function __construct($document_id, $request)
    {
        $this->document_id = $document_id;
        $this->request = $request;
    }

    public function handle()
    {
        try {
            $file = DocumentModel::find($this->document_id);
            if (!Storage::disk('public')->exists($file->file_path)) {
                throw new \Exception("Document file not found.");
            }
            $fullPath = Storage::disk('public')->path($file->file_path);

            $Document = AiDocument::fromPath($fullPath);

            $response = DocumentAnalyzerAgent::make(auth('sanctum')->user())->prompt(
                'اقرأ الملف المرفق بالكامل.استخرج النص كما هو.ثم أنشئ ملخصًا.ثم أنشئ كلمات مفتاحية.',
                attachments: [
                    $Document,
                ]
            );
            $file->update([
                'tags' => $response['key_words'] ?? null,
                'summary' => $response['summary'] ?? null,
                'extracted_text' => $response['full_text'] ?? null,
            ]);
        } catch (\Throwable $e) {

            logger()->error($e->getMessage());

            logger()->error($e->getTraceAsString());

            throw $e;
        }
    }
}
