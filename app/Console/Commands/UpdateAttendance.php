<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\AttendanceEmployee;
use App\Helpers\Utility;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;

class UpdateAttendance extends Command
{
    protected $signature = 'app:update-attendance';
    protected $description = 'Calls the attendance API for today’s data and saves the response to the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        exit;
        die();
        // $startDate = Carbon::today()->format('d/m/Y') . '_00:00';
        // $endDate = Carbon::today()->format('d/m/Y') . '_23:59';
        
        $currentTime = Carbon::now();
        $breakStart = Carbon::parse('13:45:00');
        $breakEnd = Carbon::parse('14:30:00');
        if (!$currentTime->between($breakStart, $breakEnd)) {
            $endDate = Carbon::now();
            $startDate = $endDate->copy()->subMinutes();
            
            $startDate = $startDate->format('d/m/Y_H:i');
            $endDate = $endDate->format('d/m/Y_H:i');
    
            $apiUrl = "https://api.etimeoffice.com/api/DownloadPunchDataMCID?Empcode=ALL&FromDate={$startDate}&ToDate={$endDate}";
            $authHeader = base64_encode('Qubify:Qubify:Qubify#555$:true');
            // $authHeader = base64_encode('support:support:support@1:true'); //For Testing
    
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . $authHeader,
                ])->get($apiUrl);
    
                if ($response->successful()) {
                    $data = $response->json();
                    $data = array_reverse($response->json());

                    // echo "<pre>";
                    // print_r($data);

                    if(count($data['PunchData']) > 0)
                    {
                        // Assuming $data is an array of attendance records
                        foreach (array_reverse($data['PunchData']) as $key => $record) {
                            $employee = Employee::with('user')->where('empcode', $record['Empcode'])->first(); // Ensure 'user' relationship is loaded
                        
                            if (!$employee) {
                                Log::warning('Employee not found for Empcode: ' . $record['Empcode']);
                                continue;
                            }
                        
                            $punchDate = $record['PunchDate'];
                            try {
                                $punchDateTime = Carbon::createFromFormat('d/m/Y H:i:s', $punchDate);
                            } catch (\Exception $e) {
                                Log::error('Invalid PunchDate format for Empcode: ' . $record['Empcode'], ['PunchDate' => $punchDate]);
                                continue;
                            }
                        
                            $date = $punchDateTime->format('Y-m-d');
                            $time = $punchDateTime->format('H:i:s');
                            $isExist = Helper::checkCurrentTimeAttendace($employee->id, $date, $time);
                        
                            if ($record['mcid'] == 2 && !$isExist) { // Clock-in logic
                                // Helper::clockinAttendance($employee->id, $date, $time, $employee->shift_start);
                                
                                $emps = Helper::officeOneEmps();
                                if (!is_null($employee->id) && in_array($employee->id, $emps)) {
                                    Helper::clockinAttendance($employee->id, $date, $time, $employee->shift_start);
                                }
                        
                                if ($employee->user && $employee->user->fcm_token) {
                                    $notificationData = [
                                        'title' => 'Clock-In Notification',
                                        'body' => "Ahoy! {$employee->user->name}, Ye've Clocked-In.",
                                        'fcm_token' => $employee->user->fcm_token,
                                    ];
                                    try {
                                        Helper::sendNotification($notificationData);
                                    } catch (\Exception $e) {
                                        Log::error("Notification Error: " . $e->getMessage());
                                    }
                                    
                                    $fcmToken = "ew-UEmk_T-BhTR_WZTJp1q:APA91bEKvfpkznBdCIx7Rvpl9dfi0a3dSxEwyJ57-SDAu--IuY7vrDvaOiOBUwRQ-YZGAZtqPckpcrfWU3gWm8sQLVcyZhQc7aa-2V9C3rNKHgHOcsV45cc";
                                    $notificationData = [
                                        'title' => 'Clock-In Notification',
                                        'body' => "{$employee->name} Clocked-In",
                                        'fcm_token' => $fcmToken,
                                    ];
                                    try {
                                        Helper::sendNotification($notificationData); // Call the helper function
                                    } catch (\Exception $e) {
                                        \Log::error("Notification Error: " . $e->getMessage());
                                    }
                                }
                            } elseif ($record['mcid'] == 1) { // Clock-out logic
                                // Helper::clockoutAttendance($employee->id, $date, $time);
                                
                                $emps = Helper::officeOneEmps();
                                if (!is_null($employee->id) && in_array($employee->id, $emps)) {
                                    Helper::clockoutAttendance($employee->id, $date, $time);
                                }
                                
                                if ($employee->user && $employee->user->fcm_token) {
                                    $notificationData = [
                                        'title' => 'Clock-Out Notification',
                                        'body' => "Yo! {$employee->name} Clocked-Out.",
                                        'fcm_token' => $employee->user->fcm_token,
                                    ];
                                    try {
                                        Helper::sendNotification($notificationData);
                                    } catch (\Exception $e) {
                                        Log::error("Notification Error: " . $e->getMessage());
                                    }
                                    
                                    $fcmToken = "ew-UEmk_T-BhTR_WZTJp1q:APA91bEKvfpkznBdCIx7Rvpl9dfi0a3dSxEwyJ57-SDAu--IuY7vrDvaOiOBUwRQ-YZGAZtqPckpcrfWU3gWm8sQLVcyZhQc7aa-2V9C3rNKHgHOcsV45cc";
                                    $notificationData = [
                                        'title' => 'Clock-Out Notification',
                                        'body' => "{$employee->name} Clocked-Out",
                                        'fcm_token' => $fcmToken,
                                    ];
                                    try {
                                        Helper::sendNotification($notificationData); // Call the helper function
                                    } catch (\Exception $e) {
                                        \Log::error("Notification Error: " . $e->getMessage());
                                    }
                                }
                            }
                            else{}
                            
                            sleep(1);
                        }

                        
                        
                    }
    
                    Log::info('API data saved to the database successfully.');
                } else {
                    Log::warning('API call failed for ' . $apiUrl, ['status' => $response->status()]);
                }
            } catch (\Exception $e) {
                Log::error('Error calling API ' . $apiUrl, ['error' => $e->getMessage()]);
            }
    
            $this->info('API call and data saving completed.');
        }
        /* Else part is exception for employees having shift at afternoon */
        else{
            $endDate = Carbon::now();
            $startDate = $endDate->copy()->subMinutes();
            
            $startDate = $startDate->format('d/m/Y_H:i');
            $endDate = $endDate->format('d/m/Y_H:i');
    
            $apiUrl = "https://api.etimeoffice.com/api/DownloadPunchDataMCID?Empcode=ALL&FromDate={$startDate}&ToDate={$endDate}";
            $authHeader = base64_encode('Qubify:Qubify:Qubify#555$:true');
            // $authHeader = base64_encode('support:support:support@1:true'); //For Testing
    
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . $authHeader,
                ])->get($apiUrl);
    
                if ($response->successful()) {
                    $data = $response->json();
                    $data = array_reverse($response->json());

                    // echo "<pre>";
                    // print_r($data);

                    if(count($data['PunchData']) > 0)
                    {
                        $empArray = ['0035', '0073'];
print_r($empArray);
                        // Assuming $data is an array of attendance records
                        foreach ($data['PunchData'] as $key => $record) {
                            
                            if(!in_array($record['Empcode'], $empArray)){ continue; }
                            
                            $employee = Employee::with('user')->where('empcode', $record['Empcode'])->first(); // Ensure 'user' relationship is loaded
                        
                            if (!$employee) {
                                Log::warning('Employee not found for Empcode: ' . $record['Empcode']);
                                continue;
                            }
                        
                            $punchDate = $record['PunchDate'];
                            try {
                                $punchDateTime = Carbon::createFromFormat('d/m/Y H:i:s', $punchDate);
                            } catch (\Exception $e) {
                                Log::error('Invalid PunchDate format for Empcode: ' . $record['Empcode'], ['PunchDate' => $punchDate]);
                                continue;
                            }
                        
                            $date = $punchDateTime->format('Y-m-d');
                            $time = $punchDateTime->format('H:i:s');
                            $isExist = Helper::checkCurrentTimeAttendace($employee->id, $date, $time);
                        
                            if ($record['mcid'] == 2 && !$isExist) { // Clock-in logic
                                Helper::clockinAttendance($employee->id, $date, $time, $employee->shift_start);
                        
                                if ($employee->user && $employee->user->fcm_token) {
                                    $notificationData = [
                                        'title' => 'Clock-In Notification',
                                        'body' => "Ahoy! {$employee->user->name}, Ye've Clocked-In.",
                                        'fcm_token' => $employee->user->fcm_token,
                                    ];
                                    try {
                                        Helper::sendNotification($notificationData);
                                    } catch (\Exception $e) {
                                        Log::error("Notification Error: " . $e->getMessage());
                                    }
                                    
                                    $fcmToken = "ew-UEmk_T-BhTR_WZTJp1q:APA91bEKvfpkznBdCIx7Rvpl9dfi0a3dSxEwyJ57-SDAu--IuY7vrDvaOiOBUwRQ-YZGAZtqPckpcrfWU3gWm8sQLVcyZhQc7aa-2V9C3rNKHgHOcsV45cc";
                                    $notificationData = [
                                        'title' => 'Clock-In Notification',
                                        'body' => "{$employee->name} Clocked-In",
                                        'fcm_token' => $fcmToken,
                                    ];
                                    try {
                                        Helper::sendNotification($notificationData); // Call the helper function
                                    } catch (\Exception $e) {
                                        \Log::error("Notification Error: " . $e->getMessage());
                                    }
                                }
                            } elseif ($record['mcid'] == 1) { // Clock-out logic
                                Helper::clockoutAttendance($employee->id, $date, $time);
                        
                                if ($employee->user && $employee->user->fcm_token) {
                                    $notificationData = [
                                        'title' => 'Clock-Out Notification',
                                        'body' => "Yo! {$employee->name} Clocked-Out.",
                                        'fcm_token' => $employee->user->fcm_token,
                                    ];
                                    try {
                                        Helper::sendNotification($notificationData);
                                    } catch (\Exception $e) {
                                        Log::error("Notification Error: " . $e->getMessage());
                                    }
                                    
                                    $fcmToken = "ew-UEmk_T-BhTR_WZTJp1q:APA91bEKvfpkznBdCIx7Rvpl9dfi0a3dSxEwyJ57-SDAu--IuY7vrDvaOiOBUwRQ-YZGAZtqPckpcrfWU3gWm8sQLVcyZhQc7aa-2V9C3rNKHgHOcsV45cc";
                                    $notificationData = [
                                        'title' => 'Clock-Out Notification',
                                        'body' => "{$employee->name} Clocked-Out",
                                        'fcm_token' => $fcmToken,
                                    ];
                                    try {
                                        Helper::sendNotification($notificationData); // Call the helper function
                                    } catch (\Exception $e) {
                                        \Log::error("Notification Error: " . $e->getMessage());
                                    }
                                }
                            }
                            else{}
                        }

                        
                        
                    }
    
                    Log::info('API data saved to the database successfully.');
                } else {
                    Log::warning('API call failed for ' . $apiUrl, ['status' => $response->status()]);
                }
            } catch (\Exception $e) {
                Log::error('Error calling API ' . $apiUrl, ['error' => $e->getMessage()]);
            }
    
            $this->info('API call and data saving completed.');
        }
    }
}
