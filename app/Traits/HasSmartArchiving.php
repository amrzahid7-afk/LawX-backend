<?php

namespace App\Traits;

use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini\Enums\ModelType; // 💡 استدعاء الـ Enum الخاص بالموديلات لتفادي كتابة الاسم النصي خطأ
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HasSmartArchiving
{
    // /**
    //  * استخراج النص من الملف (PDF أو صورة) باستخدام Gemini مجاناً
    //  */
    // public function extractTextFromFile(string $filePath): ?string
    // {
    //     try {
    //         $fullPath = Storage::disk('public')->path($filePath);

    //         if (!file_exists($fullPath)) {
    //             Log::error("File not found: {$filePath}");
    //             return null;
    //         }

    //         $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

    //         $mimeType = match ($extension) {
    //             'pdf' => MimeType::APPLICATION_PDF,
    //             'png' => MimeType::IMAGE_PNG,
    //             'jpg', 'jpeg' => MimeType::IMAGE_JPEG,
    //             'webp' => MimeType::IMAGE_WEBP,
    //             default => MimeType::APPLICATION_PDF,
    //         };

    //         $fileData = base64_encode(file_get_contents($fullPath));

    //         // 💡 استخدام الـ Enum الرسمي للموديل (Gemini 1.5 Pro أو Flash المستقر) لضمان التوافق مع الـ v1beta
    //         $result = Gemini::generativeModel(model: ModelType::GEMINI_1_5_PRO)->generateContent([
    //             'أنت مساعد قانوني ذكي لمنصة LawX. قم باستخراج كافة النصوص الواردة في هذا الملف بدقة وبنفس الترتيب بدون أي زيادة أو تفسير من عندك.',
    //             new Blob(
    //                 mimeType: $mimeType,
    //                 data: $fileData
    //             )
    //         ]);

    //         return trim($result->text());
    //     } catch (\Exception $e) {
    //         Log::error("Gemini OCR failed: " . $e->getMessage());
    //         return null;
    //     }
    // }

    // /**
    //  * توليد وسوم بالذكاء الاصطناعي عبر Gemini
    //  */
    // public function generateAiTags(string $text): array
    // {
    //     try {
    //         if (empty($text)) return [];

    //         $textToAnalyze = substr($text, 0, 4000);

    //         $result = Gemini::generativeModel(model: ModelType::GEMINI_1_5_PRO)->generateContent(
    //             "أنت مساعد قانوني. استخرج من 5 إلى 10 كلمات مفتاحية (وسوم) من النص القانوني التالي. أعد فقط الكلمات مفصولة بفاصلة عادية دون أي نص إضافي:\n\n" . $textToAnalyze
    //         );

    //         $tagsString = $result->text();
    //         $tags = array_map('trim', explode(',', $tagsString));
    //         return array_values(array_filter(array_unique($tags)));
    //     } catch (\Exception $e) {
    //         Log::error("Gemini tagging failed: " . $e->getMessage());
    //         return [];
    //     }
    // }

    // /**
    //  * توليد ملخص بالذكاء الاصطناعي عبر Gemini
    //  */
    // public function generateAiSummary(string $text): ?string
    // {
    //     try {
    //         if (empty($text)) return null;

    //         $textToSummarize = substr($text, 0, 6000);

    //         $result = Gemini::generativeModel(model: ModelType::GEMINI_1_5_PRO)->generateContent(
    //             "أنت مساعد قانوني محترف. لخص النص القانوني التالي في 3 أو 4 جمل واضحة ومكثفة باللغة العربية:\n\n" . $textToSummarize
    //         );

    //         return trim($result->text());
    //     } catch (\Exception $e) {
    //         Log::error("Gemini summary failed: " . $e->getMessage());
    //         return null;
    //     }
    // }
}
