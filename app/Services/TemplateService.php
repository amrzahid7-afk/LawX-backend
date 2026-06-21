<?php
// app/Services/TemplateService.php

namespace App\Services;

use App\Models\CaseModel;
use App\Models\Document;
use App\Models\File;
use App\Models\GeneratedTemplate;
use App\Models\SmartTemplate;
use App\Traits\HasTemplateVariables;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemplateService
{
    use HasTemplateVariables;
    public function uploadTemplate(array $validatedData, $file)
    {
        return DB::transaction(function () use ($validatedData, $file) {
            // storing the file 
            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $storedPath = $file->store('templates/originals', 'public');
            // add document record in db 
            $fileRecord = Document::create([
                'file_path' => $storedPath,
                'type' => $extension,
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);
            // Extract variables From File with package PhpWord
            $variables = $this->extractVariablesFromFile($storedPath, $extension);
            // add Template record in db 
            $template = SmartTemplate::create([
                'title' => $validatedData['title'],
                'file_id' => $fileRecord->id,
                'variables_json' => $variables,
                'owner_id' => $validatedData['scope'] === 'private' ? Auth::id() : null,
                'scope' => $validatedData['scope'],
                'category' => $validatedData['category'],
            ]);

            return [
                'success' => true,
                'message' => 'تم رفع القالب بنجاح',
                'data' => [
                    'template' => $template->load(['file', 'owner'])
                ],
            ];
        });
    }


    public function getTemplates(array $filters)
    {
        $query = SmartTemplate::with(['file', 'owner'])->accessibleBy(request()->user()->id);

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['scope']) && in_array($filters['scope'], ['public', 'private'])) {
            if ($filters['scope'] === 'private') {
                $query->where('owner_id', request()->user()->id);
            } else {
                $query->where('scope', 'public');
            }
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        $templates = $query->latest()->paginate(20);

        return [
            'success' => true,
            'message' => 'تم جلب القوالب بنجاح',
            'data' => [
                'templates' => $templates,
                'pagination' => [
                    'current_page' => $templates->currentPage(),
                    'total_pages'  => $templates->lastPage(),
                    'total_items'  => $templates->total(),
                    'per_page'     => $templates->perPage(),
                ],
            ],
        ];
    }

    /**
     * جلب تفاصيل قالب معين
     */
    public function getTemplateById(string $id)
    {
        $template = SmartTemplate::with(['file', 'owner', 'generatedFiles'])
            ->accessibleBy(request()->user()->id)
            ->find($id);
        return [
            'status' => true,
            'message' => 'تم جلب تفاصيل القالب بنجاح',
            'data' => ['template' => $template],
        ];
    }

    /**
     * تحديث قالب
     */
    // public function updateTemplate(array $validatedData)
    // {

    //     // get template by ID
    //     $template = SmartTemplate::find($validatedData['template_id']);
    //     // here will add the constraint to edit this template 
    //     $template->update([
    //         'title' => $validatedData['title'] ?? $template->title,
    //         'scope' => $validatedData['scope'] ?? $template->scope,
    //         'category' => $validatedData['category'] ?? $template->category,
    //     ]);
    //     if (isset($validatedData['file']) && $validatedData['file']) {
    //         $file = $validatedData['file'];
    //         $extension = $file->getClientOriginalExtension();
    //         $storedPath = $file->store('templates/originals', 'public');
    //         //  $template->file->delete();
    //         $template->file->update([
    //             'file_path' => $storedPath,
    //             'type' => $extension,
    //             'file_size' => $file->getSize(),
    //         ]);

    //         // ✅ إعادة استخراج المتغيرات باستخدام الـ Trait
    //         $variables = $this->extractVariablesFromFile($storedPath, $extension);
    //         $template->update(['variables_json' => $variables]);
    //     }

    //     return [
    //         'success' => true,
    //         'message' => 'تم تحديث القالب بنجاح',
    //         'data' => ['template' => $template->load(['file', 'owner'])],
    //     ];
    // }
    public function updateTemplate(array $validatedData)
    {
        // 1. جلب القالب
        $template = SmartTemplate::find($validatedData['template_id']);


        // 2. قيد الصلاحية (المالك فقط يمكنه التعديل)
        if ($template->owner_id && $template->owner_id !== auth('sanctum')->user()->id) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'غير مسموح بتعديل هذا القالب',
            ], 403));
        }

        // 3. تحديث البيانات الأساسية
        $template->update([
            'title'    => $validatedData['title'] ?? $template->title,
            'scope'    => $validatedData['scope'] ?? $template->scope,
            'category' => $validatedData['category'] ?? $template->category,
        ]);

        // 4. إذا تم رفع ملف جديد
        if (isset($validatedData['file']) && $validatedData['file']) {
            $file = $validatedData['file'];
            $extension = $file->getClientOriginalExtension();

            // ✅ حفظ الملف الجديد في Storage
            $storedPath = $file->store('templates/originals', 'public');

            // ✅ حذف الملف الفعلي القديم من Storage (وليس من قاعدة البيانات!)
            if ($template->file && $template->file->file_path) {
                \Storage::disk('public')->delete($template->file->file_path);
            }

            // ✅ تحديث سجل الملف في قاعدة البيانات (بدون حذفه!)
            $template->file->update([
                'file_path' => $storedPath,
                'type'      => $extension,
                'file_size' => $file->getSize(),
            ]);

            // ✅ إعادة استخراج المتغيرات من الملف الجديد
            $variables = $this->extractVariablesFromFile($storedPath, $extension);
            $template->update(['variables_json' => $variables]);
        }

        return [
            'success' => true,
            'message' => 'تم تحديث القالب بنجاح',
            'data' => [
                'template' => $template->load(['file', 'owner'])
            ],
        ];
    }
    /**
     * حذف قالب
     */
    public function deleteTemplate()
    {
        $template = SmartTemplate::find(request()->template_id);
        // validate permission to delete this template
        if ($template->owner_id && $template->owner_id !== Auth::id()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'غير مسموح بحذف هذا القالب',
            ], 403));
        }
        // delete the Template file from storaeg driver
        if ($template->file) {
            Storage::disk('public')->delete($template->file->file_path);
        }
        // delete  the Template from DB 
        $template->delete();
        return [
            'status' => true,
            'message' => 'تم حذف القالب بنجاح',
            'data' => [],
        ];
    }

    /**
     * توليد ملف من قالب (FR-LB-02)
     */
    public function generateFromTemplate($data)
    {
        $template = \App\Models\SmartTemplate::find($data['template_id']);

        if (!$template || !$template->file) {
            throw new \Exception("القالب أو الملف المرتبط به غير موجود");
        }

        $customValues = $data['custom_values'] ?? [];

        return DB::transaction(function () use ($template, $customValues) {

            $templateFilePath = $template->file->file_path;

            // ✅ التأكد من وجود الملف الفعلي في الـ Storage
            if (!Storage::disk('public')->exists($templateFilePath)) {
                throw new \Exception("ملف القالب غير موجود في التخزين: " . $templateFilePath);
            }

            $outputFileName = Str::slug($template->title) . '_' . time() . '.docx';
            $outputPath = 'templates/generated/' . $outputFileName;

            // ✅ ضمان أن المتغيرات مصفوفة (لتجنب خطأ foreach)
            $variables = is_array($template->variables_json) ? $template->variables_json : [];

            // ✅ استدعاء دالة التوليد من الـ Trait
            $success = $this->generateFromWordTemplate(
                $templateFilePath,
                $variables,
                $customValues,
                $outputPath
            );

            if (!$success) {
                throw new \Exception("فشل في معالجة ملف القالب، تأكد من أن الملف بصيغة docx صالحة");
            }

            // حفظ الملف المولد في جدول Documents
            $generatedFile = \App\Models\Document::create([
                'file_path' => $outputPath,
                'type' => 'docx',
                'uploaded_by' => auth('sanctum')->id(),
            ]);

            $generated = \App\Models\GeneratedTemplate::create([
                'template_id' => $template->id,
                'file_id' => $generatedFile->id,
                'generated_by' => auth('sanctum')->id(),
                'used_values' => $customValues,
            ]);

            $downloadUrl = Storage::disk('public')->url($outputPath);

            return [
                'success' => true,
                'message' => 'تم توليد الملف بنجاح',
                'data' => [
                    'generated' => $generated->load(['file', 'template']),
                    'download_url' => $downloadUrl,
                    'used_values' => $customValues,
                ],
            ];
        });
    }
    private function buildDefaultValues($case): array
    {
        return [
            'client_name' => $case->client?->name ?? '',
            'client_phone' => $case->client?->phone ?? '',
            'client_email' => $case->client?->email ?? '',
            'client_address' => $case->client?->address ?? '',
            'case_title' => $case->title ?? '',
            'case_description' => $case->description ?? '',
            'case_number' => $case->case_number ?? '',
            'case_date' => $case->created_at?->format('Y-m-d') ?? '',
            'lawyer_name' => Auth::user()->name ?? '',
            'today_date' => now()->format('Y-m-d'),
            'office_name' => config('app.name', 'LawX Office'),
        ];
    }

    public function getDownloadUrl(string $generatedId)
    {
        $generated = GeneratedTemplate::with('file')
            ->where('generated_by', auth('sanctum')->user()->id)
            ->find($generatedId);
        $downloadUrl = Storage::disk('public')->url($generated->file->file_path);
        return [
            'success' => true,
            'data' => [
                'download_url' => $downloadUrl,
                'file_name' => $generated->file->file_name,
                'expires_at' => now()->addMinutes(10)->toIso8601String(),
            ],
        ];
    }
}
