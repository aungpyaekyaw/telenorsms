<?php

namespace TelenorSMS\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        parent::schedule($schedule);
        $schedule->command('telenorbulksms:auth')->hourly();
    }

}