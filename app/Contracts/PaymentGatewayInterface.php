<?php

namespace App\Contracts;

use App\Models\Invoice;

interface PaymentGatewayInterface
{
    // إنشاء رابط الدفع وإرجاع الرابط
    public function createCheckoutSession(Invoice $invoice, string $successUrl, string $cancelUrl): string;

    // فحص صحة الـ Webhook (يختلف من بوابة لأخرى)
    public function verifyWebhookSignature($payload, $sigHeader): object;
}
