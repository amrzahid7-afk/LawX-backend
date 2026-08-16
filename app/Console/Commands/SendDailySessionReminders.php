<?php

namespace App\Console\Commands;

use App\Models\Session;
use App\Notifications\SessionReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendDailySessionReminders extends Command
{
    protected $signature = 'sessions:send-daily-reminders';
    protected $description = 'Send daily reminders to lawyers about today\'s sessions';

    public function handle()
    {
        $this->info('Searching for today\'s sessions...');

        $count = 0;

        Session::whereDate('date', Carbon::today())
            ->with([
                'case.leadLawyer.lawyerProfile',
                'case.team.lawyerProfile',
            ])
            ->chunk(100, function ($sessions) use (&$count) {
                foreach ($sessions as $session) {
                    if (!$session->case) {
                        continue;
                    }

                    $lawyersToNotify = collect();

                    if ($session->case->leadLawyer) {
                        $lawyersToNotify->push($session->case->leadLawyer);
                    }

                    $lawyersToNotify = $lawyersToNotify
                        ->merge($session->case->team)
                        ->filter()
                        ->unique('id')
                        ->reject(fn($lawyer) => $lawyer->lawyerProfile?->is_suspended === true);

                    foreach ($lawyersToNotify as $lawyer) {
                        $lawyer->notify(new SessionReminderNotification($session));
                        $count++;
                    }
                }
            });

        $this->info("Notifications sent to lawyers: {$count}");
        return self::SUCCESS;
    }
}
