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
                'type' => 'template',
                'file_size' => $file->getSize(),
                'uploaded_by' => request()->user()->id,
            ]);
            // Extract variables From File with package PhpWord
            $variables = $this->extractVariablesFromFile($storedPath, $extension);
            // add Template record in db 
            $template = SmartTemplate::create([
                'title' => $validatedData['title'],
                'file_id' => $fileRecord->id,
                'variables_json' => $variables,
                'owner_id' => request()->user()->id,
                'template_desc' => $validatedData['template_desc'],
                // 'scope' => $validatedData['scope'],
                'category' => $validatedData['category'],
            ]);
            // Formatting Data 
            $formattedVariables = [
                "Template ID" => $template->id,
                "variables Detected in Template" => $template->variables_json,
                "Template Title" => $template->title,
                "Template Description" => $template->template_desc,
                "Template Category" => $template->category
            ];
            return [
                'success' => true,
                'message' => 'تم رفع القالب بنجاح',
                'data' => [
                    $formattedVariables,
                ],
            ];
        });
    }
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

        DB::BeginTransaction();
        // 3. تحديث البيانات الأساسية
        $template->update([
            'title'    => $validatedData['title'] ?? $template->title,
            'template_desc'    => $validatedData['template_description'] ?? $template->template_description,
            // 'scope'    => $validatedData['scope'] ?? $template->scope,
            'category' => $validatedData['category'] ?? $template->category,
        ]);

        // 4. إذا تم رفع ملف جديد
        if (isset($validatedData['file']) && $validatedData['file']) {
            $file = $validatedData['file'];
            $extension = $file->getClientOriginalExtension();

            $storedPath = $file->store('templates/originals', 'public');

            if ($template->file && $template->file->file_path) {
                \Storage::disk('public')->delete($template->file->file_path);
            }

            $template->file->update([
                'file_path' => $storedPath,
                'type'      => "template",
                'file_size' => $file->getSize(),
            ]);

            $variables = $this->extractVariablesFromFile($storedPath, $extension);
            $template->update(['variables_json' => $variables]);
            DB::commit();
        }
        $formattedVariables = [
            "Template ID" => $template->id,
            "variables Detected in Template" => $template->variables_json,
            "Template Title" => $template->title,
            "Template Description" => $template->template_desc,
            "Template Category" => $template->category
        ];
        return [
            'success' => true,
            'message' => 'تم تحديث القالب بنجاح',
            'data' => [
                $formattedVariables
            ],
        ];
    }

    public function getTemplates(array $filters)
    {
        $query = SmartTemplate::query();
        // Filter Templates instand Of category ,  title
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }
        $templates = $query->latest()->paginate(20);
        $formattedTemplates = collect($templates->items())->map(function ($template) {
            return [
                'Template_id'         => $template->id,
                'title'      => $template->title,
                'category'   => $template->category,     // مثل: عقود / مذكرات / سندات
                'Created_At_Date'       => $template->created_at->format('Y-m-d'), // صيغة التاريخ المتواجدة بالصورة
            ];
        });
        return [
            'success' => true,
            'message' => 'تم جلب القوالب بنجاح',
            'data'    => $formattedTemplates,
            'pagination' => [
                'current_page' => $templates->currentPage(),
                'total_pages'  => $templates->lastPage(),
                'total_items'  => $templates->total(),
                'per_page'     => $templates->perPage(),
                'next_page_url' => $templates->nextPageUrl(),
                'prev_page_url' => $templates->previousPageUrl(),
                'last_page_url' => $templates->url($templates->lastPage()),
            ],
        ];
    }

    /**
     * جلب تفاصيل قالب معين
     */
    // public function getTemplateById(string $id)
    // {
    //     try {
    //         $template = SmartTemplate::with(['file', 'generatedFiles.GeneratedBy'])->find($id);

    //         $formatted_data = [
    //             "Template_ID"          => $template->id,
    //             "Template_name"        => $template->title,
    //             "Template_description" => $template->template_desc,
    //             "Template_category"    => $template->category,
    //             "Template_created_at"  => $template->created_at->format('Y-m-d'),
    //             "Template_updated_at"  => $template->updated_at->format('Y-m-d'),
    //             "Template_variables"   => $template->variables_json,

    //             "Template_file"        => $template->file && Storage::disk('public')->exists($template->file->file_path)
    //                 ? Storage::disk('public')->url($template->file->file_path)
    //                 : null,

    //             "template_file_size"   => $template->file && Storage::disk('public')->exists($template->file->file_path)
    //                 ? round(Storage::disk('public')->size($template->file->file_path) / (1024 * 1024), 2) . ' MB'
    //                 : '0 MB',

    //             "Generated_Files"      => $template->generatedFiles->map(function ($file) {
    //                 $exists = Storage::disk('public')->exists($file->file->file_path);
    //                 return [
    //                     'file_url'        => $exists ? Storage::disk('public')->url($file->file->file_path) : null,
    //                     'file_size'       => $file->file->file_size
    //                         ? round(Storage::disk('public')->size($file->file->file_path) / (1024 * 1024), 2) . ' MB'
    //                         : '0 MB',
    //                     'User_Who_create' => $file->GeneratedBy ? $file->GeneratedBy->name : 'غير معروف'
    //                 ];
    //             }),
    //         ];

    //         return [
    //             'status'  => true,
    //             'message' => 'تم جلب تفاصيل القالب بنجاح',
    //             'data'    => $formatted_data,
    //         ];
    //     } catch (\Exception $e) {
    //         return [
    //             'status' => "Fail",
    //             'Message' => "Service Fail",
    //             'Error_data' => [
    //                 'Exception' => $e->getMessage(),
    //                 'Line' => $e->getLine(),
    //                 'File' => $e->getFile()
    //             ]
    //         ];
    //     }
    // }
    // public function getTemplateById(string $id)
    // {
    //     try {
    //         // قمنا بإضافة generatedFiles.file هنا
    //         $template = SmartTemplate::with([
    //             'file',
    //             'generatedFiles.file',
    //             'generatedFiles.generated_by'
    //         ])->find($id);

    //         $formatted_data = [
    //             "Template_ID"          => $template->id,
    //             "Template_name"        => $template->title,
    //             "Template_description" => $template->template_desc,
    //             "Template_category"    => $template->category,
    //             "Template_created_at"  => $template->created_at->format('Y-m-d'),
    //             "Template_updated_at"  => $template->updated_at->format('Y-m-d'),
    //             "Template_variables"   => $template->variables_json,

    //             "Template_file"        => $template->file && Storage::disk('public')->exists($template->file->file_path)
    //                 ? Storage::disk('public')->url($template->file->file_path)
    //                 : null,

    //             "template_file_size"   => $template->file && Storage::disk('public')->exists($template->file->file_path)
    //                 ? round(Storage::disk('public')->size($template->file->file_path) / (1024 * 1024), 2) . ' MB'
    //                 : '0 MB',

    //             "Generated_Files"      => $template->generatedFiles->map(function ($gFile) {
    //                 // التأكد الآمن من وجود علاقة الملف والمسار
    //                 $filePath = ($gFile->file) ? $gFile->file->file_path : null;
    //                 $exists = $filePath ? Storage::disk('public')->exists($filePath) : false;

    //                 return [
    //                     'file_url'        => $exists ? Storage::disk('public')->url($filePath) : null,
    //                     'file_size'       => $exists
    //                         ? round(Storage::disk('public')->size($filePath) / (1024 * 1024), 2) . ' MB'
    //                         : '0 MB',
    //                     // هنا سيظهر الاسم بالشكل الصحيح الآن
    //                     'User_Who_create' => $gFile->GeneratedBy ? $gFile->GeneratedBy->name : 'غير معروف'
    //                 ];
    //             }),
    //         ];

    //         return [
    //             'status'  => true,
    //             'message' => 'تم جلب تفاصيل القالب بنجاح',
    //             'data'    => $formatted_data,
    //         ];
    //     } catch (\Exception $e) {
    //         return [
    //             'status' => "Fail",
    //             'Message' => "Service Fail",
    //             'Error_data' => [
    //                 'Exception' => $e->getMessage(),
    //                 'Line' => $e->getLine(),
    //                 'File' => $e->getFile()
    //             ]
    //         ];
    //     }
    // }
    public function getTemplateById(string $id)
    {
        try {
            // تحديث أسماء العلاقات لتشمل التحميل الصحيح للملف والمستخدم
            $template = SmartTemplate::with([
                'file',
                'generatedFiles.file',
                'generatedFiles.generatedBy' // حرف صغير g
            ])->find($id);

            $formatted_data = [
                "Template_ID"          => $template->id,
                "Template_name"        => $template->title,
                "file_type"        => $template->file->type,
                "Template_description" => $template->template_desc,
                "Template_category"    => $template->category,
                "Template_created_at"  => $template->created_at->format('Y-m-d'),
                "Template_updated_at"  => $template->updated_at->format('Y-m-d'),
                "Template_variables"   => $template->variables_json,

                "Template_file"        => $template->file && Storage::disk('public')->exists($template->file->file_path)
                    ? Storage::disk('public')->url($template->file->file_path)
                    : null,

                "template_file_size"   => $template->file && Storage::disk('public')->exists($template->file->file_path)
                    ? round(Storage::disk('public')->size($template->file->file_path) / (1024 * 1024), 2) . ' MB'
                    : '0 MB',

                "Generated_Files"      => $template->generatedFiles->map(function ($gFile) {
                    $filePath = $gFile->file ? $gFile->file->file_path : null;
                    $exists = $filePath ? Storage::disk('public')->exists($filePath) : false;

                    return [
                        'file_url'        => $exists ? Storage::disk('public')->url($filePath) : null,
                        'file_size'       => $exists
                            ? round(Storage::disk('public')->size($filePath) / (1024 * 1024), 2) . ' MB'
                            : '0 MB',
                        // استدعاء الاسم باستخدام العلاقة الجديدة بحرف صغير g
                        'User_Who_create' => $gFile->generatedBy ? $gFile->generatedBy->full_name : 'غير معروف'
                    ];
                }),
            ];

            return [
                'status'  => true,
                'message' => 'تم جلب تفاصيل القالب بنجاح',
                'data'    => $formatted_data,
            ];
        } catch (\Exception $e) {
            return [
                'status' => "Fail",
                'Message' => "Service Fail",
                'Error_data' => [
                    'Exception' => $e->getMessage(),
                    'Line' => $e->getLine(),
                    'File' => $e->getFile()
                ]
            ];
        }
    }

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

    public function generateFromTemplate($data)
    {
        $template = SmartTemplate::with('file')->find($data['template_id']);
        $customValues = $data['custom_values'] ?? [];

        return DB::transaction(function () use ($template, $customValues) {
            $templateFilePath = $template->file->file_path;

            if (!Storage::disk('public')->exists($templateFilePath)) {
                throw new \Exception("ملف القالب غير موجود في التخزين: " . $templateFilePath);
            }

            // مسار ملف الـ Word المؤقت
            $timestamp = time();
            $docxFileName = Str::slug($template->title) . '_' . $timestamp . '.docx';
            $docxPath = 'templates/generated/' . $docxFileName;

            $variables = is_array($template->variables_json) ? $template->variables_json : [];

            // call function to generate Word file 
            $success = $this->generateFromWordTemplate(
                $templateFilePath,
                $variables,
                $customValues,
                $docxPath
            );

            if (!$success) {
                throw new \Exception("فشل في معالجة ملف القالب وتوليد صيغة Word");
            }

            // 2. استدعاء دالة التحويل إلى PDF المعتمدة على Spatie
            $pdfPath = $this->convertToPdf($docxPath);

            if (!$pdfPath) {
                throw new \Exception("فشل في تحويل الملف إلى PDF عبر حزمة Spatie");
            }

            // 3. تنظيف التخزين وحذف ملف الـ Word المؤقت فوراً
            Storage::disk('public')->delete($docxPath);

            // 4. تسجيل الملف الـ PDF في قاعدة البيانات
            $generatedFile = Document::create([
                'file_path'   => $pdfPath,
                'type'        => 'pdf',
                'uploaded_by' =>  request()->user()->id,
            ]);

            GeneratedTemplate::create([
                'template_id'  => $template->id,
                'file_id'      => $generatedFile->id,
                'generated_by' => request()->user()->id,
                'used_values'  => $customValues,
            ]);

            // رابط التحميل النهائي للملف الـ PDF
            $downloadUrl = Storage::disk('public')->url($pdfPath);

            // 5. الرد المطلوب تماماً (Success Message + Download URL)
            return [
                'success'      => true,
                'message'      => 'تم توليد الملف بنجاح',
                'download_url' => $downloadUrl,
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
}
