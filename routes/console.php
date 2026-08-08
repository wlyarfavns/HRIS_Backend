<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('attendance:recap-daily')->dailyAt('23:59');

// Task 33: Schedule Auto Carry-forward Cuti setiap awal tahun
Schedule::command('leave:carry-forward')->yearly();
