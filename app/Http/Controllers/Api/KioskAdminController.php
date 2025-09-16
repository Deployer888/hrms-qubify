<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\AadhaarDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Employee;
use App\Models\AttendanceEmployee;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class KioskAdminController extends BaseController
{
    /**
     * Admin login for KIOSK
     */
    public function adminLogin(Request $request): JsonResponse
    {
        try {
            // Validate request
            $rules = [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string',
                'device_id' => 'required|string|max:255',
                'device_type' => 'required|string|max:255',
                'fcm_token' => 'required|string|max:1024',
            ];
            
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->errorResponse(__('Validation error.'), 200, $validator->errors()->toArray());
            }
            
            // Check if device type is supported
            if ($request->device_type != 'Android' && $request->device_type != 'web') {
                return $this->errorResponse(__("The device_type must be 'Android' or 'web'."), 200);
            }
            
            // Find user by email
            $user = User::where('email', $request->email)->first();
            
            // Validate credentials
            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse(__('Invalid credentials.'), 401);
            }
            
            // Check if user has company role
            if ($user->type != 'company') {
                return $this->errorResponse(__('Access denied. Only company administrators can access this panel.'), 403);
            }
            
            // Update user login information
            $user = $user->makeHidden('password');
            $user->last_login = date('Y-m-d H:i:s');
            $user->device_id = $request->device_id;
            $user->device_type = $request->device_type == 'Android' ? 1 : 2; // 1 for Android, 2 for web
            $user->fcm_token = $request->fcm_token;
            $user->save();
            
            // Remove previous tokens
            $user->tokens()->delete();
            
            // Create new token
            $tokenResult = $user->createToken('kiosk-admin-token')->accessToken;
            
            // Prepare response data
            $success = [
                'token' => $tokenResult,
                'user_name' => $user->name,
                'email' => $user->email,
                'userID' => $user->id,
                'user_role' => $user->type,
                'avatar_url' => 'https://hrm.qubifytech.com/storage/uploads/avatar/' . $user->avatar,
                'login_status' => 'true',
                'user' => $user
            ];
            
            return $this->successResponse($success);
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    
    /**
     * Get attendance logs with user details for KIOSK admin panel
     */
    public function getAttendanceLogs(Request $request): JsonResponse
    {
        try {
            // Check if user is authenticated with company role
            if (!Auth::guard('api')->check() || Auth::guard('api')->user()->type != 'company') {
                return $this->errorResponse(__('Unauthorized. Only company administrators can access this data.'), 401);
            }
            
            // Parse filters from request
            $date = $request->input('date', Carbon::now()->format('Y-m-d'));
            $departmentId = $request->input('department_id');
            $employeeId = $request->input('employee_id');
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 20);
            
            // Start building query
            $query = AttendanceEmployee::select(
                'attendance_employee.id', 
                'attendance_employee.employee_id', 
                'attendance_employee.employee_name',
                'attendance_employee.date',
                'attendance_employee.clock_in',
                'attendance_employee.clock_out',
                'attendance_employee.status'
            )
            ->where('attendance_employee.date', $date);
            
            // Join with employees and departments if needed
            if (DB::getSchemaBuilder()->hasTable('employees')) {
                $query->leftJoin('employees', 'attendance_employee.employee_id', '=', 'employees.id');
                
                if (DB::getSchemaBuilder()->hasColumn('employees', 'department_id') && 
                    DB::getSchemaBuilder()->hasTable('departments')) {
                    $query->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                          ->addSelect('departments.name as department_name');
                    
                    // Apply department filter if provided
                    if ($departmentId) {
                        $query->where('employees.department_id', $departmentId);
                    }
                }
                
                // Add employee fields if they exist
                if (DB::getSchemaBuilder()->hasColumn('employees', 'email')) {
                    $query->addSelect('employees.email');
                }
                
                if (DB::getSchemaBuilder()->hasColumn('employees', 'phone')) {
                    $query->addSelect('employees.phone');
                }
                
                if (DB::getSchemaBuilder()->hasColumn('employees', 'department_id')) {
                    $query->addSelect('employees.department_id');
                }
            }
            
            // Apply employee filter if provided
            if ($employeeId) {
                $query->where('attendance_employee.employee_id', $employeeId);
            }
            
            // Get total count for pagination
            $totalCount = $query->count();
            
            // Get paginated results
            $attendanceLogs = $query->orderBy('attendance_employee.clock_in', 'desc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();
            
            // Calculate work hours for each record
            foreach ($attendanceLogs as $log) {
                $log->work_hours = $this->calculateWorkHours($log->clock_in, $log->clock_out);
                $log->is_late = $this->isLateArrival($log->clock_in);
                $log->is_early_departure = $this->isEarlyDeparture($log->clock_out);
            }
            
            // Get departments for filter dropdown
            $departments = [];
            if (DB::getSchemaBuilder()->hasTable('departments')) {
                $departments = DB::table('departments')
                    ->select('id', 'name')
                    ->when(DB::getSchemaBuilder()->hasColumn('departments', 'is_active'), function($q) {
                        return $q->where('is_active', 1);
                    })
                    ->orderBy('name')
                    ->get();
            }
            
            $data = [
                'attendance_logs' => $attendanceLogs,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $totalCount,
                    'total_pages' => ceil($totalCount / $perPage)
                ],
                'filters' => [
                    'departments' => $departments,
                    'date' => $date
                ]
            ];
            
            return $this->successResponse($data);
            
        } catch (\Exception $e) {
            Log::error('Error retrieving attendance logs: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(__('An error occurred while fetching attendance logs.'), 500);
        }
    }
    
    /**
     * Get attendance data for dashboard charts
     */
    public function getAttendanceDashboard(Request $request): JsonResponse
    {
        try {
            // Check if user is authenticated with company role
            if (!Auth::guard('api')->check() || Auth::guard('api')->user()->type != 'company') {
                return $this->errorResponse(__('Unauthorized. Only company administrators can access this data.'), 401);
            }
            
            // Get date range
            $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
            $departmentId = $request->input('department_id');
            
            // Daily attendance counts
            $dailyAttendance = $this->getDailyAttendanceCounts($startDate, $endDate, $departmentId);
            
            // Department attendance breakdown
            $departmentBreakdown = $this->getDepartmentAttendanceBreakdown($startDate, $endDate);
            
            // Late arrival statistics
            $lateArrivals = $this->getLateArrivalStats($startDate, $endDate, $departmentId);
            
            // Top employees with perfect attendance
            $perfectAttendance = $this->getEmployeesWithPerfectAttendance($startDate, $endDate);
            
            // Recent attendance logs (today)
            $recentLogs = $this->getRecentAttendanceLogs();
            
            $data = [
                'daily_attendance' => $dailyAttendance,
                'department_breakdown' => $departmentBreakdown,
                'late_arrivals' => $lateArrivals,
                'perfect_attendance' => $perfectAttendance,
                'recent_logs' => $recentLogs,
                'summary' => [
                    'total_employees' => $this->getTotalActiveEmployees(),
                    'present_today' => $this->getPresentEmployeesCount(Carbon::now()->format('Y-m-d')),
                    'late_today' => $this->getLateEmployeesCount(Carbon::now()->format('Y-m-d')),
                    'absent_today' => $this->getAbsentEmployeesCount(Carbon::now()->format('Y-m-d'))
                ]
            ];
            
            return $this->successResponse($data);
            
        } catch (\Exception $e) {
            Log::error('Error retrieving dashboard data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(__('An error occurred while fetching dashboard data.'), 500);
        }
    }
    
    /**
     * Logout user and invalidate token
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            if ($request->user()) {
                $request->user()->token()->delete();
                return $this->successResponse(['message' => __('Successfully logged out')]);
            }
            
            return $this->errorResponse(__('User not authenticated'), 401);
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred during logout: ') . $e->getMessage(), 500);
        }
    }
    
    /**
     * Calculate work hours from clock in and clock out times
     */
    private function calculateWorkHours($clockIn, $clockOut)
    {
        if (empty($clockIn) || empty($clockOut) || $clockOut == '00:00:00') {
            return null;
        }
        
        try {
            $start = Carbon::createFromFormat('H:i:s', $clockIn);
            $end = Carbon::createFromFormat('H:i:s', $clockOut);
            
            // If end time is earlier than start time, assume it's the next day
            if ($end->lt($start)) {
                $end->addDay();
            }
            
            $diffInMinutes = $end->diffInMinutes($start);
            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;
            
            return sprintf('%02d:%02d', $hours, $minutes);
        } catch (\Exception $e) {
            Log::warning('Error calculating work hours: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check if arrival is late based on company policy
     */
    private function isLateArrival($clockIn)
    {
        if (empty($clockIn)) {
            return false;
        }
        
        try {
            $companyStartTime = env('COMPANY_START_TIME', '09:00:00');
            $gracePeriod = (int) env('LATE_ARRIVAL_GRACE_MINUTES', 15);
            
            $clockInTime = Carbon::createFromFormat('H:i:s', $clockIn);
            $startTime = Carbon::createFromFormat('H:i:s', $companyStartTime);
            $startTime->addMinutes($gracePeriod);
            
            return $clockInTime->gt($startTime);
        } catch (\Exception $e) {
            Log::warning('Error checking late arrival: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if departure is early based on company policy
     */
    private function isEarlyDeparture($clockOut)
    {
        if (empty($clockOut) || $clockOut == '00:00:00') {
            return false;
        }
        
        try {
            $companyEndTime = env('COMPANY_END_TIME', '18:00:00');
            $gracePeriod = (int) env('EARLY_DEPARTURE_GRACE_MINUTES', 15);
            
            $clockOutTime = Carbon::createFromFormat('H:i:s', $clockOut);
            $endTime = Carbon::createFromFormat('H:i:s', $companyEndTime);
            $endTime->subMinutes($gracePeriod);
            
            return $clockOutTime->lt($endTime);
        } catch (\Exception $e) {
            Log::warning('Error checking early departure: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get total active employees
     */
    private function getTotalActiveEmployees()
    {
        try {
            $query = Employee::query();
            
            if (DB::getSchemaBuilder()->hasColumn('employees', 'is_active')) {
                $query->where('is_active', 1);
            }
            
            return $query->count();
        } catch (\Exception $e) {
            Log::warning('Error counting total employees: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get present employees count for a date
     */
    private function getPresentEmployeesCount($date)
    {
        try {
            return AttendanceEmployee::where('date', $date)->count();
        } catch (\Exception $e) {
            Log::warning('Error counting present employees: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get daily attendance counts for chart
     */
    private function getDailyAttendanceCounts($startDate, $endDate, $departmentId = null)
    {
        try {
            $query = DB::table('attendance_employee')
                ->select(DB::raw('date, COUNT(*) as count'))
                ->whereBetween('date', [$startDate, $endDate])
                ->groupBy('date');
            
            if ($departmentId && DB::getSchemaBuilder()->hasTable('employees') && 
                DB::getSchemaBuilder()->hasColumn('employees', 'department_id')) {
                $query->join('employees', 'attendance_employee.employee_id', '=', 'employees.id')
                      ->where('employees.department_id', $departmentId);
            }
            
            return $query->get();
        } catch (\Exception $e) {
            Log::warning('Error getting daily attendance counts: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get department attendance breakdown
     */
    private function getDepartmentAttendanceBreakdown($startDate, $endDate)
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('departments') || 
                !DB::getSchemaBuilder()->hasTable('employees') || 
                !DB::getSchemaBuilder()->hasColumn('employees', 'department_id')) {
                return collect([]);
            }
            
            return DB::table('attendance_employee')
                ->join('employees', 'attendance_employee.employee_id', '=', 'employees.id')
                ->join('departments', 'employees.department_id', '=', 'departments.id')
                ->select(DB::raw('departments.name, COUNT(*) as count'))
                ->whereBetween('attendance_employee.date', [$startDate, $endDate])
                ->groupBy('departments.name')
                ->get();
        } catch (\Exception $e) {
            Log::warning('Error getting department breakdown: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get late arrival statistics
     */
    private function getLateArrivalStats($startDate, $endDate, $departmentId = null)
    {
        try {
            $companyStartTime = env('COMPANY_START_TIME', '09:00:00');
            $gracePeriod = (int) env('LATE_ARRIVAL_GRACE_MINUTES', 15);
            
            // Create query
            $query = DB::table('attendance_employee')
                ->select(DB::raw('date, COUNT(*) as count'))
                ->whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('clock_in');
            
            // Use database-specific approach for time comparison
            $driver = DB::connection()->getDriverName();
            
            if ($driver === 'mysql') {
                $query->whereRaw("TIME_TO_SEC(clock_in) > TIME_TO_SEC(?) + ?", [
                    $companyStartTime, 
                    $gracePeriod * 60
                ]);
            } else {
                $threshold = Carbon::createFromFormat('H:i:s', $companyStartTime)
                    ->addMinutes($gracePeriod)
                    ->format('H:i:s');
                
                $query->whereRaw("clock_in > ?", [$threshold]);
            }
            
            $query->groupBy('date');
            
            // Add department filter if provided
            if ($departmentId && DB::getSchemaBuilder()->hasTable('employees') && 
                DB::getSchemaBuilder()->hasColumn('employees', 'department_id')) {
                $query->join('employees', 'attendance_employee.employee_id', '=', 'employees.id')
                      ->where('employees.department_id', $departmentId);
            }
            
            return $query->get();
        } catch (\Exception $e) {
            Log::warning('Error getting late arrival stats: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get employees with perfect attendance
     */
    private function getEmployeesWithPerfectAttendance($startDate, $endDate)
    {
        try {
            $workingDaysCount = $this->getWorkingDaysCount($startDate, $endDate);
            
            return DB::table('attendance_employee')
                ->select('employee_id', 'employee_name', DB::raw('COUNT(DISTINCT date) as days_present'))
                ->whereBetween('date', [$startDate, $endDate])
                ->groupBy('employee_id', 'employee_name')
                ->having('days_present', '=', $workingDaysCount)
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            Log::warning('Error getting perfect attendance: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get count of working days between two dates (excluding weekends)
     */
    private function getWorkingDaysCount($startDate, $endDate)
    {
        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $days = 0;
            
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                // Skip weekends (6 = Saturday, 0 = Sunday)
                if ($date->dayOfWeek !== 0 && $date->dayOfWeek !== 6) {
                    $days++;
                }
            }
            
            return $days;
        } catch (\Exception $e) {
            Log::warning('Error calculating working days: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get recent attendance logs (today)
     */
    private function getRecentAttendanceLogs()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            
            return AttendanceEmployee::select(
                    'id', 
                    'employee_id', 
                    'employee_name',
                    'clock_in',
                    'clock_out'
                )
                ->where('date', $today)
                ->orderBy('clock_in', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            Log::warning('Error getting recent logs: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get count of late employees for a specific date
     */
    private function getLateEmployeesCount($date)
    {
        try {
            $companyStartTime = env('COMPANY_START_TIME', '09:00:00');
            $gracePeriod = (int) env('LATE_ARRIVAL_GRACE_MINUTES', 15);
            
            $query = AttendanceEmployee::where('date', $date)
                ->whereNotNull('clock_in');
            
            // Use database-specific approach for time comparison
            $driver = DB::connection()->getDriverName();
            
            if ($driver === 'mysql') {
                $query->whereRaw("TIME_TO_SEC(clock_in) > TIME_TO_SEC(?) + ?", [
                    $companyStartTime, 
                    $gracePeriod * 60
                ]);
            } else {
                $threshold = Carbon::createFromFormat('H:i:s', $companyStartTime)
                    ->addMinutes($gracePeriod)
                    ->format('H:i:s');
                
                $query->whereRaw("clock_in > ?", [$threshold]);
            }
            
            return $query->count();
        } catch (\Exception $e) {
            Log::warning('Error counting late employees: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get count of absent employees for a specific date
     */
    private function getAbsentEmployeesCount($date)
    {
        try {
            $presentEmployeeIds = AttendanceEmployee::where('date', $date)
                ->pluck('employee_id')
                ->toArray();
            
            $query = Employee::query();
            
            if (DB::getSchemaBuilder()->hasColumn('employees', 'is_active')) {
                $query->where('is_active', 1);
            }
            
            if (!empty($presentEmployeeIds)) {
                $query->whereNotIn('id', $presentEmployeeIds);
            }
            
            return $query->count();
        } catch (\Exception $e) {
            Log::warning('Error counting absent employees: ' . $e->getMessage());
            return 0;
        }
    }
}