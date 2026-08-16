<?php


use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\CleanExpiredData;

// clean expired data
Schedule::command('clean:expired-data')
    ->dailyAt('03:00')
    ->description('حذف الرموز المنتهية الصلاحية من قاعدة البيانات');
// get laws updates from site syria-law.com
Schedule::command('syria-law:sync')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->runInBackground();
// remind lawers for sessions evry day 
Schedule::command('sessions:send-daily-reminders')->dailyAt('07:30');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
