<?php

namespace App\Traits;

use PhpOffice\PhpWord\TemplateProcessor;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use Spatie\Pdf\Pdf;

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
            // ===== OLD (Local storage) — علّق ده =====
            // $fullPath = Storage::disk('public')->path($filePath);
            // if (!file_exists($fullPath)) {
            //     Log::error("File not found: {$filePath}");
            //     return [];
            // }

            // ===== NEW (Cloudinary) =====
            // نزّل القالب من Cloudinary لملف مؤقت للمعالجة المحلية
            $ext = pathinfo(parse_url($filePath, PHP_URL_PATH), PATHINFO_EXTENSION) ?: $fileType;
            $fullPath = tempnam(sys_get_temp_dir(), 'lawx_tpl_') . '.' . $ext;
            $content = @file_get_contents($filePath);
            if ($content === false) {
                Log::error("File not found: {$filePath}");
                return [];
            }
            file_put_contents($fullPath, $content);

            // استخراج المتغيرات
            $result = match (strtolower($fileType)) {
                'docx', 'doc' => $this->extractFromWord($fullPath),
                'pdf'         => $this->extractFromPdf($fullPath),
                default       => [],
            };
            @unlink($fullPath);
            return $result;
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
    public function generateFromWordTemplate(string $localTemplatePath, array $variables, array $values, string $localOutputPath): bool
    {
        try {
            // ===== OLD (Local storage) — علّق ده =====
            // $fullTemplatePath = Storage::disk('public')->path($templateFilePath);
            // $fullOutputPath = Storage::disk('public')->path($outputPath);
            // if (!file_exists($fullTemplatePath)) {
            //     Log::error("Template file not found: {$fullTemplatePath}");
            //     return false;
            // }
            // $outputDir = dirname($fullOutputPath);
            // if (!is_dir($outputDir)) {
            //     mkdir($outputDir, 0755, true);
            // }
            // if (!copy($fullTemplatePath, $fullOutputPath)) {
            //     Log::error("Failed to copy template file");
            //     return false;
            // }

            // ===== NEW (Cloudinary) =====
            // المسارات بقت محلية مؤقتة (جاية جاهزة من generateFromTemplate)
            $fullTemplatePath = $localTemplatePath;
            $fullOutputPath   = $localOutputPath;

            if (!file_exists($fullTemplatePath)) {
                Log::error("Template file not found: {$fullTemplatePath}");
                return false;
            }

            // ننسخ القالب المصدر للناتج عشان نعدّل على النسخة
            if (!copy($fullTemplatePath, $fullOutputPath)) {
                Log::error("Failed to copy template file");
                return false;
            }

            // محاولة استخدام PhpWord
            try {
                $templateProcessor = new TemplateProcessor($fullOutputPath);

                foreach ($variables as $variable) {
                    $value = $values[$variable] ?? '';
                    try {
                        $templateProcessor->setValue($variable, (string) $value);
                    } catch (\Exception $e) {
                        Log::warning("Could not set value for {$variable}: " . $e->getMessage());
                    }
                }

                $templateProcessor->saveAs($fullOutputPath);

                Log::info("Document generated successfully with PhpWord", [
                    'output' => $fullOutputPath,
                    'variables_count' => count($variables)
                ]);

                return true;
            } catch (\Exception $e) {
                Log::error("PhpWord generation failed: " . $e->getMessage());
                // Fallback: Regex على XML
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
    private function convertToPdf(string $localDocxPath): ?string
    {
        try {
            // ===== NEW (LibreOffice headless) =====
            // 1. مجلد الإخراج المؤقت للـ PDF
            $outputDir = sys_get_temp_dir();

            // 2. مسار الـ LibreOffice (يختلف بين ويندوز المحلي و Railway/Linux)
            $sofficeBinary = env('SOFFICE_BINARY', 'libreoffice');

            // 3. أمر التحويل: docx → pdf
            $command = sprintf(
                '%s --headless --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg($sofficeBinary),
                escapeshellarg($outputDir),
                escapeshellarg($localDocxPath)
            );

            exec($command, $cmdOutput, $returnCode);
            Log::info('LibreOffice conversion output', ['output' => $cmdOutput, 'code' => $returnCode]);

            // 4. اسم الـ PDF الناتج: نفس اسم الـ docx بامتداد pdf
            $pdfFileName = pathinfo($localDocxPath, PATHINFO_FILENAME) . '.pdf';
            $fullPdfPath = $outputDir . DIRECTORY_SEPARATOR . $pdfFileName;

            if (!file_exists($fullPdfPath)) {
                Log::error('LibreOffice did not produce a PDF', ['expected' => $fullPdfPath]);
                return null;
            }

            // 5. رفع الـ PDF الناتج لـ Cloudinary
            $upload = app(\Cloudinary\Cloudinary::class)
                ->uploadApi()
                ->upload($fullPdfPath, [
                    'folder' => 'lawx/templates/generated',
                    'resource_type' => 'auto',
                ]);

            @unlink($fullPdfPath); // امسح الـ PDF المؤقت

            Log::info('تم تحويل الملف إلى PDF بـ LibreOffice ورفعه لـ Cloudinary');
            return $upload['secure_url'];
        } catch (\Exception $e) {
            Log::error("LibreOffice conversion failed: " . $e->getMessage());
            throw new \Exception("فشل تحويل الملف إلى PDF: " . $e->getMessage());
        }
    }
}
