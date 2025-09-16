<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;
use App\Mail\ProbationEndingSoon;
use App\Mail\EventNotification;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Helper;

class EventNotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:daily-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check daily event if event exist then send mail to every one.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get today's date (no need to define $today twice)
        $today = Carbon::today();
    
        // Get employees on probation and active
        $employees = Employee::where('is_active', 1)->get();
    
        // Query the events for today
        $events = Event::where(function($query) use ($today) {
                $query->where('start_date', '<', $today)
                      ->where('end_date', '>', $today); // Ongoing events
            })
            ->orWhere('start_date', $today)  // Events that start today
            ->orWhere('end_date', $today)    // Events that end today
            ->get();
    
    
    echo "<pre>";
    print_r($employees);
    die;
    
    
        // If events are found, send an email to each employee
        if ($events->isNotEmpty()) {
            foreach ($events as $event) {
                foreach ($employees as $employee) {
                    try {
                        // Send email to the employee
                        // Mail::to($employee->email)->queue(new EventNotification($event));
                        Mail::to('raghubir@qubifytech.com')->queue(new EventNotification($event));

                        break;
                    } catch (\Throwable $th) {
                        // Optionally log the error for debugging or monitoring purposes
                        \Log::error("Failed to send email to employee: {$employee->email}. Error: {$th->getMessage()}");
                        continue; // Continue to the next employee
                    }
                }
            }
        }
    
        $this->info('Event notification sent.');
    }
    
}
