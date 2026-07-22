<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\PrivateChannel;

class DocumentFullySignedNotification extends Notification
{
    use Queueable;
    protected $document;
    protected $user_id;


    public function __construct($document, $user_id)
    {
        $this->document = $document;
        $this->user_id = $user_id;
    }
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'document_title' => $this->document->type,
            'document_id' => $this->document->id,
            'message' => 'The Document that You Make Request To Sign Is Fully Signed Please Check It Out.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'document_title' => $this->document->type,
            'document_id' => $this->document->id,
            'message' => 'The Document that You Make Request To Sign Is Fully Signed Please Check It Out.',
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->user_id),
        ];
    }
}
