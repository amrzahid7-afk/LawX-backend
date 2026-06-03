<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // 1. استدعاء الـ Interface الخاص بالبث
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel; // استدعاء القنوات الخاصة


class Create_Invoice_Notification extends Notification implements ShouldBroadcast // 2. تطبيق الـ Interface هنا
{
    use Queueable;

    protected $invoice_id;
    protected $client_id;


    public function __construct($invoice_id, $client_id)
    {
        $this->invoice_id = $invoice_id;
        $this->client_id = $client_id;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice_id,
            'message' => 'You have received a new invoice. Please check it out.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'invoice_id' => $this->invoice_id,
            'message' => 'You have received a new invoice. Please check it out.',
        ]);
    }
    /**
     * 3. تحديد القناة الخاصة ببث الإشعار (مهم جداً للـ UUID)
     */
    public function broadcastOn(): array
    {
        // هنا نربط الإشعار بمعرف المستخدم المستهدف المستلم للطلب (المحامي)
        return [
            new PrivateChannel('App.Models.User.' . $this->client_id),
        ];
    }
}
