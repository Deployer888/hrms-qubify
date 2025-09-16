<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\IpRestrict;
use App\Models\User;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\EmployeeBirthday;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class AttendanceEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $start_date = '';
        $end_date = '';
        $employees = [];
        $leaveDays = [];
        $attendanceData = [];
        $datesDescending = [];
        $attendanceEmployee = [];
        $currentDate = Carbon::now()->toDateString(); // Get the current date in Y-m-d format
        $holidays = false;
        $isWeekend = false;

        if(\Auth::user()->can('Manage Attendance'))
        {
            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('All Branch', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All Department', '');
            $attendanceData = [];
            $holidays = Holiday::pluck('date')->toArray();
            $employees = Employee::select('id', 'name')->get();
            $date = $request->input('date');
            $month = $request->input('month');

            if(\Auth::user()->type == 'employee')
            {
                $employeeId = \Auth::user()->employee->id;
                $attendances = [];
                // Query the attendance records
                if ($date && $employeeId) {
                    $attendances = AttendanceEmployee::whereDate('date', $date)->where('employee_id', $employeeId)->orderBy('clock_in','ASC')->get();
                }
                if($employeeId && !$date)
                {
                    $attendances = AttendanceEmployee::whereDate('date', $currentDate)->where('employee_id', $employeeId)->orderBy('clock_in','ASC')->get();
                }

                if(count($attendances)<1)
                {
                    if($date)
                    {
                        $holidays = Holiday::where('date',$date)->exists();
                    }else{
                        $holidays = Holiday::where('date',$currentDate)->exists();
                    }
                }
                if($date)
                {
                    $isWeekend = Carbon::parse($date)->isWeekend();
                    $isLeave = Helper::checkLeave($date, Auth::user()->employee->id);
                    $empLeave = Helper::getEmpLeave($date, Auth::user()->employee->id);


                }else{
                    $isWeekend = Carbon::parse($currentDate)->isWeekend();
                    $isLeave = Helper::checkLeave($currentDate, Auth::user()->employee->id);
                    $empLeave = Helper::getEmpLeave($currentDate, Auth::user()->employee->id);
                }

                $employees = Employee::all();

                // dd(Helper::getDateList($month));
                $dateList = [];
                if ($request->has('month')) {
                    $dateList = Helper::getDateList($month);
                }
                $today = Carbon::now();
                $dateList = array_reverse($dateList);
                $monthAttendance = [];
                foreach ($dateList as $key => $dateData) {
                    $attendancesData= AttendanceEmployee::with('employee')->whereDate('date', $dateData)->where('employee_id', $employeeId)->orderBy('clock_in','ASC')->get();
                    $data = [];
                    $data['hours']  = Helper::calculateTotalTimeDifference($attendancesData);
                    $data['attendance'] = $attendancesData->toarray();
                    $data['is_weekend'] = Carbon::parse($dateData)->isWeekend();
                    $isLeave = Helper::checkLeave($dateData, Auth::user()->employee->id);
                    $empLeave = Helper::getEmpLeave($dateData, Auth::user()->employee->id);
                    $data['leave_detail'] = $empLeave;
                    $data['is_leave'] = $isLeave;
                    $minHours = '08:00';
                    if($isLeave && $empLeave)
                    {
                        if($empLeave['leavetype'] == 'half')
                        {
                            $minHours = '04:00';
                        }
                        if($empLeave['leavetype'] == 'short')
                        {
                            $minHours = '06:00';
                        }
                    }
                    $data['min_hours'] = $minHours;
                    $monthAttendance[$dateData] = $data;
                }
                // Return the view with the filtered data
                return view('attendance.employee.index', [
                    'attendanceEmployee' => $attendances,
                    'monthAttendanceEmployee' => $monthAttendance??[],
                    'employees' => $employees,
                    'branch' => $branch,
                    'department' => $department,
                    'date' => $date,
                    'employee' => $employeeId,
                    'holidays' => $holidays,
                    'isWeekend' => $isWeekend,
                    'isLeave' => $isLeave,
                    'empLeave' => $empLeave,
                    'dateList' => $dateList,
                ]);
            }
            else
            {
                $employeeId = $request->input('employee');
                $branchId = $request->input('branch');
                $departmentId = $request->input('department');
                $attendances = [];
                // Query the attendance records
                if ($date && $employeeId) {
                    // If both $date and $employeeId are provided
                    $attendanceWithEmployee = Employee::where('is_active', 1)
                        ->where('id', $employeeId) // Filter by employee ID
                        ->with(['attendance' => function ($query) use ($date) {
                            $query->whereDate('date', $date)->orderBy('clock_in', 'ASC');
                        }])
                        ->get();
                } elseif ($date) {
                    // If only $date is provided
                    $attendanceWithEmployee = Employee::where('is_active', 1)
                        ->with(['attendance' => function ($query) use ($date) {
                            $query->whereDate('date', $date)->orderBy('clock_in', 'ASC');
                        }])
                        ->get();
                } else {
                    // If neither $date nor $employeeId is provided, use the current date
                    $attendanceWithEmployee = Employee::where('is_active', 1)
                        ->with(['attendance' => function ($query) use ($currentDate) {
                            $query->whereDate('date', $currentDate)->orderBy('clock_in', 'ASC');
                        }])
                        ->get();
                }

                if($date)
                    {
                        $holidays = Holiday::where('date',$date)->exists();
                        $isWeekend = Carbon::parse($date)->isWeekend();
                    }else{
                        $holidays = Holiday::where('date',$currentDate)->exists();
                        $isWeekend = Carbon::parse($currentDate)->isWeekend();

                    }

                // Return the view with the filtered data
                return view('attendance.index', [
                    'attendanceEmployee' => $attendances,
                    'attendanceWithEmployee' => $attendanceWithEmployee,
                    'employees' => $employees,
                    'branch' => $branch,
                    'department' => $department,
                    'date' => $date,
                    'employee' => $employeeId,
                    'holidays' => $holidays,
                    'isWeekend' => $isWeekend,
                ]);
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create(Request $request)
    {
        if(\Auth::user()->can('Create Attendance'))
        {
            $date = $request->date;
            $employee_id = 0;
            $employees = Employee::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');
            if ($request->employee_id) {
                $employee_id = $request->employee_id;
            }
            return view('attendance.create', compact('employees','employee_id','date'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }


    }

    public function store(Request $request)
    {
        $checkAttendance  = AttendanceEmployee::where('employee_id', $request->employee_id)
                ->where('date', $request->date)
                ->exists();
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
            'date' => 'required',
            'clock_in' => 'required',
            'clock_out' => 'nullable',
        ]);
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $startTime  = Utility::getValByName('company_start_time');
        $endTime    = Utility::getValByName('company_end_time');

        $clock_in_time = $request->clock_in . ':00';
        if($request->clock_out && $request->clock_out != '0:00')
        {
            $clock_out_time = $request->clock_out . ':00';
            $clock_out_time = Carbon::createFromFormat('H:i:s', $clock_out_time);
            $clock_in_time = Carbon::createFromFormat('H:i:s', $clock_in_time);
            if($clock_out_time<$clock_in_time){
                return redirect()->back()->with('error','Clock-in must be greater than clock-out!');
            }
        }
        else{
            $clock_out_time = '00:00:00';
        }


        // Fetch attendance records for the given employee and date
        $attendance = AttendanceEmployee::where('employee_id', $request->employee_id)
            ->where('date', $request->date)
            ->get();

        // Fetch employee data
        $employeeData = Employee::find($request->employee_id);
        // Check for overlapping attendance records

        $bigClockOutAttendance = AttendanceEmployee::where([
            'employee_id' => $request->employee_id,
            'date' => $request->date
        ])
        ->orderBy('clock_out', 'desc')
        ->first();
        $smallClockInAttendance = AttendanceEmployee::where([
            'employee_id' => $request->employee_id,
            'date' => $request->date
        ])
        ->orderBy('clock_in', 'asc')
        ->first();
        $bigClockInAttendance = AttendanceEmployee::where([
            'employee_id' => $request->employee_id,
            'date' => $request->date
        ])
        ->orderBy('clock_in', 'desc')
        ->first();

        $attendanceExistQuery = AttendanceEmployee::query();

        $attendanceExistQuery->where('employee_id', $request->employee_id)
        ->where('date', $request->date);
        if(!$request->clock_out || $request->clock_out == '0:00')
        {
            if($bigClockOutAttendance && $bigClockInAttendance->clock_out == '00:00:00')
            {
                return redirect()->back()->with('error','Clock out 00:00 already exists!');
            }
            if ($bigClockOutAttendance && $bigClockOutAttendance->clock_out<$request->clock_in) {
                return redirect()->back()->with('error','Employee Attendance Already Created.');
            }
            $attendanceExistQuery->where('clock_in', '<', $request->clock_in)
                 ->where('clock_out', '>', $request->clock_in);
        }
        else
        {
            $attendanceExistQuery->where('clock_in', '<', $request->clock_out)
                 ->where('clock_out', '>', $request->clock_in);
        }

        $attendanceExists = $attendanceExistQuery->exists();
        if($attendanceExists )
        {
            return redirect()->back()->with('error', __('Employee Attendance Already Created.'));
        }
        else
        {
            $date = date("Y-m-d");

            //late
            if (!$checkAttendance) {
                $totalLateSeconds = time() - strtotime($date . $employeeData->shift_start);
                $hours            = floor($totalLateSeconds / 3600);
                $mins             = floor($totalLateSeconds / 60 % 60);
                $secs             = floor($totalLateSeconds % 60);
                $late             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            }
            else{
                $late = '00:00:00';
            }

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

            // dd(Helper::TotalRest($request->clock_in.':00',$request->employee_id, $request->date));
            $employeeAttendance                = new AttendanceEmployee();
            $employeeAttendance->employee_id   = $request->employee_id;
            $employeeAttendance->date          = $request->date;
            $employeeAttendance->employee_name = $employeeData->name;
            $employeeAttendance->status        = 'Present';
            $employeeAttendance->clock_in      = $request->clock_in . ':00';
            $employeeAttendance->clock_out     = $request->clock_out . ':00';
            $employeeAttendance->late          = $late;
            $employeeAttendance->early_leaving = $earlyLeaving;
            $employeeAttendance->overtime      = $overtime;
            $employeeAttendance->total_rest    = Helper::TotalRest($request->clock_in.':00', $request->employee_id, $request->date);
            $employeeAttendance->created_by    = \Auth::user()->creatorId();
            $employeeAttendance->save();

            return redirect()->back()->with('success', __('Employee attendance successfully created.'));
        }
    }

    public function copy($id)
    {
        if(\Auth::user()->can('Edit Attendance'))
        {
            $attendanceEmployee = AttendanceEmployee::where('id', $id)->first();
            $employees          = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('attendance.copy', compact('attendanceEmployee', 'employees'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if(\Auth::user()->can('Edit Attendance'))
        {
            $attendanceEmployee = AttendanceEmployee::where('id', $id)->first();
            $employees          = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('attendance.edit', compact('attendanceEmployee', 'employees'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (!$request->ajax())
        {
              $clockOut = $request->input('clock_out');
              $clockIn = $request->input('clock_in');

            // Add leading zero if necessary (e.g., "0:00" -> "00:00")
            if (preg_match('/^\d:\d{2}$/', $clockOut)) {
                $clockOut = '0' . $clockOut; // Add leading zero
                $request->merge(['clock_out' => $clockOut]); // Update the request data
            }
            if (preg_match('/^\d:\d{2}$/', $clockIn)) {
                $clockIn = '0' . $clockIn; // Add leading zero
                $request->merge(['clock_in' => $clockIn]); // Update the request data
            }
            $validator = \Validator::make($request->all(), [
                'clock_in' => 'required|date_format:H:i', // 24-hour format
                'clock_out' => 'nullable|date_format:H:i', // 24-hour format
            ]);
            if($validator->fails())
            {
             $messages = $validator->getMessageBag();
             return redirect()->back()->with('error', $messages->first());
            }
        }

        $checkAttendance  = AttendanceEmployee::where('employee_id', $request->employee_id)
        ->where('date', $request->date)
        ->count();
        $startTime = Utility::getValByName('company_start_time');
        $endTime   = Utility::getValByName('company_end_time');
        if(Auth::user()->type == 'employee'){
            $employeeId      = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
            $todayAttendance = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('date', date('Y-m-d'))->OrderBy('clock_in', 'DESC')->limit(1)->first();
            if($todayAttendance && $todayAttendance->clock_out == '00:00:00')
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

                return response()->json(['success'], 200);
            }else{
                return response()->json(['error', 'message' => 'there is an error'], 400);
            }
        }
        else
        {
             $clock_in_time = $request->clock_in . ':00';
            if($request->clock_out && $request->clock_out != '00:00')
            {
                $clock_out_time = $request->clock_out . ':00';
                $clock_out_time = Carbon::createFromFormat('H:i:s', $clock_out_time);
                $clock_in_time = Carbon::createFromFormat('H:i:s', $clock_in_time);
                if($clock_out_time<$clock_in_time){
                    return redirect()->back()->with('error','Clock in must grater then clock out!');
                }
            }
            $bigClockOutAttendance = AttendanceEmployee::where([
                'employee_id' => $request->employee_id,
                'date' => $request->date
            ])
            ->orderBy('clock_out', 'desc')
            ->first();
            $smallClockInAttendance = AttendanceEmployee::where([
                'employee_id' => $request->employee_id,
                'date' => $request->date
            ])
            ->orderBy('clock_in', 'asc')
            ->first();
            $bigClockInAttendance = AttendanceEmployee::where([
                'employee_id' => $request->employee_id,
                'date' => $request->date
            ])
            ->orderBy('clock_in', 'desc')
            ->first();

            $attendanceExistQuery = AttendanceEmployee::query();
            $attendanceExistQuery->where('employee_id', $request->employee_id)
            ->where('date', $request->date);
            if(!$request->clock_out || $request->clock_out == '0:00')
            {
                if ($bigClockInAttendance->clock_out == '00:00:00' && $bigClockInAttendance->id != $id) {
                    return redirect()->back()->with('error', 'Clock out 00:00 already exists!');
                }

                $clockInTime = Carbon::parse($request->clock_in);
                $clockOutTimeBigClockOut = Carbon::parse($bigClockOutAttendance->clock_out);

                if ($clockOutTimeBigClockOut->greaterThan($clockInTime) && $bigClockOutAttendance->id != $id) {
                    return redirect()->back()->with('error', 'Employee Attendance Already Created.');
                }
                $attendanceExistQuery->where('clock_in', '<', $request->clock_in)
                     ->where('clock_out', '>', $request->clock_in);
            }
            else
            {
                $attendanceExistQuery->where('clock_in', '<', $request->clock_out)
                     ->where('clock_out', '>', $request->clock_in);
            }

            $attendanceExists = $attendanceExistQuery->first();
            if($attendanceExists && $attendanceExists->id != $id)
            {
                return redirect()->back()->with('error', __('Employee Attendance Already Created.'));
            }
            $date = $request->date;
            $employeeId = $request->employee_id;
            //late update
            $employee = Employee::find($request->employee_id); // Find employee by ID
            $shiftStart = $employee->shift_start; // Assuming shift_start is a timestamp or time column
            // Check if the provided $time is greater than the shift_start time
            if (strtotime($request->clock_in) > strtotime($shiftStart) && $checkAttendance < 2) {
                // Calculate late time in seconds (difference between $time and shift_start)
                $lateTimeInSeconds = strtotime($request->clock_in) - strtotime($shiftStart);

                // Convert late time into hours, minutes, and seconds
                $lateHours = floor($lateTimeInSeconds / 3600); // Calculate hours
                $lateMinutes = floor(($lateTimeInSeconds % 3600) / 60); // Calculate minutes
                $lateSeconds = $lateTimeInSeconds % 60; // Remaining seconds

                // Format the late time as HH:mm:ss
                $late = sprintf("%02d:%02d:%02d", $lateHours, $lateMinutes, $lateSeconds);


            } else {
                $late = '00:00:00';
            }
            //late
            // $totalLateSeconds = strtotime($request->clock_in) - strtotime($date . $startTime);

            // $hours = floor($totalLateSeconds / 3600);
            // $mins  = floor($totalLateSeconds / 60 % 60);
            // $secs  = floor($totalLateSeconds % 60);
            // $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

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

            $attendanceEmployee                = AttendanceEmployee::find($id);
            $attendanceEmployee->employee_id   = $request->employee_id;
            $attendanceEmployee->date          = $request->date;
            $attendanceEmployee->clock_in      = $request->clock_in;
            $attendanceEmployee->clock_out     = $request->clock_out;
            $attendanceEmployee->late          = $late;
            $attendanceEmployee->early_leaving = $earlyLeaving;
            $attendanceEmployee->overtime      = $overtime;
            $attendanceEmployee->total_rest    = Helper::TotalRestEdit($request->clock_in.':00', $employeeId, $date);

            $attendanceEmployee->save();

            return back()->with('success', 'Attendance updated successfully.');
            // return response()->json(['success'], 200);
        }
    }

    public function destroy($id)
    {
        if(\Auth::user()->can('Delete Attendance'))
        {
            $attendance = AttendanceEmployee::where('id', $id)->first();

            $attendance->delete();

            return redirect()->back()->with('success', __('Attendance successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function attendance(Request $request)
    {
        $settings = Utility::settings();

        if($settings['ip_restrict'] == 'on')
        {
            $userIp = request()->ip();
            $ip     = IpRestrict::where('created_by', \Auth::user()->creatorId())->whereIn('ip', [$userIp])->first();
            if(empty($ip))
            {
                return redirect()->back()->with('error', __('this ip is not allowed to clock in & clock out.'));
            }
        }


        // Restrict clock-in during break time (1:00 PM to 1:45 PM)
        $currentTime = Carbon::now();
        $breakStart = Carbon::parse('13:45:00');
        $breakEnd = Carbon::parse('14:30:00');

        // $breakStart = Carbon::parse('13:00:00');
        // $breakEnd = Carbon::parse('13:45:00');
        $empArray = ['0035', '0073'];
        $employeeCode      = !empty(\Auth::user()->employee) ? \Auth::user()->employee->empcode : 0;

        if ($currentTime->between($breakStart, $breakEnd) && !in_array($employeeCode, $empArray)) {
            // return response()->json(["🚫✈️ Oops! Clock-in denied! ✈️🚫 \n\n\"Ladies and Gentlemen, this is your Captain speaking! It appears we’re currently in the 'Break Zone,' and clocking in is temporarily grounded. Please enjoy your break, and we’ll be ready for take-off at 1:45PM when break time concludes!\" \n\n🕒 Thank you for your patience, and happy landing back to work soon!"], 403);
            return response()->json(["🚫✈️ Oops! Clock-in denied! ✈️🚫 \n\n\"Ladies and Gentlemen, this is your Captain speaking! It appears we’re currently in the 'Break Zone,' and clocking in is temporarily grounded. Please enjoy your break, and we’ll be ready for take-off at 2:30PM when break time concludes!\" \n\n🕒 Thank you for your patience, and happy landing back to work soon!"], 403);
        }


        $employeeId      = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
        $todayAttendance = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('date', date('Y-m-d'))->first();
        // if(empty($todayAttendance))
        // {

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
            $time = $request->time;
            //late
            $totalLateSeconds = time() - strtotime($date . $startTime);
            $hours            = floor($totalLateSeconds / 3600);
            $mins             = floor($totalLateSeconds / 60 % 60);
            $secs             = floor($totalLateSeconds % 60);
            $late             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

            $checkDb = AttendanceEmployee::with('employee')->where('employee_id', '=', \Auth::user()->id)->get()->toArray();

            if(empty($checkDb))
            {
                $employeeAttendance                = new AttendanceEmployee();
                $employeeAttendance->employee_id   = $employeeId;
                $employeeAttendance->employee_name   = \Auth::user()->name;
                $employeeAttendance->date          = $date;
                $employeeAttendance->status        = 'Present';
                $employeeAttendance->clock_in      = $time;
                $employeeAttendance->clock_out     = '00:00:00';
                $employeeAttendance->late          = $late;
                $employeeAttendance->early_leaving = '00:00:00';
                $employeeAttendance->overtime      = '00:00:00';
                $employeeAttendance->total_rest    = Helper::TotalRest($time, $employeeId);
                $employeeAttendance->created_by    = \Auth::user()->id;
                $employeeAttendance->save();

                $birthDate = Carbon::parse(Auth::user()->employee->dob);
                $isBirth = Auth::user()->employee->isBirthDay;

                $currentDate = Carbon::now();
                $isBirthday = $birthDate->format('m-d') === $currentDate->format('m-d') || $isBirth;

                $employee = Auth::user()->employee;
                $employee->isBirthDay = false;
                $employee->save();
                return response()->json(['success', 'is_birthday' => $isBirthday, 'late' => $late, 'totalRest' => $employeeAttendance->total_rest], 200);

                /*if ($birthDate->format('m-d') === $currentDate->format('m-d')) {
                    event(new EmployeeBirthday($employeeId));
                    Log::info("EmployeeBirthday Event is fired for employee ID: {$employeeId}");
                }
                return response()->json(['success'], 200);*/
            }
            foreach($checkDb as $check)
            {
                $employeeAttendance                = new AttendanceEmployee();
                $employeeAttendance->employee_id   = $employeeId;
                $employeeAttendance->employee_name   = \Auth::user()->name;
                $employeeAttendance->date          = $date;
                $employeeAttendance->status        = 'Present';
                $employeeAttendance->clock_in      = $time;
                $employeeAttendance->clock_out     = '00:00:00';
                $employeeAttendance->late          = $late;
                $employeeAttendance->early_leaving = '00:00:00';
                $employeeAttendance->overtime      = '00:00:00';
                $employeeAttendance->total_rest    = Helper::TotalRest($time, $employeeId);
                $employeeAttendance->created_by    = \Auth::user()->id;
                $employeeAttendance->save();

                $birthDate = Carbon::parse(Auth::user()->employee->dob);
                $currentDate = Carbon::now();

                $currentDate = Carbon::now();

                $isBirth = $birthDate->format('m-d') === $currentDate->format('m-d');

                if(!$isBirth){
                    $emp = \Auth::user()->getUSerEmployee(\Auth::user()->id);
                    $isBirth = $emp->isBirthday;
                }

                $employee = Auth::user()->employee;
                $employee->isBirthDay = false;
                $employee->save();
                // return response()->json(['success', 'is_birthday' => $isBirth, 'late' => $late, 'totalRest' => $employeeAttendance->total_rest], 200);
                return response()->json(['success', 'is_birthday' => 0, 'late' => $late, 'totalRest' => $employeeAttendance->total_rest], 200);

                // $isBirthday = $birthDate->format('m-d') === $currentDate->format('m-d');
                // return response()->json(['success', 'is_birthday' => $isBirthday], 200);

                /*if ($birthDate->format('m-d') === $currentDate->format('m-d')) {
                    event(new EmployeeBirthday($employeeId));
                    Log::info("EmployeeBirthday Event is fired for employee ID: {$employeeId}");
                }

                return response()->json(['success'], 200);*/

            }
        //  }
        // else
        // {
        //     return redirect()->back()->with('error', __('Employee are not allow multiple time clock in & clock for every day.'));
        // }
    }

    public function bulkAttendance(Request $request)
    {
        if(\Auth::user()->can('Create Attendance'))
        {

            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('Select Branch', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('Select Department', '');

            $employees = [];
            if(!empty($request->branch) && !empty($request->department))
            {
                $employees = Employee::where('created_by', \Auth::user()->creatorId())->where('branch_id', $request->branch)->where('department_id', $request->department)->get();
            }
            return view('attendance.bulk', compact('employees', 'branch', 'department'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bulkAttendanceData(Request $request)
    {

        if(\Auth::user()->can('Create Attendance'))
        {
            if(!empty($request->branch) && !empty($request->department))
            {
                $startTime = Utility::getValByName('company_start_time');
                $endTime   = Utility::getValByName('company_end_time');
                $date      = $request->date;

                $employees = $request->employee_id;
                $atte      = [];
                foreach($employees as $employee)
                {
                    $present = 'present-' . $employee;
                    $in      = 'in-' . $employee;
                    $out     = 'out-' . $employee;
                    $atte[]  = $present;
                    if($request->$present == 'on')
                    {

                        $in  = date("H:i:s", strtotime($request->$in));
                        $out = date("H:i:s", strtotime($request->$out));

                        $totalLateSeconds = strtotime($in) - strtotime($startTime);

                        $hours = floor($totalLateSeconds / 3600);
                        $mins  = floor($totalLateSeconds / 60 % 60);
                        $secs  = floor($totalLateSeconds % 60);
                        $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                        //early Leaving
                        $totalEarlyLeavingSeconds = strtotime($endTime) - strtotime($out);
                        $hours                    = floor($totalEarlyLeavingSeconds / 3600);
                        $mins                     = floor($totalEarlyLeavingSeconds / 60 % 60);
                        $secs                     = floor($totalEarlyLeavingSeconds % 60);
                        $earlyLeaving             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);


                        if(strtotime($out) > strtotime($endTime))
                        {
                            //Overtime
                            $totalOvertimeSeconds = strtotime($out) - strtotime($endTime);
                            $hours                = floor($totalOvertimeSeconds / 3600);
                            $mins                 = floor($totalOvertimeSeconds / 60 % 60);
                            $secs                 = floor($totalOvertimeSeconds % 60);
                            $overtime             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                        }
                        else
                        {
                            $overtime = '00:00:00';
                        }


                        $attendance = AttendanceEmployee::where('employee_id', '=', $employee)->where('date', '=', $request->date)->first();

                        if(!empty($attendance))
                        {
                            $employeeAttendance = $attendance;
                        }
                        else
                        {
                            $employeeAttendance              = new AttendanceEmployee();
                            $employeeAttendance->employee_id = $employee;
                            $employeeAttendance->created_by  = \Auth::user()->creatorId();
                        }


                        $employeeAttendance->date          = $request->date;
                        $employeeAttendance->status        = 'Present';
                        $employeeAttendance->clock_in      = $in;
                        $employeeAttendance->clock_out     = $out;
                        $employeeAttendance->late          = $late;
                        $employeeAttendance->early_leaving = ($earlyLeaving > 0) ? $earlyLeaving : '00:00:00';
                        $employeeAttendance->overtime      = $overtime;
                        $employeeAttendance->total_rest    = '00:00:00';
                        $employeeAttendance->save();

                    }
                    else
                    {
                        $attendance = AttendanceEmployee::where('employee_id', '=', $employee)->where('date', '=', $request->date)->first();

                        if(!empty($attendance))
                        {
                            $employeeAttendance = $attendance;
                        }
                        else
                        {
                            $employeeAttendance              = new AttendanceEmployee();
                            $employeeAttendance->employee_id = $employee;
                            $employeeAttendance->created_by  = \Auth::user()->creatorId();
                        }

                        $employeeAttendance->status        = 'Leave';
                        $employeeAttendance->date          = $request->date;
                        $employeeAttendance->clock_in      = '00:00:00';
                        $employeeAttendance->clock_out     = '00:00:00';
                        $employeeAttendance->late          = '00:00:00';
                        $employeeAttendance->early_leaving = '00:00:00';
                        $employeeAttendance->overtime      = '00:00:00';
                        $employeeAttendance->total_rest    = '00:00:00';
                        $employeeAttendance->save();
                    }
                }

                return redirect()->back()->with('success', __('Employee attendance successfully created.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Branch & department field required.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function currentTimeAttendance(){
        $employeeId = Auth::id();
        $attendance = AttendanceEmployee::select('attendance_employees.*')
                                ->join('employees', 'attendance_employees.employee_id', '=', 'employees.id')
                                ->where('employees.user_id', $employeeId)
                                ->where('attendance_employees.clock_out', '00:00:00')
                                ->orderBy('attendance_employees.id', 'desc')
                                ->first();
                                //Helper::pr($attendance);
        if ($attendance) {
            return response()->json(['clock_in' => Carbon::parse($attendance->clock_in)->toIso8601String(), 'attendance_id' => $attendance->id]);
        }

        return response()->json(['clock_in' => null]);
    }

}
