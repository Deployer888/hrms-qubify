<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\Utility;
use App\Helpers\Helper;
use App\Models\Employee;
use App\Models\Designation;
use App\Models\Leave;
use App\Models\EmployeeLocation;
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
        try {
            // Get search parameter
            $search = request()->get('search');
            
            // Build offices query with search functionality
            $officesQuery = Office::query();
            
            // Apply search filters if search term is provided
            if (!empty($search)) {
                $officesQuery->where(function($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%')
                          ->orWhere('address', 'LIKE', '%' . $search . '%')
                          ->orWhere('city', 'LIKE', '%' . $search . '%')
                          ->orWhere('state', 'LIKE', '%' . $search . '%')
                          ->orWhere('country', 'LIKE', '%' . $search . '%');
                });
            }
            
            $offices = $officesQuery->get();
            
            $employeeCounts = Employee::where('is_active', 1)
                ->select('office_id', DB::raw('count(*) as count'))
                ->groupBy('office_id')
                ->pluck('count', 'office_id')
                ->toArray();
    
            $totalEmployees = array_sum($employeeCounts);
            $totalDepartments = Department::count();
            $totalCities = $offices->pluck('city')->unique()->count();
    
            $today = Carbon::today()->format('Y-m-d');
            $presentEmployees = AttendanceEmployee::whereDate('date', $today)
                ->whereIn('employee_id', function($query) {
                    $query->select('id')
                        ->from('employees')
                        ->where('is_active', 1);
                })
                ->distinct('employee_id')
                ->count();
    
            $attendancePercentage = $totalEmployees > 0 ? round(($presentEmployees / $totalEmployees) * 100) : 0;
    
            $offices->map(function($office) use ($employeeCounts) {
                $office->employee_count = $employeeCounts[$office->id] ?? 0;
                return $office;
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Offices retrieved successfully.',
                'data' => [
                    'offices' => $offices,
                    'statistics' => [
                        'total_employees' => $totalEmployees,
                        'total_departments' => $totalDepartments,
                        'total_cities' => $totalCities,
                        'attendance_percentage' => $attendancePercentage,
                        'present_employees' => $presentEmployees
                    ]
                ]
            ], 200);
            
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error fetching offices: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching offices.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    public function detailOverview($id){
        try {
            $office = Office::find($id);
    
            if (!$office) {
                return response()->json([
                    'success' => false,
                    'message' => 'Office not found.'
                ], 404);
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
    
            return response()->json([
                'success' => true,
                'message' => 'Office overview details retrieved successfully.',
                'data' => [
                    'office' => [
                        'id' => $office->id,
                        'name' => $office->name,
                        'address' => $office->address,
                        'city' => $office->city,
                        'state' => $office->state,
                        'country' => $office->country,
                        'zip_code' => $office->zip_code,
                        'latitude' => $office->latitude,
                        'longitude' => $office->longitude,
                    ],
                    'statistics' => [
                        'total_employees' => $employeeCount,
                        'today_present' => $todayPresent,
                        'today_late' => $todayLate,
                        'today_absent' => $todayAbsent,
                        'today_leave' => $todayLeave,
                        'attendance_rate' => $attendanceRate,
                    ],
                    'departments' => $departmentStats,
                    'monthly_stats' => $monthlyStats
                ]
            ], 200);
    
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error fetching office details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching office details.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    public function detailsEmployees($id)
    {
        try {
            $office = Office::find($id);
    
            if (!$office) {
                return response()->json([
                    'success' => false,
                    'message' => 'Office not found.'
                ], 404);
            }
    
            // Get search and filter parameters
            $search = request()->get('search');
            $departmentFilter = request()->get('department_id'); // Can be single ID or comma-separated IDs
    
            // Build employees query with filters
            $employeesQuery = Employee::where('office_id', $office->id)
                ->where('is_active', 1);
    
            // Apply search filters if search term is provided
            if (!empty($search)) {
                $employeesQuery->where(function($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%')
                          ->orWhere('email', 'LIKE', '%' . $search . '%')
                          ->orWhere('phone', 'LIKE', '%' . $search . '%')
                          ->orWhere('employee_id', 'LIKE', '%' . $search . '%')
                          ->orWhereHas('department', function($q) use ($search) {
                              $q->where('name', 'LIKE', '%' . $search . '%');
                          })
                          ->orWhereHas('designation', function($q) use ($search) {
                              $q->where('name', 'LIKE', '%' . $search . '%');
                          });
                });
            }
    
            // Apply department filter if provided
            if (!empty($departmentFilter)) {
                // Handle both single ID and comma-separated IDs
                $departmentIds = is_array($departmentFilter) 
                    ? $departmentFilter 
                    : explode(',', $departmentFilter);
                
                // Remove empty values and convert to integers
                $departmentIds = array_filter(array_map('intval', $departmentIds));
                
                if (!empty($departmentIds)) {
                    $employeesQuery->whereIn('department_id', $departmentIds);
                }
            }
    
            // Get filtered employees
            $employees = $employeesQuery->with(['department', 'designation'])->get();
            $employeeCount = $employees->count();
            $employeeIds = $employees->pluck('id')->toArray();
    
            // If no employees found after filtering, return early with empty data
            if ($employeeCount === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'No employees found matching the criteria.',
                    'data' => [
                        'office' => [
                            'id' => $office->id,
                            'name' => $office->name,
                            'address' => $office->address,
                            'city' => $office->city,
                            'state' => $office->state,
                            'country' => $office->country,
                            'zip_code' => $office->zip_code,
                        ],
                        'filters' => [
                            'search' => $search,
                            'department_id' => $departmentFilter,
                            'applied_filters_count' => (!empty($search) ? 1 : 0) + (!empty($departmentFilter) ? 1 : 0)
                        ],
                        'statistics' => [
                            'total_employees' => 0,
                            'today_present' => 0,
                            'today_late' => 0,
                            'today_absent' => 0,
                            'today_leave' => 0,
                            'attendance_rate' => 0,
                            'inside_office' => 0,
                            'outside_office' => 0,
                            'avg_working_hours' => 0
                        ],
                        'departments' => [],
                        'today_attendance_logs' => [],
                    ]
                ], 200);
            }
    
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
    
            // Get all departments in one query (based on filtered employees)
            $departmentIds = $employees->pluck('department_id')->filter()->unique();
            $departments = Department::whereIn('id', $departmentIds)->get();
    
            // Get all department heads in one query
            $departmentHeads = Employee::whereIn('department_id', $departmentIds)
                ->where('is_head', 1)
                ->get()
                ->keyBy('department_id');
    
            // Calculate department statistics (based on filtered employees)
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
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'employee_id' => $employee->employee_id,
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
    
            // Get all available departments for this office (for frontend filter options)
            $allOfficeDepartments = Department::whereIn('id', function($query) use ($office) {
                $query->select('department_id')
                      ->from('employees')
                      ->where('office_id', $office->id)
                      ->where('is_active', 1)
                      ->distinct();
            })->get(['id', 'name']);
    
            return response()->json([
                'success' => true,
                'message' => 'Office details retrieved successfully.',
                'data' => [
                    'office' => [
                        'id' => $office->id,
                        'name' => $office->name,
                        'address' => $office->address,
                        'city' => $office->city,
                        'state' => $office->state,
                        'country' => $office->country,
                        'zip_code' => $office->zip_code,
                    ],
                    'filters' => [
                        'search' => $search,
                        'department_id' => $departmentFilter,
                        'applied_filters_count' => (!empty($search) ? 1 : 0) + (!empty($departmentFilter) ? 1 : 0),
                        'available_departments' => $allOfficeDepartments
                    ],
                    'statistics' => [
                        'total_employees' => $employeeCount,
                        'today_present' => $todayPresent,
                        'today_late' => $todayLate,
                        'today_absent' => $todayAbsent,
                        'today_leave' => $todayLeave,
                        'attendance_rate' => $attendanceRate,
                        'inside_office' => $insideOffice,
                        'outside_office' => $outsideOffice,
                        'avg_working_hours' => $avgWorkingHours
                    ],
                    'departments' => $departmentStats,
                    'today_attendance_logs' => $todayAttendanceLogs,
                ]
            ], 200);
    
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error fetching office details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching office details.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    public function detailsAttendance($id)
    {
        try {
            $office = Office::find($id);
    
            if (!$office) {
                return response()->json([
                    'success' => false,
                    'message' => 'Office not found.'
                ], 404);
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
                
            $absentRate = $employeeCount > 0 ? round(($todayAbsent / $employeeCount) * 100) : 0;
    
            // Get all departments in one query
            $departmentIds = $employees->pluck('department_id')->filter()->unique();
            $departments = Department::whereIn('id', $departmentIds)
                ->get();
    
            // Get all department heads in one query
            $departmentHeads = Employee::whereIn('department_id', $departmentIds)
                ->where('is_head', 1)
                ->get()
                ->keyBy('department_id');
    
            // Calculate department statistics
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
    
            return response()->json([
                'success' => true,
                'message' => 'Office details retrieved successfully.',
                'data' => [
                    'office' => [
                        'id' => $office->id,
                        'name' => $office->name,
                        'address' => $office->address,
                        'city' => $office->city,
                        'state' => $office->state,
                        'country' => $office->country,
                        'zip_code' => $office->zip_code,
                    ],
                    'statistics' => [
                        'total_employees' => $employeeCount,
                        'today_present' => $todayPresent,
                        'today_late' => $todayLate,
                        'today_absent' => $todayAbsent,
                        'today_leave' => $todayLeave,
                        'attendance_rate' => $attendanceRate,
                        'inside_office' => $insideOffice,
                        'outside_office' => $outsideOffice,
                        'avg_working_hours' => $avgWorkingHours,
                        'absentRate' => $absentRate
                    ],
                    'today_attendance_logs' => $todayAttendanceLogs,
                    'weekly_stats' => $weeklyStats,
                    'monthly_stats' => $monthlyStats
                ]
            ], 200);
    
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error fetching office details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching office details.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    public function detailsDepartment($id)
    {
        try {
            $office = Office::find($id);
    
            if (!$office) {
                return response()->json([
                    'success' => false,
                    'message' => 'Office not found.'
                ], 404);
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
    
            return response()->json([
                'success' => true,
                'message' => 'Office details retrieved successfully.',
                'data' => [
                    'office' => [
                        'id' => $office->id,
                        'name' => $office->name,
                        'address' => $office->address,
                        'city' => $office->city,
                        'state' => $office->state,
                        'country' => $office->country,
                        'zipcode' => $office->zipcode,
                    ],
                    'statistics' => [
                        'total_employees' => $employeeCount,
                        'today_present' => $todayPresent,
                        'today_late' => $todayLate,
                        'today_absent' => $todayAbsent,
                        'today_leave' => $todayLeave,
                        'attendance_rate' => $attendanceRate,
                    ],
                    'departments' => $departmentStats
                ]
            ], 200);
    
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error fetching office details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching office details.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    public function getEmployeeDetails($employeeID)
    {
        try {
            // Get employee data with eager loading to reduce queries
            $employee = Employee::with(['department', 'designation'])
                ->where('id', $employeeID)
                ->first();
    
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }
    
            $department = $employee->department;
    
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
            $leaveTypesData = LeaveType::select('id', 'title', 'days')
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
    
            // Get location history for today
            $locationHistory = EmployeeLocation::orderBy('id', 'DESC')
                ->where('employee_id', $employeeID)
                ->whereDate('time', Carbon::today())
                ->get();
    
            // Prepare response data
            $responseData = [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'employee_id' => $employee->employee_id,
                    'joining_date' => $employee->joining_date,
                    'company_doj' => $employee->company_doj,
                    'department' => $department ? $department->name : null,
                    'designation' => $employee->designation ? $employee->designation->name : null,
                    'shift_start' => $employee->shift_start,
                    'office_id' => $employee->office_id
                ],
                'office' => $office,
                'department_head' => $departmentHead ? [
                    'id' => $departmentHead->id,
                    'name' => $departmentHead->name,
                    'email' => $departmentHead->email
                ] : null,
                'attendance_metrics' => [
                    'attendance_rate' => $attendanceRate,
                    'present_days' => $presentDays,
                    'late_days' => $lateDays,
                    'absent_days' => $absentDays,
                    'leave_days' => $leaveDays,
                    'working_days_count' => $workingDaysCount
                ],
                'avg_checkin_time' => $avgCheckinTime,
                'experience_years' => $experience,
                'recent_attendances' => $recentAttendances,
                'documents' => $documents,
                'leave_data' => $leaveData,
                'location_history' => $locationHistory
            ];
    
            return response()->json([
                'success' => true,
                'message' => 'Employee details retrieved successfully',
                'data' => $responseData
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
