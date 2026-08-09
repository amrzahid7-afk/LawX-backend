<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\CaseModel;

class CaseClosedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $case;

    public function __construct(CaseModel $case)
    {
        $this->case = $case;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'case_id'    => $this->case->id,
            'case_title' => $this->case->title,
            'message'    => 'تم إغلاق قضيتك بنجاح، يرجى تقييم المحامي.',
            'type'       => 'case_closed'
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'تم إغلاك قضيتك',
            'body'  => 'يرجى تقييم تجربتك مع المحامي في قضية: ' . $this->case->title,

            'data'  => [
                'type'      => 'REVIEW_PROMPT',
                'case_id'   => $this->case->id,
                'lawyer_id' => $this->case->lead_lawyer_id,
            ]
        ]);
    }
}
