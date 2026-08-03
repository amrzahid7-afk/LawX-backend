<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\PrivateChannel;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    protected $invoice;
    protected $customMessage;

    public function __construct($invoice, $customMessage = 'تمت عملية دفع بنجاح.')
    {
        $this->invoice = $invoice;
        $this->customMessage = $customMessage;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'type' => $this->invoice->type,
            'message' => $this->customMessage,
        ];
    }
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'invoice_id' => $this->invoice->id,
            'type' => $this->invoice->type,
            'message' => $this->customMessage,
            'amount' => $this->invoice->total_amount,
        ]);
    }
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->invoice->lawyer_id),
        ];
    }
}
