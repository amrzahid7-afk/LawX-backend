<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use App\Notifications\Create_Invoice_Notification;

class InvoiceService
{
    // This service will handle all the business logic related to invoices, such as creating, updating, and generating PDF invoices.
    public function createInvoice($data)
    {
        DB::beginTransaction();
        $invoice = Invoice::create([
            'case_id' => $data['case_id'],
            'lawyer_id' => $data['lawyer_id'],
            'client_id' => $data['client_id'],
            'total_amount' => $data['total_amount'],
            'due_date' => $data['due_date'],
            'items' => json_encode($data['items']),
        ]);
        DB::commit();
        // send notify for client
        $client = $invoice->client;
        $client->notify(new Create_Invoice_Notification($invoice->id, $client->id));
        // sending response 
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
        $invoices = $client_id->client_invoices()->get();
        $formattedInvoices = $invoices->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'case_title' => $invoice->case->title ?? null,
                'lawer_name' => $invoice->lawyer->full_name ?? null,
                'total_amount' => $invoice->total_amount,
                'due_date' => $invoice->due_date,
                'items' => json_decode($invoice->items, true),
                'status' => $invoice->status,
                'created_at' => $invoice->created_at->format('Y-m-d H'),
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
