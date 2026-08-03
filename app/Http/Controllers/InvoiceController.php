<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Http\Requests\CreateInvoiceRequest;
use App\Models\CaseModel;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Traits\PaymentWebhookActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class InvoiceController extends Controller
{
    use PaymentWebhookActions;
    protected InvoiceService $invoiceService;
    protected PaymentGatewayInterface $paymentGateway;
    public function __construct(InvoiceService $invoiceService, PaymentGatewayInterface $paymentGateway)
    {
        $this->invoiceService = $invoiceService;
        $this->paymentGateway = $paymentGateway;
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
    public function get_invoices_for_lawer(Request $request)
    {
        try {
            // validate permission
            // return true always 
            if (Gate::denies('get_invoices_for_lawer', Invoice::class)) {
                return sendError("Unauthorized , You do not have permission to view invoices ", [], 403);
            }
            // call servcie
            $invoices = $this->invoiceService->get_invoices_for_lawer($request);
            // send response
            return sendResponse($invoices['data'], $invoices['status'], $invoices['code']);
        } catch (\Exception $e) {
            return sendError('Failed to retrieve invoices : ' . $e->getMessage(), [], 500);
        }
    }
    public function initiate_payment(Request $request, $invoice_id)
    {
        try {
            $invoice = Invoice::with(['case', 'lawyer', 'client'])->findOrFail($invoice_id);

            // التحقق من الصلاحيات بناءً على نوع الفاتورة
            // إذا كانت فاتورة خدمة: يجب أن يكون العميل هو من يطلب الدفع
            // إذا كانت فاتورة راتب: يجب أن يكون مدير المكتب هو من يطلب الصرف
            if (Gate::denies('pay_invoice', $invoice)) {
                return sendError('Unauthorized, you cannot pay this invoice', [], 403);
            }
            if ($invoice->status === 'paid') {
                return sendError('This invoice is already paid', [], 400);
            }

            // روابط وهمية只是为了满足 Stripe 的要求
            $successUrl = URL::route('payment.success', ['invoice' => $invoice->id]);
            $cancelUrl = URL::route('payment.cancel', ['invoice' => $invoice->id]);

            // استدعاء خدمة الدفع لإنشاء رابط سترايب
            $checkoutUrl = $this->paymentGateway->createCheckoutSession($invoice, $successUrl, $cancelUrl);

            return sendResponse([
                'message' => 'Checkout session created successfully',
                'checkout_url' => $checkoutUrl
            ], 'success', 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initiate payment',
                'Message' => $e->getMessage(),
                'Line' => $e->getLine(),
            ], 500);
        }
    }
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = $this->paymentGateway->verifyWebhookSignature($payload, $sigHeader);
        } catch (\Exception $e) {
            \Log::error('Webhook Signature Verification Failed: ' . $e->getMessage());
            return response('Invalid Signature', 400);
        }

        // استدعاء الدوال كأنها كُتبت داخل هذا الكلاس
        switch ($event->type) {
            case 'checkout.session.completed':
            case 'checkout.session.async_payment_succeeded':
                $this->handleSuccessfulPayment($event->data->object);
                break;

            case 'checkout.session.async_payment_failed':
            case 'checkout.session.expired':
                $this->handleFailedPayment($event->data->object, $event->type);
                break;
        }

        return response('Webhook Handled Successfully', 200);
    }
}
