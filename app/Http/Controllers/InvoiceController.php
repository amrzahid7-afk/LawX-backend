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
            // 1. اقرأ روابط الرجوع من الفرونت مع تحقّق أساسي
            $validated = $request->validate([
                'success_url' => ['required', 'string'],
                'cancel_url'  => ['required', 'string'],
            ]);

            // // 2. فحص whitelist لمنع Open Redirect
            // if (
            //     !$this->isAllowedReturnUrl($validated['success_url']) ||
            //     !$this->isAllowedReturnUrl($validated['cancel_url'])
            // ) {
            //     return sendError('Invalid return URL', [], 422);
            // }

            $invoice = Invoice::with(['case', 'lawyer', 'client'])->findOrFail($invoice_id);

            if (Gate::denies('pay_invoice', $invoice)) {
                return sendError('Unauthorized, you cannot pay this invoice', [], 403);
            }
            if ($invoice->status === 'paid') {
                return sendError('This invoice is already paid', [], 400);
            }

            // 3. مرّر روابط الفرونت مباشرة لـ Stripe
            $checkoutUrl = $this->paymentGateway->createCheckoutSession(
                $invoice,
                $validated['success_url'],
                $validated['cancel_url']
            );

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

    private function isAllowedReturnUrl(string $url): bool
    {
        $allowedWebHosts = [
            'app.lawx.com',
            'localhost',
            '127.0.0.1',
        ];

        $allowedMobileSchemes = [
            'lawx',   // Deep Link تطبيق الموبايل: lawx://payment/success
        ];

        $parts = parse_url($url);

        if (!$parts || !isset($parts['scheme'])) {
            return false;
        }

        if (in_array($parts['scheme'], $allowedMobileSchemes, true)) {
            return true;
        }

        if ($parts['scheme'] === 'https' && isset($parts['host'])) {
            return in_array($parts['host'], $allowedWebHosts, true);
        }

        if (
            $parts['scheme'] === 'http' && isset($parts['host'])
            && in_array($parts['host'], ['localhost', '127.0.0.1'], true)
        ) {
            return true;
        }

        return false;
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
