<?php

namespace App\Http\Controllers\Api\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\AccountList;
use App\Models\Announcement;
use App\Models\AadhaarDetail;
use App\Models\AttendanceEmployee;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Holiday;
use App\Models\LandingPageSection;
use App\Models\Meeting;
use App\Models\Order;
use App\Models\Payees;
use App\Models\LeaveType;
use App\Models\Payer;
use App\Models\Plan;
use Illuminate\Support\Facades\Storage;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Utility;
use App\Models\Leave;
use App\Helpers\Helper;
use Carbon\Carbon;
use DateTime;

class DashboardController extends BaseController
{
    public function dashboard(Request $request)
    {
        try {
            $user = Auth::user();
            $emp = Auth::user()->employee;
            $userType = Helper::userType($user);
            $employee = Helper::employeeData($user->id??0);
            $events = Event::all();
            Helper::accessEvent($events,$employee);
            $emp_leaves = [];

            if ($user->device_type == 1) {
                if($userType == 'employee' || $userType == 'director')
                {
                    $notificationData = [
                        'title' => 'Welcome to Qubify HRMS',
                        'body' => "Employee Dashboard",
                        'fcm_token' => $user->fcm_token,
                    ];

                    try {
                        // Helper::sendNotification($notificationData);
                    } catch (\Exception $e) {
                        \Log::error("Notification Error: " . $e->getMessage());
                    }

                    $announcements = Announcement::orderBy('announcements.id', 'desc')
                        ->take(5)
                        ->leftJoin('announcement_employees', 'announcements.id', '=', 'announcement_employees.announcement_id')
                        ->where('announcement_employees.employee_id', '=', $emp['id'])
                        ->orWhere(function ($q) {
                            $q->where('announcements.department_id', '["0"]')
                            ->where('announcements.employee_id', '["0"]');
                        })
                        ->where('start_date', '>=', now())
                        ->get()
                        ->toArray(); // Convert to array

                    $meetings = Meeting::orderBy('meetings.id', 'desc')
                        ->take(5)
                        ->leftJoin('meeting_employees', 'meetings.id', '=', 'meeting_employees.meeting_id')
                        ->where('meeting_employees.employee_id', '=', $emp['id'])
                        ->orWhere(function ($q) {
                            $q->where('meetings.department_id', '["0"]')
                            ->where('meetings.employee_id', '["0"]');
                        })
                        ->get()
                        ->toArray(); // Convert to array

                    $employee = Helper::employeeData($user->id??0);
                    $events = Event::whereMonth('start_date', now()->month)
                    ->get();
                    $events = Helper::accessEvent($events,$employee);
                    $arrEvents = array_map(function ($event) {
                        return [
                            'id' => $event['id'],
                            'title' => $event['title'],
                            'description' => $event['description'],
                            'start' => $event['start_date'],
                            'end' => $event['end_date'],
                            'backgroundColor' => $event['color'],
                            'borderColor' => "#fff",
                            'textColor' => "white",
                            'url' => '',
                        ];
                    }, $events);

                    $date = date("Y-m-d");
                    $employeeId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;

                    $employeeAttendanceList = AttendanceEmployee::orderBy('clock_in', 'desc')
                        ->where('employee_id', '=', $employeeId)
                        ->where('date', '=', $date)
                        ->get();

                    $totalHousrs = Helper::calculateTotalTimeDifference($employeeAttendanceList);
                    $holidays = Holiday::whereMonth('date', now()->month)
                    ->get();

                    $today = now();
                    $next15Days = $today->copy()->addDays(15);
                    $allEmployees = Employee::where('is_active', 1)
                        ->with(['user'])
                        ->select(['id', 'user_id', 'dob', 'name'])
                        ->get();
                    $birthdays = $allEmployees->filter(function ($employee) use ($today, $next15Days) {
                        $birthDate = Carbon::parse($employee->dob);
                        $currentYear = $today->year;
                        // Calculate this year's birthday
                        $thisYearBirthday = Carbon::create($currentYear, $birthDate->month, $birthDate->day);
                        // If this year's birthday has passed, check next year's birthday
                        if ($thisYearBirthday->lt($today)) {
                            $nextYearBirthday = Carbon::create($currentYear + 1, $birthDate->month, $birthDate->day);
                            return $nextYearBirthday->between($today, $next15Days);
                        }
                        // Check if this year's birthday is within next 15 days
                        return $thisYearBirthday->between($today, $next15Days);
                    })->sortBy(function ($employee) use ($today) {
                        $birthDate = Carbon::parse($employee->dob);
                        $currentYear = $today->year;
                        // Calculate next birthday occurrence
                        $nextBirthday = Carbon::create($currentYear, $birthDate->month, $birthDate->day);
                        if ($nextBirthday->lt($today)) {
                            $nextBirthday->addYear();
                        }
                        return $nextBirthday;
                    })->values()->toArray();
                    
                    /* $arrBirthdays = array_map(function ($birthday) {
                        $imageUrl = null;
                        if (isset($birthday['user']['avatar']) && $birthday['user']['avatar']) {
                            // Manual construction to ensure correct path
                            $baseUrl = config('app.url'); // https://hrm.qubifytech.com
                            $imagePath = $birthday['user']['avatar']; // uploads/avatar/Media_1750244092.jpg
                            $imageUrl = $baseUrl . '/storage/uploads/avatar/' . $imagePath;
                        }
                        return [
                            'id' => $birthday['id'],
                            'user_id' => $birthday['user_id'],
                            'dob' => $birthday['dob'],
                            'name' => $birthday['name'],
                            'image' => $imageUrl,
                        ];
                    }, $birthdays); */

                    $arrBirthdays = array_map(function ($birthday) {
                        $imageUrl = null;
                        if (isset($birthday['user']['avatar']) && $birthday['user']['avatar']) {
                            // Manual construction to ensure correct path
                            $baseUrl = config('app.url'); // https://hrm.qubifytech.com
                            $imagePath = $birthday['user']['avatar']; // uploads/avatar/Media_1750244092.jpg
                            $imageUrl = $baseUrl . '/storage/uploads/avatar/' . $imagePath;
                        }
                        
                        // Format the date from ISO format to yyyy-MM-dd
                        $formattedDob = null;
                        if (isset($birthday['dob']) && $birthday['dob']) {
                            $date = new DateTime($birthday['dob']);
                            $formattedDob = $date->format('Y-m-d');
                        }
                        
                        return [
                            'id' => $birthday['id'],
                            'user_id' => $birthday['user_id'],
                            'dob' => $formattedDob,
                            'name' => $birthday['name'],
                            'image' => $imageUrl,
                        ];
                    }, $birthdays);

                    $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                    ->where('clock_out', '00:00:00')
                    ->orderBy('id', 'desc')
                    ->exists();

                    $leavesNotTypeId = LeaveType::whereIn('title', [
                        'Maternity Leaves',
                        'Paternity Leaves',
                        // 'Bereavement leave'
                    ])->pluck('id')->toArray();



                    $leaves = Leave::with('leaveType')->where('employee_id',$emp->id)->where('status','Approve')->whereNotIn('leave_type_id',$leavesNotTypeId)->get()->toArray();

                    $totalAppliedLeaveDays = collect($leaves)->sum(function ($leave) {
                        return floatval($leave['total_leave_days']);
                    });

                    // $leavesType = LeaveType::whereNotIn('title', [
                    //     'Maternity Leaves',
                    //     'Paternity Leaves',
                    //     // 'Bereavement leave'
                    // ])->get()->toArray();
                    // dd($leavesType);
                    // $totalLeaveDays = collect($leavesType)->sum(function ($leavesT) {
                    //     return floatval($leavesT['days']);
                    // });
                    $currentYear = date('Y');
                    $currentDate = date('Y-m-d');
                    $employeeBirthday = date($currentYear . '-m-d', strtotime($employee->birth_date));
                    $isBirthdayUpcoming = $employeeBirthday >= $currentDate;
                    
                    if ($employee->is_probation == 1) {
                        // Build the whereIn array for leave types
                        $allowedLeaveTypes = ['Sick Leave'];
                        
                        // Only add Birthday Leave if employee's birthday is still upcoming this year
                        if ($isBirthdayUpcoming) {
                            $allowedLeaveTypes[] = 'Birthday Leave';
                        }
                        
                        $leavesType = LeaveType::leftJoin('leaves', function ($join) use ($employee, $currentYear) {
                                            $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                                                 ->where('leaves.employee_id', '=', $employee->id)
                                                 ->whereYear('leaves.created_at', '=', $currentYear); // Filter by current year
                                        })
                                        ->where('leave_types.created_by', '=', Auth::user()->creatorId())
                                        ->whereNotIn('leave_types.title', [
                                            'Maternity Leaves',
                                            'Paternity Leaves',
                                        ])
                                        ->whereIn('leave_types.title', $allowedLeaveTypes) // Dynamic array based on birthday
                                        ->select(
                                            'leave_types.id',
                                            'leave_types.title',
                                            DB::raw('
                                                CASE
                                                    WHEN leave_types.title = "Sick Leave" THEN
                                                        3 - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0)
                                                    WHEN leave_types.title = "Birthday Leave" THEN
                                                        CASE 
                                                            WHEN "' . $employeeBirthday . '" >= "' . $currentDate . '" THEN
                                                                leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0)
                                                            ELSE 0
                                                        END
                                                    ELSE
                                                        leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0)
                                                END AS available_days
                                            '),
                                            DB::raw('
                                                CASE
                                                    WHEN leave_types.title = "Sick Leave" THEN 3
                                                    ELSE leave_types.days
                                                END AS total_days
                                            ')
                                        )
                                        ->groupBy('leave_types.id', 'leave_types.title', 'leave_types.days')
                                        ->get()
                                        ->toArray();
                    } else {
                        $id = Auth::user()->id;
                        $leavesType = LeaveType::leftJoin('employees', function ($join) use ($id) {
                                            $join->on('employees.user_id', '=', DB::raw("'" . $id . "'"));  // Fixed: Added quotes for string value
                                        })
                                        ->leftJoin('leaves', function ($join) use ($employee, $currentYear) {
                                            $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                                                 ->where('leaves.employee_id', '=', $employee->id)
                                                 ->whereYear('leaves.created_at', '=', $currentYear); // Filter by current year
                                        })
                                        ->where('leave_types.created_by', '=', Auth::user()->creatorId())
                                        ->whereNotIn('leave_types.title', [
                                            'Maternity Leaves',
                                            'Paternity Leaves',
                                        ])
                                        // For non-probation employees, also check birthday leave availability
                                        ->where(function($query) use ($employeeBirthday, $currentDate) {
                                            $query->where('leave_types.title', '!=', 'Birthday Leave')
                                                  ->orWhere(function($subQuery) use ($employeeBirthday, $currentDate) {
                                                      $subQuery->where('leave_types.title', '=', 'Birthday Leave')
                                                               ->whereRaw("'" . $employeeBirthday . "' >= '" . $currentDate . "'");
                                                  });
                                        })
                                        ->select(
                                            'leave_types.id',
                                            'leave_types.title',
                                            DB::raw('
                                                CASE
                                                    WHEN leave_types.title = "Paid Leave" THEN
                                                        CASE
                                                            WHEN leaves.status = "Pending" AND leaves.leave_type_id = "3" THEN
                                                                employees.paid_leave_balance - COALESCE(SUM(CASE WHEN leaves.status = "Pending" AND leaves.leave_type_id = "3" THEN leaves.total_leave_days ELSE 0 END), 0)
                                                            ELSE employees.paid_leave_balance
                                                        END
                                                    WHEN leave_types.title = "Birthday Leave" THEN
                                                        CASE 
                                                            WHEN "' . $employeeBirthday . '" >= "' . $currentDate . '" THEN
                                                                leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0)
                                                            ELSE 0
                                                        END
                                                    ELSE 
                                                        (leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0))
                                                END AS available_days
                                            '),
                                            'leave_types.days as total_days'
                                        )
                                        ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                                        ->get()
                                        ->toArray();
                    }
                    // Calculate total leave days available (remaining after deductions)
                    $totalAvailableLeaveDays = collect($leavesType)->sum(function ($leavesT) {
                        return floatval($leavesT['available_days']);
                    });
                    
                    // Calculate total allocated leave days (original days before any deductions)
                    $totalAllocatedLeaveDays = collect($leavesType)->sum(function ($leavesT) {
                        return floatval($leavesT['total_days']);
                    });
                    
                    // Get total applied leaves for ALL leave types (including excluded ones like Maternity, Paternity)
                    $allAppliedLeaves = Leave::where('employee_id', $emp->id)
                                            ->where('status', 'Approve')
                                            ->sum('total_leave_days');
                    
                    $leave = [
                        'total_allocated' => round((float) $totalAllocatedLeaveDays, 2),     // Total days allocated for available leave types
                        'total_available' => round((float) $totalAvailableLeaveDays, 2),    // Total remaining days after deductions
                        'total_applied' => round((float) $allAppliedLeaves, 2),             // Total applied leaves for ALL leave types
                        'total_remaining' => round((float) ($totalAllocatedLeaveDays - $allAppliedLeaves), 2), // Total remaining from allocated
                        
                        // Keep backward compatibility
                        'total' => round((float) $totalAllocatedLeaveDays, 2),
                        'applied' => round((float) $totalAppliedLeaveDays, 2),
                        'remain' => round((float) ($totalAllocatedLeaveDays - $allAppliedLeaves), 2),
                    ];

                    $attendanceDates = AttendanceEmployee::where('employee_id', $employeeId)
                                                        ->distinct('date')
                                                        ->whereMonth('date', now()->month)
                                                        ->whereYear('date', now()->year)
                                                        ->pluck('date');

                    $start = now()->startOfMonth();
                    $end = now();

                    $dates = collect();
                    $weekends = collect();

                    while ($start <= $end) {
                        $date = $start->format('Y-m-d');
                        $dates->push($date);

                        // Check if it's a weekend (Saturday = 6, Sunday = 0)
                        if ($start->isWeekend()) {
                            $weekends->push($date);
                        }

                        $start->addDay();
                    }

                    $absent = $this->countAbsent($dates->count(),$attendanceDates->count(),$holidays->count(),$weekends->count());

                    $atte = [
                        'total' => $dates->count(),
                        'present' => $attendanceDates->count(),
                        'absent' => $absent,
                        'holidays' => $holidays->count(),
                        'weekends' => $weekends->count(),
                    ];


                    if(Auth::user()->id == 8)
                    {
                        $location = [
                            'latitude' => 30.661608,
                            'longitude' => 76.863342,
                        ];
                    }
                    else
                    {
                        $location = [
                            'latitude' => 0.0,
                            'longitude' => 0.0,
                        ];
                    }

                    $aadhaar_number = AadhaarDetail::where('employee_id',$emp->id)->value('aadhaar_number');
                    $aadhaar_base64_img = AadhaarDetail::where('employee_id',$emp->id)->value('photo_encoded');

                    if ($aadhaar_base64_img && Auth::user()->base64) {
                        $aadhaar_base64_img = Auth::user()->base64;
                    }

                    $data = [
                        'clock_status' => $attendance,
                        'aadhaar_number' => $aadhaar_number,
                        'aadhaar_base64' => $aadhaar_base64_img,
                        'location' => $location,
                        'attendance' => $atte,
                        'leave' => $leave,
                        'events' => $arrEvents,
                        'announcements' => $announcements,
                        'birthdays' => $arrBirthdays,
                        'holidays' => $holidays,
                        'meetings' => $meetings,
                        'totalHousrs' => $totalHousrs,
                        'emp_leaves' => $emp_leaves,
                        'clock_in_pin' => $employee->clock_in_pin,
                        // 'employeeAttendanceList' => $employeeAttendanceList->toArray(),
                    ];
                    return $this->successResponse($data);
                }
                else {
                    $userId = Auth::user()->creatorId();
                    // Fetch events and convert to array
                    $events = Event::where('created_by', $userId)->get()->toArray();
                    $arrEvents = array_map(function($event) {
                        return [
                            'id' => $event['id'],
                            'title' => $event['title'],
                            'start' => $event['start_date'],
                            'end' => $event['end_date'],
                            'description' => $event['description'],
                            'backgroundColor' => $event['color'],
                            'borderColor' => "#fff",
                            'textColor' => "white",
                            'url' => '',
                        ];
                    }, $events);

                    // Fetch announcements and convert to array
                    $announcements = Announcement::where('created_by', $userId)
                                                ->orderBy('id', 'desc')
                                                ->take(5)
                                                ->get()
                                                ->toArray();

                    // Fetch employees and convert to array
                    $employees = User::where('type', 'employee')->where('created_by', $userId)->get()->toArray();

                    // Fetch users and convert to array
                    $users = User::where('type', '!=', 'employee')->where('created_by', $userId)->get()->toArray();

                    $total_staff = [];
                    $total_staff['total'] = count($employees)+count($users);
                    $total_staff['users'] = count($users);
                    $total_staff['employees'] = count($employees);

                    $open_ticket = Ticket::where('status', 'open')->where('created_by', $userId)->count();
                    $close_ticket = Ticket::where('status', 'close')->where('created_by', $userId)->count();

                    $total_ticket = [];
                    $total_ticket['total'] = $close_ticket+$open_ticket;
                    $total_ticket['open'] = $open_ticket;
                    $total_ticket['close'] = $close_ticket;

                    $currentDate = date('Y-m-d');

                    $notClockIn = AttendanceEmployee::where('date', $currentDate)->pluck('employee_id')->toArray();

                    $notClockIns = Employee::where('created_by', $userId)
                                            ->where('is_active', 1)
                                            ->whereNotIn('id', $notClockIn)
                                            ->select('id', 'user_id', 'name') // Select specific fields
                                            ->get()
                                            ->map(function($notClock) use ($currentDate) {
                                                $leaveType = Helper::checkLeaveWithTypes($currentDate, $notClock->id);
                                                $notClock->status = $leaveType == 0 ? 'Absent' : ($leaveType == 'fullday Leave' ? 'Leave' : null);
                                                $notClock->class = $leaveType == 0 ? 'absent-btn' : ($leaveType == 'fullday Leave' ? 'badge badge-warning' : null);
                                                return $notClock;
                                            })->toArray(); // Convert to array // Convert to array

                    // Calculate account balance
                    $accountBalance = AccountList::where('created_by', $userId)->sum('initial_balance');

                    // Count payees and payers
                    $totalPayee = Payees::where('created_by', $userId)->count();
                    $totalPayer = Payer::where('created_by', $userId)->count();

                    // Fetch meetings and convert to array
                    $meetings = Meeting::where('created_by', $userId)->limit(5)->get()->toArray();

                    // Store all results in a single array
                    $data = [
                        'events' => $arrEvents,
                        'announcements' => $announcements,
                        'user'=>$total_staff,
                        'ticket'=>$total_ticket,
                        'notClockIns' => $notClockIns,
                        'meetings' => $meetings,
                    ];

                    $total_balance = [];
                    if ($userType == 'company')
                    {
                        $total_balance['total'] = $accountBalance;
                        $total_balance['payee'] = $totalPayee;
                        $total_balance['payer'] = $totalPayer;
                        $data['balance'] = $total_balance;
                    }
                    return $this->successResponse($data);
                }
            }
            else {
                if($userType == 'employee')
                {
                    $notificationData = [
                        'title' => 'Welcome to Qubify HRMS',
                        'body' => "Employee Dashboard",
                        'fcm_token' => $user->fcm_token,
                    ];

                    try {
                        // Helper::sendNotification($notificationData);
                    } catch (\Exception $e) {
                        \Log::error("Notification Error: " . $e->getMessage());
                    }

                    $announcements = Announcement::orderBy('announcements.id', 'desc')
                                                ->take(5)
                                                ->leftJoin('announcement_employees', 'announcements.id', '=', 'announcement_employees.announcement_id')
                                                ->where('announcement_employees.employee_id', '=', $emp['id'])
                                                ->orWhere(function ($q) {
                                                    $q->where('announcements.department_id', '["0"]')
                                                    ->where('announcements.employee_id', '["0"]');
                                                })
                                                ->where('start_date', '>=', now())
                                                ->get()
                                                ->toArray(); // Convert to array

                    $meetings = Meeting::orderBy('meetings.id', 'desc')
                                        ->take(5)
                                        ->leftJoin('meeting_employees', 'meetings.id', '=', 'meeting_employees.meeting_id')
                                        ->where('meeting_employees.employee_id', '=', $emp['id'])
                                        ->orWhere(function ($q) {
                                            $q->where('meetings.department_id', '["0"]')
                                            ->where('meetings.employee_id', '["0"]');
                                        })
                                        ->get()
                                        ->toArray(); // Convert to array

                    $employee = Helper::employeeData($user->id??0);
                    $events = Event::whereMonth('start_date', now()->month)->get();
                    $events = Helper::accessEvent($events,$employee);
                    $arrEvents = array_map(function ($event) {
                        return [
                            'id' => $event['id'],
                            'title' => $event['title'],
                            'description' => $event['description'],
                            'start' => $event['start_date'],
                            'end' => $event['end_date'],
                            'backgroundColor' => $event['color'],
                            'borderColor' => "#fff",
                            'textColor' => "white",
                            'url' => '',
                        ];
                    }, $events);

                    $date = date("Y-m-d");
                    $employeeId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;

                    $employeeAttendanceList = AttendanceEmployee::orderBy('clock_in', 'desc')
                        ->where('employee_id', '=', $employeeId)
                        ->where('date', '=', $date)
                        ->get();

                    $totalHousrs = Helper::calculateTotalTimeDifference($employeeAttendanceList);
                    $holidays = Holiday::whereMonth('date', now()->month)
                    ->get();

                    $birthdays = Employee::whereMonth('dob', now()->month)
                                        ->orderByRaw('DAY(dob)')
                                        ->with(['user'])
                                        ->select(['id', 'user_id', 'dob', 'name'])
                                        ->get()->toarray();

                    /* $arrBirthdays = array_map(function ($birthday) {
                        return [
                            'id' => $birthday['id'],
                            'user_id' => $birthday['user_id'],
                            'dob' => $birthday['dob'],
                            'name' => $birthday['name'],
                            'image' => $birthday['user']['avatar'],
                        ];
                    }, $birthdays); */

                    $arrBirthdays = array_map(function ($birthday) {
                        // Format the date from ISO format to yyyy-MM-dd
                        $formattedDob = null;
                        if (isset($birthday['dob']) && $birthday['dob']) {
                            $date = new \DateTime($birthday['dob']);
                            $formattedDob = $date->format('Y-m-d');
                        }
                        
                        return [
                            'id' => $birthday['id'],
                            'user_id' => $birthday['user_id'],
                            'dob' => $formattedDob,
                            'name' => $birthday['name'],
                            'image' => $birthday['user']['avatar'],
                        ];
                    }, $birthdays);

                    $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                                                    ->where('clock_out', '00:00:00')
                                                    ->orderBy('id', 'desc')
                                                    ->exists();

                    $leavesNotTypeId = LeaveType::whereIn('title', [
                        'Maternity Leaves',
                        'Paternity Leaves',
                        // 'Bereavement leave'
                    ])->pluck('id')->toArray();



                    $leaves = Leave::with('leaveType')->where('employee_id',$emp->id)->where('status','Approve')->whereNotIn('leave_type_id',$leavesNotTypeId)->get()->toArray();

                    $totalAppliedLeaveDays = collect($leaves)->sum(function ($leave) {
                        return floatval($leave['total_leave_days']);
                    });

                    $leavesType = LeaveType::whereNotIn('title', [
                        'Maternity Leaves',
                        'Paternity Leaves',
                        // 'Bereavement leave'
                    ])->get()->toArray();

                    $totalLeaveDays = collect($leavesType)->sum(function ($leavesT) {
                        return floatval($leavesT['days']);
                    });

                    $leave = [
                        'total' => round((float) $totalLeaveDays, 2),
                        'applied' => round((float) $totalAppliedLeaveDays, 2),
                        'remain' => round((float) ($totalLeaveDays - $totalAppliedLeaveDays), 2),
                    ];

                    $attendanceDates = AttendanceEmployee::where('employee_id', $employeeId)
                    ->distinct('date')
                    ->whereMonth('date', now()->month)
                    ->pluck('date');

                    $start = now()->startOfMonth();
                    $end = now();

                    $dates = collect();
                    $weekends = collect();

                    while ($start <= $end) {
                        $date = $start->format('Y-m-d');
                        $dates->push($date);

                        // Check if it's a weekend (Saturday = 6, Sunday = 0)
                        if ($start->isWeekend()) {
                            $weekends->push($date);
                        }

                        $start->addDay();
                    }

                    $absent = $this->countAbsent($dates->count(),$attendanceDates->count(),$holidays->count(),$weekends->count());

                    $atte = [
                        'total' => $dates->count(),
                        'present' => $attendanceDates->count(),
                        'absent' => $absent,
                        'holidays' => $holidays->count(),
                        'weekends' => $weekends->count(),
                    ];


                    if(Auth::user()->id == 8)
                    {
                        $location = [
                            'latitude' => 30.661608,
                            'longitude' => 76.863342,
                        ];
                    }
                    else
                    {
                        $location = [
                            'latitude' => 0.0,
                            'longitude' => 0.0,
                        ];
                    }

                    $aadhaar_number = AadhaarDetail::where('employee_id',$emp->id)->value('aadhaar_number');
                    $aadhaar_base64_img = AadhaarDetail::where('employee_id',$emp->id)->value('photo_encoded');

                    if (Auth::user()->base6) {
                        $aadhaar_base64_img = Auth::user()->base64;
                    }

                    $data = [
                        'clock_status' => $attendance,
                        'aadhaar_number' => $aadhaar_number,
                        'aadhaar_base64' => $aadhaar_base64_img,
                        'location' => $location,
                        'attendance' => $atte,
                        'leave' => $leave,
                        'events' => $arrEvents,
                        'announcements' => $announcements,
                        'birthdays' => $arrBirthdays,
                        'holidays' => $holidays,
                        'meetings' => $meetings,
                        'emp_leaves' => $emp_leaves,
                        'totalHousrs' => $totalHousrs,
                        // 'employeeAttendanceList' => $employeeAttendanceList->toArray(),
                    ];
                    return $this->successResponse($data);
                }
                else {
                    $userId = Auth::user()->creatorId();
                    // Fetch events and convert to array
                    $events = Event::where('created_by', $userId)->get()->toArray();
                    $arrEvents = array_map(function($event) {
                        return [
                            'id' => $event['id'],
                            'title' => $event['title'],
                            'start' => $event['start_date'],
                            'end' => $event['end_date'],
                            'description' => $event['description'],
                            'backgroundColor' => $event['color'],
                            'borderColor' => "#fff",
                            'textColor' => "white",
                            'url' => '',
                        ];
                    }, $events);

                    // Fetch announcements and convert to array
                    $announcements = Announcement::where('created_by', $userId)
                        ->orderBy('id', 'desc')
                        ->take(5)
                        ->get()
                        ->toArray();

                    // Fetch employees and convert to array
                    $employees = User::where('type', 'employee')->where('created_by', $userId)->get()->toArray();

                    // Fetch users and convert to array
                    $users = User::where('type', '!=', 'employee')->where('created_by', $userId)->get()->toArray();

                    $total_staff = [];
                    $total_staff['total'] = count($employees)+count($users);
                    $total_staff['users'] = count($users);
                    $total_staff['employees'] = count($employees);

                    $open_ticket = Ticket::where('status', 'open')->where('created_by', $userId)->count();
                    $close_ticket = Ticket::where('status', 'close')->where('created_by', $userId)->count();

                    $total_ticket = [];
                    $total_ticket['total'] = $close_ticket+$open_ticket;
                    $total_ticket['open'] = $open_ticket;
                    $total_ticket['close'] = $close_ticket;

                    $currentDate = date('Y-m-d');

                    $notClockIn = AttendanceEmployee::where('date', $currentDate)->pluck('employee_id')->toArray();

                    $notClockIns = Employee::where('created_by', $userId)
                                            ->where('is_active', 1)
                                            ->whereNotIn('id', $notClockIn)
                                            ->select('id', 'user_id', 'name') // Select specific fields
                                            ->get()
                                            ->map(function($notClock) use ($currentDate) {
                                                $leaveType = Helper::checkLeaveWithTypes($currentDate, $notClock->id);
                                                $notClock->status = $leaveType == 0 ? 'Absent' : ($leaveType == 'fullday Leave' ? 'Leave' : null);
                                                $notClock->class = $leaveType == 0 ? 'absent-btn' : ($leaveType == 'fullday Leave' ? 'badge badge-warning' : null);
                                                return $notClock;
                                            })->toArray(); // Convert to array // Convert to array

                    // Calculate account balance
                    $accountBalance = AccountList::where('created_by', $userId)->sum('initial_balance');

                    // Count payees and payers
                    $totalPayee = Payees::where('created_by', $userId)->count();
                    $totalPayer = Payer::where('created_by', $userId)->count();

                    // Fetch meetings and convert to array
                    $meetings = Meeting::where('created_by', $userId)->limit(5)->get()->toArray();

                    $birthdays = Employee::whereMonth('dob', now()->month)
                                            ->orderByRaw('DAY(dob)')
                                            ->select('id,user_id,dob,name')
                                            ->get()->toArray();

                    // Store all results in a single array
                    $data = [
                        'events' => $arrEvents,
                        'announcements' => $announcements,
                        'birthdays' => $announcements,
                        'holiday' => $announcements,
                        'user'=>$total_staff,
                        'ticket'=>$total_ticket,
                        'notClockIns' => $notClockIns,
                        'meetings' => $meetings,
                    ];

                    $total_balance = [];
                    if ($userType == 'company')
                    {
                        $total_balance['total'] = $accountBalance;
                        $total_balance['payee'] = $totalPayee;
                        $total_balance['payer'] = $totalPayer;
                        $data['balance'] = $total_balance;
                    }
                    return $this->successResponse($data);
                }
            }
            return $this->errorResponse('Bad request!.');
        } catch (\Throwable $th) {
            // return $this->errorResponse("Something went wrong!");
            return $this->errorResponse($th->getMessage());
            //throw $th;
        }
    }

    private function countAbsent($total, $present, $holidays, $weekend)
    {
        $nonWorkingDays = $holidays + $weekend;
        $workingDays = $total - $nonWorkingDays;
        $absent = $workingDays - $present;
        return $absent < 0 ? 0 : $absent;
    }

}
