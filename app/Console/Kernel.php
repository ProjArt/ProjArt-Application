<?php

namespace App\Console;

use App\Http\Services\GapsAbsencesService;
use App\Http\Services\GapsEventsService;
use App\Http\Services\GapsMarksService;
use App\Http\Services\GapsMenuService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->call(function () {
            GapsEventsService::fetchAllHoraires();
            GapsMarksService::fetchAllNotes();
            GapsAbsencesService::fetchAllAbsences();
        })->dailyAt('02:00');

        $schedule->call(function () {
            GapsMenuService::fetchMenus();
        })->dailyAt('11:02');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
