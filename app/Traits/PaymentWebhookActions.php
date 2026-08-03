<?php

namespace App\Traits;

use App\Models\Invoice;
use App\Models\Payment;
use App\Notifications\PaymentReceivedNotification;
use Illuminate\Support\Facades\DB;

trait PaymentWebhookActions
{

    // private function handleSuccessfulPayment($session)
    // {
    //     $invoice = Invoice::where('stripe_checkout_session_id', $session->id)->first();

    //     if (!$invoice || $invoice->status === 'paid') {
    //         \Log::info('Webhook: Invoice already paid or not found.');
    //         return;
    //     }
    //     try {
    //         DB::transaction(function () use ($invoice, $session) {
    //             $invoice->update(['status' => 'paid']);
    //             if ($invoice->salary) {
    //                 $invoice->salary->update(['status' => 'paid']);
    //             }
    //             Payment::create([
    //                 'invoice_id' => $invoice->id,
    //                 'transaction_id' => $session->payment_intent,
    //                 'gateway' => 'stripe',
    //                 'amount_paid' => $session->amount_total / 100,
    //                 'status' => 'completed',
    //                 'gateway_response' => $session->toArray(),
    //             ]);
    //         });

    //         \Log::info('Webhook: Database updated successfully for Invoice: ' . $invoice->id);
    //     } catch (\Exception $e) {
    //         \Log::error('Webhook: Failed to update database: ' . $e->getMessage());
    //         return; // نتوقف هنا إذا فشل حفظ الدفع
    //     }

    //     // 2. إرسال الإشعار (بعد التأكد بنجاح من حفظ قاعدة البيانات)
    //     try {
    //         if ($invoice->lawyer) {
    //             $invoice->lawyer->notify(new \App\Notifications\PaymentReceivedNotification($invoice, $invoice->lawyer));
    //             \Log::info('Webhook: Notification sent to Lawyer ID: ' . $invoice->lawyer->id);
    //         } else {
    //             \Log::warning('Webhook: Lawyer not found for this invoice!');
    //         }
    //     } catch (\Exception $e) {
    //         \Log::error('Webhook: Failed to send notification: ' . $e->getMessage());
    //     }
    // }
    private function handleSuccessfulPayment($session)
    {
        $invoice = Invoice::where('stripe_checkout_session_id', $session->id)->first();

        if (!$invoice || $invoice->status === 'paid') {
            \Log::info('Webhook: Invoice already paid or not found.');
            return;
        }

        try {
            DB::transaction(function () use ($invoice, $session) {
                $invoice->update(['status' => 'paid']);

                // تحديث حالة سجل الراتب إذا كانت الفاتورة من نوع راتب
                if ($invoice->type === 'salary' && $invoice->salary) {
                    $invoice->salary->update(['status' => 'paid']);
                }

                Payment::create([
                    'invoice_id' => $invoice->id,
                    'transaction_id' => $session->payment_intent,
                    'gateway' => 'stripe',
                    'amount_paid' => $session->amount_total / 100,
                    'status' => 'completed',
                    'gateway_response' => $session->toArray(),
                ]);
            });

            \Log::info('Webhook: Database updated successfully for Invoice: ' . $invoice->id);
        } catch (\Exception $e) {
            \Log::error('Webhook: Failed to update database: ' . $e->getMessage());
            return;
        }

        // 2. إرسال الإشعار (تحديد المستلم بناءً على نوع الفاتورة)
        try {
            $recipient = null;
            $customMessage = '';

            if ($invoice->type === 'salary' && $invoice->salary) {
                // إذا كانت فاتورة راتب: المستلم هو الموظف صاحب الراتب
                $recipient = $invoice->salary->lawyer;
                $customMessage = 'تم صرف راتبك الشهري بنجاح.';
            } elseif ($invoice->type === 'For_case') {
                // إذا كانت فاتورة خدمة: المستلم هو المحامي صاحب الفاتورة
                $recipient = $invoice->lawyer;
                $customMessage = 'لقد استلمت دفعة جديدة على إحدى فواتيرك.';
            }

            if ($recipient) {
                // نمرر الفاتورة والرسالة المخصصة
                $recipient->notify(new \App\Notifications\PaymentReceivedNotification($invoice, $customMessage));
                \Log::info('Webhook: Notification sent to User ID: ' . $recipient->id);
            } else {
                \Log::warning('Webhook: Could not determine notification recipient for Invoice ID: ' . $invoice->id);
            }
        } catch (\Exception $e) {
            \Log::error('Webhook: Failed to send notification: ' . $e->getMessage());
        }
    }
    // دالة معالجة الدفع الفاشل
    private function handleFailedPayment($session, $eventType)
    {
        $invoice = Invoice::where('stripe_checkout_session_id', $session->id)->first();
        if (!$invoice || $invoice->status === 'paid') return;

        Payment::create([
            'invoice_id' => $invoice->id,
            'transaction_id' => $session->payment_intent ?? null,
            'gateway' => 'stripe',
            'amount_paid' => 0,
            'status' => 'failed',
            'gateway_response' => ['event_type' => $eventType, 'session' => $session->toArray()],
        ]);
    }
}
