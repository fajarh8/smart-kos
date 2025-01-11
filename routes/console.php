<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('queue:work')->everyTenSeconds()->runInBackground()->withoutOverlapping();
Schedule::command('reverb:start')->everyTenSeconds()->runInBackground()->withoutOverlapping();
Schedule::command('mqtt:subscribe')->everyTenSeconds()->runInBackground()->withoutOverlapping();
