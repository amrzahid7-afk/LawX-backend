<?php

namespace App\Notifications;

use App\Models\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SessionReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تذكير بجلسة اليوم',
            'body' => "لديك جلسة اليوم في القضية: {$this->session->case->title} الساعة: {$this->session->date->format('H:i')}",
            'session_id' => $this->session->id,
            'case_id' => $this->session->case_id,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'تذكير بجلسة اليوم',
            'body' => "لديك جلسة اليوم في القضية: {$this->session->case->title} الساعة: {$this->session->date->format('H:i')}",
            'session_id' => $this->session->id,
            'case_id' => $this->session->case_id,
        ]);
    }
}
