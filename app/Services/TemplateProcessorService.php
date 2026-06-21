<?php
// app/Services/TemplateProcessorService.php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TemplateProcessorService
{
    /**
     * استخراج المتغيرات من ملف Word ({{ variable_name }})
     */
    public function extractVariables(string $filePath): array
    {
        try {
            // تحميل الملف
            $fullPath = Storage::disk('public')->path($filePath);

            if (!file_exists($fullPath)) {
                throw new \Exception("الملف غير موجود: {$filePath}");
            }

            $templateProcessor = new TemplateProcessor($fullPath);

            // جلب أسماء المتغيرات (PhpWord يعيدها بدون الأقواس)
            $variables = $templateProcessor->getVariables();

            // إزالة التكرارات
            return array_values(array_unique($variables));
        } catch (\Exception $e) {
            Log::error('Failed to extract variables: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * توليد ملف جديد من قالب بدمج القيم
     */
    public function generateDocument(
        string $templateFilePath,
        array $variables,
        array $values,
        string $outputPath
    ): bool {
        try {
            $fullTemplatePath = Storage::disk('public')->path($templateFilePath);

            if (!file_exists($fullTemplatePath)) {
                throw new \Exception("ملف القالب غير موجود");
            }

            $templateProcessor = new TemplateProcessor($fullTemplatePath);

            // استبدال كل متغير بقيمته
            foreach ($variables as $variable) {
                $value = $values[$variable] ?? '';
                $templateProcessor->setValue($variable, (string) $value);
            }

            // حفظ الملف الجديد
            $fullOutputPath = Storage::disk('public')->path($outputPath);
            $templateProcessor->saveAs($fullOutputPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to generate document: ' . $e->getMessage());
            return false;
        }
    }
}
