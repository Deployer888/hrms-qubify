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
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Illuminate\Support\Facades\Session;
use Exception;

class AttendanceEmployeeController extends Controller
{
    protected $batchId;
    
    private $compareUrl = 'https://api-us.faceplusplus.com/facepp/v3/compare';
    private $apiKey = 'MiuU8GJhmm5TaSaDxgJ_bbHewGUmx79k';
    private $apiSecret = 'zW48QNkIv_0H5M5-AdBp5JGQm9aSDdMd';

    public function __construct(){
        $this->batchId = now()->timestamp;
    }

    private function updateAttendance($emp_name,$emp_id,$date,$time)
    {
        $empAttendance = AttendanceEmployee::whereNotNull('clock_in')
        ->where('employee_id', $emp_id)
        ->where('date', $date)
        ->where(function ($query) {
            $query->whereNull('clock_out')
                ->orWhere('clock_out', '00:00:00');
        })
        ->first();

        if($empAttendance){
            $empAttendance->clock_out = $time;
            $empAttendance->save();
            return response()->json([
                'status' => true,
                'message' => 'Clock out successfully',
                'time'=> date("h:i:s A", strtotime($time)),
            ]);
        }else{
            try {
                $attendance = new AttendanceEmployee();
                $attendance->employee_name = $emp_name;
                $attendance->employee_id = $emp_id;
                $attendance->date = $date;
                $attendance->status = "Present";
                $attendance->clock_in = $time;
                $attendance->save();
            } catch (\Throwable $th) {
                throw $th;
            }          
            return response()->json([
                'status' => true,
                'message' => 'Clock in successfully',
                'time'=> date("h:i:s A", strtotime($time)),
            ]);
        }
        return response()->json([
            'status' => false,
            'message'=>'Something went wrong.'
        ]);
    }

    private function compareFaces($image1Base64, $image2Base64)
    {
        try {
            $response = Http::asForm()->post($this->compareUrl, [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'image_base64_1' => $image1Base64,
                'image_base64_2' => $image2Base64,
            ]);

            $result = $response->json();

            if (isset($result['error_message'])) {
                throw new Exception($result['error_message']);
            }

            $confidence = $result['confidence'] ?? 0;
            
            return [
                'success' => true,
                'confidence' => $confidence
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Face comparison failed: ' . $e->getMessage(),
                'confidence' => 0
            ];
        }
    }

    private function getBase64($image_code)
    {
        $image_parts = explode(";base64,", $image_code);
        if (count($image_parts) !== 2) {
            throw new \Exception('Invalid Base64 string format.');
        }
        return $image_parts[1];

    }

    private function processBase64Image($base64String)
    {
        
        try {
            // Create new manager instance with GD driver
            $manager = new ImageManager(new Driver());

            // Process the image
            $image = $manager->read($base64String);

            // Fit the image to 224x224
            $image->cover(224, 224);

            return $image;
        } catch (\Exception $e) {
            throw new \Exception('Failed to process image: ' . $e->getMessage());
        }
    }

    public function attendanceAadhaarVerification(Request $request)
    {
        $this->checkLocationInRange(30.7117041673891,76.80893456650769);
        
        try {
            $request->validate([
                'image' => 'required|string'
            ]);

            $base64Image = $this->getBase64($request->image);
            $capturedImage = $this->processBase64Image($base64Image);

            $imageName = 'captured_' . time() . '.jpg';
            $imagePath = 'public/captured/' . $imageName; // Save in storage/app/public/captured/

            Storage::put($imagePath, $capturedImage->toJpeg()->toString());

            $publicPath = '/storage/captured/' . $imageName;

            $capturedBase64Jpeg = base64_encode($capturedImage->toJpeg()->toString());

            $highestConfidence = 0;
            $bestMatch = null;

            // Get the authenticated user's photo encoding if available
            // $userPhotoEncoded = Auth::user()->base64_image ?? null;
            // if (!$userPhotoEncoded) {
            //     return false;
            // }
            $aadhaar_detail = AadhaarDetail::where('employee_id',Auth::user()->employee->id)->first();

            $userPhotoEncoded = $aadhaar_detail->photo_encoded;
            if (!$userPhotoEncoded) {
                return false;
            }

            $userPhotoEncodedBase64 = $this->processBase64Image($userPhotoEncoded);
            $userPhotoEncodedBase64Jpeg = base64_encode($userPhotoEncodedBase64->toJpeg()->toString());

            $result = $this->compareFaces($capturedBase64Jpeg, $userPhotoEncodedBase64Jpeg);
            try {
                if ($result['success'] && $result['confidence']>80) {
                    $emp = Employee::where('user_id',Auth::user()->id)->first();
                    $date = Carbon::now()->format('Y-m-d');    // e.g. "2025-04-14"
                    $time = Carbon::now()->format('H:i:s');    // e.g. "13:45:22"
                    if (!$emp) {
                        return false;
                    }
                    $att = $this->updateAttendance($emp->name, $emp->id, $date, $time);
                    if (!$att) {
                        return response()->json([
                            'status' => false,
                        ]);
                    }
                    return response()->json([
                        'status' => true,
                        'data' => $att,
                    ]);
                }
                else {
                    return response()->json([
                        'success' => false,
                        'flag' => 1,
                        'message' => "User not matched!",
                    ]);
                }
            } catch (\Throwable $th) {
               throw $th;
            }

            // Compare with each employee's photo



            

        } catch (Exception $e) {
            \Log::error('Face authentication error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Authentication error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function attendanceAadhaar()
    {
        return view('attendance.attendance');
    }
    
    public function index(Request $request)
    {
        // Initialize variables
        $start_date = '';
        $end_date = '';
        $employees = [];
        $leaveDays = [];
        $attendanceData = [];
        $datesDescending = [];
        $attendanceEmployee = [];
        $currentDate = Carbon::now()->toDateString();
        $holidays = false;
        $isWeekend = false;
        $isHoliday = false;

        if (!\Auth::user()->can('Manage Attendance')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        // Handle AJAX requests for dynamic filtering
        if ($request->ajax()) {
            if ($request->has('branch_id')) {
                $branchId = $request->input('branch_id');
                
                if ($branchId) {
                    $departments = Department::where('branch_id', $branchId)
                        ->where('created_by', \Auth::user()->creatorId())
                        ->select('id', 'name')
                        ->get();
                        
                    $employees = Employee::where('branch_id', $branchId)
                        ->where('is_active', 1)
                        ->select('id', 'name')
                        ->get();
                } else {
                    $departments = Department::where('created_by', \Auth::user()->creatorId())
                        ->select('id', 'name')
                        ->get();
                        
                    $employees = Employee::where('is_active', 1)
                        ->select('id', 'name')
                        ->get();
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'departments' => $departments,
                        'employees' => $employees
                    ]
                ]);
            }

            if ($request->has('department_id')) {
                $departmentId = $request->input('department_id');
                
                if ($departmentId) {
                    $employees = Employee::where('department_id', $departmentId)
                        ->where('is_active', 1)
                        ->select('id', 'name')
                        ->get();
                } else {
                    $employees = Employee::where('is_active', 1)
                        ->select('id', 'name')
                        ->get();
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'employees' => $employees
                    ]
                ]);
            }
        }

        // Get filter parameters
        $date = $request->input('date');
        $month = $request->input('month');
        $employeeId = $request->input('employee');
        $branchId = $request->input('branch');
        $departmentId = $request->input('department');
        $requestType = $request->input('type', 'daily');

        // Prepare dropdown data with proper filtering
        $branchQuery = Branch::where('created_by', \Auth::user()->creatorId());
        $branch = $branchQuery->pluck('name', 'id')->prepend('All Branch', '');

        $departmentQuery = Department::where('created_by', \Auth::user()->creatorId());
        if ($branchId) {
            $departmentQuery->where('branch_id', $branchId);
        }
        $department = $departmentQuery->pluck('name', 'id')->prepend('All Department', '');

        // Get employees with filters applied
        $employeeQuery = Employee::where('is_active', 1);
        if ($branchId) {
            $employeeQuery->where('branch_id', $branchId);
        }
        if ($departmentId) {
            $employeeQuery->where('department_id', $departmentId);
        }
        $employees = $employeeQuery->select('id', 'name')->get();

        $holidays = Holiday::pluck('date')->toArray();

        if (\Auth::user()->type == 'employee') {
            // Employee view logic
            $employeeId = \Auth::user()->employee->id;
            $attendances = collect([]);
            
            if ($requestType == 'daily') {
                $targetDate = $date ?: $currentDate;
                
                $attendanceWithEmployee = Employee::where('is_active', 1)
                    ->where('id', $employeeId)
                    ->with(['user', 'attendance' => function ($subQuery) use ($targetDate) {
                        $subQuery->whereDate('date', $targetDate)
                            ->orderBy('clock_in', 'ASC');
                    }])
                    ->first();

                $attendances = $attendanceWithEmployee ? $attendanceWithEmployee->attendance : collect([]);

                // Check holiday, weekend, and leave status
                $isHoliday = Holiday::where('date', $targetDate)->first();
                $isWeekend = Carbon::parse($targetDate)->isWeekend();
                $isLeave = Helper::checkLeave($targetDate, \Auth::user()->employee->id);
                $empLeave = Helper::getEmpLeave($targetDate, \Auth::user()->employee->id);

                return view('attendance.employee.index', [
                    'attendanceEmployee' => $attendances,
                    'attendanceWithEmployee' => $attendanceWithEmployee,
                    'monthAttendanceEmployee' => [],
                    'employees' => $employees,
                    'branch' => $branch,
                    'department' => $department,
                    'date' => $date,
                    'month' => $month,
                    'employee' => $employeeId,
                    'holidays' => $holidays,
                    'isWeekend' => $isWeekend,
                    'isHoliday' => $isHoliday,
                    'isLeave' => $isLeave,
                    'empLeave' => $empLeave,
                    'dateList' => [],
                    'requestType' => $requestType,
                ]);

            } else {
                // Monthly attendance for employee
                $targetMonth = $month ?: Carbon::now()->format('Y-m');
                $dateList = Helper::getDateList($targetMonth);
                $dateList = array_reverse($dateList);
                $monthAttendance = [];

                foreach ($dateList as $dateData) {
                    $attendancesData = AttendanceEmployee::with('employee')
                        ->whereDate('date', $dateData)
                        ->where('employee_id', $employeeId)
                        ->orderBy('clock_in', 'ASC')
                        ->get();

                    $data = [];
                    $data['hours'] = Helper::calculateTotalTimeDifference($attendancesData);
                    $data['attendance'] = $attendancesData->toArray();
                    $data['is_weekend'] = Carbon::parse($dateData)->isWeekend();
                    
                    $isLeave = Helper::checkLeave($dateData, \Auth::user()->employee->id);
                    $empLeave = Helper::getEmpLeave($dateData, \Auth::user()->employee->id);
                    $data['leave_detail'] = $empLeave;
                    $data['is_leave'] = $isLeave;
                    $data['is_holiday'] = Holiday::where('date', $dateData)->first();
                    
                    $minHours = '08:00';
                    if ($isLeave && $empLeave) {
                        if ($empLeave['leavetype'] == 'half') {
                            $minHours = '04:00';
                        }
                        if ($empLeave['leavetype'] == 'short') {
                            $minHours = '06:00';
                        }
                    }
                    $data['min_hours'] = $minHours;
                    $monthAttendance[$dateData] = $data;
                }

                return view('attendance.employee.index', [
                    'attendanceEmployee' => collect([]),
                    'attendanceWithEmployee' => null,
                    'monthAttendanceEmployee' => $monthAttendance,
                    'employees' => $employees,
                    'branch' => $branch,
                    'department' => $department,
                    'date' => $date,
                    'month' => $month,
                    'employee' => $employeeId,
                    'holidays' => $holidays,
                    'isWeekend' => false,
                    'isHoliday' => false,
                    'isLeave' => false,
                    'empLeave' => null,
                    'dateList' => $dateList,
                    'requestType' => $requestType,
                ]);
            }

        } else {
            // Admin/Manager view logic with comprehensive filtering
            $targetDate = $date ?: $currentDate;
            
            // Build optimized query with all filters
            $query = Employee::where('is_active', 1);

            // Apply branch filter
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            // Apply department filter  
            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }

            // Apply specific employee filter
            if ($employeeId) {
                $query->where('id', $employeeId);
            }

            if ($requestType == 'monthly' && $month) {
                // Monthly view - get employees and handle attendance by month range
                $startDate = Carbon::parse($month . '-01')->startOfMonth();
                $endDate = Carbon::parse($month . '-01')->endOfMonth();
                
                $attendanceWithEmployee = $query->with(['user', 'attendance' => function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->whereBetween('date', [$startDate, $endDate])
                        ->orderBy('date', 'DESC') // Sort by date descending (newest first)
                        ->orderBy('clock_in', 'ASC');
                }])
                ->select('id', 'name', 'user_id', 'branch_id', 'department_id', 'shift_start')
                ->orderBy('name', 'ASC') // Sort employees by name
                ->get();
                    
            } else {
                // Daily view - get employees with attendance for specific date
                $attendanceWithEmployee = $query->with(['user', 'attendance' => function ($subQuery) use ($targetDate) {
                    $subQuery->whereDate('date', $targetDate)
                        ->orderBy('clock_in', 'ASC');
                }])
                ->select('id', 'name', 'user_id', 'branch_id', 'department_id', 'shift_start')
                ->orderBy('name', 'ASC') // Sort employees by name
                ->get();
            }

            // Check if target date is holiday or weekend
            if ($date) {
                $holidays = Holiday::where('date', $date)->exists();
                $isWeekend = Carbon::parse($date)->isWeekend();
            } else {
                $holidays = Holiday::where('date', $currentDate)->exists();
                $isWeekend = Carbon::parse($currentDate)->isWeekend();
            }

            return view('attendance.index', [
                'attendanceEmployee' => [],
                'attendanceWithEmployee' => $attendanceWithEmployee,
                'employees' => $employees,
                'branch' => $branch,
                'department' => $department,
                'date' => $date,
                'month' => $month,
                'employee' => $employeeId,
                'holidays' => $holidays,
                'isWeekend' => $isWeekend,
                'requestType' => $requestType,
            ]);
        }
    }

    /*public function create()
    {
        if(\Auth::user()->can('Create Attendance'))
        {
            $employees = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', "employee")->get()->pluck('name', 'id');

            return view('attendance.create', compact('employees'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }


    }*/
    
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

        $clock_in_time = $request->clock_in;
        if($request->clock_out && $request->clock_out != '00:00')
        {
            $clock_out_time = $request->clock_out;
            $clock_out_time = Carbon::createFromFormat('H:i', $clock_out_time);
            $clock_in_time = Carbon::createFromFormat('H:i', $clock_in_time);
            if($clock_out_time<$clock_in_time){
                return redirect()->back()->with('error','Clock-in must be greater than clock-out!');
            }
        }
        else{
            $clock_out_time = '00:00';
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
        if(!$request->clock_out || $request->clock_out == '00:00')
        {
            if($bigClockOutAttendance && $bigClockInAttendance->clock_out == '00:00')
            {
                return redirect()->back()->with('error','Clock out 00:00 already exists!');
            }
            if ($bigClockOutAttendance && $bigClockOutAttendance->clock_out>$request->clock_in) {
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
                $late             = '00:00:00';
            }
            else{
                // $late             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
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
            $employeeAttendance->clock_in      = $request->clock_in;
            $employeeAttendance->clock_out     = $request->clock_out;
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

        if(Auth::user()->type == 'employee') {
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

                $attendanceEmployee = AttendanceEmployee::find($todayAttendance->id);
                // $attendanceEmployee->clock_out     = $time;
                // $attendanceEmployee->early_leaving = $earlyLeaving;
                // $attendanceEmployee->overtime      = $overtime;
                // $attendanceEmployee->save();

                if ($time > $attendanceEmployee->clock_in) {
                    $attendanceEmployee->clock_out     = $time;
                    $attendanceEmployee->early_leaving = $earlyLeaving;
                    $attendanceEmployee->overtime      = $overtime;
                    $attendanceEmployee->save();
                } else {
                    throw new \Exception('Clock out time cannot be earlier than or equal to clock in time');
                }

                return response()->json(['success'], 200);
            }else{
                return response()->json(['error', 'message' => 'there is an error'], 400);
            }
        }
        else {
             $clock_in_time = $request->clock_in;
            if($request->clock_out && $request->clock_out != '00:00')
            {
                $clock_out_time = $request->clock_out;
                $clock_out_time = Carbon::createFromFormat('H:i', $clock_out_time);
                $clock_in_time = Carbon::createFromFormat('H:i', $clock_in_time);
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
                if ($bigClockInAttendance->clock_out == '00:00' && $bigClockInAttendance->id != $id) {
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

            $attendanceEmployee = AttendanceEmployee::find($id);
            if ($request->clock_out && $request->clock_in) {
                if ($request->clock_out <= $request->clock_in && $request->clock_out != "00:00") {
                    throw new \Exception('Clock out time must be after clock in time');
                    exit();
                }
            }

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

            if($attendance) {
                $attendance->delete();
                
                // Handle AJAX requests
                if(request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => __('Attendance successfully deleted.')
                    ]);
                }
                
                return redirect()->back()->with('success', __('Attendance successfully deleted.'));
            } else {
                // Handle AJAX requests
                if(request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Attendance record not found.')
                    ], 404);
                }
                
                return redirect()->back()->with('error', __('Attendance record not found.'));
            }
        }
        else
        {
            // Handle AJAX requests
            if(request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Permission denied.')
                ], 403);
            }
            
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
    
    public function excelImport(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'clock_in_file' => 'required|file|mimes:xlsx,xls',
            'clock_out_file' => 'required|file|mimes:xlsx,xls',
        ]);
 
        $spemp = [9,11];
 
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
 
        $clock_in_data = Excel::toArray([], $request->file('clock_in_file'));
        $clock_out_data = Excel::toArray([], $request->file('clock_out_file'));
 
        $dates = $this->attendanceDate($clock_in_data[2]??[]);
        $clock_in_array = $this->attendanceArray($clock_in_data[2]??[]);
        $clock_out_array = $this->attendanceTime($this->attendanceArray($clock_out_data[2]??[]),$dates);
        $clock_in_time_array =  $this->attendanceTime($clock_in_array,$dates);
        $attendances=[];
        foreach ($clock_in_time_array as $key => $clock_in) {
            $searchEmployee = $clock_in['employee_code'];
            $searchDate = $clock_in['date'];
            $clock_out = array_values(array_filter($clock_out_array, function ($record) use ($searchEmployee, $searchDate) {
                 return $record['employee_code'] == $searchEmployee && $record['date'] == $searchDate;
            }));
            if (count($clock_in['time']) > 0)
            {
                $clockInCount = count($clock_in['time']);
                foreach ($clock_in['time'] as $i=>$inTime) {
                    $nextI = $i+1;
                    $nearestClockOut = $this->nearestEqualOrFutureTime($clock_out[0]['time']??[],$inTime);
                    $data = [];
 
                    if ($nextI < $clockInCount  && $nearestClockOut>$clock_in['time'][$i+1])
                    {
                        $data['employee_code'] = $clock_in['employee_code'];
                        $data['date'] = $clock_in['date'];
                        $data['clock_in'] = $inTime;
                        $data['clock_out'] = $inTime;
                    }
                    else {
                        $data['employee_code'] = $clock_in['employee_code'];
                        $data['date'] = $clock_in['date'];
                        $data['clock_in'] = $inTime;
                        $data['clock_out'] = $nearestClockOut;
                    }
                    if (in_array($clock_in['employee_code'],$spemp)) {
                        if ($inTime <= '11:00') {
                            Session::put('early_clock_in', $clock_in['employee_code']);
                        }
                        $earlySpClockIn = Session::get('early_clock_in');
                        if ($earlySpClockIn != $clock_in['employee_code']) {
                            $attendances[] = $data;
                            continue;
                        }
                    }
                    $earlySpClockIn = Session::get('early_clock_in');
 
                    if ($earlySpClockIn != $clock_in['employee_code']) {
                        Session::forget('early_clock_in');
                    }
 
 
                    if ($this->isTimeBetween($data['clock_in']) && $this->isTimeBetween($data['clock_out'])) {
                        continue;
                    }
                    if ($this->isTimeBetween($data['clock_in']) && !$this->isTimeBetween($data['clock_out'])) {
                        $data['clock_in'] = '14:30';
                    }
                    if (!$this->isTimeBetween($data['clock_in']) && $this->isTimeBetween($data['clock_out']))
                    {
                        $data['clock_out'] = '13:45';
                    }
                    if ($this->isTimeLunchBetween($data['clock_in'],$data['clock_out'])) {
                        $old_clock_out = $data['clock_out'] ;
                        $data['clock_out'] = '13:45';
                        $attendances[] = $data;
                        $data['clock_in'] = '14:30';
                        $data['clock_out'] = $old_clock_out;
                        $attendances[] = $data;
                        continue;
                    }
                    $attendances[] = $data;
                }
            }
        }
        $data = [];
        DB::beginTransaction();
        try {
            foreach ($attendances as $att) {
                $emps = Helper::officeTwoEmps();
                $empId = Helper::empIdWithEmpCode($att['employee_code']);
                if (!is_null($empId) && in_array($empId, $emps)) {
                    $checkAtt = AttendanceEmployee::where([
                        'employee_id' => $empId,
                        'date' => $att['date'],
                        'clock_in' => $att['clock_in'],
                        'clock_out' => $att['clock_out'] ?: '00:00:00',
                    ])->exists();
                    if (!$checkAtt) {
                        $data = [
                            'batch_id' => $this->batchId,
                            'employee_id' => $empId,
                            'employee_name' => Helper::empNameWithEmpCode($att['employee_code']),
                            'date' => $att['date'],
                            'status' => "Present",
                            'clock_in' => $att['clock_in'],
                            'clock_out' => $att['clock_out'] ?: '00:00:00',
                        ];
                        AttendanceEmployee::insertOrIgnore($data);
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', __('Someting went wrong.'));
        }
        return redirect()->back()->with('success', __('Employee importeted.'));
    }
    
    private function isTimeBetween($time)
    {
        try {
            $time = Carbon::createFromFormat('H:i', $time);
            $start = Carbon::createFromFormat('H:i:s', '13:45:00');
            $end = Carbon::createFromFormat('H:i:s', '14:30:00');
 
            return $time->between($start, $end);
        } catch (\Throwable $th) {
            return false;
        }
    }
    private function isTimeLunchBetween($from,$to)
    {
        try {
            $fromTime = Carbon::createFromFormat('H:i', $from);
            $toTime = Carbon::createFromFormat('H:i', $to);
 
            $lunchStart = Carbon::createFromFormat('H:i', '13:45');
            $lunchEnd = Carbon::createFromFormat('H:i', '14:30');
 
            // Overlap condition: (start1 < end2) && (start2 < end1)
            return $fromTime < $lunchEnd && $toTime > $lunchStart;
        } catch (\Throwable $th) {
            return false;
        }
    }
 
    public function rollbackAttendanceImport(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'batch_id' => 'required',
        ]);
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        
        AttendanceEmployee::where('batch_id', $request->batch_id)->delete();
        return back()->with('success', 'Import rolled back successfully.');
    }
 
    private function attendanceDate($atteArry = [])
    {
        $input = $atteArry[2][2];
        // Extract year and month using regex
        preg_match('/(\d{4})\/(\d{2})/', $input, $matches);
 
        $year = $matches[1] ?? null;
        $month = $matches[2] ?? null;
 
        $days = [];
        foreach ($atteArry ?? [] as $key => $value) {
            if ($key == 3) {
                $days = $value;
            }
        }
        $dates = array_map(function($day) use ($year, $month) {
            return sprintf("%s-%s-%02d", $year, $month, $day);
        }, $days);
        return $dates;
    }
 
    private function attendanceArray($atteArry = [])
    {
        $attendance_time = [];
        foreach ($atteArry ?? [] as $key => $value) {
            if ($key>3) {
                if ($key%2 == 0) {
                    $data = [];
                    $attendance = [];
                    foreach ($value as $k=>$val) {
                        if (!is_null($val)) {
                            $data[] = $val;
                        }
                    }
                    $user = [];
                    foreach ($data as $i => $v) {
                        if ($i%2 != 0) {
                            $user[$data[$i-1]] = $v;
                        }
                    }
                    $attendance['user'] = $user;
                }
                else {
                    $attendance['time'] = $value;
                    $attendance_time[]['attendance'] = $attendance;
                }
            }
        }
        return $attendance_time;
    }
 
    private function attendanceTime($clockInArr = [],$dates)
    {
        $emp_atted = [];
        foreach ($clockInArr as $key => $att) {
            foreach ($att['attendance']['time'] as $k=> $time) {
                $data = [];
                $data['employee_code'] = $att['attendance']['user']['No :'];
                $data['date'] = $dates[$k];
                                // Convert multiline string to array
                $timeArray = array_filter(array_map('trim', explode("\n", $time)));
 
                // Remove duplicates
                $uniqueTimes = array_values(array_unique($timeArray));
                foreach ($uniqueTimes as $o => $time) {
                    # code...
                }
                $data['time'] = $uniqueTimes;
                $emp_atted[] = $data;
            }
        }
        return $emp_atted;
    }
 
    private function nearestEqualOrFutureTime(array $times = [], string $referenceTime)
    {
        $referenceTimestamp = strtotime($referenceTime);
 
        $nearestTime = null;
        $smallestDiff = PHP_INT_MAX;
 
        foreach ($times as $time) {
            $timeTimestamp = strtotime($time);
            $currentDiff = $timeTimestamp - $referenceTimestamp;
 
            if ($currentDiff >= 0 && $currentDiff < $smallestDiff) {
                $smallestDiff = $currentDiff;
                $nearestTime = $time;
 
                if ($currentDiff === 0) {
                    return $time;
                }
            }
        }
 
        return $nearestTime;
    } 

}
