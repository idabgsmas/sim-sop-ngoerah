<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sop:run-scheduler')
    ->dailyAt('01:00') // Jalan setiap hari jam 01:00 Pagi
    ->timezone('Asia/Makassar'); // Sesuaikan timezone (WITA)
