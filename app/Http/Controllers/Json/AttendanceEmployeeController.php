<?php

namespace App\Http\Controllers\Json;

use App\Http\Controllers\Json\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceEmployee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\IpRestrict;
use App\Models\User;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Utility;
use Illuminate\Support\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;
use App\Events\EmployeeBirthday;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Response;


class AttendanceEmployeeController extends BaseController
{
    //
    public function empAttendance(Request $request)
    {
        
        try {
            if($request->type == 'month')
            {
                $month = $request->date;
                $employee_id = $request->employee_id;
                $dateList = Helper::getDateList($month);
                $dateList = array_reverse($dateList);
                $monthAttendance = [];
                foreach ($dateList as $key => $dateData) {
                    $attendancesData= AttendanceEmployee::with('employee')->whereDate('date', $dateData)->where('employee_id', $employee_id)->orderBy('clock_in','ASC')->get();
                    $data = [];
                    $data['hours']  = Helper::calculateTotalTimeDifference($attendancesData);
                    $data['attendance'] = $attendancesData->toarray();
                    $data['is_weekend'] = Carbon::parse($dateData)->isWeekend();
                    $isLeave = Helper::checkLeave($dateData, $employee_id);
                    $empLeave = Helper::getEmpLeave($dateData, $employee_id);
                    $data['leave_detail'] = $empLeave;
                    $data['is_leave'] = $isLeave;
                    $minHours = '08:00';
                    if($isLeave && $empLeave)
                    {   
                        if($empLeave['leavetype'] == 'half')
                        {
                            $minHours = '04:00';
                        }
                        if($empLeave['leavetype'] == 'short')
                        {
                            $minHours = '06:00';
                        }
                    }
                    $data['min_hours'] = $minHours;
                    $monthAttendance[$dateData] = $data;
                    $data['attendance'] = $monthAttendance;
                    $data['type'] = 'month';
                }
                return $this->successResponse($data, 'Testing');
    
            }
            if ($request->type == 'date') { 
                $employee_id = $request->employee_id;
                $date = $request->date;
            
                if ($date && $employee_id) {
                    $attendances = AttendanceEmployee::with('employee')->whereDate('date', $date)
                        ->where('employee_id', $employee_id)->orderBy('clock_in','ASC')
                        ->get();
            
                    $isLeave = Helper::checkLeave($date, $employee_id);
                    $empLeave = Helper::getEmpLeave($date, $employee_id);
                    $isWeekend = Carbon::parse($date)->isWeekend();
                    $minHours = '08:00';
            
                    if ($isLeave && $empLeave) {   
                        if ($empLeave['leavetype'] == 'half') {
                            $minHours = '04:00';
                        } elseif ($empLeave['leavetype'] == 'short') {
                            $minHours = '06:00';
                        }
                    }
            
                    $data = [
                        'employee_id' => $employee_id,
                        'date' => $date,
                        'attendances' => $attendances?$attendances->toarray():[],
                        'is_leave' => $isLeave,
                        'emp_leave' => $empLeave,
                        'is_weekend' => $isWeekend,
                        'min_hours' => $minHours,
                        'type' => 'date',
                        'hours' => Helper::calculateTotalTimeDifference($attendances)
                    ];
                    return $this->successResponse($data, 'Testing');
                }
            }
            
            return $this->errorResponse('Data not found');
        } catch (\Throwable $th) {
            return $this->errorResponse('Somwthing went rong.');
        }

    }

    public function index($user_id,$date): JsonResponse
    {
        
        if(Auth::user())
        {
            if (\Auth::user()->can('Manage Attendance')) 
            {
                $attendance = AttendanceEmployee::where('employee_id', User::find($user_id)->employee->id??0)
                        ->where('date', $date)
                        ->get();

                if($attendance)
                {
                    return $this->successResponse($attendance,'success',);
                }
                else
                {
                    return $this->errorResponse('Attendance not found for this user on this date.');
                }
            } 

        }
        // Return error response if permission is denied
        return $this->errorResponse('Attendance not found for this user on this date.');

    }

    public function getDepartmentsByBranch(Request $request): JsonResponse
    {
        // Get branch_id and department_id from the request
        $branchId = $request->input('branch_id');
        $departmentId = $request->input('department_id');

        // Check if branch_id is provided
        if (!$branchId) {
            return $this->errorResponse('Branch ID is required.', Response::HTTP_BAD_REQUEST);
        }

        try {
            // If both branch_id and department_id are provided, fetch departments and employees for both
            if ($departmentId) {
                $departments = Department::where('branch_id', $branchId)
                                          ->where('id', $departmentId)
                                          ->get();

                $employees = Employee::where('branch_id', $branchId)
                                     ->where('department_id', $departmentId)
                                     ->select('id', 'name')
                                     ->get();
            } else {
                // If only branch_id is provided, fetch departments and employees for that branch
                $departments = Department::where('branch_id', $branchId)->get();

                $employees = Employee::where('branch_id', $branchId)
                                    ->where('is_active', 1) // Ensure only active employees are fetched
                                    ->select('id', 'name')
                                    ->get();
            }

            // Check if departments or employees are found
            if ($departments->isEmpty() && $employees->isEmpty()) {
                return $this->errorResponse('No departments or employees found for the selected criteria.', Response::HTTP_NOT_FOUND);
            }

            // Return success response with both departments and employees
            return $this->successResponse([
                'departments' => $departments,
                'employees' => $employees
            ], 'Departments and employees found successfully.');
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return $this->errorResponse('An error occurred while fetching departments and employees.', Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
    public function getEmployeeByDepartment(Request $request): JsonResponse
    {
        $departmentId = $request->input('department_id');
        try {
            // If both branch_id and department_id are provided, fetch departments and employees for both
            if ($departmentId) {
                $employees = Employee::where('department_id', $departmentId)
                    ->where('is_active', 1)
                    ->select('id', 'name')
                    ->get();
                    // Check if departments or employees are found
                    if ($employees->isEmpty()) {
                        return $this->errorResponse('No departments or employees found for the selected criteria.', Response::HTTP_NOT_FOUND);
                    }
        
                    // Return success response with both departments and employees
                    return $this->successResponse([
                        'employees' => $employees
                    ], 'Departments and employees found successfully.');
            } 

        } catch (\Exception $e) {
            // Handle any unexpected errors
            return $this->errorResponse('An error occurred while fetching departments and employees.', Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }



}
