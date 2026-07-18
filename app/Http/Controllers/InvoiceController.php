<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInvoiceRequest;
use App\Models\CaseModel;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
            //validate permission 
            if (Gate::denies('create_invoice', [Invoice::class, CaseModel::find($validatedData['case_id'])])) {
                return sendError('un Authorized , you  dont have permission to create invoice ', [], 403);
            }
            // Call the service to create the invoice
            $response = $this->invoiceService->createInvoice($validatedData);
            // return the response    
            return sendResponse($response['data'], $response['status'], $response['code']);
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
            // validate permission
            if (Gate::denies('get_invoices_for_client', Invoice::class)) {
                return sendError("Unauthorized , You do not have permission to view invoices ", [], 403);
            }
            // call servcie
            $invoices = $this->invoiceService->get_invoices_for_client($request);
            // send response
            return sendResponse($invoices['data'], $invoices['status'], $invoices['code']);
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
