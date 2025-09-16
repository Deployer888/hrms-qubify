<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\ProbationEndingSoon;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Helper;

class UpdateProbationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:update-probation-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check employee probation period and update is_probation field if probation has ended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now();
        $employees = Employee::where('is_probation', 1)->where('is_active', 1)->get();
        
        foreach ($employees as $employee) {
            $probationEndDate = Carbon::parse($employee->company_doj)->addMonths(3);
            if ($today->greaterThanOrEqualTo($probationEndDate)) {
                $employee->is_probation = 0;
                $employee->save();
                
                /*$fcmToken = $employee->employee->user->fcm_token;
                $notificationData = [
                    'title' => 'Probation Period is Over',
                    'body' => "Ahoy, matey! Yer probation period is over—welcome aboard as a full-fledged crew member!",
                    'fcm_token' => $fcmToken,
                ];
                try {
                    Helper::sendNotification($notificationData); // Call the helper function
                } catch (\Exception $e) {
                    \Log::error("Notification Error: " . $e->getMessage());
                }*/
                
            }
            else{
                if ($probationEndDate->diffInDays($today) == 7) {
                    // Send email to HR
                    Mail::to('hr@qubifytech.com')->send(new ProbationEndingSoon($employee));
                    // Mail::to('abhishek@qubifytech.com')->send(new ProbationEndingSoon($employee));
                    
                    /*$fcmToken = User::where('type', 'hr')->first()->fcm_token;
                    $notificationData = [
                        'title' => 'Probation Period is Over',
                        'body' => "Ahoy, matey! Yer probation period is over—welcome aboard as a full-fledged crew member!",
                        'fcm_token' => $fcmToken,
                    ];
                    try {
                        Helper::sendNotification($notificationData); // Call the helper function
                    } catch (\Exception $e) {
                        \Log::error("Notification Error: " . $e->getMessage());
                    }*/
                }
            }
        }

        $this->info('Employee probation status updated successfully.');
    }
}
