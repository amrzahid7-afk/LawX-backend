<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\PaymentGatewayInterface;
use App\Services\Payment\StripePaymentService;
// use App\Services\Payment\ShamCashPaymentService; // مستقبلاً

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register: هون بنسجل الربط في الـ Container
     */
    public function register(): void
    {
        // $this->app->bind(PaymentGatewayInterface::class, function ($app) {
        //     $gateway = config('payment.default');
        //     return match ($gateway) {
        //         'stripe' => new StripePaymentService(),
        //         // 'shamcash' => new ShamCashPaymentService(),
        //         default => throw new \InvalidArgumentException(
        //             "بوابة الدفع [{$gateway}] غير مدعومة"
        //         ),
        //     };
        // });
    }
    public function boot(): void
    {
        //
    }
}
