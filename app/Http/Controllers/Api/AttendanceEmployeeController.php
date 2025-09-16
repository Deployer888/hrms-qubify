<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceEmployee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\IpRestrict;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Support\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\EmployeeBirthday;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;

class AttendanceEmployeeController extends BaseController
{
    public function attendanceClock(Request $request)
    {
        try {

            $rules = [
                'employee_id' => 'required|string',
                'date'       => 'required|date_format:Y-m-d',
                'time'       => 'required|date_format:h:i:s A', // 12-hour with AM/PM
                'status'     => 'required|in:0,1', // 0 = in, 1 = out
                'latitude'   => 'required|numeric|between:-90,90',
                'longitude'  => 'required|numeric|between:-180,180',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {  
                return $this->errorResponse(__('Validation error.'),422, $validator->errors()->toArray());
            }

            $employee = Employee::find($request->employee_id);
            if (!$employee) {
                return $this->errorResponse(__('Employee not found!'),404);
            }

            $time_24 = Carbon::createFromFormat('h:i:s A', $request->time)->format('H:i:s');

            if ($request->status  == 0) {
                $employeeAttendance = AttendanceEmployee::where([
                    'employee_id' => $request->employee_id,
                    'date'        => $request->date,
                    'clock_out'        => '00:00:00',
                ])->first();
                if ($employeeAttendance && $employeeAttendance->clock_out == '00:00:00') {
                    return $this->errorDataResponse($employeeAttendance,"You already clock in.");
                }

                $employeeAttendance                     = new AttendanceEmployee();
                $employeeAttendance->employee_id        = $request->employee_id;
                $employeeAttendance->date               = $request->date;
                $employeeAttendance->employee_name      = $employee->name;
                $employeeAttendance->status             = 'Present';
                $employeeAttendance->clock_in           = $time_24;
                $employeeAttendance->clock_out          = '00:00:00';
                $employeeAttendance->clock_in_latitude  = $request->latitude;
                $employeeAttendance->clock_in_longitude = $request->longitude;
                $employeeAttendance->created_by         = \Auth::user()->creatorId();
                $employeeAttendance->save();
                return $this->successResponse($employeeAttendance,'Clock in Successfully.');
            }
            else
            {
                $rules = [];
                $rules['attendance_id'] = 'required|numeric';
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {  
                    return $this->errorResponse(__('Validation error.'),422, $validator->errors()->toArray());
                }
                $employeeAttendance = AttendanceEmployee::find($request->attendance_id);
                if (!$employeeAttendance) {
                    return $this->errorResponse("Attendance not found.");
                }
                $employeeAttendance->clock_out              = $time_24;
                $employeeAttendance->clock_out_latitude     = $request->latitude;
                $employeeAttendance->clock_out_longitude    = $request->longitude;
                $employeeAttendance->update(); 
                return $this->successResponse($employeeAttendance);
            }

        } catch (\Throwable $th) {
            return $this->errorResponse("Something went wrong.");
            //throw $th;
        }
    }
    
    public function attendance(Request $request)
    {
        $userID = $request->user_id;
        $employeeID = $request->employee_id;
        $time = $request->time;
        $userIp = $request->ip;
        
        $user = User::find($userID);
        $employee = Employee::find($employeeID);

        $settings = Utility::settings();
        if($settings['ip_restrict'] == 'on')
        {
            $ip = IpRestrict::where('created_by', $user->creatorId())->whereIn('ip', [$userIp])->first();
            if(empty($ip))
            {
                return redirect()->back()->with('error', __('this ip is not allowed to clock in & clock out.'));
            }
        }

        $employeeId      = !empty($employee) ? $employee->id : 0;
        $todayAttendance = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('date', date('Y-m-d'))->first();
        
        $startTime = Utility::getValByName('company_start_time');
        
        $endTime   = Utility::getValByName('company_end_time');

        $attendance = AttendanceEmployee::orderBy('id', 'desc')->where('employee_id', '=', $employeeId)->where('clock_out', '=', '00:00:00')->first();

        if($attendance != null)
        {
            $attendance            = AttendanceEmployee::find($attendance->id);
            $attendance->clock_out = $endTime;
            $attendance->save();
        }

        $date = date("Y-m-d");
        //late
        $totalLateSeconds = time() - strtotime($date . $startTime);
        $hours            = floor($totalLateSeconds / 3600);
        $mins             = floor($totalLateSeconds / 60 % 60);
        $secs             = floor($totalLateSeconds % 60);
        $late             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

        $checkDb = AttendanceEmployee::where('employee_id', '=', $employeeId)->get()->toArray();

        dd($user);
        if(empty($checkDb))
        {
            $employeeAttendance                = new AttendanceEmployee();
            $employeeAttendance->employee_id   = $employeeId;
            $employeeAttendance->date          = $date;
            $employeeAttendance->status        = 'Present';
            $employeeAttendance->clock_in      = $time;
            $employeeAttendance->clock_out     = '00:00:00';
            $employeeAttendance->late          = $late;
            $employeeAttendance->early_leaving = '00:00:00';
            $employeeAttendance->overtime      = '00:00:00';
            $employeeAttendance->total_rest    = Helper::TotalRest($time);
            $employeeAttendance->created_by    = $user->id;
            $employeeAttendance->save();
            
            $birthDate = Carbon::parse($user->employee->dob);
            $isBirth = $user->employee->isBirthDay;

            $currentDate = Carbon::now();
            $isBirthday = $birthDate->format('m-d') === $currentDate->format('m-d') || $isBirth;

            $employee = $user->employee;
            $employee->isBirthDay = false;
            $employee->save();
            return response()->json(['success', 'is_birthday' => $isBirthday, 'late' => $late, 'totalRest' => $employeeAttendance->total_rest], 200);
        }

        foreach($checkDb as $check)
        {
            $employeeAttendance                = new AttendanceEmployee();
            $employeeAttendance->employee_id   = $employeeId;
            $employeeAttendance->date          = $date;
            $employeeAttendance->status        = 'Present';
            $employeeAttendance->clock_in      = $time;
            $employeeAttendance->clock_out     = '00:00:00';
            $employeeAttendance->late          = $late;
            $employeeAttendance->early_leaving = '00:00:00';
            $employeeAttendance->overtime      = '00:00:00';
            $employeeAttendance->total_rest    = Helper::TotalRest($time);
            $employeeAttendance->created_by    = $user->id;
            $employeeAttendance->save();
            
            $birthDate = Carbon::parse($user->employee->dob);
            $currentDate = Carbon::now();
            
            $currentDate = Carbon::now();
            
            $isBirth = $birthDate->format('m-d') === $currentDate->format('m-d');
            
            if(!$isBirth){
                $emp = $user->getUSerEmployee($user->id);
                $isBirth = $emp->isBirthday;
            }
            
            $employee = $user->employee;
            $employee->isBirthDay = false;
            $employee->save();
            return response()->json(['success', 'is_birthday' => $isBirth, 'late' => $late, 'totalRest' => $employeeAttendance->total_rest], 200);

        }
    }
    
    public function update(Request $request, $id)
    {
        $todayAttendance = AttendanceEmployee::where('employee_id', '=', $id)->where('date', date('Y-m-d'))->OrderBy('id', 'DESC')->limit(1)->first();
        
        
        if($todayAttendance->clock_out == '00:00:00')
        {
            $startTime = Utility::getValByName('company_start_time');
            $endTime   = Utility::getValByName('company_end_time');

            if($request->user['type'] == 'employee')
            {

                $date = date("Y-m-d");
                $time = $request->time;

                //early Leaving
                $totalEarlyLeavingSeconds = strtotime($date . $endTime) - time();
                $hours                    = floor($totalEarlyLeavingSeconds / 3600);
                $mins                     = floor($totalEarlyLeavingSeconds / 60 % 60);
                $secs                     = floor($totalEarlyLeavingSeconds % 60);
                $earlyLeaving             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                if(time() > strtotime($date . $endTime))
                {
                    //Overtime
                    $totalOvertimeSeconds = time() - strtotime($date . $endTime);
                    $hours                = floor($totalOvertimeSeconds / 3600);
                    $mins                 = floor($totalOvertimeSeconds / 60 % 60);
                    $secs                 = floor($totalOvertimeSeconds % 60);
                    $overtime             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                }
                else
                {
                    $overtime = '00:00:00';
                }
                
                $attendanceEmployee                = AttendanceEmployee::find($todayAttendance->id);
                $attendanceEmployee->clock_out     = $time;
                $attendanceEmployee->early_leaving = $earlyLeaving;
                $attendanceEmployee->overtime      = $overtime;
                $attendanceEmployee->save();

                return response()->json(['success' =>true, 'totalRest' => $employeeAttendance->total_rest], 200);
            }
            else
            {
                $date = date("Y-m-d");
                //late
                $totalLateSeconds = strtotime($request->clock_in) - strtotime($date . $startTime);

                $hours = floor($totalLateSeconds / 3600);
                $mins  = floor($totalLateSeconds / 60 % 60);
                $secs  = floor($totalLateSeconds % 60);
                $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                //early Leaving
                $totalEarlyLeavingSeconds = strtotime($date . $endTime) - strtotime($request->clock_out);
                $hours                    = floor($totalEarlyLeavingSeconds / 3600);
                $mins                     = floor($totalEarlyLeavingSeconds / 60 % 60);
                $secs                     = floor($totalEarlyLeavingSeconds % 60);
                $earlyLeaving             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);


                if(strtotime($request->clock_out) > strtotime($date . $endTime))
                {
                    //Overtime
                    $totalOvertimeSeconds = strtotime($request->clock_out) - strtotime($date . $endTime);
                    $hours                = floor($totalOvertimeSeconds / 3600);
                    $mins                 = floor($totalOvertimeSeconds / 60 % 60);
                    $secs                 = floor($totalOvertimeSeconds % 60);
                    $overtime             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                }
                else
                {
                    $overtime = '00:00:00';
                }

                $attendanceEmployee                = AttendanceEmployee::find($todayAttendance->id);
                $attendanceEmployee->employee_id   = $request->employee_id;
                $attendanceEmployee->date          = $request->date;
                $attendanceEmployee->clock_in      = $request->clock_in;
                $attendanceEmployee->clock_out     = $request->clock_out;
                $attendanceEmployee->late          = $late;
                $attendanceEmployee->early_leaving = $earlyLeaving;
                $attendanceEmployee->overtime      = $overtime;
                $attendanceEmployee->total_rest    = '00:00:00';

                $attendanceEmployee->save();
                
                return response()->json(['success' => true, 'is_birthday' => false, 'totalRest' => $employeeAttendance->total_rest], 200);
                // return response()->json(['success'], 200);
            }
        }else{
            dd('error');
        }
    }
    
    public function getTodayAttendance($employeeId){
        $todaysAttendance = AttendanceEmployee::select('*')->where('employee_id', $employeeId)->where('date', date('Y-m-d'))->get();
        if($todaysAttendance){
            return response()->json(['success' => true, 'todaysAttendance' => $todaysAttendance], 200);
        }
    }


}
