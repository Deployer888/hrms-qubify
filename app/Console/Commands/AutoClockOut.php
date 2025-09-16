<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\AttendanceEmployee;
use App\Models\Holiday;
use App\Models\Employee;
use App\Mail\ClockOutNotification;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Helper;

class AutoClockOut extends Command
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:clockout';
    // protected $signature = 'app:auto-clock-out';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically clock out employees at 1:45 PM daily, except weekends and holidays';
    // protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current day
        $today = Carbon::today();

        // Check if today is a weekend or holiday
        if ($today->isWeekend() || Holiday::where('date', $today->toDateString())->exists()) {
            $this->info("Today is a weekend or holiday. No action taken.");
            return;
        }

        // Get employees who need to be clocked out
        $employees = AttendanceEmployee::where('clock_out', '00:00:00')
                             ->whereDate('date', $today)
                            //  ->where('employee_id', 2)
                             ->get();

        foreach ($employees as $employee) {
            /* if($employee->employee_id == 3 || $employee->employee_id == 6){
                continue;
            } */
            $employee->clock_out = Carbon::createFromTime(13, 45, 0); // Set clock out to 1:45 PM
            $employee->save();
            
            $fcmToken = $employee->employee->user->fcm_token;
            $notificationData = [
                'title' => 'Auto-Clockout Notification',
                'body' => "Ahoy, matey! Ye’ve been auto-clocked out fer yer break—be sure to return afore the ship sails!",
                'fcm_token' => $fcmToken,
            ];
            try {
                Helper::sendNotification($notificationData); // Call the helper function
            } catch (\Exception $e) {
                \Log::error("Notification Error: " . $e->getMessage());
            }
            
            try {
                Mail::to($employee->employee->email)->send(new ClockOutNotification());
            } catch (\Exception $e) {
                \Log::error("Email Sending Error: " . $e->getMessage());
            }
        }

        $this->info("Employees have been clocked out at 1:45 PM successfully.");
    }
}
