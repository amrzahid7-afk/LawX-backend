<?php


use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\CleanExpiredData;

// clean expired data
Schedule::command('clean:expired-data')
    ->dailyAt('03:00')
    ->description('حذف الرموز المنتهية الصلاحية من قاعدة البيانات');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
