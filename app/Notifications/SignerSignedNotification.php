<?php

namespace App\Notifications;

use App\Models\SignatureRequest;
use App\Models\Signer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // 1. استدعاء الـ Interface الخاص بالبث
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\PrivateChannel; // استدعاء القنوات الخاصة

class SignerSignedNotification extends Notification
{
    use Queueable;
    private SignatureRequest $signatureRequest;
    private Signer $signer;
    public function __construct($signatureRequest, $signer)
    {
        $this->signatureRequest = $signatureRequest;
        $this->signer = $signer;
    }



    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->signatureRequest->id,
            'title' =>  "New Signature Notification",
            'message' => 'User : ' . $this->signer->user->full_name . ' has signed the document: ' . $this->signatureRequest->document->title,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'request_id' => $this->signatureRequest->id,
            'title' =>  "New Signature Notification",
            'message' => 'User : ' . $this->signer->user->full_name . ' has signed the document: ' . $this->signatureRequest->document->title,
        ]);
    }

    /**
     * 3. تحديد القناة الخاصة ببث الإشعار (مهم جداً للـ UUID)
     */
    public function broadcastOn(): array
    {
        // هنا نربط الإشعار بمعرف المستخدم المستهدف المستلم للطلب (المحامي)
        return [
            new PrivateChannel('App.Models.User.' . $this->signatureRequest->lawyer->id),
        ];
    }
}
