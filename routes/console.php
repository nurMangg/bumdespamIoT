<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();



Schedule::command('app:delete-old-tagihan-files')->dailyAt('01:00');
Schedule::command('app:delete-old-pelanggan-files')->dailyAt('01:00');
