<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('queue:work')->withoutOverlapping()->runInBackground();
Schedule::command('reverb:start')->withoutOverlapping()->runInBackground();
Schedule::command('mqtt:subscribe')->withoutOverlapping()->runInBackground();
