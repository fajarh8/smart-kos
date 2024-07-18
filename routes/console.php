<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('serve')->everyTenSeconds()->withoutOverlapping()->runInBackground();
Schedule::exec('npm run dev')->everyTenSeconds()->withoutOverlapping()->runInBackground();
Schedule::command('queue:work')->everyTenSeconds()->withoutOverlapping()->runInBackground();
Schedule::command('reverb:start')->everyTenSeconds()->withoutOverlapping()->runInBackground();
Schedule::command('mqtt:subscribe')->everyTenSeconds()->withoutOverlapping()->runInBackground();
// Schedule::command('schedule:work')->everySecond()->withoutOverlapping()->runInBackground();
