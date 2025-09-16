<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\AttendanceEmployee;
use App\Models\Designation;
use Illuminate\Support\Carbon;
use App\Models\Department;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\Utility;
use App\Models\Branch;
use App\Models\User;
use DB, File;
use Hash;

class AttendanceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/attendance-list",
     *     summary="Get Attendance Records",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type of attendance data to filter by (e.g., 'monthly' or 'daily').",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"monthly", "daily"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         description="Month to filter attendance by, in 'YYYY-MM' format.",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2023-08"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Specific date to filter attendance by, in 'YYYY-MM-DD' format.",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2023-08-29"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="branch",
     *         in="query",
     *         description="Branch ID to filter attendance records by.",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="department",
     *         in="query",
     *         description="Department ID to filter attendance records by.",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="attendance",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="employee_id", type="integer", example=1),
     *                     @OA\Property(property="date", type="string", format="date", example="2023-08-29"),
     *                     @OA\Property(property="status", type="string", example="Present"),
     *                     @OA\Property(property="clock_in", type="string", format="time", example="09:00:00"),
     *                     @OA\Property(property="clock_out", type="string", format="time", example="17:00:00")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="branches",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="string",
     *                     example="All"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="departments",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="string",
     *                     example="HR"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    // old code 
    // public function index(Request $request)
    // {
    //     try {
    //         if (!\Auth::user()->getAllPermissions()->pluck('name')->contains('Manage Attendance')) {
    //             return response()->json(['error' => __('Permission denied.')], 403);
    //         }
    //         $branch = Branch::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
    //         $branch->prepend('All', '');

    //         $department = Department::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
    //         $department->prepend('All', '');

    //         if (\Auth::user()->type == 'employee') {
    //             $emp = \Auth::user()->employee ? \Auth::user()->employee->id : 0;

    //             $attendanceEmployee = AttendanceEmployee::where('employee_id', $emp);

    //             if ($request->type === 'monthly' && !empty($request->month)) {
    //                 $month = date('m', strtotime($request->month));
    //                 $year  = date('Y', strtotime($request->month));

    //                 $start_date = date($year . '-' . $month . '-01');
    //                 $end_date   = date($year . '-' . $month . '-t');

    //                 $attendanceEmployee->whereBetween('date', [$start_date, $end_date]);
    //             } elseif ($request->type === 'daily' && !empty($request->date)) {
    //                 $attendanceEmployee->where('date', $request->date);
    //             } else {
    //                 $month      = date('m');
    //                 $year       = date('Y');
    //                 $start_date = date($year . '-' . $month . '-01');
    //                 $end_date   = date($year . '-' . $month . '-t');

    //                 $attendanceEmployee->whereBetween('date', [$start_date, $end_date]);
    //             }

    //             $attendanceEmployee = $attendanceEmployee->orderBy('date', 'DESC')->get();
    //         } else {
    //             $employee = Employee::select('id')->where('created_by', \Auth::user()->creatorId());

    //             if (!empty($request->branch)) {
    //                 $employee->where('branch_id', $request->branch);
    //             }

    //             if (!empty($request->department)) {
    //                 $employee->where('department_id', $request->department);
    //             }

    //             $employeeIds = $employee->pluck('id');

    //             $attendanceEmployee = AttendanceEmployee::whereIn('employee_id', $employeeIds);

    //             if ($request->type === 'monthly' && !empty($request->month)) {
    //                 $month = date('m', strtotime($request->month));
    //                 $year  = date('Y', strtotime($request->month));

    //                 $start_date = date($year . '-' . $month . '-01');
    //                 $end_date   = date($year . '-' . $month . '-t');

    //                 $attendanceEmployee->whereBetween('date', [$start_date, $end_date]);
    //             } elseif ($request->type === 'daily' && !empty($request->date)) {
    //                 $attendanceEmployee->where('date', $request->date);
    //             } else {
    //                 $month      = date('m');
    //                 $year       = date('Y');
    //                 $start_date = date($year . '-' . $month . '-01');
    //                 $end_date   = date($year . '-' . $month . '-t');

    //                 $attendanceEmployee->whereBetween('date', [$start_date, $end_date]);
    //             }

    //             $attendanceEmployee = $attendanceEmployee->orderBy('date', 'DESC')->get();
    //         }

    //         return response()->json([
    //             'attendance' => $attendanceEmployee,
    //             'branches' => $branch,
    //             'departments' => $department
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Server error'], 500);
    //     }
    // }
    public function index(Request $request)
    {
        try {
            if (!\Auth::user()->getAllPermissions()->pluck('name')->contains('Manage Attendance')) {
                return response()->json(['error' => __('Permission denied.')], 403);
            }
            $branch = Branch::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
            $branch->prepend('All', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
            $department->prepend('All', '');

            if (\Auth::user()->type == 'employee') {
                $emp = \Auth::user()->employee ? \Auth::user()->employee->id : 0;

                $attendanceEmployee = AttendanceEmployee::where('employee_id', $emp);

                if ($request->type === 'monthly' && !empty($request->month)) {
                    $month = date('m', strtotime($request->month));
                    $year  = date('Y', strtotime($request->month));

                    $start_date = date($year . '-' . $month . '-01');
                    $end_date   = date($year . '-' . $month . '-t');

                    $attendanceEmployee->whereBetween('date', [$start_date, $end_date]);
                } elseif ($request->type === 'daily' && !empty($request->date)) {
                    $attendanceEmployee->where('date', $request->date);
                } else {
                    $month      = date('m');
                    $year       = date('Y');
                    $start_date = date($year . '-' . $month . '-01');
                    $end_date   = date($year . '-' . $month . '-t');

                    $attendanceEmployee->whereBetween('date', [$start_date, $end_date]);
                }

                $attendanceEmployee = $attendanceEmployee->orderBy('date', 'DESC')->get();
            } else {
                $employee = Employee::select('id')->where('created_by', \Auth::user()->creatorId());

                if (!empty($request->branch)) {
                    $employee->where('branch_id', $request->branch);
                }

                if (!empty($request->department)) {
                    $employee->where('department_id', $request->department);
                }

                $employeeIds = $employee->pluck('id');

                $attendanceEmployee = AttendanceEmployee::whereIn('employee_id', $employeeIds);

                if ($request->type === 'monthly' && !empty($request->month)) {
                    $month = date('m', strtotime($request->month));
                    $year  = date('Y', strtotime($request->month));

                    $start_date = date($year . '-' . $month . '-01');
                    $end_date   = date($year . '-' . $month . '-t');

                    $attendanceEmployee->whereBetween('date', [$start_date, $end_date]);
                } elseif ($request->type === 'daily' && !empty($request->date)) {
                    $attendanceEmployee->where('date', $request->date);
                } else {
                    $month      = date('m');
                    $year       = date('Y');
                    $start_date = date($year . '-' . $month . '-01');
                    $end_date   = date($year . '-' . $month . '-t');

                    $attendanceEmployee->whereBetween('date', [$start_date, $end_date]);
                }

                $attendanceEmployee = $attendanceEmployee->orderBy('date', 'DESC')->get();
            }

            return response()->json([
                'attendance' => $attendanceEmployee,
                'branches' => $branch,
                'departments' => $department
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/attendance-create",
     *     summary="Retrieve Employees for Attendance Creation",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Employees retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employees retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve employees",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve employees."),
     *             @OA\Property(property="error", type="string", example="Error message details.")
     *         )
     *     )
     * )
     */
    public function create()
    {
        if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Create Attendance')) {
            try {
                $employees = User::where('created_by', \Auth::user()->creatorId())
                                 ->where('type', 'employee')
                                 ->get(['id', 'name']);

                return response()->json([
                    'success' => true,
                    'message' => 'Employees retrieved successfully.',
                    'data' => $employees,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve employees.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Permission denied.',
            ], 403);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/attendance-store",
     *     summary="Create Employee Attendance",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="employee_id", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date", example="2023-08-29"),
     *             @OA\Property(property="clock_in", type="string", format="time", example="09:00"),
     *             @OA\Property(property="clock_out", type="string", format="time", example="17:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Employee attendance successfully created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="string", example="Employee attendance successfully created.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Employee Attendance Already Created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Employee Attendance Already Created.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="The date field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An error occurred while processing your request.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            if (!\Auth::user()->getAllPermissions()->pluck('name')->contains('Create Attendance')) {
                return response()->json(['error' => __('Permission denied.')], 403);
            }

            // Validate the request data
            $validator = \Validator::make($request->all(), [
                'employee_id' => 'required',
                'date' => 'required|date_format:Y-m-d',
                'clock_in' => 'required|date_format:H:i',
                'clock_out' => 'required|date_format:H:i',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            // Fetch company start and end times
            $startTime = Utility::getValByName('company_start_time');
            $endTime = Utility::getValByName('company_end_time');

            // Check if attendance for the employee on the same date already exists
            $attendance = AttendanceEmployee::where('employee_id', $request->employee_id)
                                            ->where('date', $request->date)
                                            ->where('clock_out', '00:00:00')
                                            ->exists();

            if ($attendance) {
                return response()->json(['error' => __('Employee Attendance Already Created.')], 409);
            }

            // Calculate late time
            $date = date("Y-m-d");
            $totalLateSeconds = strtotime($request->clock_in) - strtotime($date . ' ' . $startTime);
            $late = gmdate('H:i:s', max($totalLateSeconds, 0));

            // Calculate early leaving time
            $totalEarlyLeavingSeconds = strtotime($date . ' ' . $endTime) - strtotime($request->clock_out);
            $earlyLeaving = gmdate('H:i:s', max($totalEarlyLeavingSeconds, 0));

            // Calculate overtime
            $overtime = '00:00:00';
            if (strtotime($request->clock_out) > strtotime($date . ' ' . $endTime)) {
                $totalOvertimeSeconds = strtotime($request->clock_out) - strtotime($date . ' ' . $endTime);
                $overtime = gmdate('H:i:s', $totalOvertimeSeconds);
            }

            // Create a new attendance record
            $employeeAttendance = new AttendanceEmployee();
            $employeeAttendance->employee_id = $request->employee_id;
            $employeeAttendance->date = $request->date;
            $employeeAttendance->status = 'Present';
            $employeeAttendance->clock_in = $request->clock_in . ':00';
            $employeeAttendance->clock_out = $request->clock_out . ':00';
            $employeeAttendance->late = $late;
            $employeeAttendance->early_leaving = $earlyLeaving;
            $employeeAttendance->overtime = $overtime;
            $employeeAttendance->total_rest = '00:00:00';
            $employeeAttendance->created_by = \Auth::user()->creatorId();
            $employeeAttendance->save();

            return response()->json(['success' => __('Employee attendance successfully created.')], 201);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json(['error' => __('An error occurred while processing your request.')], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/attendance-edit/{id}",
     *     summary="Edit Attendance Record",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the attendance record to edit",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of attendance record and employees",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="attendanceEmployee",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=1),
     *                 @OA\Property(property="date", type="string", format="date", example="2023-08-29"),
     *                 @OA\Property(property="clock_in", type="string", format="time", example="09:00:00"),
     *                 @OA\Property(property="clock_out", type="string", format="time", example="17:00:00"),
     *                 @OA\Property(property="status", type="string", example="Present"),
     *                 @OA\Property(property="late", type="string", format="time", example="00:15:00"),
     *                 @OA\Property(property="early_leaving", type="string", format="time", example="00:00:00"),
     *                 @OA\Property(property="overtime", type="string", format="time", example="01:00:00"),
     *                 @OA\Property(property="total_rest", type="string", format="time", example="00:30:00"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             ),
     *             @OA\Property(
     *                 property="employees",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attendance record not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Attendance record not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An error occurred while processing your request.")
     *         )
     *     )
     * )
     */
    public function edit($id)
    {
        try {
            if (!\Auth::user()->getAllPermissions()->pluck('name')->contains('Edit Attendance')) {
                return response()->json(['error' => __('Permission denied.')], 403);
            }

            // Retrieve the attendance record
            $attendanceEmployee = AttendanceEmployee::find($id);

            if (!$attendanceEmployee) {
                return response()->json(['error' => __('Attendance record not found.')], 404);
            }

            // Retrieve employees created by the authenticated user
            $employees = Employee::where('created_by', \Auth::user()->creatorId())
                                ->get(['id', 'name']);
            return response()->json([
                'attendanceEmployee' => $attendanceEmployee,
                'employees' => $employees,
            ], 200);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json(['error' => __('An error occurred while processing your request.')], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/attendance-update/{id}",
     *     summary="Update Attendance Record",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the attendance record to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="employee_id", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date", example="2023-08-29"),
     *             @OA\Property(property="clock_in", type="string", format="time", example="09:00"),
     *             @OA\Property(property="clock_out", type="string", format="time", example="17:00"),
     *             @OA\Property(property="time", type="string", format="time", example="17:00", description="Required for employee type only.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attendance updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="string", example="Attendance updated successfully."),
     *             @OA\Property(property="is_birthday", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attendance record not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Attendance record not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Clock in/out multiple times per day not allowed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Employee is not allowed to clock in and out multiple times per day.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An error occurred while processing your request.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {     
            $todayAttendance = AttendanceEmployee::where('employee_id', $request->employee_id)
                                ->where('date', date('Y-m-d'))
                                ->orderBy('id', 'DESC')
                                ->first();
            if (!$todayAttendance) {
                return response()->json(['error' => __('Attendance record not found.')], 404);
            }

            if ($todayAttendance->clock_out != '00:00:00') {
                return response()->json(['error' => __('Employee is not allowed to clock in and out multiple times per day.')], 409);
            }

            $startTime = Utility::getValByName('company_start_time');
            $endTime = Utility::getValByName('company_end_time');
            $date = date("Y-m-d");

            if (\Auth::user()->type == 'employee') {
                $time = $request->time;

                $totalEarlyLeavingSeconds = strtotime($date . ' ' . $endTime) - time();
                $earlyLeaving = gmdate('H:i:s', max($totalEarlyLeavingSeconds, 0));

                $overtime = '00:00:00';
                if (time() > strtotime($date . ' ' . $endTime)) {
                    $totalOvertimeSeconds = time() - strtotime($date . ' ' . $endTime);
                    $overtime = gmdate('H:i:s', $totalOvertimeSeconds);
                }

                $attendanceEmployee = AttendanceEmployee::find($todayAttendance->id);
                $attendanceEmployee->clock_out = $time;
                $attendanceEmployee->early_leaving = $earlyLeaving;
                $attendanceEmployee->overtime = $overtime;
                $attendanceEmployee->save();

                return response()->json(['success' => __('Attendance updated successfully.')], 200);

            } else {
                $totalLateSeconds = strtotime($request->clock_in) - strtotime($date . ' ' . $startTime);
                $late = gmdate('H:i:s', max($totalLateSeconds, 0));

                $totalEarlyLeavingSeconds = strtotime($date . ' ' . $endTime) - strtotime($request->clock_out);
                $earlyLeaving = gmdate('H:i:s', max($totalEarlyLeavingSeconds, 0));

                $overtime = '00:00:00';
                if (strtotime($request->clock_out) > strtotime($date . ' ' . $endTime)) {
                    $totalOvertimeSeconds = strtotime($request->clock_out) - strtotime($date . ' ' . $endTime);
                    $overtime = gmdate('H:i:s', $totalOvertimeSeconds);
                }

                $attendanceEmployee = AttendanceEmployee::find($todayAttendance->id);
                $attendanceEmployee->employee_id = $request->employee_id;
                $attendanceEmployee->date = $request->date;
                $attendanceEmployee->clock_in = $request->clock_in;
                $attendanceEmployee->clock_out = $request->clock_out;
                $attendanceEmployee->late = $late;
                $attendanceEmployee->early_leaving = $earlyLeaving;
                $attendanceEmployee->overtime = $overtime;
                $attendanceEmployee->total_rest = '00:00:00';
                $attendanceEmployee->save();

                return response()->json(['success' => __('Attendance updated successfully.'), 'is_birthday' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => __('An error occurred while processing your request.')], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/attendance-delete/{id}",
     *     summary="Delete Attendance Record",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the attendance record to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attendance successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="string", example="Attendance successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attendance record not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Attendance record not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An error occurred while processing your request.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            if (!\Auth::user()->getAllPermissions()->pluck('name')->contains('Delete Attendance')) {
                return response()->json(['error' => __('Permission denied.')], 403);
            }

            $attendance = AttendanceEmployee::find($id);
            if (!$attendance) {
                return response()->json(['error' => __('Attendance record not found.')], 404);
            }

            $attendance->delete();

            return response()->json(['success' => __('Attendance successfully deleted.')], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => __('An error occurred while processing your request.')], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/attendance",
     *     summary="Record Employee Attendance",
     *     tags={"Attendance"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="time", type="string", format="time", example="09:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attendance recorded successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="string", example="Attendance recorded successfully."),
     *             @OA\Property(property="is_birthday", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="IP restriction or permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="This IP is not allowed to clock in & clock out.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An error occurred while processing your request.")
     *         )
     *     )
     * )
     */
    public function attendance(Request $request)
    {
        try {
            $settings = Utility::settings();

            // Check for IP restriction setting
            if ($settings['ip_restrict'] == 'on') {
                $userIp = $request->ip();
                $ip = IpRestrict::where('created_by', \Auth::user()->creatorId())
                                ->where('ip', $userIp)
                                ->first();

                if (empty($ip)) {
                    return response()->json(['error' => __('This IP is not allowed to clock in & clock out.')], 403);
                }
            }

            $employeeId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
            $todayAttendance = AttendanceEmployee::where('employee_id', $employeeId)
                                                ->where('date', date('Y-m-d'))
                                                ->first();

            $startTime = Utility::getValByName('company_start_time');
            $endTime = Utility::getValByName('company_end_time');

            // Check if there is an open attendance record
            $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                                            ->where('clock_out', '00:00:00')
                                            ->orderBy('id', 'desc')
                                            ->first();

            if ($attendance != null) {
                // Close the previous attendance record
                $attendance->clock_out = $endTime;
                $attendance->save();
            }

            $date = date("Y-m-d");
            $time = $request->time;

            // Calculate lateness
            $totalLateSeconds = time() - strtotime($date . ' ' . $startTime);
            $late = gmdate('H:i:s', max($totalLateSeconds, 0));

            // Check if there is any previous attendance record for the user
            $checkDb = AttendanceEmployee::where('employee_id', \Auth::user()->id)->exists();

            // Create a new attendance record
            $employeeAttendance = new AttendanceEmployee();
            $employeeAttendance->employee_id = $employeeId;
            $employeeAttendance->date = $date;
            $employeeAttendance->status = 'Present';
            $employeeAttendance->clock_in = $time;
            $employeeAttendance->clock_out = '00:00:00';
            $employeeAttendance->late = $late;
            $employeeAttendance->early_leaving = '00:00:00';
            $employeeAttendance->overtime = '00:00:00';
            $employeeAttendance->total_rest = '00:00:00';
            $employeeAttendance->created_by = \Auth::user()->id;
            $employeeAttendance->save();

            // Check if today is the employee's birthday
            $birthDate = Carbon::parse(\Auth::user()->employee->dob);
            $currentDate = Carbon::now();
            $isBirth = $birthDate->format('m-d') === $currentDate->format('m-d') || \Auth::user()->employee->isBirthDay;

            // Reset the isBirthday flag for the employee
            $employee = \Auth::user()->employee;
            $employee->isBirthDay = false;
            $employee->save();

            return response()->json(['success' => __('Attendance recorded successfully.'), 'is_birthday' => $isBirth], 200);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json(['error' => __('An error occurred while processing your request.')], 500);
        }
    }

    /**
 * @OA\Get(
 *     path="/api/attendance-current-timer-state",
 *     summary="Get Current Attendance Timer State",
 *     tags={"Attendance"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Current attendance timer state retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="clock_in", type="string", format="date-time", example="2023-08-29T09:00:00Z"),
 *             @OA\Property(property="attendance_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="An error occurred while processing your request.")
 *         )
 *     )
 * )
 */
    public function currentTimeAttendance()
    {
        try {
            $employeeId = Auth::id();

            $attendance = AttendanceEmployee::select('attendance_employees.*')
                                ->join('employees', 'attendance_employees.employee_id', '=', 'employees.id')
                                ->where('employees.user_id', $employeeId)
                                ->where('attendance_employees.clock_out', '00:00:00')
                                ->orderBy('attendance_employees.id', 'desc')
                                ->first();

            if ($attendance) {
                return response()->json([
                    'clock_in' => Carbon::parse($attendance->clock_in)->toIso8601String(),
                    'attendance_id' => $attendance->id
                ], 200);
            }

            return response()->json(['clock_in' => null], 200);
            
        } catch (\Exception $e) {
            return response()->json(['error' => __('An error occurred while processing your request.')], 500);
        }
    }
    
    public function currentTimerState(Request $request)
    {
        try {
            $employeeId = $request->employee_id;

            $attendance = AttendanceEmployee::select('attendance_employees.*')
                                ->join('employees', 'attendance_employees.employee_id', '=', 'employees.id')
                                ->where('employees.user_id', $employeeId)
                                ->where('attendance_employees.clock_out', '00:00:00')
                                ->orderBy('attendance_employees.id', 'desc')
                                ->first();

            if ($attendance) {
                return response()->json([
                    'clock_in' => Carbon::parse($attendance->clock_in)->toIso8601String(),
                    'attendance_id' => $attendance->id
                ], 200);
            }

            return response()->json(['clock_in' => null], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => __('An error occurred while processing your request.')], 500);
        }
    }
    
    public function getEmployeeDataCumList(Request $request)
    {
        if($request->empId){
            $employeeAttendance = AttendanceEmployee::orderBy('id', 'desc')->where('employee_id', '=', $request->empId)->where('date', '=', $request->date)->first();
            $employeeAttendanceList = AttendanceEmployee::orderBy('id', 'desc')->where('employee_id', '=', $request->empId)->where('date', '=', $request->date)->get();
            $employee['employeeAttendance'] = $employeeAttendance;
            $employee['employeeAttendanceList'] = $employeeAttendanceList;
            return response()->json($employee);
        }
    }
    
    public function getTodayAttendance(Request $request)
    {
        return $todayAttendance = AttendanceEmployee::where('employee_id', '=', $request->employee_id)->where('date', date('Y-m-d'))->OrderBy('id', 'DESC')->limit(1)->first();
    }

}
