<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;

class Add_Task_Notification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $Task_id;
    public $user_id;

    public function __construct($Task_id, $user_id)
    {
        $this->Task_id = $Task_id;
        $this->user_id = $user_id;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->Task_id,
            'message' => 'You have A new task',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'task_id' => $this->Task_id,
            'message' => 'You have A new task',
        ]);
    }
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->user_id),
        ];
    }
}
