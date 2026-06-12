<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // 1. استدعاء الـ Interface الخاص بالبث
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel; // استدعاء القنوات الخاصة


class Add_Session_Notification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $session_id;
    public $client_id;

    public function __construct($session_id, $client_id)
    {
        $this->session_id = $session_id;
        $this->client_id = $client_id;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'session_id' => $this->session_id,
            'message' => 'You have A new Seesion',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'session_id' => $this->session_id,
            'message' => 'You have A new Seesion',
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
