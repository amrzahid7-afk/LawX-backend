<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Invoice;
use Stripe\StripeClient;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripePaymentService implements PaymentGatewayInterface
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    // public function createCheckoutSession(Invoice $invoice, string $successUrl, string $cancelUrl): string
    // {
    //     $amountInCents = (int) ($invoice->total_amount * 100);

    //     // بناء وصف آمن يعمل في جميع الحالات
    //     $description = "Invoice Payment #" . substr($invoice->id, 0, 8);

    //     if ($invoice->type === 'payroll' && $invoice->lawyer) {
    //         $description .= " - Payroll for " . ($invoice->lawyer->full_name ?? $invoice->lawyer->name);
    //     } elseif ($invoice->case) {
    //         $description .= " - Case: " . ($invoice->case->title ?? 'N/A');
    //     }

    //     try {
    //         $session = $this->stripe->checkout->sessions->create([
    //             'payment_method_types' => ['card'],
    //             'line_items' => [
    //                 [
    //                     'price_data' => [
    //                         'currency' => 'usd',
    //                         'product_data' => [
    //                             'name' => 'Invoice #' . substr($invoice->id, 0, 8),
    //                             'description' => $description,
    //                         ],
    //                         'unit_amount' => $amountInCents,
    //                     ],
    //                     'quantity' => 1,
    //                 ],
    //             ],
    //             'mode' => 'payment',
    //             'success_url' => $successUrl,
    //             'cancel_url' => $cancelUrl,
    //             'metadata' => [
    //                 'invoice_id' => $invoice->id,
    //             ],
    //         ]);

    //         $invoice->update([
    //             'stripe_checkout_session_id' => $session->id,
    //             'payment_gateway' => 'stripe'
    //         ]);

    //         return $session->url;
    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //         // هذا السطر سيطبع السبب الحقيقي والدقيق من سترايب في ملف اللوج
    //         \Log::error('Stripe API Error: ' . $e->getMessage());
    //         throw new \Exception($e->getMessage()); // نرمي الخطأ للـ Controller ليظهر للمستخدم
    //     }
    // }
    // public function createCheckoutSession(Invoice $invoice, string $successUrl, string $cancelUrl): string
    // {
    //     // 1. قراءة العناصر من قاعدة البيانات (JSON)
    //     $invoiceItems = json_decode($invoice->items, true) ?? [];

    //     // 2. تحضير مصفوفة العناصر لسترايب
    //     $lineItems = [];

    //     // إذا كانت الفاتورة تحتوي على عناصر تفصيلية
    //     if (count($invoiceItems) > 0) {
    //         foreach ($invoiceItems as $item) {
    //             $lineItems[] = [
    //                 'price_data' => [
    //                     'currency' => 'usd',
    //                     'product_data' => [
    //                         'name' => $item['name'] ?? 'Service Fee', // اسم البند
    //                         'description' => $item['description'] ?? null, // وصف إضافي إن وجد
    //                     ],
    //                     // تحويل المبلغ الفرعي لكينتات (مضروباً في 100)
    //                     'unit_amount' => (int) (($item['price'] ?? $item['amount'] ?? 0) * 100),
    //                 ],
    //                 'quantity' => $item['quantity'] ?? 1, // الكمية (تكون 1 غالباً في الفواتير القانونية)
    //             ];
    //         }
    //     } else {
    //         // إذا لم تكن هناك عناصر، نرسل المبلغ الإجمالي كبند واحد (الفور السابقة)
    //         $lineItems[] = [
    //             'price_data' => [
    //                 'currency' => 'usd',
    //                 'product_data' => [
    //                     'name' => 'Invoice #' . substr($invoice->id, 0, 8),
    //                     'description' => "Total payment for invoice",
    //                 ],
    //                 'unit_amount' => (int) ($invoice->total_amount * 100),
    //             ],
    //             'quantity' => 1,
    //         ];
    //     }

    //     // 3. إعداد وصف الفاتورة بناءً على نوعها
    //     $description = $invoice->type === 'payroll'
    //         ? "Payroll Payment - Lawyer: " . ($invoice->lawyer->name ?? 'N/A')
    //         : "Service Invoice - Case: " . ($invoice->case->title ?? 'N/A');

    //     try {
    //         $session = $this->stripe->checkout->sessions->create([
    //             'payment_method_types' => ['card'],
    //             'line_items' => $lineItems, // إرسال العناصر التفصيلية هنا
    //             'mode' => 'payment',
    //             'success_url' => $successUrl,
    //             'cancel_url' => $cancelUrl,
    //             'metadata' => [
    //                 'invoice_id' => $invoice->id,
    //             ],
    //             // إضافة بيانات الفاتورة في أعلى الصفحة
    //             'custom_text' => [
    //                 'submit' => [
    //                     'message' => "Invoice ID: " . substr($invoice->id, 0, 8) . " | Due Date: " . $invoice->due_date,
    //                 ]
    //             ]
    //         ]);

    //         $invoice->update([
    //             'stripe_checkout_session_id' => $session->id,
    //             'payment_gateway' => 'stripe'
    //         ]);

    //         return $session->url;
    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //         \Log::error('Stripe API Error: ' . $e->getMessage());
    //         throw new \Exception($e->getMessage());
    //     }
    // }
    public function createCheckoutSession(Invoice $invoice, string $successUrl, string $cancelUrl): string
    {
        // 1. قراءة العناصر من قاعدة البيانات (JSON)
        $invoiceItems = json_decode($invoice->items, true) ?? [];

        // 2. تحضير مصفوفة العناصر لسترايب
        $lineItems = [];

        // إذا كانت الفاتورة تحتوي على عناصر تفصيلية
        if (count($invoiceItems) > 0) {
            foreach ($invoiceItems as $item) {

                // تجهيز بيانات المنتج
                $productData = [
                    'name' => $item['name'] ?? 'Service Fee',
                ];

                // نرسل الوصف فقط إذا كان موجوداً وغير فارغ (لأن سترايب ترفض القيم الفارغة)
                if (!empty($item['description'])) {
                    $productData['description'] = $item['description'];
                }

                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => $productData,
                        // تحويل المبلغ الفرعي لكينتات (مضروباً في 100)
                        'unit_amount' => (int) (($item['price'] ?? $item['amount'] ?? 0) * 100),
                    ],
                    'quantity' => $item['quantity'] ?? 1,
                ];
            }
        } else {
            // إذا لم تكن هناك عناصر، نرسل المبلغ الإجمالي كبند واحد
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Invoice #' . substr($invoice->id, 0, 8),
                        'description' => "Total payment for invoice",
                    ],
                    'unit_amount' => (int) ($invoice->total_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        try {
            $session = $this->stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems, // إرسال العناصر التفصيلية هنا
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                ],
                // إضافة بيانات الفاتورة في أعلى الصفحة
                'custom_text' => [
                    'submit' => [
                        'message' => "Invoice ID: " . substr($invoice->id, 0, 8) . " | Due Date: " . $invoice->due_date,
                    ]
                ]
            ]);

            $invoice->update([
                'stripe_checkout_session_id' => $session->id,
                'payment_gateway' => 'stripe'
            ]);

            return $session->url;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
    public function verifyWebhookSignature($payload, $sigHeader): object
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            return Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            throw new \Exception('Invalid Stripe Webhook Signature');
        }
    }
}
