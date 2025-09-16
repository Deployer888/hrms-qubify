<?php

namespace App\Http\Controllers\Api\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\User;
use App\Models\Branch;
use App\Models\Designation;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\Document;
use App\Models\Holiday;
use App\Models\EmployeeDocument;
use App\Models\DucumentUpload;
use App\Models\Utility;
use App\Models\AadhaarDetail;
use App\Models\AttendanceEmployee;
use App\Helpers\Helper;
use Carbon\Carbon;
use App\Mail\EmployeePinResetMail;
use App\Models\Plan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Mail\UserCreate;

class EmployeeController extends BaseController
{
    private function countAbsent($total, $present, $holidays, $weekend)
    {
        $nonWorkingDays = $holidays + $weekend;
        $workingDays = $total - $nonWorkingDays;
        $absent = $workingDays - $present;
        return $absent < 0 ? 0 : $absent;
    }

    /**
     * @OA\Delete(
     *     path="/api/employee/destroy/{id}",
     *     summary="Delete an employee and their associated data",
     *     description="This endpoint deletes an employee record, their associated user record, and any related documents.",
     *     tags={"Employee Sidebar"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the employee to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Employee and associated data successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Employee not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error.")
     *         )
     *     )
     * )
     */

    public function destroy($id)
    {
        try {
            // Check if the user has the necessary permission
            $auth = Auth::user();
            if ($auth->getAllPermissions()->pluck('name')->contains('Delete Employee')) {
                // Retrieve the employee and related user
                $employee = Employee::find($id);  // Will automatically return 404 if not found
                $user = User::find($employee->user_id??0);  // Find the related user
                if (!$employee  && !$user) {
                    return $this->errorResponse('Employee not found.',404);
                }

                // Retrieve the employee's documents
                $emp_documents = EmployeeDocument::where('employee_id', $employee->employee_id)->get();

                // Delete associated documents and files
                $dir = storage_path('uploads/document/');
                foreach ($emp_documents as $emp_document) {
                    // Delete document record
                    $emp_document->delete();

                    // Check if the document file exists before deleting
                    if (!empty($emp_document->document_value) && file_exists($dir . $emp_document->document_value)) {
                        unlink($dir . $emp_document->document_value);
                    }
                }

                // Delete the employee and user records
                $employee->delete();
                $user->delete();

                // Success response
                return $this->successResponse('','Employee and associated data successfully deleted.');

            } else {
                // Permission denied
                return $this->errorResponse('Permission denied.', 403);
            }

        } catch (\Throwable $th) {
            // Catch any other unexpected errors
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/employee",
     *     summary="This api use for to get employee but same on website Empployee sidebar tag.",
     *     description="This endpoint retrieves employee data based on user type and request parameters, including filtering by probation status, active status, and pagination.",
     *     tags={"Employee Sidebar"},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="probation",
     *         in="query",
     *         required=false,
     *         description="Filter employees based on probation status (0 for not on probation, 1 for probation)",
     *         @OA\Schema(
     *             type="integer",
     *             enum={0, 1},
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         required=false,
     *         description="Filter employees based on active status (0 for inactive, 1 for active)",
     *         @OA\Schema(
     *             type="integer",
     *             enum={0, 1},
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         required=false,
     *         description="Number of employees per page. Defaults to 10 if not provided.",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page position ",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of employees",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="employee",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="empcode", type="string"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="dob", type="string", format="date"),
     *                     @OA\Property(property="gender", type="string"),
     *                     @OA\Property(property="phone", type="string"),
     *                     @OA\Property(property="address", type="string"),
     *                     @OA\Property(property="email", type="string"),
     *                     @OA\Property(property="employee_id", type="string"),
     *                     @OA\Property(property="branch_id", type="integer"),
     *                     @OA\Property(property="department_id", type="integer"),
     *                     @OA\Property(property="designation_id", type="integer"),
     *                     @OA\Property(property="company_doj", type="string", format="date"),
     *                     @OA\Property(property="date_of_exit", type="string", format="date", nullable=true),
     *                     @OA\Property(property="documents", type="string", nullable=true),
     *                     @OA\Property(property="account_holder_name", type="string"),
     *                     @OA\Property(property="account_number", type="string"),
     *                     @OA\Property(property="bank_name", type="string"),
     *                     @OA\Property(property="bank_identifier_code", type="string"),
     *                     @OA\Property(property="branch_location", type="string"),
     *                     @OA\Property(property="tax_payer_id", type="string"),
     *                     @OA\Property(property="salary_type", type="integer", nullable=true),
     *                     @OA\Property(property="salary", type="integer"),
     *                     @OA\Property(property="shift_start", type="string", format="time"),
     *                     @OA\Property(property="is_active", type="integer"),
     *                     @OA\Property(property="is_probation", type="integer"),
     *                     @OA\Property(property="created_by", type="integer"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="paid_leave_balance", type="integer"),
     *                     @OA\Property(property="isBirthday", type="integer"),
     *                     @OA\Property(property="is_team_leader", type="integer"),
     *                     @OA\Property(property="team_leader_id", type="integer"),
     *                     @OA\Property(property="daily_status", type="string", nullable=true),
     *                     @OA\Property(property="branch", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     ),
     *                     @OA\Property(property="department", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     ),
     *                     @OA\Property(property="designation", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid probation or active value",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid probation/active value. It must be 0 or 1.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */

    public function employee(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Employee::query();

            if ($user->type == 'employee') {
                // $query->where('user_id', '=',$user->id);
                $query->where('created_by', $user->creatorId());
                if (isset($request->probation) ) {
                    if (in_array($request->probation, [0, 1])) {
                        $query->where('is_probation', $request->probation)
                              ->with('branch', 'department', 'designation');
                    } else {
                        return $this->errorResponse('Invalid probation value. It must be 0 or 1.', 200);
                    }
                }
                if (isset($request->active) ) {
                    if (in_array($request->active, [0, 1])) {
                        $query->where('is_active', $request->active)
                              ->with('branch', 'department', 'designation');
                    } else {
                        return $this->errorResponse('Invalid active value. It must be 0 or 1.', 200);
                    }
                }
            } else {
                $query->where('created_by', $user->creatorId());
                if (isset($request->probation) ) {
                    if (in_array($request->probation, [0, 1])) {
                        $query->where('is_probation', $request->probation)
                              ->with('branch', 'department', 'designation');
                    } else {
                        return $this->errorResponse('Invalid probation value. It must be 0 or 1.', 200);
                    }
                }
                if (isset($request->active) ) {
                    if (in_array($request->active, [0, 1])) {
                        $query->where('is_active', $request->active)
                              ->with('branch', 'department', 'designation');
                    } else {
                        return $this->errorResponse('Invalid active value. It must be 0 or 1.', 200);
                    }
                }
            }

            // Get the employees data
            if ($request->paginate) {

                if($request->paginate > 0)
                {
                    $employees = $query->paginate($request->paginate);
                }
                else {
                    $employees = $query->paginate(10);
                }
            }
            else
            {
                $employees = $query->get();
            }

            $data = [
                'employee' => $employees->toarray()
            ];
            return $this->successResponse($data);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/employee/{id}",
     *     summary="Show Employee Detail same as website to check employee detail",
     *     description="Fetches the details of an employee, including personal, company, bank account, document, and leave information.",
     *     operationId="showEmployee",
     *     tags={"Employee Sidebar"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee ID to retrieve details",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee details fetched successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employee details fetched successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", description="Employee Not Found"),
     *     @OA\Response(response="500", description="Internal Server Error")
     * )
     */
    public function show($id)
    {
        try {
            $auth = Auth::user();
            
            $employeeData = Employee::with('branch', 'department', 'designation')->find($id);
            if (!$employeeData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }
    
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $currentDate = Carbon::now();
            $teamLeaderDetails = $employeeData->getTeamLeaderNameAndId();
           
            $aadhaar_base64_img = User::where('id', $employeeData->user_id)->value('base64')
                ?? AadhaarDetail::where('employee_id', $employeeData->id)->value('photo_encoded');
           
            $attendanceRecords = AttendanceEmployee::where('employee_id', $id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->get();
    
            $presentDates = $attendanceRecords->unique('date');
           
            $holidays = Holiday::whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->get();
    
            $start = now()->startOfMonth();
            $end = now();
            $dates = collect();
            $weekends = collect();
    
            while ($start <= $end) {
                $date = $start->format('Y-m-d');
                $dates->push($date);
    
                if ($start->isWeekend()) {
                    $weekends->push($date);
                }
    
                $start->addDay();
            }
    
            $todayDays = $dates->count();
            $workingDays = $dates->count() - $weekends->count() - $holidays->count();
            $presentDays = $presentDates->count();
            $absentDays = max(0, $workingDays - $presentDays);
           
            $attendanceRate = $workingDays > 0
                ? round(($presentDays / $workingDays) * 100, 2)
                : 0;
    
            $checkInTimes = $attendanceRecords->pluck('clock_in')->filter();
           
            $averageCheckInMinutes = $checkInTimes->isEmpty()
                ? null
                : $checkInTimes->avg(function ($time) {
                    $time = Carbon::parse($time);
                    return ($time->hour * 60) + $time->minute;
                });
    
            $averageCheckInFormatted = $averageCheckInMinutes
                ? floor($averageCheckInMinutes / 60) . ':' . str_pad($averageCheckInMinutes % 60, 2, '0', STR_PAD_LEFT)
                : 'N/A';
    
            $joinDate = Carbon::parse($employeeData->company_doj);
            $workExperience = $joinDate->diff($currentDate);
            $workExperienceFormatted = $workExperience->y;
    
            $atte = [
                'total_days' => $todayDays,
                'total_working_days' => $workingDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'holidays' => $holidays->count(),
                'weekends' => $weekends->count(),
                'attendance_rate' => $attendanceRate . '%',
                'average_check_in' => $averageCheckInFormatted,
            ];
    
            $employee = [
                'employee_id' => $employeeData->id ?? '',
                'name' => $employeeData->name ?? '',
                'gender' => $employeeData->gender ?? '',
                'email' => $employeeData->email ?? '',
                'personal_email' => $employeeData->personal_email ?? '',
                'dob' => $employeeData->dob ?? '',
                'phone' => $employeeData->phone ?? '',
                'address' => $employeeData->address ?? '',
                'salary' => $employeeData->salary ?? '',
                'is_active' => $employeeData->is_active ?? '',
                'base64' => $aadhaar_base64_img ?? '',
                'team_leader' => $teamLeaderDetails->name ?? '',
                'work_experience' => $workExperienceFormatted,
            ];
    
            $bankAccountDetail = [
                'account_holder_name' => $employeeData->account_holder_name ?? '',
                'account_number' => $employeeData->account_number ?? '',
                'bank_name' => $employeeData->bank_name ?? '',
                'bank_ifsc_code' => $employeeData->bank_identifier_code ?? '',
                'branch_location' => $employeeData->branch_location ?? '',
                'pan_code' => $employeeData->tax_payer_id ?? '',
            ];
    
            $companyDetail = [
                'branch' => $employeeData->branch->name ?? '',
                'department' => $employeeData->department->name ?? '',
                'designation' => $employeeData->designation->name ?? '',
                'doj' => $employeeData->company_doj ?? '',
                'shift_start' => $employeeData->shift_start ?? '',
            ];
    
            // Fixed: Remove the undefined 'document' relationship
            $documentDetails = [];
            $employeeDocData = EmployeeDocument::where('employee_id', $id)->get();
            
            foreach ($employeeDocData as $key => $doc) {
                // Use document_value directly or create a proper key
                $documentKey = $doc->document_type ?? 'document_' . ($key + 1);
                $documentDetails[$documentKey] = $doc->document_value ?? '';
            }
    
            // Get user data safely
            $user = User::find($employeeData->user_id);
            if ($user) {
                $user = $user->makeHidden('password');
                $employee['employee_code'] = $user->employeeIdFormat($employeeData->employee_id);
            } else {
                $employee['employee_code'] = 'N/A';
            }
    
            $leaveArray = [];
            $leaveType = LeaveType::with(['leaves' => function($query) use ($id, $currentYear) {
                $query->where('employee_id', $id)
                    ->whereYear('applied_on', $currentYear)
                    ->where('status', 'Approve');
            }])->get();
    
            foreach ($leaveType as $value) {
                $sum = $value->leaves->sum('total_leave_days');
                $leaveArray[] = [
                    'title' => $value->title,
                    'total' => $value->days,
                    'available' => $value->days - $sum,
                    'availed' => $sum,
                ];
            }
    
            $data = [
                'personal_detail' => $employee,
                'company_detail' => $companyDetail,
                'bank_account_detail' => $bankAccountDetail,
                'document_detail' => $documentDetails, // Fixed typo: was 'document_detai'
                'leaves_detail' => $leaveArray, // Added missing leaves_detail
                'attendance' => $atte,
            ];
    
            return response()->json([
                'success' => true,
                'message' => 'Employee details fetched successfully',
                'data' => $data
            ], 200);
    
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/employee/{id}/active-inactive/{action}",
     *     summary="Activate or Deactivate a Emplyee",
     *     description="Activate or deactivate a employee based on the `action` parameter.",
     *     tags={"Employee Sidebar"},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="action",
     *         in="path",
     *         required=true,
     *         description="Action to be taken on the employee. Use `1` to activate and `0` to deactivate.",
     *         @OA\Schema(type="integer", enum={0, 1}, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Employee id.",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User status updated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User status updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="employee",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=123),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="is_active", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid action or request.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid action."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="error", type="string", example="Invalid action")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied. HR role required.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="error", type="string", example="Permission denied")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="error", type="string", example="User not found")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="error", type="string", example="Internal server error")
     *             )
     *         )
     *     )
     * )
     */

    public function activeInactive($id,$action)
    {
        try {
            $auth = Auth::user();
            if (!in_array($action, [0, 1])) {
                return $this->errorResponse('Invalid action', 400);
            }
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Edit Employee') && $auth->type != 'employee')
            {
                $employee  = Employee::where('id',$id)->first();
                $user = User::find($employee->user_id);
                if (!$employee && !$user)
                {
                    return $this->errorResponse('Employee not found', 404);
                }
                $user->is_active = $action;
                $user->update();
                $employee->is_active = $action;
                $employee->update();
                $data = [
                    'employee_id'=> $employee->id,
                    'user_id'=> $user->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'is_active' => intval($user->is_active),
                ];

                $data = [
                    'employee' => $data,
                ];
                $message = intval($user->is_active) ? 'Employee successfully activated.' : 'Employee status deactivated.';
                return $this->successResponse($data,$message);
            }
            return $this->errorResponse('Permission denied.', 200);
        }  catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/employee",
     *     summary="Create a new employee",
     *     description="Creates a new employee and user with all relevant data.",
     *     operationId="storeEmployee",
     *     tags={"Employee Sidebar"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "dob", "personal_email", "gender", "phone", "email", "password", "address", "branch_id", "department_id", "designation_id", "company_doj", "shift_start", "salary", "account_holder_name", "account_number", "bank_name", "bank_ifsc_code", "branch_location", "pan_card_no", "is_probation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="personal_email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="gender", type="string", example="male"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="email", type="string", example="john.doe@company.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="address", type="string", example="123 Main St, City, Country"),
     *             @OA\Property(property="branch_id", type="integer", example=1),
     *             @OA\Property(property="department_id", type="integer", example=1),
     *             @OA\Property(property="designation_id", type="integer", example=1),
     *             @OA\Property(property="company_doj", type="string", format="date", example="2025-02-20"),
     *             @OA\Property(property="shift_start", type="string", format="time", example="09:00:00"),
     *             @OA\Property(property="salary", type="integer", example=50000),
     *             @OA\Property(property="document", type="array", @OA\Items(type="string", format="binary")),
     *             @OA\Property(property="account_holder_name", type="string", example="John Doe"),
     *             @OA\Property(property="account_number", type="string", example="123456789012"),
     *             @OA\Property(property="bank_name", type="string", example="Bank of XYZ"),
     *             @OA\Property(property="bank_ifsc_code", type="string", example="IFSC1234"),
     *             @OA\Property(property="branch_location", type="string", example="Main Branch, City"),
     *             @OA\Property(property="pan_card_no", type="string", example="ABCDE1234F"),
     *             @OA\Property(property="is_team_leader", type="boolean", example=false),
     *             @OA\Property(property="team_leader_id", type="integer", example=1),
     *             @OA\Property(property="is_probation", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employee successfully created."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="employee", type="object",
     *                     @OA\Property(property="user_id", type="integer", example=65),
     *                     @OA\Property(property="name", type="string", example="Tesing"),
     *                     @OA\Property(property="dob", type="string", format="date", example="2003-02-20"),
     *                     @OA\Property(property="gender", type="string", example="Male"),
     *                     @OA\Property(property="phone", type="string", example="2323232323"),
     *                     @OA\Property(property="address", type="string", example="Testing address"),
     *                     @OA\Property(property="email", type="string", example="testingemail@gmail.coom"),
     *                     @OA\Property(property="salary", type="string", example="30000"),
     *                     @OA\Property(property="password", type="string", example="12345678"),
     *                     @OA\Property(property="branch_id", type="string", example="1"),
     *                     @OA\Property(property="department_id", type="string", example="2"),
     *                     @OA\Property(property="designation_id", type="string", example="2"),
     *                     @OA\Property(property="company_doj", type="string", format="date", example="2025-02-20"),
     *                     @OA\Property(property="documents", type="string", example="1"),
     *                     @OA\Property(property="account_holder_name", type="string", example="John"),
     *                     @OA\Property(property="account_number", type="string", example="1234567890"),
     *                     @OA\Property(property="bank_name", type="string", example="BANK NAME"),
     *                     @OA\Property(property="bank_identifier_code", type="string", example="null"),
     *                     @OA\Property(property="branch_location", type="string", example="Gurugram"),
     *                     @OA\Property(property="tax_payer_id", type="string", example="null"),
     *                     @OA\Property(property="shift_start", type="string", example="09:00:00"),
     *                     @OA\Property(property="is_team_leader", type="string", example="0"),
     *                     @OA\Property(property="team_leader_id", type="string", example="null"),
     *                     @OA\Property(property="is_probation", type="integer", example=1),
     *                     @OA\Property(property="created_by", type="string", example="2"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-20T10:33:49.000000Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-20T10:33:49.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=40)
     *                 ),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="name", type="string", example="Tesing"),
     *                     @OA\Property(property="email", type="string", example="testingemail@gmail.coom"),
     *                     @OA\Property(property="personal_email", type="string", example="testing@gmai.com"),
     *                     @OA\Property(property="type", type="string", example="employee"),
     *                     @OA\Property(property="lang", type="string", example="en"),
     *                     @OA\Property(property="created_by", type="string", example="2"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-20T10:33:49.000000Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-20T10:33:49.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=65)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        try {
            $auth = Auth::user();
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Create Employee'))
            {
                // Validate the request
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'dob' => 'required|date|date_format:Y-m-d',
                        'personal_email' => 'required|unique:users',
                        'gender' => 'required',
                        'phone' => 'required',
                        'email' => 'required|unique:users',
                        'password' => 'required',
                        'address' => 'required',
                        'branch_id' => 'required|exists:branches,id',
                        'department_id' => 'required|exists:departments,id',
                        'designation_id' => 'required|exists:designations,id',
                        'company_doj' => 'required|date|date_format:Y-m-d',
                        'shift_start' => 'required|date_format:H:i:s',
                        'salary' => 'required|integer',
                        'document.*' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,zip|max:20480',
                        'account_holder_name' => 'required|string|max:255',
                        'account_number' => 'required|numeric',
                        'bank_name' => 'required|string|max:255',
                        'bank_ifsc_code' => 'required|string|max:20',
                        'branch_location' => 'required|string|max:255',
                        'pan_card_no' => 'required|string|size:10',
                        'is_team_leader' => 'nullable|boolean',
                        'team_leader_id' => 'nullable|exists:users,id|required_if:is_team_leader,0',
                        'is_probation' => 'required|boolean',
                    ],
                    [
                        'branch_id.exists' => 'The selected branch does is invalid.',
                        'department_id.exists' => 'The selected department is invalid.',
                        'designation_id.exists' => 'The selected designation is not valid.',
                        'shift_start.date_format' => 'The shift start time must be in the format HH:MM:SS.',
                        'is_team_leader.boolean' => 'Team leader status must be true or false.',
                        'team_leader_id.exists' => 'The selected team leader ID is invalid.',
                        'is_probation.required' => 'Probation status is required.',
                        'is_probation.boolean' => 'Probation status must be true or false.',
                        'team_leader_id.required_if' => 'Select team leader.',
                        'team_leader_id.exists' => 'The selected team leader is invalid.',
                    ]
                );
                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 400,$validator->errors()->toarray());
                }

                // Check if user has reached the maximum employee limit
                $objUser = User::find(\Auth::user()->creatorId());
                $totalEmployee = $objUser->countEmployees();
                $plan = Plan::find($objUser->plan);

                if ($totalEmployee >= $plan->max_employees && $plan->max_employees != -1) {
                    return $this->errorResponse('Your employee limit is over, Please upgrade plan.', 403);
                }

                // Create the new user
                $user = User::create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'personal_email' => $request['personal_email'],
                    'password' => Hash::make($request['password']),
                    'type' => 'employee',
                    'lang' => 'en',
                    'created_by' => \Auth::user()->creatorId(),
                ]);

                // Assign role to user
                $user->assignRole('employee');

                // Handle probation period
                if ($request->has('company_doj')) {
                    $dateOfJoining = Carbon::parse($request->company_doj);
                    $currentDate = Carbon::now();

                    $isProbation = $dateOfJoining->greaterThanOrEqualTo($currentDate->subMonths(3)) ? 1 : 0;
                    $request['is_probation'] = $isProbation;
                }

                // Create the employee record
                $employee = Employee::create([
                    'user_id' => $user->id,
                    'name' => $request['name'],
                    'dob' => $request['dob'],
                    'gender' => $request['gender'],
                    'phone' => $request['phone'],
                    'address' => $request['address'],
                    'email' => $request['email'],
                    'salary' => $request['salary'],
                    'password' => $request['password'],
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'designation_id' => $request['designation_id'],
                    'company_doj' => $request['company_doj'],
                    'documents' => !empty($request->document) ? implode(',', array_keys($request->document)) : null,
                    'account_holder_name' => $request['account_holder_name'],
                    'account_number' => $request['account_number'],
                    'bank_name' => $request['bank_name'],
                    'bank_identifier_code' => $request['bank_ifsc_code'],
                    'branch_location' => $request['branch_location'],
                    'tax_payer_id' => $request['pan_card_no'],
                    'shift_start' => $request['shift_start'],
                    'is_team_leader' => $request['is_team_leader'] ?? 'default_value',
                    'team_leader_id' => $request['team_leader'],
                    'is_probation' => $request['is_probation'],
                    'created_by' => \Auth::user()->creatorId(),
                ]);

                // Handle document upload if exists
                if ($request->hasFile('document')) {
                    foreach ($request->document as $key => $document) {
                        $filenameWithExt = $document->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $document->getClientOriginalExtension();
                        $docName = Document::where('id', $key)->pluck('name')->first();
                        $fileNameToStore = $request->name . '_' . $docName . '.' . $extension;
                        $dir = storage_path('uploads/document/');
                        $imagePath = $dir . $filenameWithExt;

                        if (File::exists($imagePath)) {
                            File::delete($imagePath);
                        }

                        if (!file_exists($dir)) {
                            mkdir($dir, 0777, true);
                        }

                        $document->storeAs('uploads/document/', $fileNameToStore);

                        // Save the document in the database
                        EmployeeDocument::create([
                            'employee_id' => $employee->id,
                            'document_id' => $key,
                            'document_value' => $fileNameToStore,
                            'created_by' => \Auth::user()->creatorId(),
                        ]);
                    }
                }

                // Send an email to the user if configured
                $settings = Utility::settings();
                if ($settings['employee_create'] == 1) {
                    try {
                        Mail::to($user->email)->send(new UserCreate($user));
                    } catch (\Exception $e) {
                        $smtpError = __('E-Mail has been not sent due to SMTP configuration');
                    }
                }

                // Return success response
                $data = [
                    'employee' => $employee,
                    'user' => $user,
                ];

                return $this->successResponse($data,'Employee successfully created.');

            } else {
                return $this->errorResponse('Permission denied.',403);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/update/employee/{id}",
     *     summary="Update an existing employee",
     *     description="Updates an existing employee and user with all relevant data.",
     *     operationId="updateEmployee",
     *     tags={"Employee Sidebar"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the employee to update"
     *     ),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "dob", "personal_email", "gender", "phone", "email", "address", "branch_id", "department_id", "designation_id", "company_doj", "shift_start", "salary", "account_holder_name", "account_number", "bank_name", "bank_ifsc_code", "branch_location", "pan_card_no", "is_probation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="personal_email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="gender", type="string", example="male"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="email", type="string", example="john.doe@company.com"),
     *             @OA\Property(property="address", type="string", example="123 Main St, City, Country"),
     *             @OA\Property(property="branch_id", type="integer", example=1),
     *             @OA\Property(property="department_id", type="integer", example=1),
     *             @OA\Property(property="designation_id", type="integer", example=1),
     *             @OA\Property(property="company_doj", type="string", format="date", example="2025-02-20"),
     *             @OA\Property(property="shift_start", type="string", format="time", example="09:00:00"),
     *             @OA\Property(property="salary", type="integer", example=50000),
     *             @OA\Property(property="document", type="array", @OA\Items(type="string", format="binary")),
     *             @OA\Property(property="account_holder_name", type="string", example="John Doe"),
     *             @OA\Property(property="account_number", type="string", example="123456789012"),
     *             @OA\Property(property="bank_name", type="string", example="Bank of XYZ"),
     *             @OA\Property(property="bank_ifsc_code", type="string", example="IFSC1234"),
     *             @OA\Property(property="branch_location", type="string", example="Main Branch, City"),
     *             @OA\Property(property="pan_card_no", type="string", example="ABCDE1234F"),
     *             @OA\Property(property="is_team_leader", type="boolean", example=false),
     *             @OA\Property(property="team_leader_id", type="integer", example=1),
     *             @OA\Property(property="is_probation", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employee successfully updated."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="employee", type="object",
     *                     @OA\Property(property="user_id", type="integer", example=65),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *                     @OA\Property(property="gender", type="string", example="Male"),
     *                     @OA\Property(property="phone", type="string", example="1234567890"),
     *                     @OA\Property(property="address", type="string", example="123 Main St, City, Country"),
     *                     @OA\Property(property="email", type="string", example="john.doe@company.com"),
     *                     @OA\Property(property="salary", type="integer", example=50000),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="department_id", type="integer", example=1),
     *                     @OA\Property(property="designation_id", type="integer", example=1),
     *                     @OA\Property(property="company_doj", type="string", format="date", example="2025-02-20"),
     *                     @OA\Property(property="documents", type="string", example="1"),
     *                     @OA\Property(property="account_holder_name", type="string", example="John Doe"),
     *                     @OA\Property(property="account_number", type="string", example="123456789012"),
     *                     @OA\Property(property="bank_name", type="string", example="Bank of XYZ"),
     *                     @OA\Property(property="bank_identifier_code", type="string", example="IFSC1234"),
     *                     @OA\Property(property="branch_location", type="string", example="Main Branch, City"),
     *                     @OA\Property(property="tax_payer_id", type="string", example="ABCDE1234F"),
     *                     @OA\Property(property="shift_start", type="string", example="09:00:00"),
     *                     @OA\Property(property="is_team_leader", type="boolean", example=false),
     *                     @OA\Property(property="team_leader_id", type="integer", example=1),
     *                     @OA\Property(property="is_probation", type="boolean", example=true),
     *                     @OA\Property(property="created_by", type="integer", example=2),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-20T10:33:49.000000Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-20T10:33:49.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=40)
     *                 ),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@company.com"),
     *                     @OA\Property(property="personal_email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="type", type="string", example="employee"),
     *                     @OA\Property(property="lang", type="string", example="en"),
     *                     @OA\Property(property="created_by", type="integer", example=2),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-20T10:33:49.000000Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-20T10:33:49.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=65)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        try {
            $auth = Auth::user();
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Edit Employee'))
            {
                // Find the existing employee
                $employee = Employee::find($id);
                $user = User::find($employee->user_id);

                if (!$employee && !$user) {
                    return $this->errorResponse('Employee not found.',404);
                }

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'dob' => 'required|date|date_format:Y-m-d',
                        'personal_email' => 'required|unique:users,personal_email,' . $employee->user_id,
                        'gender' => 'required',
                        'phone' => 'required',
                        'email' => 'required|unique:users,email,' . $employee->user_id,
                        'address' => 'required',
                        'branch_id' => 'required|exists:branches,id',
                        'department_id' => 'required|exists:departments,id',
                        'designation_id' => 'required|exists:designations,id',
                        'company_doj' => 'required|date|date_format:Y-m-d',
                        'shift_start' => 'required|date_format:H:i:s',
                        'salary' => 'required|integer',
                        'document.*' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,zip|max:20480',
                        'account_holder_name' => 'required|string|max:255',
                        'account_number' => 'required|numeric',
                        'bank_name' => 'required|string|max:255',
                        'bank_ifsc_code' => 'required|string|max:20',
                        'branch_location' => 'required|string|max:255',
                        'pan_card_no' => 'required|string|size:10',
                        'is_team_leader' => 'nullable|boolean',
                        'team_leader_id' => 'nullable|exists:users,id|required_if:is_team_leader,0',
                        'is_probation' => 'required|boolean',
                    ],
                    [
                        'branch_id.exists' => 'The selected branch does is invalid.',
                        'department_id.exists' => 'The selected department is invalid.',
                        'designation_id.exists' => 'The selected designation is not valid.',
                        'shift_start.date_format' => 'The shift start time must be in the format HH:MM:SS.',
                        'is_team_leader.boolean' => 'Team leader status must be true or false.',
                        'team_leader_id.exists' => 'The selected team leader ID is invalid.',
                        'is_probation.required' => 'Probation status is required.',
                        'is_probation.boolean' => 'Probation status must be true or false.',
                        'team_leader_id.required_if' => 'Select team leader.',
                        'team_leader_id.exists' => 'The selected team leader is invalid.',
                    ]
                );
                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 400, $validator->errors()->toArray());
                }



                // Update user details
                $user->update([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'personal_email' => $request['personal_email'],
                    'type' => 'employee',
                    'lang' => 'en',
                ]);

                // Update employee record
                $employee->update([
                    'name' => $request['name'],
                    'dob' => $request['dob'],
                    'gender' => $request['gender'],
                    'phone' => $request['phone'],
                    'address' => $request['address'],
                    'salary' => $request['salary'],
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'designation_id' => $request['designation_id'],
                    'company_doj' => $request['company_doj'],
                    'account_holder_name' => $request['account_holder_name'],
                    'account_number' => $request['account_number'],
                    'bank_name' => $request['bank_name'],
                    'bank_identifier_code' => $request['bank_ifsc_code'],
                    'branch_location' => $request['branch_location'],
                    'tax_payer_id' => $request['pan_card_no'],
                    'shift_start' => $request['shift_start'],
                    'is_team_leader' => $request['is_team_leader'] ?? 0,
                    'team_leader_id' => $request['team_leader_id'],
                    'is_probation' => $request['is_probation'],
                    'updated_by' => \Auth::user()->creatorId(),
                ]);

                // Handle document upload if exists
                if ($request->hasFile('document')) {
                    foreach ($request->document as $key => $document) {
                        $filenameWithExt = $document->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $document->getClientOriginalExtension();
                        $docName = Document::where('id', $key)->pluck('name')->first();
                        $fileNameToStore = $request->name . '_' . $docName . '.' . $extension;
                        $dir = storage_path('uploads/document/');
                        $imagePath = $dir . $filenameWithExt;

                        if (File::exists($imagePath)) {
                            File::delete($imagePath);
                        }

                        if (!file_exists($dir)) {
                            mkdir($dir, 0777, true);
                        }

                        $document->storeAs('uploads/document/', $fileNameToStore);

                        // Save the document in the database
                        EmployeeDocument::updateOrCreate(
                            ['employee_id' => $employee->id, 'document_id' => $key],
                            ['document_value' => $fileNameToStore, 'updated_by' => \Auth::user()->creatorId()]
                        );
                    }
                }

                // Return success response
                $data = [
                    'employee' => $employee,
                    'user' => $user,
                ];

                return $this->successResponse($data, 'Employee successfully updated.');

            } else {
                return $this->errorResponse('Permission denied.', 403);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/employee-department-with-branch/{id}",
     *     summary="Get branch with departments and employees (user in employee create/update)",
     *     description="Retrieves a branch along with its associated departments and employees.",
     *     operationId="employeeAndDepartment",
     *     tags={"Employee Sidebar"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Branch ID "
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Branch, departments, and employees retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Development"),
     *                 @OA\Property(property="created_by", type="integer", example=2),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-23T10:47:50.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-23T10:47:50.000000Z"),
     *                 @OA\Property(property="departments", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Software Development"),
     *                     @OA\Property(property="created_by", type="integer", example=2),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-23T10:48:50.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-23T10:48:50.000000Z")
     *                 )),
     *                 @OA\Property(property="employees", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="empcode", type="string", example="0002"),
     *                     @OA\Property(property="name", type="string", example="Abhishek Kumar"),
     *                     @OA\Property(property="dob", type="string", format="date", example="1987-08-18"),
     *                     @OA\Property(property="gender", type="string", example="Male"),
     *                     @OA\Property(property="phone", type="string", example="7696080786"),
     *                     @OA\Property(property="address", type="string", example="Krishna Enclave, Dhakoli, Zirakpur"),
     *                     @OA\Property(property="email", type="string", example="abhishek@qubifytech.com"),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="department_id", type="integer", example=1),
     *                     @OA\Property(property="salary", type="integer", example=60000),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-24T14:59:28.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-10T05:30:09.000000Z")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Branch not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Branch not found"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function employeeAndDepartment($id)
    {
        try {
            $branch = Branch::with('departments','employees')->where('id',$id)->first();
            if (!$branch) {
                return $this->errorResponse('Branch not found.',404);
            }
            return $this->successResponse($branch);
            //code...
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/employee-with-department/{id}",
     *     summary="Get employees by department",
     *     description="Retrieves a department along with its associated employees.",
     *     operationId="employeeWithDepartment",
     *     tags={"Employee Sidebar"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Department ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Department and employees retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="branch_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Software Development"),
     *                 @OA\Property(property="created_by", type="integer", example=2),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-23T10:48:50.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-23T10:48:50.000000Z"),
     *                 @OA\Property(property="employees", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="empcode", type="string", example="0002"),
     *                     @OA\Property(property="name", type="string", example="Abhishek Kumar"),
     *                     @OA\Property(property="dob", type="string", format="date", example="1987-08-18"),
     *                     @OA\Property(property="gender", type="string", example="Male"),
     *                     @OA\Property(property="phone", type="string", example="7696080786"),
     *                     @OA\Property(property="address", type="string", example="Krishna Enclave, Dhakoli, Zirakpur"),
     *                     @OA\Property(property="email", type="string", example="abhishek@qubifytech.com"),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="department_id", type="integer", example=1),
     *                     @OA\Property(property="salary", type="integer", example=60000),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-24T14:59:28.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-10T05:30:09.000000Z")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Department not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Department not found"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function employeeWithDepartment($id)
    {
        try {
            $department = Department::with('employees')->where('id',$id)->first();
            if (!$department) {
                return $this->errorResponse('Branch not found.',404);
            }
            return $this->successResponse($department);
            //code...
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }

    }
    /**
     * @OA\Get(
     *     path="/api/employee/last-login",
     *     summary="Get last login details of employees",
     *     description="Retrieves the last login details of employees created by the authenticated user.",
     *     operationId="lastLogin",
     *     tags={"Staff Sidebar"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         required=false,
     *         description="Number of employees per page. Defaults to 10 if not provided.",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page position ",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Last login details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=4),
     *                 @OA\Property(property="name", type="string", example="Happy Singh"),
     *                 @OA\Property(property="type", type="string", example="employee"),
     *                 @OA\Property(property="email", type="string", example="happy@qubifytech.com"),
     *                 @OA\Property(property="last_login", type="string", format="date-time", example="2025-02-19 20:43:35"),
     *                 @OA\Property(property="employee_id", type="integer", example=1)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No employees found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Employee not found"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function lastLogin(Request $request)
    {
        try {
            $query = User::where('users.created_by', \Auth::user()->creatorId())
                ->select('users.id', 'users.name', 'users.type', 'users.email', 'users.last_login', 'employees.id as employee_id')
                ->join('employees', 'employees.user_id', '=', 'users.id');

            if ($request->has('paginate') && $request->paginate > 0) {
                $users = $query->paginate($request->paginate);
            } else {
                $users = $query->get();
            }

            if ($users->isEmpty()) {
                return $this->successResponse($users,'Employee not found.');
            }

            return $this->successResponse($users);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
    
    public function employeePin(Request $request){
        try {
            $validator = \Validator::make($request->all(), [
                'pin' => [
                    'required',
                    'string',
                    'size:6',
                    'regex:/^[0-9]{6}$/' // Only 6 digits allowed
                ]
            ], [
                'pin.required' => 'PIN is required.',
                'pin.size' => 'PIN must be exactly 6 digits.',
                'pin.regex' => 'PIN must contain only numbers (0-9).'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please login first.'
                ], 401);
            }
    
            $employee = $user->employee; // Assuming relationship exists
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found for this user.'
                ], 404);
            }
    
            $pin = $request->pin;
    
            
            $employee->update([
                'clock_in_pin' => $pin, 
            ]);
    
            \Log::info('Employee PIN updated', [
                'employee_id' => $employee->id,
                'user_id' => $user->id,
                'employee_name' => $employee->name ?? $user->name,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Employee PIN has been successfully updated.',
                'data' => [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name ?? $user->name,
                    'pin_updated_at' => now()->format('Y-m-d H:i:s')
                ]
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error('Employee PIN update failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the PIN. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
    public function resetEmployeePin(Request $request)
    {
        try {
            // Get the authenticated user
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please login first.'
                ], 401);
            }
    
            // Check if user has an employee record
            $employee = $user->employee; // Assuming relationship exists
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found for this user.'
                ], 404);
            }
    
            // Check if user has email
            if (!$user->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'No email address found for this employee. Please contact HR to update your email.'
                ], 400);
            }
    
            $newPin = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            $employee->update([
                'clock_in_pin' => $newPin
            ]);
    
            $emailData = [
                'employee_name' => $employee->name ?? $user->name,
                'employee_id' => $employee->employee_id ?? $employee->id,
                'new_pin' => $newPin,
                'company_name' => env('APP_NAME', 'Qubify')
            ];
    
            try {
                Mail::to($user->email)->send(new EmployeePinResetMail($emailData));
                
                $emailSent = true;
                $emailMessage = 'PIN reset email sent successfully.';
                
            } catch (\Exception $mailException) {
                \Log::error('Failed to send PIN reset email', [
                    'employee_id' => $employee->id,
                    'user_email' => $user->email,
                    'error' => $mailException->getMessage()
                ]);
                // dd($mailException->getMessage());
                $emailSent = false;
                $emailMessage = 'PIN was reset but email could not be sent. Please contact HR.';
            }
    
            // Log the PIN reset activity
            \Log::info('Employee PIN reset', [
                'employee_id' => $employee->id,
                'user_id' => $user->id,
                'employee_name' => $employee->name ?? $user->name,
                'user_email' => $user->email,
                'email_sent' => $emailSent
            ]);
    
            return response()->json([
                'success' => true,
                'message' => $emailSent 
                    ? 'Your clock-in PIN has been reset successfully. A new PIN has been sent to your email address.'
                    : 'Your clock-in PIN has been reset successfully, but we could not send the email. Please contact HR for your new PIN.',
                'data' => [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name ?? $user->name,
                    'email_address' => $user->email,
                    'email_sent' => $emailSent,
                    'reset_at' => now()->format('Y-m-d H:i:s')
                ]
            ], 200);
    
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Employee PIN reset failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resetting your PIN. Please try again or contact HR for assistance.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}

