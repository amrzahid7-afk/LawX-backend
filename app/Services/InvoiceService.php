<?php

namespace App\Services;

use App\Models\CaseModel;
use App\Models\Document;
use App\Models\Invoice;
use App\Notifications\Create_Invoice_Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{

    public function createInvoice($data)
    {
        DB::beginTransaction();

        $case = CaseModel::with(['clients', 'leadLawyer'])->find($data['case_id']);
        $primaryClient = $case->clients->firstWhere('pivot.is_primary', 1);

        if (!$primaryClient) {
            DB::rollBack();
            throw new \Exception("No primary client found for this case.");
        }
        $invoice = Invoice::create([
            'case_id'     => $data['case_id'],
            'lawyer_id'   => $case->leadLawyer->id,
            'client_id'   => $primaryClient->id,
            'total_amount' => $data['total_amount'],
            'due_date'    => $data['due_date'],
            'type'    => $data['type'],
            'items'       => json_encode($data['items']),
        ]);
        if (isset($data['documents']) && is_array($data['documents'])) {
            foreach ($data['documents'] as $file) {
                // ===== OLD (Local storage) — علّق ده =====
                // $path = $file->store('invoices', 'public');

                // ===== NEW (Cloudinary) =====
                $path = app(\Cloudinary\Cloudinary::class)
                    ->uploadApi()
                    ->upload($file->getRealPath(), [
                        'folder' => 'lawx/invoices',
                        'resource_type' => 'auto',
                    ])['secure_url'];

                Document::create([
                    'case_id' => $data['case_id'],
                    'file_path' => $path,
                    'type' => 'invoice',
                    'uploaded_by' => auth('sanctum')->id(),
                    'nots' => $data['nots'] ?? null,
                    'invoice_id' => $invoice->id,
                ]);
            }
        }
        DB::commit();
        $client = $invoice->client;
        $client->notify(new Create_Invoice_Notification($invoice->id, $client->id));
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Invoice created successfully',
                'invoice_ID' => $invoice->id
            ],
            'code' => '201'
        ];
    }
    public function get_invoices_for_client($request)
    {
        $client_id = $request->user();
        $invoices = $client_id->client_invoices()
            ->with(['documents', 'case', 'lawyer'])
            ->where('status', $request->input('filter'))
            ->where('type', 'For_case')
            ->get();
        $formattedInvoices = $invoices->map(function ($invoice) {
            $files = $invoice->documents->map(function ($doc) {
                return [
                    'file_url' => $doc->file_path,
                    'file_name' => $doc->type,
                    'file_size' => "1 MB",
                ];
            });
            return [
                'id' => $invoice->id,
                'case_title' => $invoice->case->title ?? null,
                'lawer_name' => $invoice->lawyer->full_name ?? $invoice->lawyer->name,
                'total_amount' => $invoice->total_amount,
                'due_date' => $invoice->due_date,
                'items' => json_decode($invoice->items, true),
                'status' => $invoice->status,
                'created_at' => $invoice->created_at->format('Y-m-d H'),
                'files' => $files,
            ];
        });
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Invoices retrieved successfully',
                'invoices' => $formattedInvoices
            ],
            'code' => '200'
        ];
    }
    // public function get_invoices_for_lawer($request)
    // {
    //     $lawyer = auth('sanctum')->user();

    //     // 1. جلب الفواتير مع تحميل العلاقات مسبقاً (Eager Loading)
    //     $invoices = Invoice::with(['documents', 'case', 'client', 'salary'])
    //         ->where('lawyer_id', $lawyer->id)
    //         // ->where('type', 'For_case')
    //         ->get();

    //     // 2. دالة مساعدة لتنسيق الملفات (لتجنب تكرار الكود)
    //     $formatFiles = function ($documents) {
    //         return $documents->map(function ($doc) {
    //             return [
    //                 'file_url' => Storage::url($doc->file_path),
    //                 'file_name' => $doc->type,
    //                 'file_size' => Storage::size($doc->file_path)
    //             ];
    //         });
    //     };

    //     // 3. التجميع الاحترافي باستخدام Collections
    //     $groupedInvoices = $invoices->groupBy('type')->map(function ($typeInvoices, $type) use ($formatFiles) {

    //         // التجميع الثاني بحسب الحالة (paid, unpaid)
    //         return $typeInvoices->groupBy('status')->map(function ($statusInvoices) use ($type, $formatFiles) {

    //             return $statusInvoices->map(function ($invoice) use ($type, $formatFiles) {
    //                 $files = $formatFiles($invoice->documents);

    //                 // بيانات أساسية مشتركة بين كل أنواع الفواتير
    //                 $baseData = [
    //                     'id' => $invoice->id,
    //                     'total_amount' => $invoice->total_amount,
    //                     'due_date' => $invoice->due_date,
    //                     'status' => $invoice->status,
    //                     'created_at' => $invoice->created_at->format('Y-m-d H'),
    //                     'files' => $files,
    //                 ];

    //                 // بيانات خاصة بفاتورة القضايا (For_case)
    //                 if ($type === 'For_case') {
    //                     $baseData['case_title'] = $invoice->case->title ?? null;
    //                     $baseData['client_name'] = $invoice->client->full_name ?? null;
    //                     $baseData['items'] = json_decode($invoice->items, true);
    //                 }

    //                 // بيانات خاصة بفاتورة الرواتب (salary)
    //                 if ($type === 'salary' && $invoice->salary) {
    //                     $baseData['salary_details'] = [
    //                         "lawyer_name" => $invoice->lawyer->full_name,
    //                         'month' => $invoice->salary->month,
    //                         'year' => $invoice->salary->year,
    //                         'base_salary' => $invoice->salary->base_salary,
    //                         'total_approved_expenses' => $invoice->salary->total_approved_expenses,
    //                         'net_salary' => $invoice->salary->net_salary,

    //                     ];
    //                 }

    //                 return $baseData;
    //             });
    //         });
    //     })->toArray(); // تحويل الكولكشن لمصفوفة عادية لإرسالها كـ JSON

    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'Invoices retrieved successfully',
    //             'invoices' => $groupedInvoices
    //         ],
    //         'code' => '200'
    //     ];
    // }
    public function get_invoices_for_lawer($request)
    {
        $lawyer = auth('sanctum')->user();

        $invoices = Invoice::with(['documents', 'case', 'client', 'salary'])
            ->where('lawyer_id', $lawyer->id)
            ->get();

        $formatFiles = function ($documents) {
            return $documents->map(function ($doc) {
                return [
                    'file_url' => $doc->file_path,
                    'file_name' => $doc->type,
                    'file_size' => '1 MB'
                ];
            });
        };

        // حساب مجاميع غير المدفوع قبل التجميع
        $unpaidInvoices = $invoices->where('status', '!=', 'paid');

        $totalUnpaidInput  = (float) $unpaidInvoices
            ->where('type', 'For_case')
            ->sum('total_amount');

        $totalUnpaidOutput = (float) $unpaidInvoices
            ->where('type', 'salary')
            ->sum('total_amount');

        $groupedInvoices = $invoices->groupBy('type')->map(function ($typeInvoices, $type) use ($formatFiles) {
            return $typeInvoices->groupBy('status')->map(function ($statusInvoices) use ($type, $formatFiles) {
                return $statusInvoices->map(function ($invoice) use ($type, $formatFiles) {
                    $files = $formatFiles($invoice->documents);

                    $baseData = [
                        'id' => $invoice->id,
                        'total_amount' => $invoice->total_amount,
                        'due_date' => $invoice->due_date,
                        'status' => $invoice->status,
                        'created_at' => $invoice->created_at->format('Y-m-d H'),
                        'files' => $files,
                    ];

                    if ($type === 'For_case') {
                        $baseData['case_title'] = $invoice->case->title ?? null;
                        $baseData['client_name'] = $invoice->client->full_name ?? null;
                        $baseData['items'] = json_decode($invoice->items, true);
                    }

                    if ($type === 'salary' && $invoice->salary) {
                        $baseData['salary_details'] = [
                            "lawyer_name" => $invoice->lawyer->full_name,
                            'month' => $invoice->salary->month,
                            'year' => $invoice->salary->year,
                            'base_salary' => $invoice->salary->base_salary,
                            'total_approved_expenses' => $invoice->salary->total_approved_expenses,
                            'net_salary' => $invoice->salary->net_salary,
                        ];
                    }

                    return $baseData;
                });
            });
        })->toArray();

        return [
            'status' => 'success',
            'data' => [
                'message' => 'Invoices retrieved successfully',
                'summary' => [
                    'total_unpaid_input'  => $totalUnpaidInput,
                    'total_unpaid_output' => $totalUnpaidOutput,
                ],
                'invoices' => $groupedInvoices
            ],
            'code' => '200'
        ];
    }
}
