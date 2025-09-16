<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('employee:update-clockout')->timezone('Asia/Kolkata')->dailyAt('23:59');
        $schedule->command('event:daily-check')->timezone('Asia/Kolkata')->dailyAt('10:00');
        // Monthly leave accrual processing - runs on last day of month at 23:55
        $schedule->command('leaves:monthly-accrual')
            ->timezone('Asia/Kolkata')
            ->cron('55 23 L * *') // Last day of month at 23:55
            ->withoutOverlapping(30); // Prevent overlapping with 30-minute timeout
        $schedule->command('queue:work --stop-when-empty')->timezone('Asia/Kolkata')->everyMinute();
        $schedule->command('check:employee-birthdays')->timezone('Asia/Kolkata')->daily();
        $schedule->command('auto:clockout')->timezone('Asia/Kolkata')->dailyAt('13:45');
        $schedule->command('employee:update-probation-status')->timezone('Asia/Kolkata')->dailyAt('01:00');
        $schedule->command('app:update-attendance')->timezone('Asia/Kolkata')->everyMinute();
        $schedule->command('employees:deactivate')->timezone('Asia/Kolkata')->monthlyOn(10, '00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
