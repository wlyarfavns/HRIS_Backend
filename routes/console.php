<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rekap absensi harian
Schedule::command('attendance:recap-daily')->dailyAt('23:59');

// Generate jatah cuti baru dan bawa sisa cuti (berjalan tiap 1 Januari jam 00:00)
Schedule::command('leave:carry-forward')->yearly();