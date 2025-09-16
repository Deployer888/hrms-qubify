<?php

namespace App\Helpers;
use App\Models\AttendanceEmployee;
use App\Models\Employee;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Leave;
use App\Models\Utility;
use App\Jobs\PlayMusicJob;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\Guzzle6HttpHandler;
use GuzzleHttp\Client as GuzzleClient;

class Helper
{
    public static function getRoles()
    {
        $roles = Role::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        foreach ($roles as $roleId => $roleName)
        {
            if(\Auth::user()->roles->contains('name', $roleName))
                return $roleName;
        }
        
    }
    
    public static function getUserRoles($employee)
    {
        return $employee->user->roles;
    }
    
    public static function getUserRoleNames()
    {
        return \Auth::user()->roles->pluck('name');
    }
 
    public static function getDateList($yearMonth)
    {
        $currentMonth = Carbon::now()->format('Y-m');

        if ($yearMonth === $currentMonth) {
            // Current month: From 1st to today
            $startDate = Carbon::parse($yearMonth . '-01');
            $endDate = Carbon::now();
        } else {
            // Other months: Full month
            $startDate = Carbon::parse($yearMonth . '-01');
            $endDate = $startDate->copy()->endOfMonth();
        }

        // Generate date range
        $dates = CarbonPeriod::create($startDate, $endDate);

        // Convert to an array of formatted dates
        return collect($dates)->map(fn ($date) => $date->toDateString())->all();
    }
    
    public static function getEmpLeave($date,$employee_id)
    {
        try {
            $leave = Leave::where('employee_id', $employee_id)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where('status', 'Approve')
            ->first();
            return $leave?$leave->toarray():false;
        } catch (\Throwable $th) {
            return false;
        }

    }
    public static function dynLateTime($shift_time, $time)
    {
        // Parse the shift time and the actual time using Carbon
        $shiftTime = Carbon::parse($shift_time);
        $shiftGraceTime = Carbon::parse($shift_time)->addMinutes(15);
        $actualTime = Carbon::parse($time);

        // Check if the actual time is later than the shift time (late)
        if ($actualTime->gt($shiftGraceTime)) {
            $lateMinutes = $shiftTime->diffInMinutes($actualTime);
            $time = number_format($lateMinutes, 0);
            return "{$time} min (Late)";
        }
        // Check if the actual time is exactly the same as shift time (on-time)
        elseif ($actualTime->eq($shiftTime)) {
            return "(On-Time)";
        }
        // Check if the actual time is earlier than the shift time (early)
        elseif ($actualTime->lt($shiftTime)) {
            $earlyMinutes = $shiftTime->diffInMinutes($actualTime);
            return "(Early)";
        }

        // If the employee clocked in within grace period (between shift time and grace time)
        return "(With-In Time)";
    }
    
    public static function dynRestTime($clock_out, $clock_in)
    {
        $clock_out = Carbon::parse($clock_out);
        $clock_in = Carbon::parse($clock_in);
        // Calculate the difference in minutes
        $differenceInMinutes = $clock_out->diffInMinutes($clock_in);
        $time = number_format($differenceInMinutes, 0);
        return "{$time} min (Rest)";  
    }
    
    public static function convertTimeToMinutesAndSeconds($time)
    {
        // Split the time into hours, minutes, and seconds
        list($hours, $minutes, $seconds) = explode(':', $time);

        // Convert hours to minutes and add to the total minutes
        $totalMinutes = ($hours * 60) + $minutes;

        // Return the time in "X min Y sec" format
        return $totalMinutes . ' min ';
        // return $totalMinutes . ' min ' . $seconds . ' sec';
    }
    
    public static function convertTimeToMinutes($time)
    {
        // Split the time into hours, minutes, and seconds
        list($hours, $minutes, $seconds) = explode(':', $time);

        // Convert hours to minutes, add minutes, and convert seconds to a fraction of a minute
        return ($hours * 60) + $minutes + ($seconds / 60);
    }

    /* public static function sendNotification($data)
    {
        $serviceAccountPath = storage_path('app/qubifyhrm-firebase-adminsdk-1gu0h-8b1ad87acc.json');

        // Prepare the message payload
        $message = [
            'message' => [
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['body'],
                ],
                'token' => $data['fcm_token'], // Use `registration_ids` for multiple tokens
            ],
        ];

        // Authentication with Firebase
        $scope = 'https://www.googleapis.com/auth/firebase.messaging';
        $credentials = new ServiceAccountCredentials($scope, $serviceAccountPath);

        // Create a Guzzle client instance
        $guzzleClient = new GuzzleClient();
        $httpHandler = new Guzzle6HttpHandler($guzzleClient);

        try {
            $credentials->fetchAuthToken($httpHandler); // Fetch auth token
            $accessToken = $credentials->getLastReceivedToken()['access_token'];

            // Send the HTTP v1 request
            $client = new GuzzleClient();
            $response = $client->post(
                'https://fcm.googleapis.com/v1/projects/qubifyhrm/messages:send',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($message),
                ]
            );

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('FCM Send Error: ' . $e->getMessage());
            throw $e;
        }
    } */

    public static function sendNotification($data)
    {
        $serviceAccountPath = storage_path('app/hrms-88b49-d63f416a8846.json');
        // $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
        // dd($serviceAccount);
        // Prepare the message payload
        $message = [
        'message' => [
                'token' => $data['fcm_token'],
     
                // DATA-ONLY so Android calls onMessageReceived()
                'data' => [
                    'title'  => $data['title'] ?? 'HRMS',
                    'body'   => $data['body']  ?? 'You have a new message',
                ],
     
                // ðŸ‘‡ MUST be INSIDE 'message'
                'android' => [
                    'priority' => 'HIGH',
                    'direct_boot_ok' => true,
                ],
            ],
        ];
 
        // Authentication with Firebase
        $scope = 'https://www.googleapis.com/auth/firebase.messaging';
        $credentials = new ServiceAccountCredentials($scope, $serviceAccountPath);
 
        // Create a Guzzle client instance
        $guzzleClient = new GuzzleClient();
        $httpHandler = new Guzzle6HttpHandler($guzzleClient);
        
 
        try {
            $credentials->fetchAuthToken($httpHandler); // Fetch auth token
            $accessToken = $credentials->getLastReceivedToken()['access_token'];
            // Send the HTTP v1 request
            $client = new GuzzleClient();
            $response = $client->post(
                'https://fcm.googleapis.com/v1/projects/hrms-88b49/messages:send',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($message),
                ]
            );
            
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('FCM Send Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function formatTimeDifference($checkIn, $checkOut) {
        $checkInTime = strtotime($checkIn);
        $checkOutTime = strtotime($checkOut);
        $diffInSeconds = abs($checkOutTime - $checkInTime);

        if ($diffInSeconds < 60) {
            return $diffInSeconds . ' secs';
        }

        $diffInMinutes = floor($diffInSeconds / 60);
        $remainingSeconds = $diffInSeconds % 60;

        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' mins ' . $remainingSeconds . ' secs';
        }

        $diffInHours = floor($diffInMinutes / 60);
        $remainingMinutes = $diffInMinutes % 60;

        return $diffInHours . ' hrs ' . $remainingMinutes . ' mins ' . $remainingSeconds . ' secs';
    }

    // public static function calculateTotalTimeDifference($attendanceList) {
    //     $totalSeconds = 0;
    //     $workTime = 0;
    //     $currentTime = time();
    //     /*foreach ($attendanceList as $attendance) {
    //         $checkInTime = strtotime($attendance->clock_in);
    //         $checkOutTime = strtotime($attendance->clock_out);
    //         if ($attendance->clock_out != '00:00:00') {
    //             $totalSeconds += abs($checkOutTime - $checkInTime);
    //         }
    //         else if ($checkInTime) {
    //             $totalSeconds += abs($currentTime - $checkInTime);
    //         }
    //     }*/

    //     foreach ($attendanceList as $attendance) {
    //         if ($attendance->clock_in && $attendance->clock_out) {
    //             $clockInTime = $attendance->clock_in;
    //             $clockOutTime = $attendance->clock_out;
    //             if ($clockOutTime > $clockInTime) {
    //                 $clockInTime = strtotime($clockInTime);
    //                 $clockOutTime = strtotime( $clockOutTime);
    //                 if ($attendance->clock_out != '00:00:00') {
    //                     $dailyWorkTime = round(($clockOutTime - $clockInTime) / 3600, 2);
    //                     $workTime += $dailyWorkTime;
    //                 }
    //                 else if ($clockInTime) {
    //                     $workTime += abs($currentTime - $clockInTime);
    //                 }
    //             }
    //         }
    //     }
    //     $timeFormat = sprintf('%02d:%02d Hrs', floor($workTime), ($workTime - floor($workTime)) * 60);
    //     // return gmdate("H:i", $workTime);
    //     return $timeFormat;
    // }

    public static function calculateTotalTimeDifference($attendanceList) {
        // dd($attendanceList);
        $totalSeconds = 0;
        $currentTime = time();

        foreach ($attendanceList as $attendance) {
            if ($attendance->clock_in) {
                $clockInTime = strtotime($attendance->clock_in);
                $clockOutTime = $attendance->clock_out && $attendance->clock_out != '00:00:00'
                    ? strtotime($attendance->clock_out)
                    : $currentTime;

                // Add the difference in seconds to totalSeconds
                if ($clockOutTime > $clockInTime) {
                    $totalSeconds += ($clockOutTime - $clockInTime);
                }
            }
        }

        // Convert total seconds to hours and minutes
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);

        // Format the time as HH:MM Hrs
        $timeFormat = sprintf('%02d:%02d Hrs', $hours, $minutes);

        return $timeFormat;
    }


    public static function pr($pr) {
        echo "<pre>";
        print_r($pr);
        echo "<br>";
        die('---END---');
        return ;
    }

    /*public static function totalLeaveAvailed($id, $startOfMonth, $endOfMonth, $leaveType = null) {
        $currentYear = now()->year;
        $status = ['Approve', 'Pending'];
        $totalLeaveAvailed = Leave::where('employee_id', $id)
                            ->whereYear('created_at', $currentYear)
                            ->whereIn('status', $status);
        if($leaveType){
            $totalLeaveAvailed = $totalLeaveAvailed->where('leave_type_id', $leaveType)->where('start_date', '>=', $startOfMonth);
        }
        else{
            $totalLeaveAvailed = $totalLeaveAvailed->whereBetween('start_date', [$startOfMonth, $endOfMonth]);
        }
        $totalLeaveAvailed = $totalLeaveAvailed->sum('total_leave_days');
        return $totalLeaveAvailed;
    }*/
    
    // Version with better error handling
    public static function totalLeaveAvailed($id, $startOfMonth, $endOfMonth, $leaveType = null) {
        try {
            $currentYear = now()->year;
            $status = ['Approve', 'Pending'];
            
            // Validate inputs
            if (empty($id) || empty($startOfMonth) || empty($endOfMonth)) {
                return 0;
            }
            
            // Build query
            $query = Leave::where('employee_id', $id)
                        ->whereYear('created_at', $currentYear)
                        ->whereIn('status', $status);
            
            // Apply filters
            if ($leaveType) {
                $query->where('leave_type_id', $leaveType)
                      ->where('start_date', '>=', $startOfMonth);
            } else {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth]);
            }
            
            return $query->sum('total_leave_days') ?? 0;
            
        } catch (Exception $e) {
            // Log error if needed
            // Log::error('Error calculating leave: ' . $e->getMessage());
            return 0;
        }
    }


    public static function check_permissions($permission) {

        $user = \Auth::user();
        if ($user && $user->getAllPermissions()->pluck('name')->contains($permission)) {
            return true;
        }
        return false;
    }

    public static function TotalRest($time , $emp_id, $date = null) {
        if(!$date){
           $date = date('Y-m-d');
        }
        $employeeId      = !empty($emp_id) ? $emp_id : 0;
        $secondLatestData = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('date', $date)->latest()->first();
        $latestClockOut = $secondLatestData->clock_out??'00:00:00';
        if(!$secondLatestData){
            $totalRest = '00:00:00';
            return $totalRest;
        }
        $currentTime = date('H:i:s');
        $diff = date_diff(date_create($latestClockOut), date_create($time));
        $totalRest = $diff->format('%H:%I:%S');
        return $totalRest;
    }

    public static function TotalRestEdit($time , $emp_id, $date = null) {
        if(!$date){
           $date = date('Y-m-d');
        }
        $employeeId      = !empty($emp_id) ? $emp_id : 0;
        $secondLatestData = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('date', $date)->latest()->skip(1)->first();
        $latestClockOut = $secondLatestData->clock_out??'00:00:00';
        if(!$secondLatestData){
            $totalRest = '00:00:00';
            return $totalRest;
        }
        $currentTime = date('H:i:s');
        $diff = date_diff(date_create($latestClockOut), date_create($time));
        $totalRest = $diff->format('%H:%I:%S');
        return $totalRest;
    }

    public static function FormatTime($time) {
        if (!empty($time)) {
            list($hours, $minutes, $seconds) = explode(':', $time);

            $formattedLate = '';

            $hours = (int)$hours;
            $minutes = (int)$minutes;
            $seconds = (int)$seconds;

            if ($hours > 0) {
                $formattedLate .= $hours . 'hr ';
            }

            if ($minutes > 0) {
                $formattedLate .= $minutes . 'm ';
            } elseif ($hours > 0 && $minutes === 0) {
                $formattedLate .= '0m ';
            }

            // $formattedLate .= $seconds . 's';

            $time = trim($formattedLate);
            return $time;
        }
    }

    public static function getLatestAttendance($employeeId) {
        $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                                        ->where('clock_out', '!=', '00:00:00')
                                        ->whereDate('date', '=', now()->toDateString())
                                        ->orderBy('created_at', 'desc')
                                        ->first();
        return $clockOut = $attendance ? $attendance->clock_out : '';
    }

    public static function getTotalAttendanceTime($employeeId) {
        $date = date("Y-m-d");
        $employeeAttendanceList = AttendanceEmployee::orderBy('id', 'desc')->where('employee_id', '=', !empty($employeeId) ? $employeeId : 0)->where('date', '=', $date)->get();
        return self::calculateTotalTimeDifference($employeeAttendanceList);
    }

    public static function checkLeave($date, $employeeId) {
        $leave = Leave::where('employee_id', $employeeId)
                  ->whereDate('start_date', '<=', $date)
                  ->whereDate('end_date', '>=', $date)
                  ->whereIn('status', ['Approve', 'Pending'])
                  ->first();

        return $leave ? 1 : 0;
    }

    public static function checkLeaveWithTypes($date, $employeeId) {
        $currentYear = now()->year;
        $leave = Leave::where('employee_id', $employeeId)
                  ->whereDate('start_date', '<=', $date)
                  ->whereDate('end_date', '>=', $date)
                  ->whereYear('created_at', $currentYear)
                  ->whereIn('status', ['Approve', 'Pending'])
                  ->first();

        if(!empty($leave)){
            if($leave->leavetype == 'half') {
                    return 'Present along with Half-Day Leave';
                }
                else if($leave->leavetype == 'full'){
                    return 'Full Day Leave';
                }
                else if($leave->leavetype == 'short'){
                    return 'Present along with Short Leave';
                }
        }

        return 0;
    }

    public static function checkCurrentTimeAttendace($emp_id, $date, $time){
        return AttendanceEmployee::where('employee_id', $emp_id)
        ->where('clock_in', '!=', '00:00:00')
        ->where('clock_out', '00:00:00')
        ->where('date', $date)
        ->exists();
    }

    public static function clockinAttendance($emp_id, $date, $time, $startTime) {
        $date = date("Y-m-d", strtotime($date));

        $totalLateSeconds = strtotime($time) - strtotime($date . $startTime);
        $hours = floor($totalLateSeconds / 3600);
        $mins = floor($totalLateSeconds / 60 % 60);
        $secs = floor($totalLateSeconds % 60);
        $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

        $employeeData = Employee::find($emp_id);

        if($employeeData->clock_out != '00:00:00') {

            $employeeAttendance = new AttendanceEmployee();
            $employeeAttendance->employee_id = $emp_id;
            $employeeAttendance->employee_name    = $employeeData->name;
            $employeeAttendance->date = $date;
            $employeeAttendance->status = 'Present';
            $employeeAttendance->clock_in = $time;
            $employeeAttendance->clock_out = '00:00:00';
            $employeeAttendance->late = $late;
            $employeeAttendance->early_leaving = '00:00:00';
            $employeeAttendance->overtime = '00:00:00';
            $employeeAttendance->total_rest = Helper::TotalRest($time, $emp_id);
            $employeeAttendance->created_by = $emp_id;
            $employeeAttendance->save();
        }

        // Dispatch the job after 10 minutes
        PlayMusicJob::dispatch($emp_id)->delay(now()->addMinutes(10));

        return true;
    }

    public static function clockoutAttendance($emp_id, $date, $time) {
        // Format the date correctly
        $date = date("Y-m-d", strtotime($date));

        // Fetch the last attendance record for the employee where clock_out is '00:00:00'
        $todayAttendance = AttendanceEmployee::where('employee_id', $emp_id)
            ->where('date', $date)
            ->where('clock_out', '00:00:00')
            ->orderBy('id', 'desc')
            ->first();

        // Update the attendance record
        if($todayAttendance) {
            $todayAttendance->clock_out = $time;
            $todayAttendance->save();
        }

        return true;
    }
    
    public static function empIdWithEmpCode(int $emp_code)
    {
        $empData = Employee::where('empcode2', $emp_code)->value('id');
        return $empData;
    }
 
    public static function empNameWithEmpCode($emp_code)
    {
        $empData = Employee::where('empcode2', $emp_code)->value('name');
        return $empData;
    }
 
    public static function attendanceBatchList()
    {
        return AttendanceEmployee::distinct()
            ->whereNotNull('batch_id')
            ->orderBy('batch_id', 'desc')
            ->pluck('batch_id');
    }
 
    public static function officeOneEmps()
    {
        return [1,4,7,8,9,11,23,24,26,31];
    }
    public static function officeTwoEmps()
    {
        return [30,29,22,6,5,2,3];
    }

    public static function userType($user)
    {
        return $user->type??false;
    }
 
    public static function accessEvent($events, $employee = null )
    {
        //currently using in api /api/dashboard/
        $data = [];
        foreach ($events as $event) {
            if($employee)
            {
                if ($event->branch_id != 0) {
                     if ($event->branch_id == $employee->branch_id) {
                         if ($event->department_id != '["0"]')
                         {
                             if ($employee->department_id && $event->department_id && in_array($employee->department_id,json_decode($event->department_id)??[]))
                             {
                                 if ($event->employee_id != '["0"]') {
                                     if ($employee->id && $event->employee_id && in_array($employee->id,json_decode($event->employee_id)??[])) {
                                         $data[] = $event->toarray();
                                         # code...
                                     }
                                     # code...
                                 }
                                 else
                                 {
                                    $data[] = $event->toarray();
                                }
                                 # code...
                             }
                             # code...
                         }
                         else
                         {
                            if ($event->employee_id != '["0"]') {
                                if ($employee->id && $event->employee_id && in_array($employee->id,json_decode($event->employee_id)??[])) {
                                    $data[] = $event->toarray();
                                    # code...
                                }
                                # code...
                            }
                            else
                            {
                               $data[] = $event->toarray();
                           }
                         }
                         # code...
                     }
                    # code...
                }
                else
                {
                     if ($event->department_id != '["0"]') {
                         if ($employee->department_id && $event->department_id && in_array($employee->department_id,json_decode($event->department_id)??[])) {
                             if ($event->employee_id != '["0"]') {
                                 if ($employee->id && $event->employee_id && in_array($employee->id,json_decode($event->employee_id)??[])) {
                                     $data[] = $event->toarray();
                                     # code...
                                 }
                                 # code...
                             }
                             else
                             {
                                 $data[] = $event->toarray();
                             }
                             # code...
                         }
                         # code...
                     }
                     else
                     {
                         if ($event->employee_id != '["0"]') {
                             if ($employee->id && $event->employee_id && in_array($employee->id,json_decode($event->employee_id)??[])) {
                                 $data[] =  $event->toarray();
                                 # code...
                             }
                             # code...
                         }
                         else
                         {
                             $data[] = $event->toarray();
                         }
                     }
                }
            }
            # code...
        }
        return $data;
    }
 
    public static function employeeData($id)
    {
        $employee = Employee::where('user_id', '=', $id)->first();
        return $employee;
    }

}
