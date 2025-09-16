<?php
namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Department;
use App\Models\AttendanceEmployee;
use App\Models\Leave;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;

class DashController extends Controller
{
    // Updated Controller Function
    public function index()
    {
        $user = Auth::user();
        $roles = ['employee', 'super admin'];
        if(!in_array($user->type, $roles)) {
            $creatorId = $user->creatorId();

            $offices = Office::where('created_by', $creatorId)->get();
            $departments = Department::where('created_by', $creatorId)->get();
            $branches = \App\Models\Branch::where('created_by', $creatorId)->get();

            $startDate = Carbon::now()->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');

            // Overall counters
            $totalPresentCount = 0;
            $totalAbsentCount = 0;
            $totalLateCount = 0;
            $totalLeaveCount = 0;
            $totalFullLeave = 0;
            $totalHalfLeave = 0;
            $totalShortLeave = 0;
            $totalEmployees = 0;

            // Office-specific data
            $officesData = [];
            $weeklyTrendData = [];

            // Get the current date
            $currentDate = Carbon::now();

            // Find the last 5 workdays (excluding weekends)
            $workdayCount = 0;
            $dayCounter = 0;

            while ($workdayCount < 5) { // We need 5 workdays
                // Go back one day at a time
                $checkDate = Carbon::now()->subDays($dayCounter);
                $dayOfWeek = $checkDate->dayOfWeek;
                
                // Skip weekends (0 = Sunday, 6 = Saturday)
                if ($dayOfWeek !== 0 && $dayOfWeek !== 6) {
                    // Format the date (e.g., "Apr 29")
                    $formattedDate = $checkDate->format('M d');
                    
                    // Add to the beginning of the array to maintain chronological order
                    array_unshift($weeklyTrendData, [
                        'day' => $formattedDate,  // Using formatted date instead of day name
                        'date' => $checkDate->format('Y-m-d'),  // Keep full date for database queries
                        'present' => 0,
                        'absent' => 0,
                        'late' => 0,
                        'leave' => 0
                    ]);
                    
                    $workdayCount++;
                }
                
                $dayCounter++;
            }

            $employees = Employee::where('created_by', $creatorId)
                                ->where('is_active', 1)
                                ->get();
            
            $totalEmployees = $employees->whereNotNull('office_id')->count();

            foreach ($offices as $office) {
                $officeEmployees = $employees->where('office_id', $office->id);
                $officeEmployeeCount = $officeEmployees->count();
                
                // Reset counts for each office
                $officePresent = 0;
                $officeAbsent = 0;
                $officeLate = 0;
                $officeLeave = 0;
                
                // Generate weekly data for each office based on actual attendance
                $officeWeeklyData = [];
                
                // Loop through each day in our weekly trend data
                foreach ($weeklyTrendData as $dayIndex => $dayData) {
                    $dayDate = $dayData['date'];
                    
                    // Get attendance data for this office's employees on this day
                    $dayStats = $this->getAttendanceDataForDate($officeEmployees, $dayDate);
                    
                    // Store this day's data for this office
                    $officeWeeklyData[] = [
                        'day' => $dayData['day'],  // This is the formatted date (e.g., "Apr 29")
                        'present' => $dayStats['present'],
                        'absent' => $dayStats['absent'],
                        'late' => $dayStats['late'],
                        'leave' => $dayStats['leave']
                    ];
                    
                    // Add to overall weekly trend data
                    $weeklyTrendData[$dayIndex]['present'] += $dayStats['present'];
                    $weeklyTrendData[$dayIndex]['absent'] += $dayStats['absent'];
                    $weeklyTrendData[$dayIndex]['late'] += $dayStats['late'];
                    $weeklyTrendData[$dayIndex]['leave'] += $dayStats['leave'];
                }
                
                // Calculate leave type breakdown using new method
                $leaveBreakdown = $this->calculateLeaveTypeBreakdown($officeEmployees, $startDate);
                $officeFullLeave = $leaveBreakdown['full_leave'];
                $officeHalfLeave = $leaveBreakdown['half_leave'];
                $officeShortLeave = $leaveBreakdown['short_leave'];
                $officeLeave = $leaveBreakdown['total_leave'];

                // Add to total counters
                $totalFullLeave += $officeFullLeave;
                $totalHalfLeave += $officeHalfLeave;
                $totalShortLeave += $officeShortLeave;
                $totalLeaveCount += $officeLeave;

                // Process current day attendance data for this office
                foreach ($officeEmployees as $employee) {
                    $leaveStatus = Helper::checkLeaveWithTypes($startDate, $employee->id);

                    // Consider any attendance record as present
                    $attendanceExists = AttendanceEmployee::where('employee_id', $employee->id)
                                                        ->where('date', $startDate)
                                                        ->exists();
                                            
                    if ($attendanceExists) {
                        $officePresent++;
                        $totalPresentCount++;

                        $checkFirstRecord = AttendanceEmployee::where('employee_id', $employee->id)
                                                    ->where('date', $startDate)
                                                    ->orderBy('id', 'asc')
                                                    ->first();
                        
                        $wasLate = false;

                        if ($checkFirstRecord && $checkFirstRecord->late !== '00:00:00' && 
                            $checkFirstRecord->late !== null && 
                            strpos($checkFirstRecord->late, '-') === false) {
                            $wasLate = true;
                        }
                        
                        if ($wasLate) {
                            $officeLate++;
                            $totalLateCount++;
                        }
                    } else {
                        if ($leaveStatus == 0) {
                            $officeAbsent++;
                            $totalAbsentCount++;
                        }
                    }
                }

                // Calculate attendance rate for this office
                $attendanceRate = $officeEmployeeCount > 0
                                ? round(($officePresent / $officeEmployeeCount) * 100)
                                : 0;

                // Store department and branch info for filtering
                $officeDepartments = DB::table('employees')
                                    ->where('office_id', $office->id)
                                    ->where('created_by', $creatorId)
                                    ->where('is_active', 1)
                                    ->pluck('department_id')
                                    ->unique()
                                    ->toArray();
                                    
                $officeBranches = DB::table('employees')
                                    ->where('office_id', $office->id)
                                    ->where('created_by', $creatorId)
                                    ->where('is_active', 1)
                                    ->pluck('branch_id')
                                    ->unique()
                                    ->toArray();

                // Store office-specific data
                $officesData[$office->id] = [
                    'id' => $office->id,
                    'name' => $office->name,
                    'total' => $officeEmployeeCount, 
                    'present' => $officePresent,
                    'absent' => $officeAbsent,
                    'late' => $officeLate,
                    'on_leave' => $officeLeave,
                    'full_leave' => $officeFullLeave,
                    'half_leave' => $officeHalfLeave,
                    'short_leave' => $officeShortLeave,
                    'attendance_rate' => $attendanceRate,
                    'weeklyData' => $officeWeeklyData,
                    'departments' => $officeDepartments,
                    'branches' => $officeBranches
                ];
            }
            
            // Now that we've processed all offices, we can remove the 'date' field from weeklyTrendData
            foreach ($weeklyTrendData as &$dayData) {
                unset($dayData['date']);
            }

            // Calculate overall attendance rate
            $overallAttendanceRate = $totalEmployees > 0 ? round(($totalPresentCount/$totalEmployees) * 100) : 0;

            // Calculate dynamic percentages for each status
            $presentPercent = $totalEmployees > 0 ? round(($totalPresentCount / $totalEmployees) * 100, 1) : 0;
            $absentPercent = $totalEmployees > 0 ? round(($totalAbsentCount / $totalEmployees) * 100, 1) : 0;
            // Late percentage should be calculated against total employees, not just present employees
            $latePercent = $totalEmployees > 0 ? round(($totalLateCount / $totalEmployees) * 100, 1) : 0;
            $leavePercent = $totalEmployees > 0 ? round(($totalLeaveCount / $totalEmployees) * 100, 1) : 0;

            // Debug information - remove this after testing
            \Log::info('Dashboard Debug Info', [
                'totalEmployees' => $totalEmployees,
                'totalPresentCount' => $totalPresentCount,
                'totalAbsentCount' => $totalAbsentCount,
                'totalLateCount' => $totalLateCount,
                'totalLeaveCount' => $totalLeaveCount,
                'presentPercent' => $presentPercent,
                'absentPercent' => $absentPercent,
                'latePercent' => $latePercent,
                'leavePercent' => $leavePercent
            ]);

            // Set overall office data for summary display
            $officeData = [
                'total' => $totalEmployees,
                'present' => $totalPresentCount,
                'absent' => $totalAbsentCount,
                'late' => $totalLateCount,
                'on_leave' => $totalLeaveCount,
                'full_leave' => $totalFullLeave,
                'half_leave' => $totalHalfLeave,
                'short_leave' => $totalShortLeave,
                'attendance_rate' => $overallAttendanceRate,
                'present_percent' => $presentPercent,
                'absent_percent' => $absentPercent,
                'late_percent' => $latePercent,
                'leave_percent' => $leavePercent
            ];
            
            /* $notClockIn    = AttendanceEmployee::where('date', '=', date('Y-m-d', strtotime($currentDate)))->get()->pluck('employee_id');
            $notClockIns = Employee::where('created_by', '=', \Auth::user()->creatorId())->where('is_active', 1)->whereNotIn('id', $notClockIn)->get();

            foreach($notClockIns as $notClock){
                $date = date("Y-m-d");
                $leaveType = Helper::checkLeaveWithTypes($date, $notClock->id);

                if($leaveType == 0){
                    $notClock['status'] = 'Absent';
                    $notClock['class'] = 'absent-btn';
                }else if($leaveType == 'morning halfday'){
                    $notClock['status'] = '1st Half Leave';
                    $notClock['class'] = 'badge badge-warning leave-btn';
                }else if($leaveType == 'afternoon halfday'){
                    $notClock['status'] = '2nd Half Leave';
                    $notClock['class'] = 'badge badge-warning leave-btn';
                }else if($leaveType == 'fullday Leave'){
                    $notClock['status'] = 'Leave';
                    $notClock['class'] = 'badge badge-warning leave-btn';
                // }
                }else if($leaveType == 'on short leave'){
                    $notClock['status'] = 'Short Leave';
                    $notClock['class'] = 'badge badge-warning leave-btn';
                }
            } */

            // Get all employees
            $allEmployees = Employee::where('created_by', '=', \Auth::user()->creatorId())
                                ->where('is_active', 1)
                                ->get();

            // Get attendance records for the date
            $attendanceRecords = AttendanceEmployee::where('date', '=', date('Y-m-d', strtotime($currentDate)))
                                    ->get()
                                    ->pluck('employee_id')
                                    ->toArray();

            $employeeList = [];

            foreach($allEmployees as $employee){
                $date = date("Y-m-d", strtotime($currentDate));
                
                // Check if employee has clocked in
                $hasAttendance = in_array($employee->id, $attendanceRecords);
                
                if($hasAttendance) {
                    // Employee is present
                    $employee['status'] = 'Present';
                    $employee['class'] = 'badge badge-success present-btn';
                } else {
                    // Check leave status
                    $leaveType = Helper::checkLeaveWithTypes($date, $employee->id);
                    
                    if($leaveType == 0){
                        $employee['status'] = 'Absent';
                        $employee['class'] = 'badge badge-danger absent-btn';
                    } else if($leaveType == 'Present along with Half-Day Leave'){
                        $employee['status'] = 'Half-Day Leave';
                        $employee['class'] = 'badge badge-warning leave-btn';
                    } else if($leaveType == 'Full Day Leave'){
                        $employee['status'] = 'Full Day Leave';
                        $employee['class'] = 'badge badge-warning leave-btn';
                    } else if($leaveType == 'Present along with Short Leave'){
                        $employee['status'] = 'Short Leave';
                        $employee['class'] = 'badge badge-warning leave-btn';
                    }
                }
                
                $employeeList[] = $employee;
            }

            $notClockIns = $employeeList;

            return view('dashboard.dashboard-new', compact(
                'offices',
                'departments',
                'branches',
                'startDate',
                'endDate',
                'totalEmployees',
                'officeData',
                'officesData',
                'weeklyTrendData',
                'notClockIns'
            ));
        }
        else {
            return back();
        }
    }

    private function getAttendanceDataForDate($employees, $date)
    {
        $present = 0;
        $absent = 0;
        $late = 0;
        $leave = 0;
        
        foreach ($employees as $employee) {
            // Check if employee was on leave that day
            $leaveStatus = Helper::checkLeaveWithTypes($date, $employee->id);
            
            if ($leaveStatus === 'Full Day Leave') {
                $leave++;
                continue;
            }
            
            // Check if employee was present that day
            $attendanceExists = AttendanceEmployee::where('employee_id', $employee->id)
                                                ->where('date', $date)
                                                ->exists();
            
            if ($attendanceExists) {
                $present++;
                
                // Check if employee was late that day
                $checkFirstRecord = AttendanceEmployee::where('employee_id', $employee->id)
                                                    ->where('date', $date)
                                                    ->orderBy('id', 'asc')
                                                    ->first();
                
                $wasLate = false;
                if ($checkFirstRecord && $checkFirstRecord->late !== '00:00:00' && strpos($checkFirstRecord->late, '-') === false) {
                    $wasLate = true;
                    $late++;
                }
            } else {
                if ($leaveStatus == 0) {
                    $absent++;
                }
            }
        }
        
        return [
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'leave' => $leave
        ];
    }

    /**
     * Calculate leave type breakdown for given employees and date
     * Uses the existing Helper method to ensure consistency
     * 
     * @param \Illuminate\Database\Eloquent\Collection $employees
     * @param string $date
     * @return array
     */
    private function calculateLeaveTypeBreakdown($employees, $date)
    {
        try {
            // Validate inputs
            if (!$employees || !is_object($employees) || !method_exists($employees, 'pluck')) {
                \Log::warning('Invalid employees collection provided to calculateLeaveTypeBreakdown');
                return $this->getEmptyLeaveBreakdown();
            }

            if (!$date || !$this->isValidDate($date)) {
                \Log::warning('Invalid date provided to calculateLeaveTypeBreakdown: ' . $date);
                return $this->getEmptyLeaveBreakdown();
            }

            $fullLeave = 0;
            $halfLeave = 0;
            $shortLeave = 0;

            // Use the existing Helper method to ensure consistency
            foreach ($employees as $employee) {
                try {
                    if (!$employee || !isset($employee->id) || !is_numeric($employee->id)) {
                        continue;
                    }

                    $leaveStatus = Helper::checkLeaveWithTypes($date, $employee->id);
                    
                    if ($leaveStatus === 'Full Day Leave') {
                        $fullLeave++;
                    } elseif ($leaveStatus === 'Present along with Half-Day Leave') {
                        $halfLeave++;
                    } elseif ($leaveStatus === 'Present along with Short Leave') {
                        $shortLeave++;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error processing employee ' . ($employee->id ?? 'unknown') . ': ' . $e->getMessage());
                    continue;
                }
            }

            $totalLeave = $fullLeave + $halfLeave + $shortLeave;

            return [
                'full_leave' => $fullLeave,
                'half_leave' => $halfLeave,
                'short_leave' => $shortLeave,
                'total_leave' => $totalLeave
            ];

        } catch (\Exception $e) {
            // Log error and return zero counts as fallback
            \Log::error('Error calculating leave type breakdown: ' . $e->getMessage(), [
                'employees_count' => $employees ? $employees->count() : 'null',
                'date' => $date,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->getEmptyLeaveBreakdown();
        }
    }



    /**
     * Get empty leave breakdown array
     * 
     * @return array
     */
    private function getEmptyLeaveBreakdown()
    {
        return [
            'full_leave' => 0,
            'half_leave' => 0,
            'short_leave' => 0,
            'total_leave' => 0
        ];
    }

    /**
     * Validate if a date string is valid
     * 
     * @param string $date
     * @return bool
     */
    private function isValidDate($date)
    {
        if (!is_string($date)) {
            return false;
        }

        try {
            $parsedDate = Carbon::parse($date);
            return $parsedDate->format('Y-m-d') === $date;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get filtered dashboard data via AJAX for office-specific filtering
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilteredData(Request $request)
    {
        try {
            // Validate user authentication and authorization
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $roles = ['employee', 'super admin'];
            if (in_array($user->type, $roles)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Validate creator ID
            $creatorId = $user->creatorId();
            if (!$creatorId || !is_numeric($creatorId)) {
                \Log::error('Invalid creator ID for user: ' . $user->id);
                return response()->json(['error' => 'Invalid user data'], 400);
            }

            // Validate and sanitize input
            $officeId = $request->input('office', 'all');
            if ($officeId !== 'all' && (!is_numeric($officeId) || $officeId <= 0)) {
                return response()->json(['error' => 'Invalid office ID'], 400);
            }

            // Validate date
            $startDate = Carbon::now()->format('Y-m-d');
            if (!$this->isValidDate($startDate)) {
                \Log::error('Invalid date generated: ' . $startDate);
                return response()->json(['error' => 'Invalid date'], 500);
            }

            // Verify office exists if specific office is requested
            if ($officeId !== 'all') {
                $officeExists = Office::where('id', $officeId)
                                    ->where('created_by', $creatorId)
                                    ->exists();
                
                if (!$officeExists) {
                    return response()->json(['error' => 'Office not found'], 404);
                }
            }

            // Get employees based on office filter with validation
            $employeeQuery = Employee::where('created_by', $creatorId)
                                   ->where('is_active', 1);
            
            if ($officeId !== 'all') {
                $employeeQuery->where('office_id', $officeId);
            }
            
            $employees = $employeeQuery->get();
            
            // Validate employees collection
            if (!$employees || !is_object($employees)) {
                \Log::error('Failed to retrieve employees for creator: ' . $creatorId);
                return response()->json(['error' => 'Failed to retrieve employee data'], 500);
            }

            // Calculate leave breakdown using new method with error handling
            $leaveBreakdown = $this->calculateLeaveTypeBreakdown($employees, $startDate);
            
            // Initialize counters with validation
            $totalPresentCount = 0;
            $totalAbsentCount = 0;
            $totalLateCount = 0;
            $totalEmployees = $employees->count();

            // Process each employee with error handling
            foreach ($employees as $employee) {
                try {
                    // Validate employee object
                    if (!$employee || !isset($employee->id) || !is_numeric($employee->id)) {
                        \Log::warning('Invalid employee object encountered');
                        continue;
                    }

                    $leaveStatus = Helper::checkLeaveWithTypes($startDate, $employee->id);

                    // Skip employees on full day leave for present/absent calculation
                    if ($leaveStatus === 'Full Day Leave') {
                        continue;
                    }

                    $attendanceExists = AttendanceEmployee::where('employee_id', $employee->id)
                                                        ->where('date', $startDate)
                                                        ->exists();
                                            
                    if ($attendanceExists) {
                        $totalPresentCount++;

                        // Check for late arrival with error handling
                        try {
                            $checkFirstRecord = AttendanceEmployee::where('employee_id', $employee->id)
                                                        ->where('date', $startDate)
                                                        ->orderBy('id', 'asc')
                                                        ->first();
                            
                            if ($checkFirstRecord && 
                                $checkFirstRecord->late !== '00:00:00' && 
                                $checkFirstRecord->late !== null && 
                                strpos($checkFirstRecord->late, '-') === false) {
                                $totalLateCount++;
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Error checking late status for employee ' . $employee->id . ': ' . $e->getMessage());
                        }
                    } else {
                        if ($leaveStatus == 0) {
                            $totalAbsentCount++;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error processing employee ' . ($employee->id ?? 'unknown') . ': ' . $e->getMessage());
                    continue;
                }
            }

            // Calculate percentages with division by zero protection
            $presentPercent = $totalEmployees > 0 ? round(($totalPresentCount / $totalEmployees) * 100, 1) : 0;
            $absentPercent = $totalEmployees > 0 ? round(($totalAbsentCount / $totalEmployees) * 100, 1) : 0;
            $latePercent = $totalEmployees > 0 ? round(($totalLateCount / $totalEmployees) * 100, 1) : 0;
            $leavePercent = $totalEmployees > 0 ? round(($leaveBreakdown['total_leave'] / $totalEmployees) * 100, 1) : 0;

            // Validate calculated values
            $responseData = [
                'success' => true,
                'data' => [
                    'total' => max(0, $totalEmployees),
                    'present' => max(0, $totalPresentCount),
                    'absent' => max(0, $totalAbsentCount),
                    'late' => max(0, $totalLateCount),
                    'on_leave' => max(0, $leaveBreakdown['total_leave']),
                    'full_leave' => max(0, $leaveBreakdown['full_leave']),
                    'half_leave' => max(0, $leaveBreakdown['half_leave']),
                    'short_leave' => max(0, $leaveBreakdown['short_leave']),
                    'present_percent' => max(0, min(100, $presentPercent)),
                    'absent_percent' => max(0, min(100, $absentPercent)),
                    'late_percent' => max(0, min(100, $latePercent)),
                    'leave_percent' => max(0, min(100, $leavePercent)),
                    'office_id' => $officeId
                ]
            ];

            return response()->json($responseData);

        } catch (\Exception $e) {
            \Log::error('Error in getFilteredData: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'office_id' => $request->input('office'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while fetching data'
            ], 500);
        }
    }













    /* public function index()
    {
        $user = Auth::user();
        $creatorId = $user->creatorId();

        $offices = Office::where('created_by', $creatorId)->get();
        $departments = Department::where('created_by', $creatorId)->get();
        $branches = \App\Models\Branch::where('created_by', $creatorId)->get();

        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');

        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $leaveCount = 0;
        $officeData = [];

        $employees = Employee::where('created_by', $creatorId)
                            ->where('is_active', 1)
                            ->get();

        $present = 0;
        $absent = 0;
        $late = 0;
        $onLeave = 0;
        foreach ($offices as $key => $office) {
            $officeEmployees = $employees->where('office_id', $office->id);

            foreach ($officeEmployees as $employee) {
                $leaveStatus = Helper::checkLeaveWithTypes($startDate, $employee->id);

                // If on full-day leave
                if ($leaveStatus === 'fullday Leave') {
                    $onLeave++;
                    $leaveCount++;
                    continue;
                }

                // Consider any attendance record as present
                $attendanceExists = AttendanceEmployee::where('employee_id', $employee->id)
                                                    ->where('date', $startDate)
                                                    ->exists();
                                        
                if ($attendanceExists) {
                    $present++;
                    $presentCount++;

                    $wasLate = AttendanceEmployee::where('employee_id', $employee->id)
                                                ->where('date', $startDate)
                                                ->where('late', '!=', '00:00:00')
                                                ->exists();

                    if ($wasLate) {
                        $late++;
                        $lateCount++;
                    }
                } else {
                    if ($leaveStatus == 0) {
                        $absent++;
                        $absentCount++;
                    }
                }
            }


            // echo "<pre>";
            // print_r($presentCount);
            // die;


            $officeData = [
                'name' => $office->name,
                'total' => $officeEmployees->count(),
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'on_leave' => $leaveCount,
                'attendance_rate' => $officeEmployees->count() > 0
                                    ? round(($presentCount / $officeEmployees->count()) * 100)
                                    : 0
            ];
        }

        $totalEmployees = $employees->count();
        $attendanceRate = $totalEmployees > 0
                        ? round(($presentCount / $totalEmployees) * 100)
                        : 0;

        return view('dashboard.dashboard-new', compact(
            'offices',
            'departments',
            'branches',
            'startDate',
            'endDate',
            'totalEmployees',
            'presentCount',
            'absentCount',
            'lateCount',
            'leaveCount',
            'officeData'
        ));
    } */
    
    /**
     * Get office attendance data for the last 7 days, including today
     * Includes separate counts for present, absent, late, and leave
     */
    /* private function getOfficeWeeklyAttendanceData($creatorId)
    {
        // Calculate date range for exactly one week, including today
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6); // 6 days back + today = 7 days total
        
        // Format dates for display and queries
        $formattedEndDate = $endDate->format('Y-m-d');
        $formattedStartDate = $startDate->format('Y-m-d');
        
        // Create date range for the last 7 days, including today
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $dates = collect($dateRange)->map(fn($date) => $date->format('Y-m-d'))->all();
        
        // Log the dates to verify we have 7 days
        \Log::info('Weekly date range: ' . count($dates) . ' days', [
            'start' => $formattedStartDate, 
            'end' => $formattedEndDate, 
            'dates' => $dates
        ]);
        
        // Get all offices
        $offices = Office::where('created_by', $creatorId)->get();
        $officeData = [];
        
        foreach ($offices as $office) {
            // Get all active employees for this office
            $employeeIds = Employee::where('created_by', $creatorId)
                ->where('office_id', $office->id)
                ->where('is_active', 1)
                ->pluck('id')
                ->toArray();
            
            if (empty($employeeIds)) {
                continue;
            }
            
            // Initialize data structure for this office
            $officeAttendanceData = [
                'name' => $office->name,
                'employeeCount' => count($employeeIds),
                'dates' => [],
                'weeklyTrend' => []
            ];
            
            // Process each day in the range
            foreach ($dates as $date) {
                $dateObj = Carbon::parse($date);
                $formattedDate = $dateObj->format('M d'); // e.g., "Apr 16"
                
                // Initialize counters for this day
                $presentCount = 0;
                $absentCount = 0;
                $lateCount = 0;
                $leaveCount = 0;
                
                // Check attendance for each employee on this day
                foreach ($employeeIds as $employeeId) {
                    // Check if employee is on leave
                    $leaveStatus = Helper::checkLeaveWithTypes($date, $employeeId);
                    
                    if ($leaveStatus == 'fullday Leave') {
                        $leaveCount++;
                        continue;
                    }
                    
                    // Skip weekends for absence count
                    $dayOfWeek = $dateObj->dayOfWeek;
                    if ($dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY) {
                        continue;
                    }
                    
                    // Check attendance
                    $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                        ->where('date', $date)
                        ->first();
                    
                    if ($attendance) {
                        $presentCount++;
                        
                        // Check if employee was late
                        if ($attendance->late != '00:00:00') {
                            $lateCount++;
                        }
                    } else {
                        // Only count as absent if not on leave and not a weekend
                        if ($leaveStatus == 0) {
                            $absentCount++;
                        }
                    }
                }
                
                // Add data for this day
                $officeAttendanceData['dates'][] = [
                    'date' => $formattedDate,
                    'presentCount' => $presentCount,
                    'absentCount' => $absentCount,
                    'lateCount' => $lateCount,
                    'leaveCount' => $leaveCount
                ];
                
                // Calculate percentage for trend data
                $totalExpected = count($employeeIds);
                if ($dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY) {
                    $totalExpected = 0; // No expected attendance on weekends
                }
                
                $percentage = $totalExpected > 0 ? 
                    round(($presentCount / $totalExpected) * 100, 2) : 0;
                
                // Add to weekly trend data
                $officeAttendanceData['weeklyTrend'][] = [
                    'date' => $formattedDate,
                    'percentage' => $percentage
                ];
            }
            
            // Verify we have 7 days of data
            if (count($officeAttendanceData['dates']) !== 7) {
                \Log::warning('Office ' . $office->id . ' has ' . count($officeAttendanceData['dates']) . ' days of data instead of 7');
            }
            
            $officeData[$office->id] = $officeAttendanceData;
        }
        
        return [
            'startDate' => $formattedStartDate,
            'endDate' => $formattedEndDate,
            'officeData' => $officeData
        ];
    } */
    
    /* public function getDashboardData($creatorId, $office = 'all', $department = 'all', $branch = 'all', $startDate = null, $endDate = null)
    {
        // Keep existing code...
        if (!$startDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
        }
        
        // Base employee query
        $employeeQuery = Employee::where('created_by', $creatorId)
                                ->where('is_active', 1); // Only include active employees
        
        // Apply filters
        if ($office != 'all') {
            $employeeQuery->where('office_id', $office);
        }
        if ($department != 'all') {
            $employeeQuery->where('department_id', $department);
        }
        if ($branch != 'all') {
            $employeeQuery->where('branch_id', $branch);
        }
        
        $employees = $employeeQuery->get();
        $employeeIds = $employees->pluck('id')->toArray();
        
        // Generate date ranges for the report
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $dates = collect($dateRange)->map(fn ($date) => $date->format('Y-m-d'))->all();
        $totalDays = count($dates);
        $workingDays = $this->getWorkingDays($startDate, $endDate);
        
        // Initialize counters
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $leaveCount = 0;
        
        // Process each employee's attendance for the date range
        foreach ($employeeIds as $employeeId) {
            foreach ($dates as $date) {
                // Check if employee is on leave
                $leaveStatus = Helper::checkLeaveWithTypes($date, $employeeId);
                
                if ($leaveStatus == 'fullday Leave') {
                    $leaveCount++;
                    continue;
                }
                
                // Skip weekends when counting absences
                $dayOfWeek = Carbon::parse($date)->dayOfWeek;
                if ($dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY) {
                    continue;
                }
                
                // Check attendance
                $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                    ->where('date', $date)
                    ->first();
                
                if ($attendance) {
                    $presentCount++;
                    
                    // Check if employee was late
                    if ($attendance->late != '00:00:00') {
                        $lateCount++;
                    }
                } else {
                    // Only count as absent if not on leave and not a weekend
                    if ($leaveStatus == 0) {
                        $absentCount++;
                    }
                }
            }
        }
        
        // Calculate attendance percentage more accurately
        $totalExpectedAttendance = count($employeeIds) * $workingDays;
        $attendancePercentage = $totalExpectedAttendance > 0 ? 
            round(($presentCount / $totalExpectedAttendance) * 100, 2) : 0;
        
        // Weekly attendance trend
        // $weeklyTrend = $this->getDailyAttendanceTrend($employeeIds, $startDate, $endDate);
        // Replace weekly trend calculation with fixed 7-day trend
        $weeklyTrend = $this->getLast7DaysAttendanceTrend($employeeIds);
        
        // Get 7-day detailed data for each office
        $weeklyOfficeData = $this->getOfficeWeeklyAttendanceData($creatorId);
        
        // Office-specific data
        $officeData = $this->getOfficeSpecificData($creatorId, $startDate, $endDate, $dates, $workingDays);
        
        // Merge weekly data into office data
        foreach ($officeData as $officeId => $data) {
            if (isset($weeklyOfficeData['officeData'][$officeId])) {
                $officeData[$officeId]['weeklyAttendanceData'] = $weeklyOfficeData['officeData'][$officeId]['dates'];
                // Keep original weeklyTrend for backward compatibility
            }
        }
        
        return [
            'totalEmployees' => count($employeeIds),
            'totalDays' => $totalDays,
            'workingDays' => $workingDays,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'lateCount' => $lateCount,
            'leaveCount' => $leaveCount,
            'attendancePercentage' => $attendancePercentage,
            'weeklyTrend' => $weeklyTrend,
            'officeData' => $officeData,
            'weeklyDateRange' => [
                'startDate' => $weeklyOfficeData['startDate'],
                'endDate' => $weeklyOfficeData['endDate']
            ]
        ];
    } */
    
    /**
     * Get attendance data for exactly the last 7 days (including today)
     */
    /* private function getLast7DaysAttendanceTrend($employeeIds)
    {
        // Calculate date range for exactly the last 7 days, including today
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(6); // 6 days back + today = 7 days
        
        // Format dates for queries
        $endDateStr = $endDate->format('Y-m-d');
        $startDateStr = $startDate->format('Y-m-d');
        
        // Create the exact 7-day range
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $trend = [];
        
        // Process each day
        foreach ($dateRange as $date) {
            $dateStr = $date->format('Y-m-d');
            $displayDate = $date->format('M d'); // e.g., "Apr 16"
            
            // Initialize counters
            $presentCount = 0;
            $absentCount = 0;
            $lateCount = 0;
            $leaveCount = 0;
            
            // Process each employee for this day
            foreach ($employeeIds as $employeeId) {
                // Check leave status
                $leaveStatus = Helper::checkLeaveWithTypes($dateStr, $employeeId);
                if ($leaveStatus == 'fullday Leave') {
                    $leaveCount++;
                    continue;
                }
                
                // Check if weekend
                $isWeekend = ($date->dayOfWeek === Carbon::SATURDAY || $date->dayOfWeek === Carbon::SUNDAY);
                
                // Check attendance
                $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                    ->where('date', $dateStr)
                    ->first();
                
                if ($attendance) {
                    $presentCount++;
                    
                    // Check if late
                    if ($attendance->late != '00:00:00') {
                        $lateCount++;
                    }
                } else if (!$isWeekend && $leaveStatus == 0) {
                    $absentCount++;
                }
            }
            
            // Add data for this day
            $trend[] = [
                'date' => $displayDate,
                'presentCount' => $presentCount,
                'absentCount' => $absentCount,
                'lateCount' => $lateCount,
                'leaveCount' => $leaveCount
            ];
        }
        
        // Verify we have exactly 7 days of data
        if (count($trend) !== 7) {
            \Log::warning('Expected 7 days of trend data but got ' . count($trend));
        }
        
        return $trend;
    } */
    
    /* private function getOfficeSpecificData($creatorId, $startDate, $endDate, $dates, $workingDays)
    {
        $officeData = [];
        $offices = Office::where('created_by', $creatorId)->get();
        
        foreach ($offices as $office) {
            $officeEmployeeIds = Employee::where('created_by', $creatorId)
                ->where('office_id', $office->id)
                ->where('is_active', 1)
                ->pluck('id')
                ->toArray();
            
            if (empty($officeEmployeeIds)) {
                continue;
            }
            
            // Initialize counters for this office
            $officePresentCount = 0;
            $officeAbsentCount = 0;
            $officeLateCount = 0;
            $officeLeaveCount = 0;
            
            // Process each employee's attendance for the date range
            foreach ($officeEmployeeIds as $employeeId) {
                foreach ($dates as $date) {
                    // Check if employee is on leave
                    $leaveStatus = Helper::checkLeaveWithTypes($date, $employeeId);
                    
                    if ($leaveStatus == 'fullday Leave') {
                        $officeLeaveCount++;
                        continue;
                    }
                    
                    // Skip weekends when counting absences
                    $dayOfWeek = Carbon::parse($date)->dayOfWeek;
                    if ($dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY) {
                        continue;
                    }
                    
                    // Check attendance
                    $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                        ->where('date', $date)
                        ->first();
                    
                    if ($attendance) {
                        $officePresentCount++;
                        
                        // Check if employee was late
                        if ($attendance->late != '00:00:00') {
                            $officeLateCount++;
                        }
                    } else {
                        // Only count as absent if not on leave and not a weekend
                        if ($leaveStatus == 0) {
                            $officeAbsentCount++;
                        }
                    }
                }
            }
            
            $officeExpectedAttendance = count($officeEmployeeIds) * $workingDays;
            $officeAttendancePercentage = $officeExpectedAttendance > 0 ? 
                round(($officePresentCount / $officeExpectedAttendance) * 100, 2) : 0;
            
            $officeWeeklyTrend = $this->getWeeklyAttendanceTrend($officeEmployeeIds, $startDate, $endDate);
            
            $officeData[$office->id] = [
                'name' => $office->name,
                'employeeCount' => count($officeEmployeeIds),
                'presentCount' => $officePresentCount,
                'absentCount' => $officeAbsentCount,
                'lateCount' => $officeLateCount,
                'leaveCount' => $officeLeaveCount,
                'attendancePercentage' => $officeAttendancePercentage,
                'weeklyTrend' => $officeWeeklyTrend
            ];
        }
        
        return $officeData;
    } */
    
    /* public function enhanceOfficeData($offices, $officeData)
    {
        foreach ($offices as $office) {
            if (isset($officeData[$office->id])) {
                $office->stats = $officeData[$office->id];
            } else {
                $office->stats = [
                    'name' => $office->name,
                    'employeeCount' => 0,
                    'presentCount' => 0,
                    'absentCount' => 0,
                    'lateCount' => 0,
                    'leaveCount' => 0,
                    'attendancePercentage' => 0,
                    'weeklyTrend' => []
                ];
            }
        }
        
        return $offices;
    } */
    
    /* private function getWorkingDays($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = 0;
        
        while ($start->lte($end)) {
            // Assuming weekends (Saturday and Sunday) are non-working days
            if ($start->dayOfWeek !== Carbon::SATURDAY && $start->dayOfWeek !== Carbon::SUNDAY) {
                $days++;
            }
            $start->addDay();
        }
        
        return $days;
    } */
    
    /* private function getWeeklyAttendanceTrend($employeeIds, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $trend = [];
        
        // If date range is less than a week, do daily trend instead
        if ($start->diffInDays($end) < 7) {
            return $this->getDailyAttendanceTrend($employeeIds, $startDate, $endDate);
        }
        
        // Group by week
        while ($start->lte($end)) {
            $weekStart = $start->copy()->startOfWeek();
            $weekEnd = min($start->copy()->endOfWeek(), $end);
            
            $weekWorkingDays = $this->getWorkingDays($weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d'));
            $weekTotalExpected = count($employeeIds) * $weekWorkingDays;
            
            $weekPresent = 0;
            $weekDates = CarbonPeriod::create($weekStart, $weekEnd);
            
            foreach ($employeeIds as $employeeId) {
                foreach ($weekDates as $date) {
                    $dateStr = $date->format('Y-m-d');
                    
                    // Skip weekends
                    if ($date->dayOfWeek === Carbon::SATURDAY || $date->dayOfWeek === Carbon::SUNDAY) {
                        continue;
                    }
                    
                    // Skip if on leave
                    $leaveStatus = Helper::checkLeaveWithTypes($dateStr, $employeeId);
                    if ($leaveStatus == 'fullday Leave') {
                        continue;
                    }
                    
                    // Check attendance
                    $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                        ->where('date', $dateStr)
                        ->first();
                    
                    if ($attendance) {
                        $weekPresent++;
                    }
                }
            }
            
            $weekPercentage = $weekTotalExpected > 0 ? 
                round(($weekPresent / $weekTotalExpected) * 100, 2) : 0;
                
            $trend[] = [
                'week' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
                'percentage' => $weekPercentage
            ];
            
            $start = $start->addDays(7);
        }
        
        return $trend;
    } */
    
    /**
     * Get daily attendance trend data with detailed breakdown
     */
    /* private function getDailyAttendanceTrend($employeeIds, $startDate, $endDate)
    {
        // Create a date range for the last 7 days
        if (Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate)) > 7) {
            // If more than 7 days, show only the last 7 days
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
            $startDate = Carbon::parse($endDate)->subDays(6)->format('Y-m-d');
        }
        
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $trend = [];
        
        foreach ($dateRange as $date) {
            $dateStr = $date->format('Y-m-d');
            
            // Get formatted date for display
            $displayDate = $date->format('M d');
            
            // Initialize counters
            $presentCount = 0;
            $absentCount = 0;
            $lateCount = 0;
            $leaveCount = 0;
            $dailyTotal = count($employeeIds);
            
            // Count attendance for each employee
            foreach ($employeeIds as $employeeId) {
                // Check if on leave
                $leaveStatus = Helper::checkLeaveWithTypes($dateStr, $employeeId);
                if ($leaveStatus == 'fullday Leave') {
                    $leaveCount++;
                    continue;
                }
                
                // Check if weekend (still count weekends but separately)
                $isWeekend = ($date->dayOfWeek === Carbon::SATURDAY || $date->dayOfWeek === Carbon::SUNDAY);
                
                // Check attendance
                $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                    ->where('date', $dateStr)
                    ->first();
                
                if ($attendance) {
                    $presentCount++;
                    
                    // Check if employee was late
                    if ($attendance->late != '00:00:00') {
                        $lateCount++;
                    }
                } else if (!$isWeekend && $leaveStatus == 0) {
                    // Only count as absent if not on leave and not a weekend
                    $absentCount++;
                }
            }
            
            // Calculate percentage
            $attendanceTotal = max(1, $dailyTotal - $leaveCount); // Avoid division by zero
            $percentage = round(($presentCount / $attendanceTotal) * 100, 2);
            
            // Store all data for this day
            $trend[] = [
                'date' => $displayDate,
                'presentCount' => $presentCount,
                'absentCount' => $absentCount,
                'lateCount' => $lateCount,
                'leaveCount' => $leaveCount,
                'percentage' => $percentage
            ];
        }
        
        return $trend;
    } */
    
    /* public function getFilteredData(Request $request)
    {
        $user = Auth::user();
        $creatorId = $user->creatorId();
        
        $office = $request->input('office', 'all');
        $department = $request->input('department', 'all');
        $branch = $request->input('branch', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $dashboardData = $this->getDashboardData($creatorId, $office, $department, $branch, $startDate, $endDate);
        
        return response()->json($dashboardData);
    } */
    
    /**
     * Get real-time attendance status for today
     */
    /* public function getTodayAttendanceStatus(Request $request)
    {
        $user = Auth::user();
        $creatorId = $user->creatorId();
        $today = Carbon::now()->format('Y-m-d');
        
        // Get filter parameters
        $office = $request->input('office', 'all');
        $department = $request->input('department', 'all');
        $branch = $request->input('branch', 'all');
        
        // Build employee query with filters
        $employeeQuery = Employee::where('created_by', $creatorId)
                               ->where('is_active', 1);
        
        if ($office != 'all') {
            $employeeQuery->where('office_id', $office);
        }
        if ($department != 'all') {
            $employeeQuery->where('department_id', $department);
        }
        if ($branch != 'all') {
            $employeeQuery->where('branch_id', $branch);
        }
        
        $employees = $employeeQuery->get();
        
        // Get attendance status for each employee
        $attendanceData = [];
        $presentCount = 0;
        $absentCount = 0;
        $leaveCount = 0;
        $lateCount = 0;
        
        foreach ($employees as $employee) {
            // Check if employee is on leave
            $leaveStatus = Helper::checkLeaveWithTypes($today, $employee->id);
            
            if ($leaveStatus == 'fullday Leave') {
                $leaveCount++;
                $status = 'On Leave';
            } else {
                // Check attendance for today
                $attendance = AttendanceEmployee::where('employee_id', $employee->id)
                    ->where('date', $today)
                    ->first();
                
                if ($attendance) {
                    $presentCount++;
                    $status = 'Present';
                    
                    if ($attendance->late != '00:00:00') {
                        $lateCount++;
                        $status = 'Late';
                    }
                    
                    // Check if currently clocked in
                    $isCurrentlyWorking = AttendanceEmployee::where('employee_id', $employee->id)
                        ->where('date', $today)
                        ->where('clock_out', '00:00:00')
                        ->exists();
                    
                    if ($isCurrentlyWorking) {
                        $status = 'Working Now';
                    }
                } else {
                    // Only count as absent if not on leave and not a weekend
                    $dayOfWeek = Carbon::parse($today)->dayOfWeek;
                    if ($dayOfWeek !== Carbon::SATURDAY && $dayOfWeek !== Carbon::SUNDAY && $leaveStatus == 0) {
                        $absentCount++;
                        $status = 'Absent';
                    }
                }
            }
            
            // Only include if not a weekend or if present today (regardless of weekend)
            $dayOfWeek = Carbon::parse($today)->dayOfWeek;
            $isWeekend = ($dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY);
            
            if (!$isWeekend || ($status == 'Present' || $status == 'Late' || $status == 'Working Now')) {
                $attendanceData[] = [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->designation ? $employee->designation->name : '',
                    'department' => $employee->department ? $employee->department->name : '',
                    'office' => $employee->office ? $employee->office->name : '',
                    'status' => $status,
                    'workTime' => $attendance ? Helper::calculateTotalTimeDifference(
                        AttendanceEmployee::where('employee_id', $employee->id)
                            ->where('date', $today)
                            ->get()
                    ) : '00:00 Hrs'
                ];
            }
        }
        
        return response()->json([
            'attendanceData' => $attendanceData,
            'summary' => [
                'totalEmployees' => count($employees),
                'presentCount' => $presentCount,
                'absentCount' => $absentCount,
                'lateCount' => $lateCount,
                'leaveCount' => $leaveCount
            ]
        ]);
    } */
}