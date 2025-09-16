<?php

namespace App\Http\Controllers;

use App\Models\AccountList;
use App\Models\Announcement;
use App\Models\AttendanceEmployee;
use App\Models\Employee;
use App\Models\Event;
use App\Models\LandingPageSection;
use App\Models\Meeting;
use App\Models\Order;
use App\Models\Payees;
use App\Models\Office;
use App\Models\Leave;
use App\Models\Holiday;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Payer;
use App\Models\Plan;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Utility;
use App\Services\DashboardMetricsService;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;
use Carbon\Carbon;  // Make sure to import Carbon at the top of your file
use DateTime;  // Make sure to import Carbon at the top of your file
use DB;  // Make sure to import Carbon at the top of your file

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::check())
        {
            $user = Auth::user();

            if($user->type == 'employee')
            {
                // **Send Notification**
                $notificationData = [
                    'title' => 'Welcome to Qubify HRMS',
                    'body' => "Employee Dashboard",
                    'fcm_token' => $user->fcm_token,
                ];
                try {
                    Helper::sendNotification($notificationData); // Call the helper function
                } catch (\Exception $e) {
                    \Log::error("Notification Error: " . $e->getMessage());
                }

                $emp = Employee::where('user_id', '=', $user->id)->first();

                $announcements = Announcement::orderBy('announcements.id', 'desc')->take(5)->leftjoin('announcement_employees', 'announcements.id', '=', 'announcement_employees.announcement_id')->where('announcement_employees.employee_id', '=', $emp->id)->orWhere(
                    function ($q){
                        $q->where('announcements.department_id', '["0"]')->where('announcements.employee_id', '["0"]');
                    }
                )->get();

                $employees = Employee::get();
                $meetings  = Meeting::orderBy('meetings.id', 'desc')->take(5)->leftjoin('meeting_employees', 'meetings.id', '=', 'meeting_employees.meeting_id')->where('meeting_employees.employee_id', '=', $emp->id)->orWhere(
                    function ($q){
                        $q->where('meetings.department_id', '["0"]')->where('meetings.employee_id', '["0"]');
                    }
                )->get();
                
                $events = Event::where('start_date', '>=', now()->format('Y-m-d'))
                                    ->orderBy('start_date', 'asc')
                                    ->limit(5)
                                    ->get();

                $allEvents = Event::where('start_date', '>=', now()->format('Y-m-d'))
                                    ->orderBy('start_date', 'asc')
                                    ->get();

                $allEvents = Event::all();
                $arrEvents = [];
                foreach ($allEvents as $event) {
                    $arr['id']    = $event['id'];
                    $arr['title'] = $event['title'];
                    $arr['description'] = $event['description'];
                    $arr['start'] = $event['start_date'];
                    $arr['end']   = $event['end_date'];
                    //                $arr['allDay']    = !0;
                    //                $arr['className'] = 'bg-danger';
                    $arr['backgroundColor'] = $event['color'];
                    $arr['borderColor']     = "#fff";
                    $arr['textColor']       = "white";
                    $arr['url']             = route('event.edit', $event['id']);

                    $arrEvents[] = $arr;
                }
                $arrEvents = str_replace('"[', '[', str_replace(']"', ']', json_encode($arrEvents)));

                $date = date("Y-m-d");
                $time = date("H:i:s");
                /*$employeeAttendance = AttendanceEmployee::orderBy('clock_in', 'desc')->where('employee_id', '=', !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0)->where('date', '=', $date)->first();
                $employeeAttendanceList = AttendanceEmployee::orderBy('clock_in', 'desc')
                    ->where('employee_id', '=', !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0)
                    ->where('date', '=', $date)
                    ->get();*/
                
                $employeeAttendance = AttendanceEmployee::orderBy('clock_in', 'desc')
                    ->where('employee_id', '=', !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0)
                    ->where('date', '=', $date)
                    ->first();
                    
                $currentDate = Carbon::now()->toDateString();

                $employeeAttendanceList = Employee::with(['attendance' => function ($query) use ($currentDate) {
                            $query->whereDate('date', $currentDate)->orderBy('clock_in', 'ASC');
                        }])
                        ->where('is_active', 1)
                        ->where('id', !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0)
                        ->get();

                $offices = Office::all();
                $departments = Department::all();
                $branches = Branch::all();

                $attendanceMetrics = $this->calculateAttendanceMetrics($emp->id);
                // $currentMonth = date('Y-m');
                // $attendanceMetrics = $this->calculateAttendanceMetrics($emp->id, $currentMonth);

                $officeTime['startTime'] = Utility::getValByName('company_start_time');
                $officeTime['endTime']   = Utility::getValByName('company_end_time');
                return view('dashboard.employee-dashnew', compact('attendanceMetrics', 'events', 'arrEvents', 'announcements', 'employees', 'meetings', 'employeeAttendance', 'officeTime', 'offices', 'employeeAttendanceList', 'departments', 'branches'));
            }
            else if($user->type == 'super admin')
            {
                // return redirect()->to('dash');
                
                
                $user                       = \Auth::user();
                $user['total_user']         = $user->countCompany();
                $user['total_paid_user']    = $user->countPaidCompany();
                $user['total_orders']       = Order::total_orders();
                $user['total_orders_price'] = Order::total_orders_price();
                $user['total_plan']         = Plan::total_plan();
                $user['most_purchese_plan'] = (!empty(Plan::most_purchese_plan()) ? Plan::most_purchese_plan()->name : '');

                $chartData = $this->getOrderChart(['duration' => 'week']);

                // Get dynamic dashboard metrics
                $dashboardMetrics = $this->getDashboardMetrics();
                
                // Get recent activities
                $activityService = new ActivityService();
                $recentActivities = $activityService->getRecentActivities(6);

                return view('dashboard.super_admin', compact('user', 'chartData', 'dashboardMetrics', 'recentActivities'));
            }
            else
            {                
                return redirect()->to('dash');              
                
                $events    = Event::where('created_by', '=', \Auth::user()->creatorId())->get();
                $arrEvents = [];

                foreach($events as $event)
                {
                    $arr['id']    = $event['id'];
                    $arr['title'] = $event['title'];
                    $arr['start'] = $event['start_date'];
                    $arr['end']   = $event['end_date'];
                    $arr['description'] = $event['description'];

                    $arr['backgroundColor'] = $event['color'];
                    $arr['borderColor']     = "#fff";
                    $arr['textColor']       = "white";
                    $arr['url']             = route('event.edit', $event['id']);

                    $arrEvents[] = $arr;
                }

                $announcements = Announcement::orderBy('announcements.id', 'desc')->take(5)->where('created_by', '=', \Auth::user()->creatorId())->get();

                $emp           = User::where('type', '=', 'employee')->where('created_by', '=', \Auth::user()->creatorId())->get();
                $countEmployee = count($emp);

                $user      = User::where('type', '!=', 'employee')->where('created_by', '=', \Auth::user()->creatorId())->get();
                $countUser = count($user);

                $countTicket      = Ticket::where('created_by', '=', \Auth::user()->creatorId())->count();
                $countOpenTicket  = Ticket::where('status', '=', 'open')->where('created_by', '=', \Auth::user()->creatorId())->count();
                $countCloseTicket = Ticket::where('status', '=', 'close')->where('created_by', '=', \Auth::user()->creatorId())->count();

                $currentDate = date('Y-m-d');

                $employees     = User::where('type', '=', 'employee')->where('created_by', '=', \Auth::user()->creatorId())->get();
                $countEmployee = count($employees);
                $notClockIn    = AttendanceEmployee::where('date', '=', $currentDate)->get()->pluck('employee_id');
                $notClockIns = Employee::where('created_by', '=', \Auth::user()->creatorId())->where('is_active', 1)->whereNotIn('id', $notClockIn)->get();
                foreach($notClockIns as $notClock){
                    $date = date("Y-m-d");
                    $currentTime = date("H:i:s");
                    $firstHalf = "13:00:00";
                    $secondHalf = "14:00:00";
                    $leaveType = Helper::checkLeaveWithTypes($date, $notClock->id);
                    if($leaveType == 0){
                        $notClock['status'] = 'Absent';
                        $notClock['class'] = 'absent-btn';
                    }else if($leaveType == 'morning halfday'){
                        if($currentTime <= $firstHalf){
                            $notClock['status'] = '1st Half Leave';
                            $notClock['class'] = 'badge badge-warning';
                        }
                    }else if($leaveType == 'afternoon halfday'){
                        if($currentTime >= $secondHalf){
                            $notClock['status'] = '2nd Half Leave';
                            $notClock['class'] = 'badge badge-warning';
                        }
                    }else if($leaveType == 'fullday Leave'){
                        $notClock['status'] = 'Leave';
                        $notClock['class'] = 'badge badge-warning';
                    // }
                    }else if($leaveType == 'on short leave'){
                        $notClock['status'] = 'Short Leave';
                        $notClock['class'] = 'badge badge-warning';
                    }
                }
                $accountBalance = AccountList::where('created_by', '=', \Auth::user()->creatorId())->sum('initial_balance');

                $totalPayee = Payees::where('created_by', '=', \Auth::user()->creatorId())->count();
                $totalPayer = Payer::where('created_by', '=', \Auth::user()->creatorId())->count();

                $meetings = Meeting::where('created_by', '=', \Auth::user()->creatorId())->limit(5)->get();

                return view('dashboard.dashboard', compact('arrEvents', 'announcements', 'employees', 'meetings', 'countEmployee', 'countUser', 'countTicket', 'countOpenTicket', 'countCloseTicket', 'notClockIns', 'countEmployee', 'accountBalance', 'totalPayee', 'totalPayer'));
            }
        }
        else
        {
            if(!file_exists(storage_path() . "/installed"))
            {
                header('location:install');
                die;
            }
            else
            {
                // return redirect('login');   // Extra code only for this demo site

                return view('front.index');



                $settings = Utility::settings();
                if($settings['display_landing_page'] == 'on')
                {
                    $plans = Plan::get();
                    $get_section = LandingPageSection::orderBy('section_order', 'ASC')->get();

                    return view('layouts.landing', compact('plans','get_section'));
                }
                else
                {
                    return redirect('login');
                }

            }
        }
    }

    /**
     * Calculate all attendance metrics for an employee
     * 
     * @param int $employeeId Employee ID
     * @param string|null $month Month in format 'Y-m' (e.g. '2025-05'). Defaults to current month.
     * @return array Array containing attendance metrics
     */
    public function calculateAttendanceMetrics($employeeId = null)
    {
        // Validate and sanitize employee ID
        if (!$employeeId && Auth::user() && Auth::user()->type == 'employee' && !empty(Auth::user()->employee)) {
            $employeeId = Auth::user()->employee->id;
        }
        
        // Validate employee ID is numeric and exists
        if (!$employeeId || !is_numeric($employeeId)) {
            \Log::warning('Invalid employee ID provided to calculateAttendanceMetrics', ['employeeId' => $employeeId]);
            return $this->getDefaultMetrics();
        }
        
        // Verify employee exists and is active
        $employee = Employee::where('id', $employeeId)->where('is_active', 1)->first();
        if (!$employee) {
            \Log::warning('Employee not found or inactive', ['employeeId' => $employeeId]);
            return $this->getDefaultMetrics();
        }
        
        // Get current month start date and today's date
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-d'); // Today
        
        // Create cache key based on employee ID, start date, and end date
        $cacheKey = "attendance_metrics_{$employeeId}_{$startDate}_{$endDate}";
        
        // Try to get cached data first (cache for 5 minutes)
        $cachedMetrics = \Cache::get($cacheKey);
        if ($cachedMetrics) {
            \Log::info('Using cached attendance metrics', ['employeeId' => $employeeId, 'cacheKey' => $cacheKey]);
            return $cachedMetrics;
        }
        
        // Default return values
        $metrics = [
            'presentRate' => 0, 
            'absentRate' => 0, 
            'lateRate' => 0, 
            'presentDays' => 0, 
            'absentDays' => 0, 
            'lateDays' => 0, 
            'totalDays' => 0,
            'avgCheckIn' => null
        ];
        
        // Get company settings for office hours
        $companyStartTime = Utility::getValByName('company_start_time');
        
        // Convert company start time to seconds for proper comparison
        $startTimeObj = new DateTime($companyStartTime);
        $startTimeSeconds = $startTimeObj->format('H') * 3600 + $startTimeObj->format('i') * 60 + $startTimeObj->format('s') + 900;
        
        // Get all working days in the period (excluding weekends and holidays)
        $workingDays = [];
        $currentDate = new DateTime($startDate);
        $endDateTime = new DateTime($endDate);
        
        // Get holidays
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->toArray();
        
        // Get approved leaves for the employee
        $leaves = Leave::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->get();
        
        $leaveDates = [];
        foreach ($leaves as $leave) {
            $leaveStart = new DateTime($leave->start_date);
            $leaveEnd = new DateTime($leave->end_date);
            
            while ($leaveStart <= $leaveEnd) {
                $leaveDates[] = $leaveStart->format('Y-m-d');
                $leaveStart->modify('+1 day');
            }
        }
        
        // Get weekend configuration
        $weekends = ['Saturday', 'Sunday']; // Default weekends, modify as per your company policy
        
        // Loop through dates and check if they are working days
        while ($currentDate <= $endDateTime) {
            $currentDateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->format('l');
            
            // Check if the date is a working day (not a weekend, holiday, or leave)
            if (!in_array($dayOfWeek, $weekends) && 
                !in_array($currentDateStr, $holidays) && 
                !in_array($currentDateStr, $leaveDates)) {
                $workingDays[] = $currentDateStr;
            }
            
            $currentDate->modify('+1 day');
        }
        
        $totalWorkingDays = count($workingDays);
        $metrics['totalDays'] = $totalWorkingDays;
        
        if ($totalWorkingDays == 0) {
            return $metrics;
        }
        
        // Get attendance records for the employee in this period
        $attendanceRecords = AttendanceEmployee::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('date');
        
        // Calculate average first check-in time and count present, absent, late days
        $totalFirstCheckInSeconds = 0;
        $checkInCount = 0;
        
        foreach ($workingDays as $day) {
            if (isset($attendanceRecords[$day])) {
                $metrics['presentDays']++;
                
                // Get the first clock-in for this day
                $dayRecords = $attendanceRecords[$day]->sortBy('clock_in');
                $firstClockIn = $dayRecords->first();
                
                if ($firstClockIn && !empty($firstClockIn->clock_in)) {
                    // Calculate seconds from midnight for average
                    $timeObj = new DateTime($firstClockIn->clock_in);
                    $seconds = $timeObj->format('H') * 3600 + $timeObj->format('i') * 60 + $timeObj->format('s');
                    $totalFirstCheckInSeconds += $seconds;
                    $checkInCount++;
                    
                    // Check if late - employee is late if their clock-in time is after the company start time
                    if ($seconds > $startTimeSeconds) {
                        $metrics['lateDays']++;
                    }
                }
            }
        }
        
        // Calculate leave days properly for the current period
        $leaveDays = 0;
        foreach ($leaveDates as $leaveDate) {
            if ($leaveDate >= $startDate && $leaveDate <= $endDate) {
                $dayOfWeek = date('l', strtotime($leaveDate));
                // Only count leave days that fall on working days
                if (!in_array($dayOfWeek, $weekends) && !in_array($leaveDate, $holidays)) {
                    $leaveDays++;
                }
            }
        }
        $metrics['leaveDays'] = $leaveDays;
        
        // Calculate absent days (working days - present days - leave days)
        $metrics['absentDays'] = $totalWorkingDays - $metrics['presentDays'] - $leaveDays;
        
        // Ensure absent days is not negative
        if ($metrics['absentDays'] < 0) {
            $metrics['absentDays'] = 0;
        }

        // Calculate on leave days for the current month (keep for backward compatibility)
        $onLeaveDays = Leave::where('employee_id', $employeeId)
            ->whereIn('status', ['Approve', 'Approved'])
            ->whereMonth('start_date', Carbon::now()->month)
            ->whereYear('start_date', Carbon::now()->year)
            ->count();
        $metrics['onLeaveDays'] = $onLeaveDays;
        
        // Calculate rates (as percentages)
        if ($totalWorkingDays > 0) {
            $metrics['presentRate'] = round(($metrics['presentDays'] / $totalWorkingDays) * 100, 2);
            $metrics['absentRate'] = round(($metrics['absentDays'] / $totalWorkingDays) * 100, 2);
            
            // Calculate late rate as percentage
            if ($metrics['presentDays'] > 0) {
                $metrics['lateRate'] = round(($metrics['lateDays'] / $metrics['presentDays']) * 100, 2);
            }
        }
        
        // Calculate average check-in time
        if ($checkInCount > 0) {
            $avgSeconds = $totalFirstCheckInSeconds / $checkInCount;
            $hours = floor($avgSeconds / 3600);
            $minutes = floor(($avgSeconds % 3600) / 60);
            $seconds = floor($avgSeconds % 60);
            
            $metrics['avgCheckIn'] = sprintf('%02d:%02d', $hours, $minutes);
        }
        
        // Calculate previous month's metrics
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $previousWorkingDays = 0;
        $previousPresentDays = 0;
        $previousAbsentDays = 0;
        $previousLateDays = 0;

        // Loop through each day of the previous month
        for ($date = $previousMonthStart->copy(); $date->lte($previousMonthEnd); $date->addDay()) {
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            // Check for holidays
            $isHoliday = Holiday::where('date', $date->format('Y-m-d'))->exists();
            if ($isHoliday) {
                continue;
            }

            // Check for approved leaves
            $isLeave = Leave::where('employee_id', $employeeId)
                ->whereIn('status', ['Approve', 'Approved'])
                ->where(function ($query) use ($date) {
                    $query->where('start_date', '<=', $date->format('Y-m-d'))
                        ->where('end_date', '>=', $date->format('Y-m-d'));
                })
                ->exists();

            if ($isLeave) {
                continue;
            }

            $previousWorkingDays++;

            // Check attendance for the day
            $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                ->whereDate('date', $date->format('Y-m-d'))
                ->first();

            if ($attendance) {
                $previousPresentDays++;

                // Check for late arrival
                $companyStartTime = Utility::getValByName('company_start_time');
                if ($companyStartTime) {
                    $clockInTime = Carbon::parse($attendance->clock_in);
                    $startTime = Carbon::parse($companyStartTime);

                    if ($clockInTime->gt($startTime)) {
                        $previousLateDays++;
                    }
                }
            } else {
                $previousAbsentDays++;
            }
        }

        // Calculate previous month's rates
        $metrics['previous_present_percent'] = $previousWorkingDays > 0 ? round(($previousPresentDays / $previousWorkingDays) * 100, 2) : 0;
        $metrics['previous_absent_percent'] = $previousWorkingDays > 0 ? round(($previousAbsentDays / $previousWorkingDays) * 100, 2) : 0;
        $metrics['previous_late_percent'] = $previousPresentDays > 0 ? round(($previousLateDays / $previousPresentDays) * 100, 2) : 0;

        // Calculate previous month's on leave days
        $previousOnLeaveDays = 0;
        $previousOnLeaveDays = Leave::where('employee_id', $employeeId)
            ->whereIn('status', ['Approve', 'Approved'])
            ->whereMonth('start_date', Carbon::now()->subMonth()->month)
            ->whereYear('start_date', Carbon::now()->subMonth()->year)
            ->count();
        $metrics['previous_on_leave_days'] = $previousOnLeaveDays;

        // Validate and sanitize all numeric values
        $metrics['presentDays'] = max(0, intval($metrics['presentDays']));
        $metrics['absentDays'] = max(0, intval($metrics['absentDays']));
        $metrics['lateDays'] = max(0, intval($metrics['lateDays']));
        $metrics['leaveDays'] = max(0, intval($metrics['leaveDays']));
        $metrics['totalDays'] = max(0, intval($metrics['totalDays']));
        
        // Validate percentages are within valid range
        $metrics['presentRate'] = max(0, min(100, floatval($metrics['presentRate'])));
        $metrics['absentRate'] = max(0, min(100, floatval($metrics['absentRate'])));
        $metrics['lateRate'] = max(0, min(100, floatval($metrics['lateRate'])));

        // Add pie chart data structure with validated data
        $metrics['pieChartData'] = [
            'labels' => ['Present', 'Absent', 'Late', 'Leave'],
            'data' => [
                $metrics['presentDays'],
                $metrics['absentDays'],
                $metrics['lateDays'],
                $metrics['leaveDays']
            ],
            'colors' => ['#10b981', '#ef4444', '#f59e0b', '#8b5cf6']
        ];

        // Final validation: ensure data consistency
        $totalCalculated = $metrics['presentDays'] + $metrics['absentDays'] + $metrics['leaveDays'];
        if ($totalCalculated > $metrics['totalDays']) {
            \Log::warning('Attendance metrics calculation inconsistency detected', [
                'employeeId' => $employeeId,
                'totalCalculated' => $totalCalculated,
                'totalDays' => $metrics['totalDays'],
                'metrics' => $metrics
            ]);
        }

        // Cache the results for 5 minutes to improve performance
        \Cache::put($cacheKey, $metrics, 300); // 300 seconds = 5 minutes
        \Log::info('Cached attendance metrics', ['employeeId' => $employeeId, 'cacheKey' => $cacheKey]);

        return $metrics;
    }

    /**
     * Get default metrics structure for error cases
     */
    private function getDefaultMetrics()
    {
        return [
            'presentRate' => 0.0, 
            'absentRate' => 0.0, 
            'lateRate' => 0.0, 
            'presentDays' => 0, 
            'absentDays' => 0, 
            'lateDays' => 0, 
            'leaveDays' => 0,
            'totalDays' => 0,
            'avgCheckIn' => null,
            'onLeaveDays' => 0,
            'previous_present_percent' => 0.0,
            'previous_absent_percent' => 0.0,
            'previous_late_percent' => 0.0,
            'previous_on_leave_days' => 0,
            'pieChartData' => [
                'labels' => ['Present', 'Absent', 'Late', 'Leave'],
                'data' => [0, 0, 0, 0],
                'colors' => ['#10b981', '#ef4444', '#f59e0b', '#8b5cf6']
            ]
        ];
    }
    
    
    
    
    
    


    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if($arrParam['duration'])
        {
            if($arrParam['duration'] == 'week')
            {
                $previous_week = strtotime("-2 week +1 day");
                for($i = 0; $i < 14; $i++)
                {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask          = [];
        $arrTask['label'] = [];
        $arrTask['data']  = [];
        foreach($arrDuration as $date => $label)
        {

            $data               = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][]  = $data->total;
        }

        return $arrTask;
    }

    /**
     * Get all dashboard metrics
     *
     * @return array
     */
    public function getDashboardMetrics(): array
    {
        $metricsService = new DashboardMetricsService();
        return $metricsService->getAllMetrics();
    }
}
