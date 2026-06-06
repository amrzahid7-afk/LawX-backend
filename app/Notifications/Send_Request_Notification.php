<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // 1. استدعاء الـ Interface الخاص بالبث
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel; // استدعاء القنوات الخاصة

class Send_Request_Notification extends Notification implements ShouldBroadcast // 2. تطبيق الـ Interface هنا
{
    use Queueable;

    protected $requestData;

    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->requestData['id'],
            'title' => $this->requestData['title_request'],
            'message' => 'New request: ' . $this->requestData['title_request'],
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'request_id' => $this->requestData['id'],
            'title' => $this->requestData['title_request'],
            'message' => 'New request: ' . $this->requestData['title_request'],
        ]);
    }

    /**
     * 3. تحديد القناة الخاصة ببث الإشعار (مهم جداً للـ UUID)
     */
    public function broadcastOn(): array
    {
        // هنا نربط الإشعار بمعرف المستخدم المستهدف المستلم للطلب (المحامي)
        return [
            new PrivateChannel('App.Models.User.' . $this->requestData['assigned_lawyer_id']),
        ];
    }
}
