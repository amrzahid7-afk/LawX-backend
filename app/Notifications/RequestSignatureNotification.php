<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\PrivateChannel;

class RequestSignatureNotification extends Notification
{
    use Queueable;
    protected $request_signature_id;
    protected $Document_id;
    protected $user_id;


    public function __construct($request_signature_id, $Document_id, $user_id)
    {
        $this->request_signature_id = $request_signature_id;
        $this->Document_id = $Document_id;
        $this->user_id = $user_id;
    }
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'request_signature_id' => $this->request_signature_id,
            'Document_id' => $this->Document_id,
            'message' => 'You have received a new Document To sign It Please check it out.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'request_signature_id' => $this->request_signature_id,
            'Document_id' => $this->Document_id,
            'message' => 'You have received a new Document To sign It Please check it out.',
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->user_id),
        ];
    }
}
