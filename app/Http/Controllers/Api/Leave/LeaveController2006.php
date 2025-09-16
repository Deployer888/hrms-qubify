<?php

namespace App\Http\Controllers\Api\Leave;

use App\Models\Employee;
use App\Models\User;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Mail\{LeaveActionSend, LeaveRequest};
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Exports\LeaveExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\Api\BaseController;

class LeaveController extends BaseController
{

    /**
     * @OA\Get(
     *     path="/api/leave",
     *     summary="Get all leaves",
     *     description="Retrieves all leaves for employees or the authenticated employee's leaves",
     *     tags={"Leave Management"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Leaves retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="employee_id", type="integer", example=5),
     *                             @OA\Property(property="leave_type_id", type="integer", example=2),
     *                             @OA\Property(property="applied_on", type="string", format="date", example="2023-06-15"),
     *                             @OA\Property(property="start_date", type="string", format="date", example="2023-06-20"),
     *                             @OA\Property(property="end_date", type="string", format="date", example="2023-06-21"),
     *                             @OA\Property(property="total_leave_days", type="number", format="float", example=2),
     *                             @OA\Property(property="leave_reason", type="string", example="Family emergency"),
     *                             @OA\Property(property="remark", type="string", example="Will be available on phone"),
     *                             @OA\Property(property="status", type="string", enum={"Pending", "Approve", "Reject"}, example="Pending"),
     *                             @OA\Property(property="created_by", type="integer", example=1),
     *                             @OA\Property(property="leavetype", type="string", enum={"full", "half", "short"}, example="full"),
     *                             @OA\Property(property="start_time", type="string", example="09:00 AM", nullable=true),
     *                             @OA\Property(property="end_time", type="string", example="11:00 AM", nullable=true),
     *                             @OA\Property(property="day_segment", type="string", enum={"morning", "afternoon"}, example="morning", nullable=true)
     *                         )
     *                     ),
     *                     @OA\Schema(
     *                         type="object",
     *                         @OA\Property(
     *                             property="data",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=29),
     *                                 @OA\Property(property="name", type="string", example="Raghubir"),
     *                                 @OA\Property(
     *                                     property="employee_leaves",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=1),
     *                                         @OA\Property(property="employee_id", type="integer", example=5),
     *                                         @OA\Property(property="leave_type_id", type="integer", example=2),
     *                                         @OA\Property(property="applied_on", type="string", format="date", example="2023-06-15"),
     *                                         @OA\Property(property="start_date", type="string", format="date", example="2023-06-20"),
     *                                         @OA\Property(property="end_date", type="string", format="date", example="2023-06-21"),
     *                                         @OA\Property(property="total_leave_days", type="number", format="float", example=2),
     *                                         @OA\Property(property="leave_reason", type="string", example="Family emergency"),
     *                                         @OA\Property(property="remark", type="string", example="Will be available on phone"),
     *                                         @OA\Property(property="status", type="string", enum={"Pending", "Approve", "Reject"}, example="Pending"),
     *                                         @OA\Property(property="created_by", type="integer", example=1),
     *                                         @OA\Property(property="leavetype", type="string", enum={"full", "half", "short"}, example="full"),
     *                                         @OA\Property(property="start_time", type="string", example="09:00 AM", nullable=true),
     *                                         @OA\Property(property="end_time", type="string", example="11:00 AM", nullable=true),
     *                                         @OA\Property(property="day_segment", type="string", enum={"morning", "afternoon"}, example="morning", nullable=true)
     *                                     )
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while fetching leaves")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Manage Leave'))
        {
            try {
                $currentYear = now()->year;

                if ($user->type == 'employee')
                {
                    // For employee users - show only their leaves
                    $employee = Employee::where('user_id', $user->id)->where('is_active', 1)->first();

                    if (!$employee) {
                        return $this->errorResponse(__('Employee not found.'));
                    }
                    $leaves = Leave::select('leaves.*', 'leave_types.title as category')
                        ->join('leave_types', 'leaves.leave_type_id', 'leave_types.id')
                        ->where('leaves.employee_id', $employee->id)
                        ->whereYear('leaves.created_at', $currentYear)
                        ->orderBy('leaves.id', 'DESC')
                        ->get();

                    return $this->successResponse($leaves);
                }
                else
                {
                    // For admin/HR - show all employees with their leaves
                    $employees = Employee::select('id', 'name')
                        ->with('employeeLeaves')
                        ->where('is_active', 1)
                        ->get()
                        ->sortByDesc(function ($employee) {
                            return $employee->employeeLeaves->isEmpty()
                                ? Carbon::createFromFormat('Y-m-d H:i:s', '1900-01-01 00:00:00')
                                : $employee->employeeLeaves->first()->applied_on;
                        });

                    // Format the data as a sequential array (not keyed by employee ID)
                    $formattedData = [];
                    foreach ($employees as $employee) {
                        $formattedData[] = [
                            'id' => $employee->id,
                            'name' => $employee->name,
                            'employee_leaves' => $employee->employeeLeaves
                        ];
                    }

                    return $this->successResponse(['data' => $formattedData]);
                }
            } catch (\Exception $e) {
                return $this->errorResponse(__('An error occurred while fetching leaves: ' . $e->getMessage()));
            }
        } else {
            return $this->errorResponse(__('Permission denied.'), 200);
        }
    }


    public function getLeaveTypes($id)
    {
        try {
            $currentYear = date('Y');
    
            $employee = Employee::find($id);
            if (!$employee) {
                return $this->errorResponse(__('Employee not found.'), 200);
            }
            $id = $employee->user_id;
            
            $currentYear = date('Y');
            $currentDate = date('Y-m-d');
            $employeeBirthday = date($currentYear . '-m-d', strtotime($employee->birth_date));
            $isBirthdayUpcoming = $employeeBirthday >= $currentDate;
    
            if ($employee->is_probation == 1) {
                $allowedLeaveTypes = ['Sick Leave'];
                if ($isBirthdayUpcoming) {
                    $allowedLeaveTypes[] = 'Birthday Leave';
                }
                $leaveTypes = LeaveType::leftJoin('employees', function ($join) use ($id) {
                                $join->on('employees.user_id', '=', DB::raw($id));
                            })
                            ->leftJoin('leaves', function ($join) use ($employee) {
                                $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                                    ->where('leaves.employee_id', '=', $employee->id);
                            })
                            ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                            ->whereIn('leave_types.title', $allowedLeaveTypes) // Only show allowed leave types for probation
                            ->select(
                                'leave_types.id',
                                'leave_types.title',
                                DB::raw('
                                    CASE
                                        WHEN leave_types.title = "Sick Leave" THEN
                                            leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0) - 2
                                        WHEN leave_types.title = "Birthday Leave" THEN
                                            CASE 
                                                WHEN "' . $employeeBirthday . '" >= "' . $currentDate . '" THEN
                                                    leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0)
                                                ELSE 0
                                            END
                                        ELSE
                                            leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0)
                                    END AS days
                                '),
                                DB::raw('
                                    CONCAT(leave_types.title, " (", 
                                        CASE
                                            WHEN leave_types.title = "Sick Leave" THEN
                                                leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0) - 2
                                            WHEN leave_types.title = "Birthday Leave" THEN
                                                CASE 
                                                    WHEN "' . $employeeBirthday . '" >= "' . $currentDate . '" THEN
                                                        leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0)
                                                    ELSE 0
                                                END
                                            ELSE
                                                leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0)
                                        END,
                                    ")") AS title_with_days
                                ')
                            )
                            ->groupBy('leave_types.id', 'leave_types.title', 'leave_types.days')
                            ->get();
            } else {
                $leaveTypes = LeaveType::leftJoin('employees', function ($join) use ($id) {
                                $join->on('employees.user_id', '=', DB::raw($id));
                            })
                            ->leftJoin('leaves', function ($join) use ($employee) {
                                $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                                    ->where('leaves.employee_id', '=', $employee->id);
                            })
                            ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                            // Add birthday check for regular employees - exclude Birthday Leave if birthday has passed
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
                                                    leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") AND YEAR(leaves.created_at) = "' . $currentYear . '" THEN leaves.total_leave_days ELSE 0 END), 0)
                                                ELSE 0
                                            END
                                        ELSE
                                            (leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") AND YEAR(leaves.created_at) = "' . $currentYear . '" THEN leaves.total_leave_days ELSE 0 END), 0))
                                    END AS days
                                '),
                                DB::raw('
                                    CONCAT(leave_types.title, " (", 
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
                                                        leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") AND YEAR(leaves.created_at) = "' . $currentYear . '" THEN leaves.total_leave_days ELSE 0 END), 0)
                                                    ELSE 0
                                                END
                                            ELSE
                                                (leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") AND YEAR(leaves.created_at) = "' . $currentYear . '" THEN leaves.total_leave_days ELSE 0 END), 0))
                                        END,
                                    ")") AS title_with_days
                                ')
                            )
                            ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                            ->get();
            }
            
            // Convert collection to array and then back to objects for consistent response format
            $leaveTypes = collect($leaveTypes->toArray())->map(function ($item) {
                return (object) $item;
            });
    
            return $this->successResponse($leaveTypes, "Leave types fetched successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred while fetching leave types: ' . $e->getMessage()), 500);
        }
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Leave')) {
            try {
                if (Auth::user()->type == 'employee') {
                    $employees = Employee::where('user_id', '=', $user->id)
                        ->get()
                        ->pluck('name', 'id');
                } else {
                    $employees = Employee::where('created_by', '=', $user->creatorId())
                        ->where('is_active', 1)
                        ->get()
                        ->pluck('name', 'id');
                }

                return $this->successResponse(['employees' => $employees]);
            } catch (\Exception $e) {
                return $this->errorResponse(__('An error occurred while preparing leave creation: ' . $e->getMessage()));
            }
        } else {
            return $this->errorResponse(__('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Leave')) {
            try {
                $validator = \Validator::make($request->all(), [
                    'leave_type_id' => 'required|integer|exists:leave_types,id',
                    'start_date' => 'required|date|after_or_equal:today',
                    'end_date' => 'nullable|date|after_or_equal:start_date',
                    'leave_reason' => 'required|string|min:10',
                    'remark' => 'nullable|string|max:500',
                    'is_halfday' => 'nullable|in:full,half,short',
                    'day_segment' => 'nullable|in:morning,afternoon',
                    'start_time' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 200, $validator->errors()->toArray());
                }

                // Get employee based on user type
                if ($user->type == 'employee') {
                    $employee = Employee::where('user_id', $user->id)->first();
                    if (!$employee) {
                        return $this->errorResponse(__('Employee not found.'));
                    }
                } else {
                    $employee = Employee::where('id', $request->employee_id)->first();
                    if (!$employee) {
                        return $this->errorResponse(__('Employee not found.'));
                    }
                }

                // Calculate total leave days
                $startDate = new \DateTime($request->start_date);
                $endDate = new \DateTime($request->end_date ?? $request->start_date);
                $endDate->modify('+1 day'); // Include end date
                $interval = new \DateInterval('P1D');
                $daterange = new \DatePeriod($startDate, $interval, $endDate);
                $total_leave_days = 0;

                // Handle different leave types (full day, half day, short leave)
                if (isset($request->is_halfday) && $request->is_halfday == 'half') {
                    $total_leave_days = 0.5;
                } elseif (isset($request->is_halfday) && $request->is_halfday == 'short') {
                    $startTime = Carbon::createFromFormat('h:i A', trim($request->start_time));
                    $endTime = (clone $startTime)->addHours(2);
                    $diffInHours = $startTime->diffInHours($endTime);

                    if ($diffInHours == 1) {
                        $total_leave_days = 0.5 / 4;
                    } elseif ($diffInHours == 2 || ($diffInHours > 1 && $diffInHours < 2)) {
                        $total_leave_days = 0.5 / 4 * 2;
                    } elseif ($diffInHours == 3 || ($diffInHours > 2 && $diffInHours < 3)) {
                        $total_leave_days = 0.5 / 4 * 3;
                    }
                } else {
                    $interval = $startDate->diff($endDate);
                    $total_leave_days = $interval->days;

                    if ($total_leave_days <= 7) {
                        $total_leave_days = 0;
                        foreach ($daterange as $date) {
                            // Check if the day is not Saturday (6) or Sunday (7)
                            if ($date->format('N') < 6) {
                                $total_leave_days++;
                            }
                        }
                    }
                }

                // Format end time if needed
                $endTime = isset($endTime) ? $endTime->format('g:i A') : null;

                // Check if the employee has already applied for leave on these dates
                $existingLeave = Leave::where('employee_id', $employee->id)
                    ->where(function($query) use ($startDate, $endDate) {
                        $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                            ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                    })
                    ->where('status', '!=', 'Reject')
                    ->first();

                if ($existingLeave) {
                    return $this->errorResponse(__('Leave has already been applied for the selected date range.'));
                }

                // Fetch and validate leave type
                $leaveType = LeaveType::find($request->leave_type_id);
                if (!$leaveType) {
                    return $this->errorResponse(__('Invalid leave type selected.'));
                }

                // For probation employees, validate leave types
                if ($employee->is_probation == 1 && !in_array($leaveType->title, ['Sick Leave', 'Birthday Leave'])) {
                    return $this->errorResponse(__('Employees on probation can only apply for Sick Leave or Birthday Leave.'));
                }

                // For birthday leave, validate if it's the employee's birth month
                if ($leaveType->title == 'Birthday Leave' && Carbon::parse($employee->dob)->month != Carbon::now()->month) {
                    return $this->errorResponse(__('Birthday Leave can only be applied during your birth month.'));
                }

                // Calculate total leave taken for this leave type in the current year
                $currentYear = date('Y');
                $status = ['Pending', 'Approve'];
                $leavesTaken = Leave::where('employee_id', $employee->id)
                    ->where('leave_type_id', $request->leave_type_id)
                    ->whereYear('start_date', $currentYear)
                    ->whereIn('status', $status)
                    ->sum('total_leave_days');

                // Special handling for Paid Leave
                if ($leaveType->title == 'Paid Leave') {
                    $availableBalance = $employee->paid_leave_balance;
                    if (($leavesTaken + $total_leave_days) > $availableBalance) {
                        return $this->errorResponse(__('You have exceeded your available paid leave balance.'));
                    }
                } else {
                    // Regular leave validation
                    if (($leavesTaken + $total_leave_days) > $leaveType->days) {
                        return $this->errorResponse(__('You have exceeded the maximum allowed leave days for this leave type.'));
                    }
                }

                // Create the leave record
                $leave = new Leave();
                $leave->employee_id = $employee->id;
                $leave->leave_type_id = $request->leave_type_id;
                $leave->applied_on = date('Y-m-d');
                $leave->start_date = $request->start_date;
                $leave->end_date = $request->end_date ?? $request->start_date;
                $leave->total_leave_days = $total_leave_days;
                $leave->leave_reason = $request->leave_reason;
                $leave->remark = $request->remark ?? null;
                $leave->status = 'Pending';
                $leave->created_by = $user->creatorId();
                $leave->leavetype = $request->is_halfday ?? 'full';

                // Additional fields for short leave and half day
                if (isset($request->is_halfday) && $request->is_halfday == 'short') {
                    $leave->start_time = $request->start_time;
                    $leave->end_time = $endTime;
                } elseif (isset($request->is_halfday) && $request->is_halfday == 'half') {
                    $leave->day_segment = $request->day_segment ?? 'morning';
                }

                $leave->save();

                // Update paid leave balance if needed
                if ($request->leave_type_id == 3) {
                    $employee->paid_leave_balance = $employee->paid_leave_balance - $total_leave_days;
                    $employee->update();
                }

                // Send email notifications
                try {
                    $employeeEmail = $employee->email;
                    $employeeName = $employee->name;
                    $additionalReceivers = [
                        'karan@qubifytech.com',
                        // Add other receivers as needed
                    ];

                    $teamLeader = '';
                    if ($employee->is_team_leader == 0) {
                        $teamLeader = $employee->getTeamLeaderNameAndId();
                    }

                    if (!empty($teamLeader)) {
                        Mail::to($teamLeader)
                            ->cc($additionalReceivers)
                            ->send(new LeaveRequest($leave, $employeeEmail, $employeeName));

                        // Notification to Company, HR, TL, Employee
                        $tlid = Employee::find($teamLeader->id)->user_id;
                    } else {
                        Mail::to($additionalReceivers)->send(new LeaveRequest($leave, $employeeEmail, $employeeName));
                    }
                } catch (\Exception $e) {
                    \Log::error('Mail sending failed: ' . $e->getMessage());
                    // Continue execution even if mail fails
                }

                return $this->successResponse([], __('Leave successfully created.'));
            } catch (\Exception $e) {
                return $this->errorResponse(__('An error occurred while creating leave: ' . $e->getMessage()));
            }
        } else {
            return $this->errorResponse(__('Permission denied.'));
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Leave')) {
            try {
                $leave = Leave::findOrFail($id);
                if ($leave->created_by == $user->creatorId()) {
                    // Your existing logic for editing leave
                    // ...

                    return $this->successResponse($leave);
                } else {
                    return $this->errorResponse(__('Permission denied.'));
                }
            } catch (\Exception $e) {
                return $this->errorResponse(__('An error occurred while fetching leave for editing: ' . $e->getMessage()));
            }
        } else {
            return $this->errorResponse(__('Permission denied.'));
        }
    }

    /**
     * @OA\Put(
     *     path="/api/leaves/{id}",
     *     summary="Update Leave Request",
     *     description="Updates an existing leave request with validation and notifications",
     *     operationId="updateLeave",
     *     tags={"Leave Management"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Leave ID to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"leave_type_id", "start_date", "leave_reason"},
     *             @OA\Property(property="employee_id", type="integer", example=1, description="Employee ID (for admin/HR only)"),
     *             @OA\Property(property="leave_type_id", type="integer", example=1, description="Leave type ID"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-06-15", description="Leave start date"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-06-16", description="Leave end date"),
     *             @OA\Property(property="leave_reason", type="string", example="Personal work", description="Reason for leave"),
     *             @OA\Property(property="remark", type="string", example="Additional notes", description="Additional remarks"),
     *             @OA\Property(property="status", type="string", enum={"Pending", "Approve", "Reject"}, example="Approve", description="Leave status (admin/HR only)"),
     *             @OA\Property(property="is_halfday", type="string", enum={"full", "half", "short"}, example="full", description="Leave duration type"),
     *             @OA\Property(property="day_segment", type="string", enum={"morning", "afternoon"}, example="morning", description="Half day segment"),
     *             @OA\Property(property="start_time", type="string", example="10:00 AM", description="Short leave start time"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Leave updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Leave successfully updated"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Permission denied"),
     *     @OA\Response(response=404, description="Leave not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Check if user has permission to edit leaves
            if (!$user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Leave') && 
                !$user->can('Edit Leave')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied'
                ], 403);
            }
    
            $leave = Leave::find($id);
            if (!$leave) {
                return response()->json([
                    'success' => false,
                    'message' => 'Leave not found'
                ], 404);
            }
    
            // Check if user has permission to edit this specific leave
            if ($leave->created_by != $user->creatorId()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied'
                ], 403);
            }
    
            // Different validation and update logic based on user type
            if ($user->type == 'employee') {
                // Employees can only update their own leaves and specific fields
                $validator = \Validator::make($request->all(), [
                    'leave_type_id' => 'required|integer|exists:leave_types,id',
                    'start_date' => 'required|date|after_or_equal:today',
                    'end_date' => 'nullable|date|after_or_equal:start_date',
                    'leave_reason' => 'required|string|min:10',
                    'remark' => 'nullable|string|max:500',
                    'is_halfday' => 'nullable|in:full,half,short',
                    'day_segment' => 'nullable|in:morning,afternoon',
                    'start_time' => 'nullable|string',
                ]);
    
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => $validator->errors()
                    ], 422);
                }
    
                // Only allow updates if leave is still in Pending status for employees
                if ($leave->status != 'Pending') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only pending leaves can be updated'
                    ], 422);
                }
    
                $employee = Employee::where('user_id', $user->id)->first();
                if (!$employee) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Employee not found'
                    ], 404);
                }
    
                // Check if employee is trying to edit someone else's leave
                if ($leave->employee_id != $employee->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can only edit your own leaves'
                    ], 403);
                }
            } else {
                // Admin/HR can update all fields
                $validator = \Validator::make($request->all(), [
                    'employee_id' => 'nullable|integer|exists:employees,id',
                    'leave_type_id' => 'required|integer|exists:leave_types,id',
                    'start_date' => 'required|date',
                    'end_date' => 'nullable|date|after_or_equal:start_date',
                    'leave_reason' => 'required|string',
                    'remark' => 'nullable|string|max:500',
                    'status' => 'nullable|in:Pending,Approve,Reject',
                    'is_halfday' => 'nullable|in:full,half,short',
                    'day_segment' => 'nullable|in:morning,afternoon',
                    'start_time' => 'nullable|string',
                ]);
    
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => $validator->errors()
                    ], 422);
                }
    
                // Use provided employee_id or keep existing one
                $employeeId = $request->employee_id ?? $leave->employee_id;
                $employee = Employee::find($employeeId);
                if (!$employee) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Employee not found'
                    ], 404);
                }
            }
    
            // Calculate total leave days
            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date ?? $request->start_date);
            $endDate->modify('+1 day'); // Include end date
            $interval = new \DateInterval('P1D');
            $daterange = new \DatePeriod($startDate, $interval, $endDate);
            $total_leave_days = 0;
    
            // Handle different leave types (full day, half day, short leave)
            $leaveType = $request->is_halfday ?? 'full';
            $calculatedEndTime = null;
    
            if ($leaveType == 'half') {
                $total_leave_days = 0.5;
            } elseif ($leaveType == 'short') {
                if ($request->start_time) {
                    $startTime = \DateTime::createFromFormat('h:i A', $request->start_time);
                    $calculatedEndTime = clone $startTime;
                    $calculatedEndTime->modify('+2 hours');
                    
                    $startTimeCarbon = Carbon::createFromFormat('h:i A', trim($request->start_time));
                    $endTimeCarbon = Carbon::createFromFormat('h:i A', $calculatedEndTime->format('g:i A'));
                    $diffInHours = $startTimeCarbon->diffInHours($endTimeCarbon);
    
                    if ($diffInHours == 1) {
                        $total_leave_days = 0.5 / 4;
                    } elseif ($diffInHours == 2 || ($diffInHours > 1 && $diffInHours < 2)) {
                        $total_leave_days = 0.5 / 4 * 2;
                    } elseif ($diffInHours == 3 || ($diffInHours > 2 && $diffInHours < 3)) {
                        $total_leave_days = 0.5 / 4 * 3;
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Start time is required for short leave'
                    ], 422);
                }
            } else {
                $intervalDays = $startDate->diff($endDate);
                $total_leave_days = $intervalDays->days;
    
                if ($total_leave_days <= 7) {
                    $total_leave_days = 0;
                    foreach ($daterange as $date) {
                        // Check if the day is not Saturday (6) or Sunday (7)
                        if ($date->format('N') < 6) {
                            $total_leave_days++;
                        }
                    }
                }
            }
    
            // Check if the leave type is valid
            $leaveTypeModel = LeaveType::find($request->leave_type_id);
            if (!$leaveTypeModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid leave type selected'
                ], 422);
            }
    
            // For probation employees, validate leave types
            if ($employee->is_probation == 1 && !in_array($leaveTypeModel->title, ['Sick Leave', 'Birthday Leave'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employees on probation can only apply for Sick Leave or Birthday Leave'
                ], 422);
            }
    
            // For birthday leave, validate if it's the employee's birth month
            if ($leaveTypeModel->title == 'Birthday Leave' && Carbon::parse($employee->dob)->month != Carbon::parse($request->start_date)->month) {
                return response()->json([
                    'success' => false,
                    'message' => 'Birthday Leave can only be applied during your birth month'
                ], 422);
            }
    
            // Calculate total leave taken for this leave type in the current year (excluding current leave)
            $currentYear = date('Y');
            $status = ['Pending', 'Approve'];
            $leavesTaken = Leave::where('employee_id', $employee->id)
                ->where('leave_type_id', $request->leave_type_id)
                ->where('id', '!=', $id) // Exclude current leave
                ->whereYear('start_date', $currentYear)
                ->whereIn('status', $status)
                ->sum('total_leave_days');
    
            // Handle paid leave balance adjustment if leave type changes
            if (isset($employee->paid_leave_balance)) {
                if ($leave->leave_type_id == 3 && $request->leave_type_id != 3) {
                    // Refund the old leave days if changing from paid leave to another type
                    $employee->paid_leave_balance = $employee->paid_leave_balance + $leave->total_leave_days;
                    $employee->save();
                } elseif ($leave->leave_type_id != 3 && $request->leave_type_id == 3) {
                    // Deduct new paid leave days if changing to paid leave
                    if ($employee->paid_leave_balance < $total_leave_days) {
                        return response()->json([
                            'success' => false,
                            'message' => 'You have insufficient paid leave balance'
                        ], 422);
                    }
                    $employee->paid_leave_balance = $employee->paid_leave_balance - $total_leave_days;
                    $employee->save();
                } elseif ($leave->leave_type_id == 3 && $request->leave_type_id == 3) {
                    // Adjust paid leave balance for changes in days
                    $difference = $leave->total_leave_days - $total_leave_days;
                    if (($employee->paid_leave_balance + $difference) < 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient paid leave balance for the updated days'
                        ], 422);
                    }
                    $employee->paid_leave_balance = $employee->paid_leave_balance + $difference;
                    $employee->save();
                }
            }
    
            // Special handling for Paid Leave
            if ($leaveTypeModel->title == 'Paid Leave' && isset($employee->paid_leave_balance)) {
                $availableBalance = $employee->paid_leave_balance;
                if (($leavesTaken + $total_leave_days) > $availableBalance) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have exceeded your available paid leave balance'
                    ], 422);
                }
            } else {
                // Regular leave validation
                if (($leavesTaken + $total_leave_days) > $leaveTypeModel->days) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have exceeded the maximum allowed leave days for this leave type'
                    ], 422);
                }
            }
    
            // Check for date conflicts (for all users)
            $existingLeave = Leave::where('employee_id', $employee->id)
                ->where('id', '!=', $id) // Exclude current leave
                ->where(function($query) use ($request) {
                    $startDate = $request->start_date;
                    $endDate = $request->end_date ?? $request->start_date;
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                              ->where('end_date', '>=', $endDate);
                        });
                })
                ->where('status', '!=', 'Reject')
                ->first();
    
            if ($existingLeave) {
                return response()->json([
                    'success' => false,
                    'message' => 'Leave has already been applied for the selected date range'
                ], 422);
            }
    
            // Update leave fields
            $leave->leave_type_id = $request->leave_type_id;
            $leave->start_date = date('Y-m-d', strtotime($request->start_date));
            $leave->end_date = date('Y-m-d', strtotime($request->end_date ?? $request->start_date));
            $leave->total_leave_days = $total_leave_days;
            $leave->leave_reason = $request->leave_reason;
            $leave->remark = $request->remark ?? $leave->remark;
            $leave->leavetype = $leaveType;
    
            // Additional fields for admin/HR
            if ($user->type != 'employee') {
                if (isset($request->status)) {
                    $leave->status = $request->status;
                }
                if (isset($request->employee_id)) {
                    $leave->employee_id = $request->employee_id;
                }
            }
    
            // Handle different leave type specifics
            if ($leaveType == 'short' && $calculatedEndTime) {
                $leave->start_time = $request->start_time;
                $leave->end_time = $calculatedEndTime->format('g:i A');
                $leave->day_segment = $request->day_segment;
            } elseif ($leaveType == 'half') {
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
    
            $leave->save();
    
            // Send email and push notifications
            try {
                $employeeEmail = $employee->email;
                $employeeName = $employee->name;
                $additionalReceivers = [
                    // 'chitranshu@qubifytech.com',
                    // 'sharma.chitranshu@gmail.com',
                    // 'hr@qubifytech.com',
                    // 'swatichamannegi@gmail.com',
                    'karan@qubifytech.com',
                ];
    
                $teamLeader = null;
                if ($employee->is_team_leader == 0) {
                    $teamLeader = $employee->getTeamLeaderNameAndId();
                }
    
                // Send emails
                if (!empty($teamLeader)) {
                    try {
                        Mail::to($teamLeader)
                            ->cc($additionalReceivers)
                            ->send(new LeaveRequest($leave, $employeeEmail, $employeeName, 'update'));
                    } catch (\Exception $e) {
                        \Log::error("Mail Error: " . $e->getMessage());
                    }
    
                    // Send FCM notifications
                    $tlid = Employee::find($teamLeader->id)->user_id ?? null;
                    $fcmTokens = User::whereIn('type', ['hr', 'company'])
                        ->orWhere('id', $employee->user_id)
                        ->when($tlid, function($query) use ($tlid) {
                            return $query->orWhere('id', $tlid);
                        })
                        ->orWhere('id', 5)
                        ->whereNotNull('fcm_token')
                        ->pluck('fcm_token', 'name')
                        ->toArray();
    
                    foreach ($fcmTokens as $name => $fcmToken) {
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
                        Mail::to($additionalReceivers)->send(new LeaveRequest($leave, $employeeEmail, $employeeName, 'update'));
                    } catch (\Exception $e) {
                        \Log::error("Mail Error: " . $e->getMessage());
                    }
                }
    
            } catch (\Exception $e) {
                \Log::error('Notification sending failed: ' . $e->getMessage());
                // Continue execution even if notifications fail
            }
    
            // Load relationships for response
            $leave->load(['leaveType']);
    
            return response()->json([
                'success' => true,
                'message' => 'Leave successfully updated',
                'data' => $leave
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error('Leave update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating leave: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Delete Leave')) {
            try {
                $leave = Leave::findOrFail($id);
                if ($leave->created_by == $user->creatorId()) {
                    $leave->delete();
                    return $this->successResponse(__('Leave successfully deleted.'));
                } else {
                    dd($e);
                    return $this->errorResponse(__('Permission denied.'));
                }
            } catch (\Exception $e) {
                return $this->errorResponse(__('An error occurred while deleting leave: ' . $e->getMessage()));
            }
        } else {
            return $this->errorResponse(__('Permission denied.'));
        }
    }
}
