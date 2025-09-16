<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Utility;
use App\Helpers\Helper;
use App\Models\Employee;
use App\Models\Designation;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\AttendanceEmployee;
use App\Models\EmployeeDocument;
use App\Models\Department;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;

class OfficeController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Office')) {
            $offices = Office::where('created_by', '=', \Auth::user()->creatorId())->get();

            // Count employees and departments efficiently
            $employeeCounts = Employee::where('created_by', \Auth::user()->creatorId())
                ->where('is_active', 1)
                ->select('office_id', DB::raw('count(*) as count'))
                ->groupBy('office_id')
                ->pluck('count', 'office_id')
                ->toArray();

            // Get total metrics
            $totalEmployees = array_sum($employeeCounts);
            $totalDepartments = Department::where('created_by', \Auth::user()->creatorId())->count();
            $totalCities = $offices->pluck('city')->unique()->count();

            // Calculate average office attendance from AttendanceEmployee records for today
            $today = Carbon::today()->format('Y-m-d');
            $presentEmployees = AttendanceEmployee::whereDate('date', $today)
                ->whereIn('employee_id', function($query) {
                    $query->select('id')
                        ->from('employees')
                        ->where('created_by', \Auth::user()->creatorId())
                        ->where('is_active', 1);
                })
                ->distinct('employee_id')
                ->count();

            $attendancePercentage = $totalEmployees > 0 ? round(($presentEmployees / $totalEmployees) * 100) : 0;

            return view('office.index', compact('offices', 'totalEmployees', 'totalDepartments', 'totalCities', 'attendancePercentage'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('Create Office')) {
            return view('office.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Office')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:100',
                    'location' => 'required',
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'radius' => 'required|numeric',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $office             = new Office();
            $office->name       = $request->name;
            $office->location   = $request->location;
            $office->latitude   = $request->latitude;
            $office->longitude  = $request->longitude;
            $office->radius     = $request->radius;
            $office->address    = $request->address;
            $office->city       = $request->city;
            $office->state      = $request->state;
            $office->country    = $request->country;
            $office->zip_code   = $request->zip_code;
            $office->phone      = $request->phone;
            $office->email      = $request->email;
            $office->created_by = \Auth::user()->creatorId();
            $office->save();

            return redirect()->route('office.index')->with('success', __('Office successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        $office = Office::find($id);
        
        if (!$office) {
            return redirect()->route('office.index')->with('error', __('Office not found.'));
        }
        
        if ($office->created_by != \Auth::user()->creatorId()) {
            return redirect()->route('office.index')->with('error', __('Permission denied.'));
        }
    
        if (!\Auth::user()->can('Manage Office')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    
        // Get all employees of this office with single query
        $employees = Employee::where('office_id', $office->id)
            ->where('is_active', 1)
            ->get();
        $employeeCount = $employees->count();
        $employeeIds = $employees->pluck('id')->toArray();
    
        // Get current date and month/year for statistics
        $today = Carbon::today()->format('Y-m-d');
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        $daysInMonth = Carbon::now()->daysInMonth;
    
        // Get all attendance records for today grouped by employee_id (keeping all records per employee)
        $todayAttendanceData = AttendanceEmployee::whereIn('employee_id', $employeeIds)
            ->where('date', $today)
            ->orderBy('clock_in') // Order by clock_in time to ensure correct order
            ->get()
            ->groupBy('employee_id');
    
        // Count unique employees who have attendance records today
        $todayPresentEmployeeIds = $todayAttendanceData->keys()->toArray();
        $todayPresent = count($todayPresentEmployeeIds);
    
        // Check leave status for all employees at once and cache the results
        $leaveStatuses = [];
        foreach ($employeeIds as $empId) {
            $leaveStatuses[$empId] = Helper::checkLeaveWithTypes($today, $empId);
        }
    
        // Count today's late arrivals - an employee is considered late if their first clock-in was late
        $todayLate = 0;
        foreach ($todayAttendanceData as $employeeId => $records) {
            if ($records->count() > 0) {
                $firstRecord = $records->sortBy('clock_in')->first();
                if ($firstRecord->late !== '00:00:00' && $firstRecord->late !== '-' && $firstRecord->late !== null) {
                    $todayLate++;
                }
            }
        }
    
        // Count employees on leave
        $todayLeave = collect($leaveStatuses)->filter(function($status) {
            return $status === 'fullday Leave';
        })->count();
    
        // Calculate absentees
        $todayAbsent = $employeeCount - ($todayPresent + $todayLeave);
    
        // Calculate attendance rate correctly - only considering employees who should be present
        $attendanceEligibleCount = $employeeCount - $todayLeave;
        $attendanceRate = $attendanceEligibleCount > 0 
            ? round(($todayPresent / $attendanceEligibleCount) * 100) 
            : ($employeeCount > 0 ? 100 : 0); // If everyone is on leave, attendance rate is 100%
    
        // Get all departments in one query
        $departmentIds = $employees->pluck('department_id')->filter()->unique();
        $departments = Department::whereIn('id', $departmentIds)
            ->where('created_by', \Auth::user()->creatorId())
            ->get();
    
        // Get all department heads in one query
        $departmentHeads = Employee::whereIn('department_id', $departmentIds)
            ->where('is_head', 1)
            ->get()
            ->keyBy('department_id');
    
        // Calculate department statistics
        $departmentStats = [];
        $employeesByDept = $employees->groupBy('department_id');
    
        foreach ($departments as $department) {
            $deptEmployees = $employeesByDept->get($department->id, collect());
            $deptEmployeeCount = $deptEmployees->count();
            
            if ($deptEmployeeCount > 0) {
                $deptEmployeeIds = $deptEmployees->pluck('id')->toArray();
                
                // Calculate metrics for this department
                $deptPresentCount = 0;
                $deptLateCount = 0;
                
                // Count department attendance based on unique employees
                foreach ($deptEmployeeIds as $empId) {
                    if (isset($todayAttendanceData[$empId])) {
                        $deptPresentCount++;
                        
                        // Check if first clock-in was late
                        $firstRecord = $todayAttendanceData[$empId]->sortBy('clock_in')->first();
                        if ($firstRecord->late !== '00:00:00' && $firstRecord->late !== '-' && $firstRecord->late !== null) {
                            $deptLateCount++;
                        }
                    }
                }
                
                // Count department leaves
                $deptLeaveCount = 0;
                foreach ($deptEmployeeIds as $empId) {
                    if (isset($leaveStatuses[$empId]) && $leaveStatuses[$empId] === 'fullday Leave') {
                        $deptLeaveCount++;
                    }
                }
                
                $deptAbsentCount = $deptEmployeeCount - ($deptPresentCount + $deptLeaveCount);
                
                // Get department head
                $departmentHead = $departmentHeads->get($department->id);
                
                // Calculate department attendance rate properly
                $deptAttendanceEligible = $deptEmployeeCount - $deptLeaveCount;
                $deptPercentage = $deptAttendanceEligible > 0 
                    ? round(($deptPresentCount / $deptAttendanceEligible) * 100) 
                    : ($deptEmployeeCount > 0 ? 100 : 0);
                
                $departmentStats[] = [
                    'id' => $department->id,
                    'name' => $department->name,
                    'head' => $departmentHead ? $departmentHead->name : 'Not Assigned',
                    'head_avatar' => $departmentHead ? $departmentHead->avatar : null,
                    'total' => $deptEmployeeCount,
                    'present' => $deptPresentCount,
                    'late' => $deptLateCount,
                    'absent' => $deptAbsentCount,
                    'leave' => $deptLeaveCount,
                    'percentage' => $deptPercentage
                ];
            }
        }
    
        // Prepare today's attendance logs
        $designationIds = $employees->pluck('designation_id')->filter()->unique();
        $designations = Designation::whereIn('id', $designationIds)->get()->keyBy('id');
        $departmentsKeyed = Department::whereIn('id', $departmentIds)->get()->keyBy('id');
    
        $todayAttendanceLogs = [];
        foreach ($employees as $employee) {
            $attendanceRecords = $todayAttendanceData->get($employee->id, collect());
            
            if ($attendanceRecords->count() > 0) {
                // Get first clock-in of the day (earliest time)
                $firstRecord = $attendanceRecords->sortBy('clock_in')->first();
                $clockIn = $firstRecord->clock_in ? Carbon::parse($firstRecord->clock_in)->format('h:i A') : '--';
                
                // Get last clock-out of the day (latest time)
                $lastRecord = $attendanceRecords
                            ->sortByDesc('clock_in')
                            ->first();

                $clockOut = $lastRecord && $lastRecord->clock_out && $lastRecord->clock_out != "00:00:00" ? Carbon::parse($lastRecord->clock_out)->format('h:i A') : '--';
                
                // Calculate total hours worked
                $date = date("Y-m-d");
                $hoursWorked = AttendanceEmployee::orderBy('clock_in', 'desc')
                    ->where('employee_id', '=', !empty($employee) ? $employee->id : 0)
                    ->where('date', '=', $date)
                    ->get();
                
                /*// If both clock-in and clock-out exist, calculate total hours
                if ($firstRecord->clock_in && $lastRecord->clock_out) {
                    // This is a simple calculation from first in to last out
                    $startTime = Carbon::parse($firstRecord->clock_in);
                    $endTime = Carbon::parse($lastRecord->clock_out);
                    $diffInMinutes = $endTime->diffInMinutes($startTime);
                    
                    // Format the hours and minutes
                    $hours = floor($diffInMinutes / 60);
                    $minutes = $diffInMinutes % 60;
                    $hoursWorked = $hours . 'h ' . $minutes . 'm';
                }*/
                
                // Determine status
                $status = 'Present';
                // Check if first entry was late
                if ($firstRecord->late !== '00:00:00' && $firstRecord->late !== '-' && $firstRecord->late !== null) {
                    $status = 'Late';
                }
            } else {
                // No attendance records found
                $clockIn = '--';
                $clockOut = '--';
                $hoursWorked = '--';
                
                $leaveStatus = $leaveStatuses[$employee->id] ?? 0;
                $status = $leaveStatus === 'fullday Leave' ? 'On Leave' : 'Absent';
            }
            
            $department = $departmentsKeyed->get($employee->department_id);
            $designation = $designations->get($employee->designation_id);
            
            $todayAttendanceLogs[] = [
                'id' => $employee->id,
                'name' => $employee->name,
                'avatar' => $employee->avatar,
                'position' => $designation ? $designation->name : 'Not Assigned',
                'department' => $department ? $department->name : 'Not Assigned',
                'department_id' => $department ? $department->id : null,
                'date' => Carbon::parse($today)->format('M d, Y'),
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'status' => $status,
                'hours_worked' => $hoursWorked
            ];
        }
    
        // Calculate weekly statistics efficiently
        $weeklyStats = [];
        $workdayDates = [];
        
        // Get the last 7 work days
        $date = Carbon::today();
        while (count($workdayDates) < 7) {
            if ($date->isWeekday()) {
                $workdayDates[] = $date->format('Y-m-d');
            }
            $date->subDay();
        }
        
        // Get all attendance records for these dates in one query
        $weeklyAttendance = AttendanceEmployee::whereIn('employee_id', $employeeIds)
            ->whereIn('date', $workdayDates)
            ->select('employee_id', 'date', 'late', 'clock_in')
            ->get()
            ->groupBy('date');
                
        // Process each workday
        foreach ($workdayDates as $dateString) {
            $checkDate = Carbon::parse($dateString);
            
            // Get attendance data for this day
            $dayAttendanceRecords = $weeklyAttendance->get($dateString, collect());
            
            // Count unique employees present on this day
            $dayPresentCount = $dayAttendanceRecords->pluck('employee_id')->unique()->count();
            
            // Get late count - an employee is considered late if their first clock-in was late
            $lateEmployees = [];
            foreach ($dayAttendanceRecords->groupBy('employee_id') as $empId => $records) {
                $firstRecord = $records->sortBy('clock_in')->first();
                if ($firstRecord->late !== '00:00:00' && $firstRecord->late !== '-' && $firstRecord->late !== null) {
                    $lateEmployees[] = $empId;
                }
            }
            $dayLateCount = count($lateEmployees);
            
            // Get leave count
            $dayLeaveCount = 0;
            foreach ($employeeIds as $empId) {
                $leaveStatus = Helper::checkLeaveWithTypes($dateString, $empId);
                if ($leaveStatus === 'fullday Leave') {
                    $dayLeaveCount++;
                }
            }
            
            $dayAbsentCount = $employeeCount - ($dayPresentCount + $dayLeaveCount);
            
            // Calculate attendance rate properly
            $dayAttendanceEligible = $employeeCount - $dayLeaveCount;
            $dayAttendanceRate = $dayAttendanceEligible > 0 
                ? round(($dayPresentCount / $dayAttendanceEligible) * 100) 
                : ($employeeCount > 0 ? 100 : 0);
            
            $weeklyStats[] = [
                'day' => $checkDate->format('D'),
                'date' => $checkDate->format('M d'),
                'present' => $dayPresentCount,
                'late' => $dayLateCount,
                'absent' => $dayAbsentCount,
                'leave' => $dayLeaveCount,
                'attendance_rate' => $dayAttendanceRate
            ];
        }
        
        // Reverse to maintain chronological order
        $weeklyStats = array_reverse($weeklyStats);
    
        // Generate monthly statistics
        $monthlyStats = [];
        
        // Create a CarbonPeriod for the current month
        $period = CarbonPeriod::create(
            Carbon::create($currentYear, $currentMonth, 1),
            Carbon::create($currentYear, $currentMonth, $daysInMonth)
        );
        
        // Get all attendance data for this month
        $monthlyAttendance = AttendanceEmployee::whereIn('employee_id', $employeeIds)
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->select('employee_id', 'date')
            ->get()
            ->groupBy('date');
                
        foreach ($period as $date) {
            // Skip future dates
            if ($date->isFuture()) {
                continue;
            }
            
            $dateString = $date->format('Y-m-d');
            
            // Get present count - count unique employees present on this day
            $dayAttendance = $monthlyAttendance->get($dateString, collect());
            $dayPresentCount = $dayAttendance->pluck('employee_id')->unique()->count();
            
            // Get leave count - using cached results or querying as needed
            $dayLeaveCount = 0;
            foreach ($employeeIds as $empId) {
                $leaveStatus = Helper::checkLeaveWithTypes($dateString, $empId);
                if ($leaveStatus === 'fullday Leave') {
                    $dayLeaveCount++;
                }
            }
            
            $dayAbsentCount = $employeeCount - ($dayPresentCount + $dayLeaveCount);
            
            $monthlyStats[] = [
                'date' => $date->day,
                'present' => $dayPresentCount,
                'absent' => $dayAbsentCount,
                'leave' => $dayLeaveCount
            ];
        }
    
        // Calculate inside/outside office statistics
        $insideOffice = $todayPresent;
        $outsideOffice = $employeeCount - $todayPresent;
    
        // Calculate average working hours
        $avgWorkingHours = 0;
        $employeesWithFullAttendance = 0;
    
        foreach ($todayAttendanceData as $employeeId => $records) {
            if ($records->count() > 0) {
                $firstRecord = $records->sortBy('clock_in')->first();
                $lastRecord = $records->sortByDesc('clock_out')->first();
                
                if ($firstRecord->clock_in && $lastRecord && $lastRecord->clock_out) {
                    $clockIn = Carbon::parse($firstRecord->clock_in);
                    $clockOut = Carbon::parse($lastRecord->clock_out);
                    $minutesWorked = $clockOut->diffInMinutes($clockIn);
                    $avgWorkingHours += $minutesWorked;
                    $employeesWithFullAttendance++;
                }
            }
        }
    
        if ($employeesWithFullAttendance > 0) {
            $avgWorkingHours = round(($avgWorkingHours / $employeesWithFullAttendance) / 60, 1);
        }
        // dd($todayAttendanceLogs);
        return view('office.show', compact(
            'office',
            'employees',
            'employeeCount',
            'todayPresent',
            'todayLate',
            'todayAbsent',
            'todayLeave',
            'attendanceRate',
            'monthlyStats',
            'insideOffice',
            'outsideOffice',
            'departmentStats',
            'todayAttendanceLogs',
            'weeklyStats',
            'avgWorkingHours'
        ));
    }

    public function employee($employeeID)
    {
        // try {
            // Get employee data with eager loading to reduce queries
            $employee = Employee::with(['department', 'designation'])
                ->where('id', $employeeID)
                ->first();
                
// echo "<pre>";
// print_r($employee->department->name);
// die;
                
            $department = $employee->department;

            if (!$employee) {
                return redirect()->back()->with('error', __('Employee not found.'));
            }

            // Get office information
            $office = Office::find($employee->office_id);

            // Get department head efficiently 
            $departmentHead = Employee::where('department_id', $employee->department_id)
                ->where('is_head', config('constants.roles.department_head'))
                ->first();

            // Get attendance data
            $currentMonth = date('m');
            $currentYear = date('Y');
            $currentDay = date('d');

            // Fetch all attendance records for the month in a single query
            $attendances = AttendanceEmployee::where('employee_id', $employeeID)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->get();

            // Group attendance by date for easier processing
            $attendancesByDate = $attendances->groupBy('date');

            // Count working days up to current date (excluding weekends)
            $workingDaysCount = 0;
            for ($day = 1; $day <= $currentDay; $day++) {
                $date = $currentYear . '-' . $currentMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                $dayOfWeek = date('N', strtotime($date));
                
                // 6 and 7 represent Saturday and Sunday
                if ($dayOfWeek < 6) {
                    $workingDaysCount++;
                }
            }

            // Calculate attendance metrics
            $presentDays = $attendancesByDate->count();
            $lateDays = $attendances->where('status', 'late')->count();
            
            // Get leaves for the current month in a single query
            $leaves = Leave::where('employee_id', $employeeID)
                ->whereMonth('start_date', $currentMonth)
                ->whereYear('start_date', $currentYear)
                ->whereIn('status', ['Approve', 'Pending'])
                ->get();
                
            $leaveDays = 0;
            
            // Calculate leave days within the current month
            foreach ($leaves as $leave) {
                $startDate = Carbon::parse($leave->start_date);
                $endDate = Carbon::parse($leave->end_date);
                
                // Ensure dates are within current month
                $startDate = $startDate->month != $currentMonth ? Carbon::create($currentYear, $currentMonth, 1) : $startDate;
                $endDate = $endDate->month != $currentMonth ? Carbon::create($currentYear, $currentMonth, $daysInMonth) : $endDate;
                
                // Count only weekdays
                for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                    if ($date->isWeekday() && $date->day <= $currentDay) {
                        $leaveDays++;
                    }
                }
            }
            
            // Calculate absent days
            $absentDays = $workingDaysCount - ($presentDays + $leaveDays);
            $absentDays = max(0, $absentDays); // Ensure it's not negative
            
            // Calculate attendance rate - cap at 100%
            $attendanceRate = $workingDaysCount > 0 ? min(100, round(($presentDays / ($workingDaysCount - $leaveDays)) * 100)) : 0;
            
            // If all assigned days were leave days, set to 100%
            if ($workingDaysCount === $leaveDays) {
                $attendanceRate = 100;
            }

            // Calculate average check-in time
            $avgCheckinTime = $employee->shift_start ?? '09:00 AM';

            // Get the first clock-in for each day
            $dailyFirstClockIns = collect();
            foreach ($attendancesByDate as $date => $dayAttendances) {
                // Sort by clock_in time
                $sortedAttendances = $dayAttendances->sortBy('clock_in');
                
                // Get the first record with a valid clock_in
                $firstValidAttendance = $sortedAttendances->first(function($att) {
                    return !empty($att->clock_in);
                });
                
                if ($firstValidAttendance && !empty($firstValidAttendance->clock_in)) {
                    $dailyFirstClockIns->push($firstValidAttendance);
                }
            }

            // Calculate average from first clock-ins of each day
            if ($dailyFirstClockIns->count() > 0) {
                $totalMinutes = 0;
                
                foreach ($dailyFirstClockIns as $attendance) {
                    $time = Carbon::parse($attendance->clock_in);
                    $totalMinutes += ($time->hour * 60) + $time->minute;
                }
                
                $avgMinutes = $totalMinutes / $dailyFirstClockIns->count();
                $hours = floor($avgMinutes / 60);
                $minutes = round($avgMinutes % 60);
                $avgCheckinTime = sprintf(
                    '%d:%02d %s',
                    $hours > 12 ? $hours - 12 : ($hours == 0 ? 12 : $hours),
                    $minutes,
                    $hours >= 12 ? 'PM' : 'AM'
                );
            }

            // Calculate work experience
            $joiningDate = Carbon::parse($employee->company_doj ?? $employee->joining_date);
            $experience = round($joiningDate->diffInYears(Carbon::now()), 1);

            // Get recent attendance logs
            $recentAttendances = AttendanceEmployee::where('employee_id', $employeeID)
                ->orderBy('date', 'desc')
                ->orderBy('clock_in', 'desc')
                ->limit(7)
                ->get();

            // Get employee documents
            $documents = EmployeeDocument::where('employee_id', $employeeID)->get();

            // Get leave data efficiently
            $leaveTypesData = LeaveType::where('created_by', '=', \Auth::user()->creatorId())
                ->select('id', 'title', 'days')
                ->get();

            // Get approved and pending leaves in a single query
            $usedLeaves = Leave::where('employee_id', $employeeID)
                ->whereIn('status', ['Approve', 'Pending'])
                ->whereYear('start_date', $currentYear)
                ->select('leave_type_id', DB::raw('SUM(total_leave_days) as used_days'))
                ->groupBy('leave_type_id')
                ->pluck('used_days', 'leave_type_id')
                ->toArray();

            // Build leave data array
            $leaveData = [];
            foreach ($leaveTypesData as $leaveType) {
                $key = strtolower(str_replace(' ', '_', $leaveType->title));
                $usedDays = $usedLeaves[$leaveType->id] ?? 0;
                
                $leaveData[$key] = [
                    'used' => $usedDays,
                    'total' => $leaveType->days
                ];
            }

            // Empty placeholders for inactive features
            $locationHistory = [];
            $activities = [];

            return view('office.employee', compact(
                'employee',
                'department',
                'office',
                'departmentHead',
                'attendanceRate',
                'avgCheckinTime',
                'experience',
                'recentAttendances',
                'documents',
                'leaveData',
                'activities',
                'locationHistory',
                'presentDays',
                'lateDays',
                'absentDays',
                'leaveDays',
                'workingDaysCount'
            ));
        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', __('Something went wrong.'));
        // }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('Edit Office')) {
            $office = Office::find($id);

            if ($office->created_by == \Auth::user()->creatorId()) {
                return view('office.edit', compact('office'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('Edit Office')) {
            $office = Office::find($id);

            if ($office->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'location' => 'required',
                        'city' => 'required',
                        'country' => 'required',
                        'phone' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $office->name = $request->name;
                $office->location = $request->location;
                $office->address = $request->address;
                $office->city = $request->city;
                $office->state = $request->state;
                $office->country = $request->country;
                $office->zip_code = $request->zip_code;
                $office->phone = $request->phone;
                $office->email = $request->email;
                $office->latitude = $request->latitude;
                $office->longitude = $request->longitude;
                $office->radius = $request->radius;
                $office->save();

                return redirect()->route('office.index')->with('success', __('Office successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('Delete Office')) {
            $office = Office::find($id);

            if ($office->created_by == \Auth::user()->creatorId()) {
                // Begin transaction to safely delete related data
                DB::beginTransaction();
                
                try {
                    // Check for dependent relationships before deletion
                    $hasEmployees = Employee::where('office_id', $id)->exists();
                    
                    if ($hasEmployees) {
                        DB::rollBack();
                        return redirect()->back()->with('error', __('Cannot delete office with associated employees.'));
                    }
                    
                    $office->delete();
                    DB::commit();
                    
                    return redirect()->route('office.index')->with('success', __('Office successfully deleted.'));
                } catch (\Exception $e) {
                    DB::rollBack();
                    return redirect()->back()->with('error', __('An error occurred while deleting the office.'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Method to get attendance data for AJAX
    public function getAttendanceData(Request $request, Office $office)
    {
        if (!\Auth::user()->can('Manage Office')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $month = $request->month ?? Carbon::now()->format('m');
        $year = $request->year ?? Carbon::now()->format('Y');

        // Get employees for this office
        $employeeIds = Employee::where('office_id', $office->id)
            ->where('is_active', 1)
            ->pluck('id')
            ->toArray();
            
        $employeeCount = count($employeeIds);
        
        if ($employeeCount === 0) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        // Create date range
        $period = CarbonPeriod::create(
            Carbon::create($year, $month, 1),
            Carbon::create($year, $month, Carbon::create($year, $month, 1)->daysInMonth)
        );
        
        // Get all attendance data for the month in one query
        $attendanceData = AttendanceEmployee::whereIn('employee_id', $employeeIds)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->select('date', DB::raw('count(distinct employee_id) as present_count'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');
            
        // Get all leave data for the month in one query
        $leaveDates = [];
        foreach ($employeeIds as $empId) {
            $leaves = Leave::where('employee_id', $empId)
                ->where('status', 'Approve')
                ->where(function($query) use ($year, $month) {
                    $startOfMonth = Carbon::create($year, $month, 1)->format('Y-m-d');
                    $endOfMonth = Carbon::create($year, $month, Carbon::create($year, $month, 1)->daysInMonth)->format('Y-m-d');
                    
                    $query->where(function($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('start_date', '>=', $startOfMonth)
                          ->where('start_date', '<=', $endOfMonth);
                    })->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('end_date', '>=', $startOfMonth)
                          ->where('end_date', '<=', $endOfMonth);
                    })->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('start_date', '<', $startOfMonth)
                          ->where('end_date', '>', $endOfMonth);
                    });
                })
                ->get();
                
            foreach ($leaves as $leave) {
                $startDate = max(Carbon::parse($leave->start_date), Carbon::create($year, $month, 1));
                $endDate = min(Carbon::parse($leave->end_date), Carbon::create($year, $month, Carbon::create($year, $month, 1)->daysInMonth));
                
                for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                    $dateStr = $date->format('Y-m-d');
                    if (!isset($leaveDates[$dateStr])) {
                        $leaveDates[$dateStr] = 0;
                    }
                    $leaveDates[$dateStr]++;
                }
            }
        }

        $data = [];
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayPresent = isset($attendanceData[$dateStr]) ? $attendanceData[$dateStr]->present_count : 0;
            $dayLeave = isset($leaveDates[$dateStr]) ? $leaveDates[$dateStr] : 0;
            $dayAbsent = $employeeCount - ($dayPresent + $dayLeave);
            
            $data[] = [
                'date' => $date->day,
                'present' => $dayPresent,
                'late' => 0,
                'absent' => $dayAbsent < 0 ? 0 : $dayAbsent,
                'leave' => $dayLeave
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Method to get live employee locations
    public function getLiveLocations(Office $office)
    {
        if (!\Auth::user()->can('Manage Office')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $today = Carbon::today()->format('Y-m-d');

        // Get employee IDs for this office
        $employeeIds = Employee::where('office_id', $office->id)
            ->where('is_active', 1)
            ->pluck('id')
            ->toArray();
            
        // Get department info for all relevant departments in one query
        $departments = Department::whereIn('id', function($query) use ($employeeIds) {
            $query->select('department_id')
                ->from('employees')
                ->whereIn('id', $employeeIds)
                ->distinct();
        })->get()->keyBy('id');
        
        // Get all attendance records with location data
        $attendances = AttendanceEmployee::with(['employee'])
            ->whereIn('employee_id', $employeeIds)
            ->where('date', $today)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
            
        $attendanceData = $attendances->map(function ($attendance) use ($departments) {
            $departmentId = $attendance->employee->department_id ?? null;
            $departmentName = $departmentId && isset($departments[$departmentId]) ? 
                $departments[$departmentId]->name : '';
                
            return [
                'id' => $attendance->id,
                'employee_id' => $attendance->employee_id,
                'name' => $attendance->employee->name,
                'department' => $departmentName,
                'latitude' => $attendance->latitude,
                'longitude' => $attendance->longitude,
                'is_inside_office' => $attendance->is_inside_office ?? $this->isInsideOfficeRadius(
                    $attendance->latitude, 
                    $attendance->longitude, 
                    $office->latitude, 
                    $office->longitude, 
                    $office->radius
                ),
                'clock_in' => $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('h:i A') : null,
                'clock_out' => $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('h:i A') : null,
                'status' => $attendance->status ?? 'Present'
            ];
        });

        return response()->json([
            'success' => true,
            'office' => [
                'name' => $office->name,
                'latitude' => $office->latitude,
                'longitude' => $office->longitude,
                'radius' => $office->radius
            ],
            'employees' => $attendanceData
        ]);
    }
    
    /**
     * Helper function to calculate if a point is inside office radius
     * 
     * @param float $lat1 Employee latitude
     * @param float $lon1 Employee longitude
     * @param float $lat2 Office latitude
     * @param float $lon2 Office longitude
     * @param float $radius Office radius in meters
     * @return bool Whether employee is inside office radius
     */
    private function isInsideOfficeRadius($lat1, $lon1, $lat2, $lon2, $radius)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2 || !$radius) {
            return false;
        }
        
        // Earth's radius in meters
        $earthRadius = 6371000;
        
        // Convert latitude and longitude from degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        
        // Haversine formula
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        return $distance <= $radius;
    }
}