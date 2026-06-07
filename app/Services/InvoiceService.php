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
    // This service will handle all the business logic related to invoices, such as creating, updating, and generating PDF invoices.
    // public function createInvoice($data)
    // {
    //     DB::beginTransaction();
    //     $case = CaseModel::with(['clients', 'lead_lawyer'])->find($data['case_id']);
    //     $invoice = Invoice::create([
    //         'case_id' => $data['case_id'],
    //         'lawyer_id' => $case->lead_lawyer->id,
    //         'client_id' => $case->clients->map(fn($client) => $client->where('is_primary', 1)->first())->first()->id, // Assuming the invoice is for the first client of the case
    //         'total_amount' => $data['total_amount'],
    //         'due_date' => $data['due_date'],
    //         'items' => json_encode($data['items']),
    //     ]);
    //     DB::commit();
    //     // send notify for client
    //     $client = $invoice->client;
    //     $client->notify(new Create_Invoice_Notification($invoice->id, $client->id));
    //     // sending response 
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'Invoice created successfully',
    //             'invoice_ID' => $invoice->id
    //         ],
    //         'code' => '201'
    //     ];
    // }
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
            'items'       => json_encode($data['items']),
        ]);
        if (isset($data['documents']) && is_array($data['documents'])) {
            foreach ($data['documents'] as $file) {
                $path = $file->store('invoices', 'public');
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
    // public function get_invoices_for_client($request)
    // {
    //     $client_id = $request->user();
    //     $invoices = $client_id->client_invoices()->get();
    //     $formattedInvoices = $invoices->map(function ($invoice) {
    //         return [
    //             'id' => $invoice->id,
    //             'case_title' => $invoice->case->title ?? null,
    //             'lawer_name' => $invoice->lawyer->full_name ?? null,
    //             'total_amount' => $invoice->total_amount,
    //             'due_date' => $invoice->due_date,
    //             'items' => json_decode($invoice->items, true),
    //             'status' => $invoice->status,
    //             'created_at' => $invoice->created_at->format('Y-m-d H'),
    //         ];
    //     });
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'Invoices retrieved successfully',
    //             'invoices' => $formattedInvoices
    //         ],
    //         'code' => '200'
    //     ];
    // }
    public function get_invoices_for_client($request)
    {
        $client_id = $request->user();
        $invoices = $client_id->client_invoices()
            ->with(['documents', 'case', 'lawyer'])
            ->get();
        $formattedInvoices = $invoices->map(function ($invoice) {
            $files = $invoice->documents->map(function ($doc) {
                return [
                    'file_url' => Storage::url($doc->file_path),
                    'file_name' => $doc->type,
                    'file_size' => Storage::size($doc->file_path)
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
}
