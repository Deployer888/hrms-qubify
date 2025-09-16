<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\MobileNotification;
use App\Mail\{LeaveActionSend, LeaveRequest};
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Imports\EmployeesImport;
use App\Exports\LeaveExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Services\FcmService;
use Illuminate\Htt;

class LeaveController extends Controller
{
    private $fcmService;
    
    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function index()
    {
        $currentYear = now()->year;
        if(\Auth::user()->can('Manage Leave'))
        {
            // $leaves = Leave::where('created_by', '=', \Auth::user()->creatorId())->get();
            if(\Auth::user()->type == 'employee')
            {
                $user     = \Auth::user();
                $employee = Employee::where('user_id', '=', $user->id)->where('is_active', 1)->first();
                $leaves   = Leave::where('employee_id', '=', $employee->id)->whereYear('created_at', $currentYear)->orderBy('id','DESC')->get();
                $selfLeaves = true; // for TeamLeader member-leaves view page logic
            }
            else
            {
                // $leaves = Employee::with('employeeLeaves')->where('is_active', 1)->get()->sortByDesc(function($employee) {
                $leaves = Employee::with('employeeLeaves')->where('is_active', 1)->get()->sortByDesc(function($employee) {
                    return $employee->employeeLeaves->isEmpty() 
                        ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', '1900-01-01 00:00:00')
                        : $employee->employeeLeaves->first()->applied_on;
                });
                // $leaves = Leave::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id','DESC')->get();
                $selfLeaves = false; // for TeamLeader member-leaves view page logic
            }
            // dd($leaves); 
            return view('leave.index', compact('leaves', 'selfLeaves'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        $currentYear = now()->year;
        if(\Auth::user()->can('Create Leave'))
        {
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
            $totalLeaveAvailed = '';
            if(Auth::user()->type == 'employee')
            {
                $employees = Employee::where('user_id', '=', \Auth::user()->id)->get()->pluck('name', 'id');
                $employee = Employee::where('user_id', '=', \Auth::user()->id)->first();
                $id = \Auth::user()->id;
                
                if(Auth::user()->employee->is_probation == 1){
                    $leavetypes = LeaveType::leftJoin('employees', function ($join) use ($id) {
                                        $join->on('employees.user_id', '=', DB::raw($id));
                                    })
                                    ->leftJoin('leaves', function ($join) use ($employee) {
                                        $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                                             ->where('leaves.employee_id', '=', $employee->id);
                                    })
                                    ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                                    ->whereIn('leave_types.title', ['Sick Leave']) // Only show Sick Leave for probation employees
                                    ->select(
                                        'leave_types.id',
                                        'leave_types.title',
                                       DB::raw('
                                            CASE
                                                WHEN leave_types.title = "Sick Leave" THEN
                                                    leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0) - 2
                                                ELSE
                                                    leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0)
                                            END AS days
                                        ')
                                    )
                                    ->groupBy('leave_types.id', 'leave_types.title', 'leave_types.days') // Adjust groupBy to match the select statement
                                    ->get();
                }
                else {
                    // Get all leave types for the employee
                    $allLeaveTypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
                    $leavetypes = collect();
                    $formattedDate = now()->format('Y-m-d');

                    foreach ($allLeaveTypes as $leaveType) {
                        // Skip gender-specific leave types
                        if ($employee->gender == "Male" && $leaveType->title == "Maternity Leaves") {
                            continue;
                        }
                        if ($employee->gender == "Female" && $leaveType->title == "Paternity Leaves") {
                            continue;
                        }
                        
                        // Calculate available balance using the SAME logic as employee details page
                        if ($leaveType->title == 'Paid Leave') {
                            // Use real-time balance calculation for paid leave
                            if ($employee->is_probation == 1) {
                                $availableBalance = 0;
                            } else {
                                // Fix: Use proper balance calculation from accrual system
                                $breakdown = $employee->getBalanceBreakdown();
                                $availableBalance = $breakdown['available_balance']; // Use available_balance which excludes pending
                            }
                        } else {
                            // Use EXACT same logic as employee details page for other leave types
                            $leavesAvailed = Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leaveType->id);
                            
                            if ($employee->is_probation == 1) {
                                $availableBalance = max(0, $leaveType->days - $leavesAvailed - 2);
                            } else {
                                $availableBalance = max(0, $leaveType->days - $leavesAvailed);
                            }
                        }
                        
                        // Create object with same structure as before
                        $leaveTypeObj = (object) [
                            'id' => $leaveType->id,
                            'title' => $leaveType->title,
                            'days' => $availableBalance
                        ];
                        
                        $leavetypes->push($leaveTypeObj);
                    }
                }

                $status = ['Pending'];
            
                $totalLeaveAvailed = Leave::where('employee_id', $employee->id)
                            ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                            ->whereIn('status', $status)
                            ->where('leave_type_id', 3)
                            ->sum('total_leave_days');
                
            }
            else
            {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->where('is_active', 1)->get()->pluck('name', 'id');
                $leavetypes = LeaveType::where('created_by', \Auth::user()->creatorId())
                    ->with(['leaves' => function ($query) use ($currentYear){
                        $query->selectRaw('leave_type_id, SUM(total_leave_days) as total_leave_days')
                                ->whereYear('created_at', $currentYear)
                                ->groupBy('leave_type_id');
                    }])->get();
                
                // Iterate through each LeaveType to update days with total_leave_days or keep original value
                $leavetypes->each(function ($leavetype) {
                    $totalLeaveDays = $leavetype->leaves->sum('total_leave_days');
                    $leavetype->days = $totalLeaveDays > 0 ? $totalLeaveDays : $leavetype->days;
                });

            }
            
            // Convert collection to array and then back to objects
            $leavetypes = collect($leavetypes->toArray())->map(function ($item) {
                return (object) $item;
            });

            // echo $leavetypes;
            $leavetypes_days = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
            return view('leave.create', compact('employees', 'leavetypes', 'leavetypes_days', 'totalLeaveAvailed', 'startOfMonth', 'endOfMonth'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    
    public function getLeaveBalance(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $leaveTypeId = $request->get('leave_type_id');
        $startOfMonth = $request->get('start_of_month');
        $endOfMonth = $request->get('end_of_month');
        $currentYear = now()->year;
    
        $totalLeaveAvailed = Helper::totalLeaveAvailed($employeeId, $startOfMonth, $endOfMonth);
        $leaveTitle = LeaveType::where('id', '=', $request->get('leave_type_id'))->pluck('title')->first();
        return response()->json(['totalLeaveAvailed' => $totalLeaveAvailed, 'leaveTitle' => $leaveTitle]);
    }

    public function getEmployeeLeaveBalances(Request $request)
    {
        $employeeId = $request->get('employee_id');
        
        if (!$employeeId) {
            return response()->json(['error' => 'Employee ID is required'], 400);
        }
        
        // Force fresh data - clear any model caching
        $employee = Employee::find($employeeId);
        if ($employee) {
            $employee->refresh(); // Force reload from database
        }
        
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
        
        // Get all leave types for this company
        $leaveTypes = LeaveType::where('created_by', $employee->created_by)->get();
        $leaveBalances = [];
        $formattedDate = now()->format('Y-m-d');
        
        foreach ($leaveTypes as $leaveType) {
            // Skip gender-specific leave types
            if ($employee->gender == "Male" && $leaveType->title == "Maternity Leaves") {
                continue;
            }
            if ($employee->gender == "Female" && $leaveType->title == "Paternity Leaves") {
                continue;
            }
            
            // For probation employees, only show Sick Leave
            if ($employee->is_probation == 1 && $leaveType->title != 'Sick Leave') {
                continue;
            }
            
            // Calculate available balance using the SAME logic as employee details page
            if ($leaveType->title == 'Paid Leave') {
                // Use real-time balance calculation for paid leave (same as employee/show.blade.php)
                if ($employee->is_probation == 1) {
                    $availableBalance = 0;
                } else {
                    $breakdown = $employee->getBalanceBreakdown();
                    $availableBalance = $breakdown['available_balance']; // Fix: Use available_balance which excludes pending
                }
            } else {
                // Use EXACT same logic as employee details page for other leave types
                $leavesAvailed = Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leaveType->id);
                
                if ($employee->is_probation == 1) {
                    $availableBalance = max(0, $leaveType->days - $leavesAvailed - 2);
                } else {
                    $availableBalance = max(0, $leaveType->days - $leavesAvailed);
                }
            }
            
            $leaveBalances[] = [
                'id' => $leaveType->id,
                'title' => $leaveType->title,
                'days' => $availableBalance, // Add days field for consistency
                'available_balance' => $availableBalance,
                'display_text' => $leaveType->title . ' (' . $availableBalance . ')'
            ];
        }
        
        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'is_probation' => $employee->is_probation
            ],
            'leave_balances' => $leaveBalances,
            'timestamp' => now()->timestamp // Add timestamp for debugging
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
          ->header('Pragma', 'no-cache')
          ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $currentYear = now()->year;
        if (\Auth::user()->can('Create Leave')) {
            $validator = \Validator::make(
                $request->all(), [
                    'leave_type_id' => 'required',
                    'start_date' => 'required|date|after_or_equal:today',
                    // 'end_date' => 'required|date|after_or_equal:start_date',
                    'leave_reason' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if(\Auth::user()->type == 'employee') {
                $employee = Employee::where('user_id', \Auth::user()->id)->first();
            }
            else {
                $employee = Employee::where('id', $request->employee_id)->first();
            }
            
            // Calculate total leave days
            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date);
            $endDate->modify('+1 day'); // Include end date
            $interval = new \DateInterval('P1D');
            $daterange = new \DatePeriod($startDate, $interval, $endDate);
            $total_leave_days = 0;
            $startTime = $request->start_time;
            $endTime = null;

            if($startTime) {
                try {
                    // Create DateTime object
                    $startTimeObj = new \DateTime($startTime);
                    
                    // Create end time (clone to avoid modifying original)
                    $endTimeObj = clone $startTimeObj;
                    $endTimeObj->modify('+2 hours');
                    
                    // Store as DateTime objects or formatted strings as needed
                    $endTime = $endTimeObj;
                    // OR if you need formatted strings:
                    // $endTime = $endTimeObj->format('h:i A');
                    
                } catch (\Exception $e) {
                    // Handle invalid time format
                    error_log("Invalid time format: " . $startTime);
                    $endTime = null;
                }
            }

            

            // Fix: Proper leave days calculation based on duration
            if ($request->is_halfday == 'half') {
                $total_leave_days = 0.5;
            } elseif ($request->is_halfday == 'short') {
                $total_leave_days = 0.25; // Short leave is always 0.25 days (2 hours)
            } else {
                // Full day calculation
                $interval = $startDate->diff($endDate);
                // Get the number of days as an integer
                $total_leave_days = $interval->days;

                if($total_leave_days <= 7){
                    $total_leave_days = 0;
                    foreach ($daterange as $date) {
                        // Check if the day is not Saturday (6) or Sunday (7)
                        if ($date->format('N') < 6) { // 'N' gives day of the week, 1 (Monday) to 7 (Sunday)
                            $total_leave_days++;
                        }
                    }
                }
            }

            if($endTime) { $endTime = $endTime->format('g:i A'); }

            // Fetch allowed leave days for the selected leave type
            $leaveType = LeaveType::find($request->leave_type_id);
            if (!$leaveType) {
                return redirect()->back()->with('error', __('Invalid leave type selected.'));
            }

            // Calculate total leave taken by the employee for the current year for this leave type
            $currentYear = date('Y');
            $status = ['Pending', 'Approve'];
            $leavesTaken = Leave::where('employee_id', $employee->id)
                ->where('leave_type_id', $request->leave_type_id)
                ->whereYear('start_date', $currentYear)
                ->whereIn('status', $status)
                ->sum('total_leave_days');

            if (($leavesTaken + $total_leave_days) > $leaveType->days) {
                return redirect()->back()->with('error', __('You have exceeded the maximum allowed leave days for this leave type.'));
            }

            $leave = new Leave();
            if (\Auth::user()->type == "employee") {
                $leave->employee_id = $employee->id;
            } else {
                $leave->employee_id = $request->employee_id;
            }
            // dd($request->start_date, $request->end_date);
            $leave->leave_type_id = $request->leave_type_id;
            $leave->applied_on = date('Y-m-d');
            $leave->start_date = date('Y-m-d', strtotime($request->start_date));
            $leave->end_date = date('Y-m-d', strtotime($request->end_date));
            $leave->total_leave_days = $total_leave_days;
            $leave->leave_reason = $request->leave_reason;
            $leave->remark = $request->remark;
            $leave->status = 'Pending';
            $leave->created_by = \Auth::user()->creatorId();
            $leave->leavetype = $request->is_halfday;
            if ($request->is_halfday == 'short') {
                $leave->start_time = $request->start_time;
                $leave->end_time = $endTime;
            } elseif ($request->is_halfday == 'half') {
                $leave->day_segment = $request->day_segment;
                $leave->is_halfday = 1;
            }
            
            $leave->save();
            
            // Note: Balance deduction is handled by the accrual system automatically
            // The getBalanceBreakdown() method calculates available balance including pending leaves
            try {
                $employee = Employee::find($leave->employee_id);
                $employeeEmail = $employee->email;
                $employeeName = $employee->name;
                $additionalReceivers = [
                    'chitranshu@qubifytech.com',
                    'sharma.chitranshu@gmail.com',
                    'hr@qubifytech.com',
                    'shivani@qubifytech.com',
                    'abhishek@qubifytech.com',
                    // 'qubifydeveloper@gmail.com',
                    // 'karan@qubifytech.com',
                ];

                $teamLeader = '';
                $tlid = '';
                if($employee->is_team_leader == 0){
                    $teamLeader = $employee->getTeamLeaderNameAndId();
                    $tlid = Employee::find($teamLeader->id)->user_id;
                }

                $emails = array();
                if(!empty($teamLeader)) {
                    Mail::to($teamLeader)
                            ->cc($additionalReceivers)
                            ->send(new LeaveRequest($leave, $employeeEmail, $employeeName));
                    
                    // Notification to Company, HR, TL, Employee
                    $fcmTokens = User::whereIn('type', ['hr', 'company'])
                                        ->orWhere('id', $employee->user_id)
                                        ->orWhere('id', $tlid)
                                        ->orWhere('id', 5)
                                        ->pluck('fcm_token','name')
                                        ->toArray();

                    foreach ($fcmTokens as $key => $fcmToken) {
                        $notificationData = [
                            'title' => "Leave Notification",
                            'body' => "Leave Applied by " . $employee->name,
                            'fcm_token' => $fcmToken,
                        ];
                        try {
                            Helper::sendNotification($notificationData); // Call the helper function
                        } catch (\Exception $e) {
                            \Log::error("Notification Error: " . $e->getMessage());
                        }
                    }
                    
                } else {
                    Mail::to($additionalReceivers)->send(new LeaveRequest($leave, $employeeEmail, $employeeName));
                }

                $users = User::whereIn('type', ['hr', 'company'])
                            ->orWhere('id', $employee->user_id)
                            ->when($tlid, function($query) use ($tlid) {
                                return $query->orWhere('id', $tlid);
                            })
                            ->orWhere('id', 5)
                            ->get(['id', 'name', 'fcm_token']);
                foreach ($users as $user) {
                    if (!empty($user->fcm_token)) {
                        $notificationData = [
                            'title' => "Leave Notification",
                            'body' => "Leave Applied by " . $employee->name,
                            'fcm_token' => $user->fcm_token,
                        ];
                        try {
                            $result = $this->fcmService->sendToDevice(
                                $notificationData['fcm_token'],
                                $notificationData['title'],
                                $notificationData['body']
                            );
                            $notification = MobileNotification::create([
                                'user_id' => $user->id,
                                'title' => $notificationData['title'],
                                'body' => $notificationData['body'],
                                'is_read' => false,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error("Notification Error for user {$user->id}: " . $e->getMessage());
                        }
                    }
                }


            } catch (\Exception $e) {
                Log::error('Mail sending failed: ' . $e->getMessage());
            }
            
            return redirect()->back()->with('success', __('Leave successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Leave $leave)
    {
        return redirect()->route('leave.index');
    }

    public function edit(Leave $leave)
    {
        $currentYear = now()->year;
        if(\Auth::user()->can('Edit Leave'))
        {
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
            if($leave->created_by == \Auth::user()->creatorId())
            {
                $totalLeaveAvailed = '';
                $employees  = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                
                if(Auth::user()->type == 'employee')
                {
                    $employees = Employee::where('user_id', '=', \Auth::user()->id)->get()->pluck('name', 'id');
                    $employee = Employee::where('user_id', '=', \Auth::user()->id)->first();
                    $id = \Auth::user()->id;
                    
                    if(Auth::user()->employee->is_probation == 1){
                        $leavetypes = LeaveType::leftJoin('employees', function ($join) use ($id) {
                                            $join->on('employees.user_id', '=', DB::raw($id));
                                        })
                                        ->leftJoin('leaves', function ($join) use ($employee) {
                                            $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                                                ->where('leaves.employee_id', '=', $employee->id);
                                        })
                                        ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                                        ->whereIn('leave_types.title', ['Sick Leave']) // Only show Sick Leave for probation employees
                                        ->select(
                                            'leave_types.id',
                                            'leave_types.title',
                                        DB::raw('
                                                CASE
                                                    WHEN leave_types.title = "Sick Leave" THEN
                                                        leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0) - 2
                                                    ELSE
                                                        leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0)
                                                END AS days
                                            ')
                                        )
                                        ->groupBy('leave_types.id', 'leave_types.title', 'leave_types.days') // Adjust groupBy to match the select statement
                                        ->get();
                    }
                    else {
                        // Get all leave types for the employee using corrected balance calculation
                        $allLeaveTypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
                        $leavetypes = collect();
                        $formattedDate = now()->format('Y-m-d');

                        foreach ($allLeaveTypes as $leaveType) {
                            // Skip gender-specific leave types
                            if ($employee->gender == "Male" && $leaveType->title == "Maternity Leaves") {
                                continue;
                            }
                            if ($employee->gender == "Female" && $leaveType->title == "Paternity Leaves") {
                                continue;
                            }
                            
                            // Calculate available balance using the SAME logic as employee details page
                            if ($leaveType->title == 'Paid Leave') {
                                // Use real-time balance calculation for paid leave
                                if ($employee->is_probation == 1) {
                                    $availableBalance = 0;
                                } else {
                                    $breakdown = $employee->getBalanceBreakdown();
                                    $availableBalance = $breakdown['current_balance'];
                                }
                            } else {
                                // Use EXACT same logic as employee details page for other leave types
                                $leavesAvailed = Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leaveType->id);
                                
                                if ($employee->is_probation == 1) {
                                    $availableBalance = max(0, $leaveType->days - $leavesAvailed - 2);
                                } else {
                                    $availableBalance = max(0, $leaveType->days - $leavesAvailed);
                                }
                            }
                            
                            // Create object with same structure as before
                            $leaveTypeObj = (object) [
                                'id' => $leaveType->id,
                                'title' => $leaveType->title,
                                'days' => $leaveType->title == 'Paid Leave' ? $employee->paid_leave_balance : $availableBalance
                            ];
                            
                            $leavetypes->push($leaveTypeObj);
                        }
                    }
                
                        $status = ['Pending'];
                        
                
                    $totalLeaveAvailed = Leave::where('employee_id', $employee->id)
                                ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                                ->whereIn('status', $status)
                                ->where('leave_type_id', 3)
                                ->sum('total_leave_days');
                    
                }
                else
                {
                    $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->where('is_active', 1)->get()->pluck('name', 'id');
                    
                    // For HR users editing a leave, show balances for the specific employee whose leave is being edited
                    $employee = Employee::find($leave->employee_id);
                    
                    if ($employee) {
                        // Get all leave types for the employee using corrected balance calculation
                        $allLeaveTypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
                        $leavetypes = collect();
                        $formattedDate = now()->format('Y-m-d');
                        
                        foreach ($allLeaveTypes as $leaveType) {
                            // Skip gender-specific leave types
                            if ($employee->gender == "Male" && $leaveType->title == "Maternity Leaves") {
                                continue;
                            }
                            if ($employee->gender == "Female" && $leaveType->title == "Paternity Leaves") {
                                continue;
                            }
                            
                            // Calculate available balance using the SAME logic as employee details page
                            if ($leaveType->title == 'Paid Leave') {
                                // Use real-time balance calculation for paid leave
                                if ($employee->is_probation == 1) {
                                    $availableBalance = 0;
                                } else {
                                    $breakdown = $employee->getBalanceBreakdown();
                                    $availableBalance = $breakdown['current_balance'];
                                }
                            } else {
                                // Use EXACT same logic as employee details page for other leave types
                                $leavesAvailed = Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leaveType->id);
                                
                                if ($employee->is_probation == 1) {
                                    $availableBalance = max(0, $leaveType->days - $leavesAvailed - 2);
                                } else {
                                    $availableBalance = max(0, $leaveType->days - $leavesAvailed);
                                }
                            }
                            
                            // Create object with same structure as before
                            $leaveTypeObj = (object) [
                                'id' => $leaveType->id,
                                'title' => $leaveType->title,
                                'days' => $availableBalance
                            ];
                            
                            $leavetypes->push($leaveTypeObj);
                        }
                    } else {
                        // Fallback if employee not found
                        $leavetypes = LeaveType::where('created_by', \Auth::user()->creatorId())->get();
                    }

                }
                
                // Convert collection to array and then back to objects
                $leavetypes = collect($leavetypes->toArray())->map(function ($item) {
                    return (object) $item;
                });
                
                $leavetypes_days = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
                
                return view('leave.edit', compact('leave', 'employees', 'leavetypes', 'leavetypes_days', 'totalLeaveAvailed', 'startOfMonth', 'endOfMonth'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $leave)
    {
        $leave = Leave::find($leave);

        if(\Auth::user()->can('Edit Leave'))
        {
            if($leave->created_by == Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                        'leave_type_id' => 'required',
                        'start_date' => 'required',
                        'leave_reason' => 'required',
                    ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $startDate = new \DateTime($request->start_date);
                $endDate = new \DateTime($request->end_date);
                $endDate->modify('+1 day');
                $interval = new \DateInterval('P1D');
                $daterange = new \DatePeriod($startDate, $interval, $endDate);
                
                // Issue #4 Fix: Calculate end time for short leave with proper format handling
                $calculatedEndTime = null;
                if ($request->is_halfday == 'short') {
                    try {
                        // Try 24-hour format first (HTML5 time input)
                        if (preg_match('/^\d{2}:\d{2}$/', $request->start_time)) {
                            $startTime = \DateTime::createFromFormat('H:i', $request->start_time);
                        } else {
                            // Fallback to 12-hour format
                            $startTime = \DateTime::createFromFormat('h:i A', $request->start_time);
                        }
                        $calculatedEndTime = clone $startTime;
                        $calculatedEndTime->modify('+2 hours');
                    } catch (\Exception $e) {
                        // Log error and set default
                        \Log::error('Time parsing error: ' . $e->getMessage());
                        $calculatedEndTime = null;
                    }
                }

                if ($request->is_halfday == 'half') {
                    $total_leave_days = 0.5;
                } elseif ($request->is_halfday == 'short') {
                    $total_leave_days = 0.25; // Short leave is always 0.25 days (2 hours)
                } else {
                    $interval = $startDate->diff($endDate);
                    $total_leave_days = $interval->days;

                    if($total_leave_days <= 7){
                        $total_leave_days = 0;
                        foreach ($daterange as $date) {
                            if ($date->format('N') < 6) {
                                $total_leave_days++;
                            }
                        }
                    }
                }

                // Fetch allowed leave days for the selected leave type
                $leaveType = LeaveType::find($request->leave_type_id);
                if (!$leaveType) {
                    return redirect()->back()->with('error', __('Invalid leave type selected.'));
                }

                // Calculate total leave taken by the employee for the current year for this leave type
                $currentYear = date('Y');
                $status = ['Pending', 'Approve'];
                $leavesTaken = Leave::where('employee_id', $leave->employee_id)
                    ->where('leave_type_id', $request->leave_type_id)
                    ->whereYear('start_date', $currentYear)
                    ->whereIn('status', $status)
                    ->where('id', '!=', $leave->id)
                    ->sum('total_leave_days');

                if (($leavesTaken + $total_leave_days) > $leaveType->days) {
                    return redirect()->back()->with('error', __('You have exceeded the maximum allowed leave days for this leave type.'));
                }

                $leave->employee_id = $leave->employee_id;
                $leave->leave_type_id = $request->leave_type_id;
                $leave->start_date = date('Y-m-d', strtotime($request->start_date));
                $leave->end_date = date('Y-m-d', strtotime($request->end_date));
                $leave->total_leave_days = $total_leave_days;
                $leave->leave_reason = $request->leave_reason;
                $leave->remark = $request->remark;
                $leave->leavetype = $request->is_halfday;
                
                // Issue #4 Fix: Handle different leave types with proper time formatting
                if ($request->is_halfday == 'short') {
                    $leave->start_time = $request->start_time;
                    $leave->end_time = $calculatedEndTime ? $calculatedEndTime->format('H:i') : null;
                    $leave->day_segment = $request->day_segment;
                } elseif ($request->is_halfday == 'half') {
                    $leave->day_segment = $request->day_segment;
                    $leave->is_halfday = 1;
                    $leave->start_time = null;
                    $leave->end_time = null;
                } else {
                    $leave->start_time = null;
                    $leave->end_time = null;
                    $leave->day_segment = null;
                    $leave->is_halfday = 0;
                }
                
                // Issue #4 Fix: Handle balance adjustments for leave updates
                $originalLeave = Leave::find($leave->id);
                $originalDays = $originalLeave->total_leave_days;
                $originalStatus = $originalLeave->status;
                $originalLeaveTypeId = $originalLeave->leave_type_id;
                
                $leave->save();
                
                // Note: Balance adjustments are handled by the accrual system automatically
                // The getBalanceBreakdown() method calculates balance based on leave records
                
                $employee = Employee::find($leave->employee_id);

                // Update Notification to Company, HR, TL, Employee (rest of your existing code)
                $employeeEmail = $employee->email;
                $employeeName = $employee->name;
                $additionalReceivers = [
                    'chitranshu@qubifytech.com',
                    'sharma.chitranshu@gmail.com',
                    'hr@qubifytech.com',
                    'shivani@qubifytech.com',
                    'abhishek@qubifytech.com',
                ];

                $teamLeader = '';
                if($employee->is_team_leader == 0){
                    $teamLeader = $employee->getTeamLeaderNameAndId();
                }

                $emails = array();
                if(!empty($teamLeader)) {
                    Mail::to($teamLeader)
                            ->cc($additionalReceivers)
                            ->send(new LeaveRequest($leave, $employeeEmail, $employeeName, 'update'));
                    
                    $tlid = Employee::find($teamLeader->id)->user_id;
                    $fcmTokens = User::whereIn('type', ['hr', 'company'])
                                        ->orWhere('id', $employee->user_id)
                                        ->orWhere('id', $tlid)
                                        ->orWhere('id', 5)
                                        ->pluck('fcm_token','name')
                                        ->toArray();

                    foreach ($fcmTokens as $key => $fcmToken) {
                        $notificationData = [
                            'title' => "Leave Notification",
                            'body' => "Leave updated by " . $employee->name,
                            'fcm_token' => $fcmToken,
                        ];
                        try {
                            Helper::sendNotification($notificationData);
                        } catch (\Exception $e) {
                            \Log::error("Notification Error: " . $e->getMessage());
                        }
                    }
                    
                } else {
                    try {
                        Mail::to($additionalReceivers)->send(new LeaveRequest($leave, $employeeEmail, $employeeName));
                    } catch (\Exception $e) {
                        \Log::error("Mail Error: " . $e->getMessage());
                    }
                }

                return redirect()->back()->with('success', __('Leave successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Leave $leave)
    {
        if(\Auth::user()->can('Delete Leave'))
        {
            if($leave->created_by == \Auth::user()->creatorId())
            {
                $employee = Employee::find($leave->employee_id);
                if($leave->leave_type_id == 3){
                    $employee['paid_leave_balance'] = $employee['paid_leave_balance'] + $leave->total_leave_days;
                    $employee->update();
                }
                $leave->delete();

                return redirect()->route('leave.index')->with('success', __('Leave successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function export()
    {
        $name = 'leave_' . date('Y-m-d i:h:s');
        $data = Excel::download(new LeaveExport(), $name . '.xlsx'); ob_end_clean();

        return $data;
    }

    public function action($id)
    {
        $leave     = Leave::find($id);
        $employee  = Employee::find($leave->employee_id);
        $leavetype = LeaveType::find($leave->leave_type_id);
        if($leave->leavetype == 'half'){
            $leavetype['leaveName'] = ($leave->day_segment).'  Halfday';
        }else if($leave->leavetype == 'short'){
            $leavetype['leaveName'] = 'Short';
            $leavetype['startTime'] = $leave->start_time;
            $leavetype['endTime'] = $leave->end_time;
        }else if($leave->leavetype == 'full'){
            $leavetype['leaveName'] = '';
        }

        return view('leave.action', compact('employee', 'leavetype', 'leave'));
    }
    
    public function showReason($id)
    {
        $leave = Leave::find($id);
        return view('leave.reason', compact('leave'));
    }

    public function changeaction(Request $request)
    {
        $leave = Leave::find($request->leave_id);

        $leave->status = $request->status;
        if($request->status == 'Reject'){
            $leave->reject_reason = $request->reject_reason;
        }
        else if($leave->status == 'Approval')
        {
            $leave->status = 'Approve';
        }
        $employee = Employee::find($leave->employee_id);
        if($leave->leave_type_id == 3){
            $employee['paid_leave_balance'] = $employee['paid_leave_balance'] + $leave->total_leave_days;
            $employee->update();
        }

        $leave->save();

         // twilio
         $setting = Utility::settings(\Auth::user()->creatorId());
         $emp = Employee::find($leave->employee_id);
         if (isset($setting['twilio_leave_approve_notification']) && $setting['twilio_leave_approve_notification'] == 1) {
            $msg = __("Your leave has been").' '.$leave->status.'.';
            Utility::send_twilio_msg($emp->phone,$msg);
         }

        $setings = Utility::settings();
        if($setings['leave_status'] == 1)
        {
            $employee     = Employee::where('id', $leave->employee_id)->where('created_by', '=', \Auth::user()->creatorId())->first();
            $leave->name  = !empty($employee->name) ? $employee->name : '';
            $leave->email = !empty($employee->email) ? $employee->email : '';
            $leave->email = !empty($employee->email) ? $employee->email : '';
            $emails = [$leave->email, $employee->user->personal_email]; 
            try
            {
                $additionalReceivers = [
                    'chitranshu@qubifytech.com',
                    'sharma.chitranshu@gmail.com',
                    'hr@qubifytech.com',
                    'shivani@qubifytech.com',
                    'abhishek@qubifytech.com',
                    // 'qubifydeveloper@gmail.com'
                ];
                
                $tlid = '';
                if($employee->is_team_leader == 0){
                    $teamLeader = $employee->getTeamLeaderNameAndId();
                    $additionalReceivers[] = $teamLeader->email;
                    $tlid = Employee::find($teamLeader->id)->user_id;
                }

                Mail::to($emails)
                        ->cc($additionalReceivers)
                        ->send(new LeaveActionSend($leave));
                
                // Notification to Company, HR, TL, Employee
                $fcmTokens = User::whereIn('type', ['hr', 'company'])
                                    ->orWhere('id', $employee->user_id)
                                    ->orWhere('id', $tlid)
                                    ->orWhere('id', 5)
                                    ->pluck('fcm_token','name')
                                    ->toArray();

                foreach ($fcmTokens as $key => $fcmToken) {
                    $notificationData = [
                        'title' => "Leave Notification",
                        'body' => "Leave Applied by " . $employee->name,
                        'fcm_token' => $fcmToken,
                    ];
                    try {
                        Helper::sendNotification($notificationData); // Call the helper function
                    } catch (\Exception $e) {
                        \Log::error("Notification Error: " . $e->getMessage());
                    }
                }

                $users = User::Where('id', $employee->user_id)
                            ->get(['id', 'name', 'fcm_token']);
                foreach ($users as $user) {
                    if (!empty($user->fcm_token)) {
                        $notificationBody = "Your leave has been " . $leave->status;
                        if ($leave->status == 'Reject' && !empty($leave->reject_reason)) {
                            $notificationBody .= " due to: " . $leave->reject_reason;
                        }
                        $notificationData = [
                            'title' => "Leave Notification",
                            'body' => $notificationBody,
                            'fcm_token' => $user->fcm_token,
                        ];
                        try {
                            $result = $this->fcmService->sendToDevice(
                                $notificationData['fcm_token'],
                                $notificationData['title'],
                                $notificationData['body']
                            );
                            $notification = MobileNotification::create([
                                'user_id' => $user->id,
                                'title' => $notificationData['title'],
                                'body' => $notificationData['body'],
                                'is_read' => false,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error("Notification Error for user {$user->id}: " . $e->getMessage());
                        }
                    }
                }
                        
            }
            catch(\Exception $e)
            {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }
            if(Auth::user()->type == 'employee')
            {
                return redirect()->route('employee.member-leaves')->with('success', __('Leave status successfully updated.'). (isset($smtp_error) ? $smtp_error : ''));
            }
            else{
                return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.'). (isset($smtp_error) ? $smtp_error : ''));
            }

        }
        if(Auth::user()->type == 'employee')
        {
            return redirect()->route('employee.member-leaves')->with('success', __('Leave status successfully updated.'));
        }
        else{
            return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.'));
        }

        return redirect()->back()->with('success', __('Leave status successfully updated.'));
    }

    public function jsoncount(Request $request)
    {
        $leave_counts = LeaveType::select(\DB::raw('COALESCE(SUM(leaves.total_leave_days),0) AS total_leave, leave_types.title, leave_types.days,leave_types.id'))
                                 ->leftjoin('leaves', function ($join) use ($request){
            $join->on('leaves.leave_type_id', '=', 'leave_types.id');
            $join->where('leaves.employee_id', '=', $request->employee_id);
        }
        )->groupBy('leaves.leave_type_id')->get();

        return $leave_counts;

    }

    public function getPaidLeaveBalance($id)
    {
        $employee = Employee::find($id);
        $currentYear = now()->year;
    
        if ($employee && $employee->is_probation == 0) {
            // Get all leave types for the employee using corrected balance calculation
            $allLeaveTypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
            $leavetypes = collect();
            $formattedDate = now()->format('Y-m-d');
            
            foreach ($allLeaveTypes as $leaveType) {
                // Skip gender-specific leave types
                if ($employee->gender == "Male" && $leaveType->title == "Maternity Leaves") {
                    continue;
                }
                if ($employee->gender == "Female" && $leaveType->title == "Paternity Leaves") {
                    continue;
                }
                
                // Calculate available balance using the SAME logic as employee details page
                if ($leaveType->title == 'Paid Leave') {
                    // Use real-time balance calculation for paid leave
                    $breakdown = $employee->getBalanceBreakdown();
                    $availableBalance = $breakdown['current_balance'];
                } else {
                    // Use EXACT same logic as employee details page for other leave types
                    $leavesAvailed = Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leaveType->id);
                    $availableBalance = max(0, $leaveType->days - $leavesAvailed);
                }
                
                // Create object with same structure as before
                $leaveTypeObj = (object) [
                    'id' => $leaveType->id,
                    'title' => $leaveType->title,
                    'days' => $availableBalance
                ];
                
                $leavetypes->push($leaveTypeObj);
            }
    
            return response()->json(['leavetypes' => $leavetypes, 'employee' => $employee]);
        }else{
            // For probation employees - only show limited leave types with corrected calculation
            $allLeaveTypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())
                                    ->whereIn('title', ['Sick Leave', 'Birthday Leave'])
                                    ->get();
            $leavetypes = collect();
            $formattedDate = now()->format('Y-m-d');
            
            foreach ($allLeaveTypes as $leaveType) {
                // Use EXACT same logic as employee details page for probation employees
                $leavesAvailed = Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leaveType->id);
                $availableBalance = max(0, $leaveType->days - $leavesAvailed - 2); // Probation penalty
                
                // Create object with same structure as before
                $leaveTypeObj = (object) [
                    'id' => $leaveType->id,
                    'title' => $leaveType->title,
                    'days' => $availableBalance
                ];
                
                $leavetypes->push($leaveTypeObj);
            }

            return response()->json(['leavetypes' => $leavetypes, 'employee' => $employee]);
        }
    
        return response()->json(['error' => 'Employee not found'], 404);
    }

    public function checkExistingLeave(Request $request)
    {
        $employeeId = $request->employee_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $leaveId = $request->leave_id;
        $currentYear = now()->year;
    
        $leaveExists = Leave::where('employee_id', $employeeId)
                        ->whereIn('status', ['Pending', 'Approve'])
                        ->where('id', '!=', $leaveId)
                        ->where(function($query) use ($startDate, $endDate, $currentYear) {
                            $query->whereYear('created_at', $currentYear)
                                    ->whereBetween('start_date', [$startDate, $endDate])
                                    ->orWhereBetween('end_date', [$startDate, $endDate])
                                    ->orWhereRaw('? BETWEEN start_date AND end_date', [$startDate])
                                    ->orWhereRaw('? BETWEEN start_date AND end_date', [$endDate]);
                        })
                        ->exists();
    
        return response()->json(['exists' => $leaveExists]);
    }
    
    public function approveLeave($id)
    {
        $user = Auth::user();
        $leave = Leave::find($id);
        $employeeUser  = Employee::where('user_id',$user->id)->first();
        if(!$employeeUser){
            $employeeUser = User::find($user->id);
        }
        if($leave->employee_id == $employeeUser->id)
        {
            return redirect()->route('home')->with('error', __('Permission denied.'));
        }
        if(\Auth::user()->can('Manage Leave') && ($user->type != 'employee' || $employeeUser->is_team_leader == 1))
        {
            $data = [
                "_token" => csrf_token(),
                "leave_id" => $id,
                "reject_reason" => null,
                "status" => "Approval",
            ];
            $this->changeaction(new Request($data)); // Wrap data in a Request object
            if(Auth::user()->type == 'employee')
            {
                return redirect()->route('employee.member-leaves');
            }
            else{
                return redirect()->route('leave.index');
            }
        }
        else
        {
            return redirect()->route('home')->with('error', __('Permission denied.'));
        }
        
    }

    public function rejectLeave($id)
    {
        $leave     = Leave::find($id);
        $employee  = Employee::find($leave->employee_id);
        $leavetype = LeaveType::find($leave->leave_type_id);
        if($leave->leavetype == 'half'){
            $leavetype['leaveName'] = ($leave->day_segment).'  Halfday';
        }else if($leave->leavetype == 'short'){
            $leavetype['leaveName'] = 'Short';
            $leavetype['startTime'] = $leave->start_time;
            $leavetype['endTime'] = $leave->end_time;
        }else if($leave->leavetype == 'full'){
            $leavetype['leaveName'] = '';
        }
        $viewContent = View::make('leave.action', ['employee'=>$employee, 'leavetype'=>$leavetype, 'leave'=>$leave])->render(); // Render the view as a string
        $currentYear = now()->year;
        if(\Auth::user()->can('Manage Leave'))
        {
            // $leaves = Leave::where('created_by', '=', \Auth::user()->creatorId())->get();
            if(\Auth::user()->type == 'employee')
            {
                $user     = \Auth::user();
                $employee = Employee::where('user_id', '=', $user->id)->where('is_active', 1)->first();
                $leaves   = Leave::where('employee_id', '=', $employee->id)->whereYear('created_at', $currentYear)->orderBy('id','DESC')->get();
                $selfLeaves = true; // for TeamLeader member-leaves view page logic
            }
            else
            {
                $leaves = Employee::with('employeeLeaves')->where('is_active', 1)->get()->sortByDesc(function($employee) {
                    return $employee->employeeLeaves->isEmpty()
                        ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', '1900-01-01 00:00:00')
                        : $employee->employeeLeaves->first()->applied_on;
                });
                // $leaves = Employee::with('employeeLeaves')->where('is_active', 1)->get()->sortByDesc(function($employee) {

                // $leaves = Leave::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id','DESC')->get();
                $selfLeaves = false; // for TeamLeader member-leaves view page logic
            }
            $data = $viewContent;
            // dd($leaves);
            return view('leave.index', compact('leaves', 'selfLeaves','data'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}
