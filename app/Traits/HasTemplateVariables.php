<?php

namespace App\Traits;

use PhpOffice\PhpWord\TemplateProcessor;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait HasTemplateVariables
{
    /**
     * استخراج المتغيرات من الملف بناءً على نوعه
     * 
     * @param string $filePath مسار الملف في Storage
     * @param string $fileType نوع الملف (docx, pdf)
     * @return array قائمة بالمتغيرات
     */
    public function extractVariablesFromFile(string $filePath, string $fileType): array
    {
        try {
            $fullPath = Storage::disk('public')->path($filePath);
            // check if the file exists
            if (!file_exists($fullPath)) {
                Log::error("File not found: {$filePath}");
                return [];
            }

            // توجيه الاستخراج حسب نوع الملف
            return match (strtolower($fileType)) {
                'docx', 'doc' => $this->extractFromWord($fullPath),
                'pdf'         => $this->extractFromPdf($fullPath),
                default       => [],
            };
        } catch (\Exception $e) {
            Log::error('Variable extraction failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * استخراج المتغيرات من ملف Word
     * يبحث عن الأنماط: {{ variable_name }} أو ${variable_name}
     */
    private function extractFromWord(string $fullPath): array
    {
        try {
            $templateProcessor = new TemplateProcessor($fullPath);

            // PhpWord يُرجع المتغيرات بصيغة ${variable} أو {{variable}}
            $variables = $templateProcessor->getVariables();

            // إزالة التكرارات وإعادة الفهرسة
            return array_values(array_unique($variables));
        } catch (\Exception $e) {
            Log::error('Word extraction error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * استخراج المتغيرات من ملف PDF
     * يستخدم Regex للبحث عن {{ variable_name }}
     */
    private function extractFromPdf(string $fullPath): array
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);

            // استخراج النص من كل صفحات PDF
            $text = $pdf->getText();

            // البحث عن المتغيرات بصيغة {{ variable_name }}
            // يدعم المسافات: {{ client_name }} أو {{client_name}}
            preg_match_all('/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/', $text, $matches);

            if (!empty($matches[1])) {
                return array_values(array_unique($matches[1]));
            }

            // محاولة البحث عن صيغة ${variable_name}
            preg_match_all('/\$\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $text, $matches);

            return !empty($matches[1])
                ? array_values(array_unique($matches[1]))
                : [];
        } catch (\Exception $e) {
            Log::error('PDF extraction error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * توليد ملف جديد من قالب (Word فقط - لأن PDF لا يدعم التعديل بسهولة)
     */
    public function generateFromWordTemplate(
        string $templateFilePath,
        array $variables,
        array $values,
        string $outputPath
    ): bool {
        try {
            $fullTemplatePath = Storage::disk('public')->path($templateFilePath);
            $fullOutputPath = Storage::disk('public')->path($outputPath);

            // ✅ التحقق من وجود الملف
            if (!file_exists($fullTemplatePath)) {
                Log::error("Template file not found: {$fullTemplatePath}");
                return false;
            }

            // ✅ إنشاء مجلد الإخراج إذا لم يكن موجوداً
            $outputDir = dirname($fullOutputPath);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // ✅ نسخ الملف الأصلي أولاً
            if (!copy($fullTemplatePath, $fullOutputPath)) {
                Log::error("Failed to copy template file");
                return false;
            }

            // ✅ محاولة استخدام PhpWord
            try {
                $templateProcessor = new TemplateProcessor($fullOutputPath);

                // استبدال كل متغير بقيمته
                foreach ($variables as $variable) {
                    $value = $values[$variable] ?? '';
                    try {
                        $templateProcessor->setValue($variable, (string) $value);
                    } catch (\Exception $e) {
                        Log::warning("Could not set value for {$variable}: " . $e->getMessage());
                    }
                }

                // حفظ الملف
                $templateProcessor->saveAs($fullOutputPath);

                Log::info("Document generated successfully with PhpWord", [
                    'output' => $fullOutputPath,
                    'variables_count' => count($variables)
                ]);

                return true;
            } catch (\Exception $e) {
                Log::error("PhpWord generation failed: " . $e->getMessage());

                // ✅ Fallback: استخدام Regex مباشرة على XML
                return $this->replaceVariablesInDocx($fullOutputPath, $values);
            }
        } catch (\Exception $e) {
            Log::error('Document generation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fallback: استبدال المتغيرات مباشرة في XML
     */
    private function replaceVariablesInDocx(string $filePath, array $values): bool
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) !== true) {
                Log::error("Cannot open DOCX file as ZIP");
                return false;
            }

            $documentXml = $zip->getFromName('word/document.xml');
            if (!$documentXml) {
                Log::error("Cannot read document.xml from DOCX");
                $zip->close();
                return false;
            }

            // استبدال المتغيرات بصيغة {{ variable_name }}
            foreach ($values as $key => $value) {
                $pattern = '/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/';
                $documentXml = preg_replace($pattern, htmlspecialchars((string) $value), $documentXml);
            }

            // تحديث الملف
            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $documentXml);
            $zip->close();

            Log::info("Document generated successfully with Regex fallback");
            return true;
        } catch (\Exception $e) {
            Log::error('Regex replacement failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * استبدال المتغيرات بصيغة {{ variable_name }} في الملف المولد
     */
    private function replaceMustacheVariables(string $filePath, array $values): void
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                $documentXml = $zip->getFromName('word/document.xml');

                if ($documentXml) {
                    // استبدال كل متغير بصيغة {{ variable_name }}
                    foreach ($values as $key => $value) {
                        $pattern = '/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/';
                        $documentXml = preg_replace($pattern, (string) $value, $documentXml);
                    }

                    // تحديث الملف
                    $zip->deleteName('word/document.xml');
                    $zip->addFromString('word/document.xml', $documentXml);
                }

                $zip->close();
            }
        } catch (\Exception $e) {
            Log::error('Mustache replacement error: ' . $e->getMessage());
        }
    }
}
