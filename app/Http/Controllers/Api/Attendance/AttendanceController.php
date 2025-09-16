<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Department;
use App\Models\AttendanceEmployee;
use App\Models\Employee;
use App\Models\User;
use App\Models\AadhaarDetail;
use App\Models\Holiday;
use Illuminate\Support\Carbon;
use App\Helpers\Helper;
use App\Models\Utility;
use App\Models\Leave;

use DateTime;


class AttendanceController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/attendance",
     *     summary="Get list of employees with attendance status",
     *     description="Retrieves a filtered list of employees with their attendance status for a specific date",
     *     tags={"Employee Management"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by employee name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="branch",
     *         in="query",
     *         description="Filter by branch ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="department",
     *         in="query",
     *         description="Filter by department ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filter by active status (1 = active, 0 = inactive)",
     *         required=false,
     *         @OA\Schema(type="integer", enum={0, 1})
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Date for attendance status (YYYY-MM-DD format)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of employees with attendance status",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employee list retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="employees",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="email", type="string", example="john@example.com"),
     *                         @OA\Property(property="phone", type="string", example="1234567890"),
     *                         @OA\Property(property="branch_id", type="integer", example=1),
     *                         @OA\Property(property="department_id", type="integer", example=1),
     *                         @OA\Property(property="is_active", type="boolean", example=true),
     *                         @OA\Property(
     *                             property="branch",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Main Branch")
     *                         ),
     *                         @OA\Property(
     *                             property="department",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="IT Department")
     *                         ),
     *                         @OA\Property(
     *                             property="attendance_status",
     *                             type="string",
     *                             example="present",
     *                             enum={"present", "absent", "leave", "weekend", "holiday"}
     *                         ),
     *                         @OA\Property(property="is_late", type="boolean", example=false),
     *                         @OA\Property(property="total_hours", type="string", example="08:30"),
     *                         @OA\Property(property="first_clock_in", type="string", example="09:05:00"),
     *                         @OA\Property(
     *                             property="leave_details",
     *                             type="object",
     *                             nullable=true,
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="leave_type_id", type="integer", example=1),
     *                             @OA\Property(property="leave_type_name", type="string", example="Sick Leave"),
     *                             @OA\Property(
     *                                 property="leavetype",
     *                                 type="string",
     *                                 example="full",
     *                                 enum={"full", "half", "short"}
     *                             ),
     *                             @OA\Property(property="start_date", type="string", format="date", example="2025-04-30"),
     *                             @OA\Property(property="end_date", type="string", format="date", example="2025-04-30"),
     *                             @OA\Property(property="total_days", type="integer", example=1),
     *                             @OA\Property(property="reason", type="string", example="Medical appointment")
     *                         ),
     *                         @OA\Property(property="base64_image", type="string", example="data:image/png;base64,...")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="filters",
     *                     type="object",
     *                     @OA\Property(
     *                         property="branches",
     *                         type="object",
     *                         additionalProperties={"type": "string"},
     *                         example={"": "All", "1": "Main Branch", "2": "Branch Office"}
     *                     ),
     *                     @OA\Property(
     *                         property="departments",
     *                         type="object",
     *                         additionalProperties={"type": "string"},
     *                         example={"": "All", "1": "IT Department", "2": "HR Department"}
     *                     )
     *                 ),
     *                 @OA\Property(property="date", type="string", format="date", example="2025-04-30"),
     *                 @OA\Property(property="is_holiday", type="string", nullable=true, example="Labor Day"),
     *                 @OA\Property(property="is_weekend", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve employee list")
     *         )
     *     )
     * )
     */
    public function getEmployeeList(Request $request)
    {
        try {
            $branches = Branch::with('departments')
                ->where('created_by', \Auth::user()->creatorId())
                ->get()
                ->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'departments' => $branch->departments->map(function ($department) {
                            return [
                                'id' => $department->id,
                                'name' => $department->name
                            ];
                        })
                    ];
                });

            // Add "All" option at the beginning
            $branches->prepend(['name' => 'All', 'id' => '', 'departments' => []]);

            $departments = Department::where('created_by', \Auth::user()->creatorId())
                ->pluck('name', 'id');
            $departments->prepend('All', '');

            // Set date - use requested date or default to current date
            $date = $request->date ? Carbon::parse($request->date)->toDateString() : Carbon::now()->toDateString();

            // Build employee query with filters
            $employeeQuery = Employee::where('created_by', \Auth::user()->creatorId());

            // Apply search filter if provided
            if (!empty($request->search)) {
                $searchTerm = $request->search;
                $employeeQuery->where('name', 'LIKE', "%{$searchTerm}%");
            }

            // Apply branch filter if provided
            if (!empty($request->branch)) {
                $employeeQuery->where('branch_id', $request->branch);
            }

            // Apply department filter if provided
            if (!empty($request->department)) {
                $employeeQuery->where('department_id', $request->department);
            }

            // Apply active status filter
            if ($request->has('is_active')) {
                $employeeQuery->where('is_active', $request->is_active);
            }

            // Get the filtered employees with necessary relations
            $employees = $employeeQuery->select('id', 'name','is_active','shift_start','user_id')
                ->orderBy('name')
                ->get();

            // Check for holidays on the given date
            $isHoliday = Holiday::where('date', $date)->first();
            $isWeekend = Carbon::parse($date)->isWeekend();

            // Get authenticated user for base64 check
            $auth = \Auth::user();

            // Prepare employee data with attendance status
            $employeesWithStatus = $employees->map(function ($employee) use ($date, $isHoliday, $isWeekend, $auth) {
                // Get attendance records for the employee on the given date
                $attendances = AttendanceEmployee::where('employee_id', $employee->id)
                    ->whereDate('date', $date)
                    ->orderBy('clock_in', 'ASC')
                    ->get();

                // Check for leave status
                $isLeave = Helper::checkLeave($date, $employee->id);
                $leaveDetails = Helper::getEmpLeave($date, $employee->id);

                // Calculate total hours
                $totalHours = Helper::calculateTotalTimeDifference($attendances);

                // Determine attendance status
                $status = 'absent';
                $isLate = false;
                $firstClockIn = null;

                if ($isHoliday) {
                    $status = 'holiday';
                } elseif ($isWeekend) {
                    $status = 'weekend';
                } elseif (count($attendances) > 0) {
                    $status = 'present';

                    // Check for late arrival
                    $firstAttendance = $attendances->first();
                    $firstClockIn = $firstAttendance->clock_in;

                    // Get company settings for start time (default to 9:00 AM if not set)
                    $startTime = '09:00:00'; // You might want to retrieve this from company settings
                    $isLate = strtotime($firstAttendance->clock_in) > strtotime($date . ' ' . $startTime);
                } elseif ($isLeave) {
                    $status = 'leave';
                } elseif ($isLate) {
                    $status = 'late';
                }

                // Add attendance status to employee data
                $employeeData = $employee->toArray();
                $employeeData['attendance_status'] = $status;
                $employeeData['total_hours'] = $totalHours;
                $employeeData['first_clock_in'] = $firstClockIn;

                // Get base64 image
                $aadhaar_base64_img = AadhaarDetail::where('employee_id', $employee->id)->value('photo_encoded');

                $user = User::where('id',$employee->user_id)->first();
                if ($user->base64) {
                    $aadhaar_base64_img = $user->base64;
                }
                $employeeData['base64_image'] = $aadhaar_base64_img;

                return $employeeData;
            });

            // Prepare response data
            $data = [
                'employees' => $employeesWithStatus,
                'filters' => [
                    'branches' => $branches,
                ],
                'date' => $date,
            ];

            return $this->successResponse($data, 'Employee list retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve employee list: ' . $e->getMessage(), 500);
        }
    }
    
    public function getEmployeeStatistics(Request $request)
    {
        try {
            $id = $request->input('id');
            $date = $request->input('date');
            $date = $date ? $date : date('Y-m-d');
            // Validate employee exists
            $employee = Employee::find($id);
            if (!$employee) {
                return $this->errorResponse('Employee not found.', 200);
            }
            $employeeAttendanceList = AttendanceEmployee::orderBy('clock_in', 'desc')
            ->where('employee_id', '=', $id)
            ->where('date', '=', $date)
            ->get();

            $formattedAttendanceData = [];
            $late = '';
            
            if (count($employeeAttendanceList) > 0) {
                $lastData = $employeeAttendanceList[count($employeeAttendanceList) - 1];
                $late = Helper::FormatTime($lastData->late);
            }
            
            foreach ($employeeAttendanceList as $employeeAttendance) {
                $checkIn = $employeeAttendance->clock_in;
                $checkOut = $employeeAttendance->clock_out;
                
                $early = strtotime($employeeAttendance->clock_in) < strtotime($employeeAttendance->employee->shift_start) ? 1 : 0;
                
                $formattedDifference = '';
                if ($checkOut != '00:00:00') {
                    $formattedDifference = $this->formatTimeDifference($checkIn, $checkOut);
                }
                
                $totalRest = Helper::FormatTime($employeeAttendance->total_rest);
                
                $status = '';
                if ($employeeAttendance->total_rest == '00:00:00') {
                    $status = ($early == 1) ? 'Arrived Early' : $late . ' (Late)';
                } else {
                    $status = $totalRest . ' (Rest)';
                }
                
                $formattedAttendanceData[] = [
                    'date' => $date,
                    'clock_in' => $employeeAttendance->clock_in,
                    'clock_out' => ($checkOut != '00:00:00') ? $checkOut : null,
                    'duration' => $formattedDifference,
                    'status' => $status,
                    'location' => $employee->office->name??'',
                ];
            }
            $data = [];
            $metrics = $this->calculateAttendanceMetrics($id);
            $data['metrics'] = $metrics;
            $data['attendance_log'] = $formattedAttendanceData;
    
            return $this->successResponse($data);
    
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    private function formatTimeDifference($checkIn, $checkOut)
    {
        $checkInTime = strtotime($checkIn);
        $checkOutTime = strtotime($checkOut);
        $diffInSeconds = abs($checkOutTime - $checkInTime);
        
        if ($diffInSeconds < 60) {
            return $diffInSeconds . ' secs';
        }
        
        $diffInMinutes = floor($diffInSeconds / 60);
        $remainingSeconds = $diffInSeconds % 60;
        
        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' mins ' . $remainingSeconds . ' secs';
        }
        
        $diffInHours = floor($diffInMinutes / 60);
        $remainingMinutes = $diffInMinutes % 60;
        
        return $diffInHours . ' hrs ' . $remainingMinutes . ' mins ' . $remainingSeconds . ' secs';
    }

    private function calculateAttendanceMetrics($employeeId = null)
    {
        if (!$employee = Employee::find($employeeId)) {
            return $this->errorResponse('Employee not found.', 200);
        }

        // Get current month start date and today's date
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-d'); // Today

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
        $startTimeSeconds = $startTimeObj->format('H') * 3600 + $startTimeObj->format('i') * 60 + $startTimeObj->format('s');

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

        $metrics['absentDays'] = $totalWorkingDays - $metrics['presentDays'];

        // Calculate rates (as percentages)
        if ($totalWorkingDays > 0) {
            $metrics['presentRate'] = round(($metrics['presentDays'] / $totalWorkingDays) * 100, 2);
            $metrics['absentRate'] = round(($metrics['absentDays'] / $totalWorkingDays) * 100, 2);

            // Calculate late rate as percentage of present days
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

        return $metrics;
    }

    // public function getAttendanceMetrics($id)
    // {
    //     try {
    //         // Validate employee exists
    //         if (!$employee = Employee::find($id)) {
    //             return $this->errorResponse('Employee not found.', 404);
    //         }
    
    //         // Date setup
    //         $startDate = now()->startOfMonth();
    //         $endDate = now();
    //         $expectedStartTime = Carbon::parse($employee->shift_start);
    
    //         // Fetch required data
    //         $attendanceRecords = AttendanceEmployee::where('employee_id', $id)
    //             ->whereBetween('date', [$startDate, $endDate])
    //             ->get()
    //             ->groupBy(function ($record) {
    //                 return Carbon::parse($record->date)->format('Y-m-d');
    //             });
    
    //         $holidays = Holiday::whereMonth('date', $startDate->month)
    //             ->whereYear('date', $startDate->year)
    //             ->pluck('date')
    //             ->toArray();
    
    //         // Initialize counters
    //         $counters = [
    //             'total_working_days' => 0,
    //             'present_days' => 0,
    //             'absent_days' => 0,
    //             'late_days' => 0,
    //             'total_late_minutes' => 0
    //         ];
    
    //         // Process each day
    //         $currentDay = $startDate->copy();
    //         while ($currentDay <= $endDate) {
    //             $date = $currentDay->format('Y-m-d');
    //             $isWeekend = $currentDay->isWeekend();
    //             $isHoliday = in_array($date, $holidays);
    
    //             if (!$isWeekend && !$isHoliday) {
    //                 $counters['total_working_days']++;
    
    //                 if (isset($attendanceRecords[$date])) {
    //                     $record = $attendanceRecords[$date]->first();
    //                     if ($record->clock_in) {
    //                         $counters['present_days']++;
    
    //                         // Check if late
    //                         $clockInTime = Carbon::parse($record->clock_in);
    //                         if ($clockInTime->gt($expectedStartTime)) {
    //                             $counters['late_days']++;
    //                             $counters['total_late_minutes'] += $clockInTime->diffInMinutes($expectedStartTime);
    //                         }
    //                     }
    //                 } else {
    //                     $counters['absent_days']++;
    //                 }
    //             }
    
    //             $currentDay->addDay();
    //         }
    
    //         // Calculate metrics
    //         $metrics = [
    //             'present_rate' => $counters['total_working_days'] > 0 
    //                 ? round(($counters['present_days'] / $counters['total_working_days']) * 100, 2) . '%'
    //                 : '0%',
                
    //             'absent_rate' => $counters['total_working_days'] > 0 
    //                 ? round(($counters['absent_days'] / $counters['total_working_days']) * 100, 2) . '%'
    //                 : '0%',
                
    //             'late_rate' => $counters['present_days'] > 0 
    //                 ? round(($counters['late_days'] / $counters['present_days']) * 100, 2) . '%'
    //                 : '0%',
                
    //             'average_clock_in' => $counters['late_days'] > 0
    //                 ? floor($counters['total_late_minutes'] / $counters['late_days'] / 60) . 'h ' . 
    //                   floor($counters['total_late_minutes'] / $counters['late_days'] % 60) . 'm late'
    //                 : 'On time'
    //         ];
    
    //         return $this->successResponse($metrics);
    
    //     } catch (\Throwable $th) {
    //         return $this->errorResponse($th->getMessage(), 500);
    //     }
    // }

}
