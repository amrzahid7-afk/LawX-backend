<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInvoiceRequest;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    public function create_invoice(CreateInvoiceRequest $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validated();
            // Call the service to create the invoice
            $response = $this->invoiceService->createInvoice($validatedData);
            // return the response    
            return response()->json([
                'status' => $response['status'],
                'data' =>  $response['data']
            ], $response['code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create invoice',
                'Message' => $e->getMessage(),
                'Line' => $e->getLine(),
                'File' => $e->getFile(),
            ], 500);
        }
    }
    public function get_invoices_for_client(Request $request)
    {
        try {
            $invoices = $this->invoiceService->get_invoices_for_client($request);
            return response()->json([
                'status' => 'success',
                'data' => $invoices
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve invoices',
                'Message' => $e->getMessage(),
                'Line' => $e->getLine(),
                'File' => $e->getFile(),
            ], 500);
        }
    }
}
