<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('crowdfunding:finish-due')->everyMinute();
// 每天凌晨计算一次分期逾期费
Schedule::command('cron:calculate-installment-fine')->daily();
