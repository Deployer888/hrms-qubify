<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\AadhaarDetail;
use App\Models\Utility;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Gate;
use Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreate;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Document;
use App\Models\LeaveType;
use App\Models\EmployeeDocument;
use App\Models\DucumentUpload;
use DB, File;

class EmployeeController extends Controller
{
    
    public function getAllEmployees()
    {
        $allEmployees = AadhaarDetail::select('name', 'employee_id', 'photo_encoded as aadhar_photo', 'photo_encoded_optimized as live_photo')->get();

        return response()->json([
            'data' => $allEmployees
        ]);
    }

    public function getEmployee($userId)
    {
        // try{
            $employee = Employee::where('user_id', $userId)->first()->makeHidden('password');
            $empId = $userId;
            $user = User::find($userId)->makeHidden('password');
            $userRole = $user->roles->first();

            $documents = DucumentUpload::whereIn(
                    'role', [
                        $userRole->id,
                        0,
                    ]
                )->where('created_by', \Auth::user()->creatorId())->get();

            // Get the name directly from the first result
            $branches = Branch::where('id', $employee->branch_id)->value('name');
            $departments = Department::where('id', $employee->department_id)->value('name');
            $designations = Designation::where('id', $employee->designation_id)->value('name');

            $employeesId = $user->employeeIdFormat($employee->employee_id);

            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            $leaves = LeaveType::leftJoin('employees', function($join) use ($empId) {
                    $join->on('employees.id', '=', DB::raw($empId));
                })
                ->leftJoin('leaves', function($join) use ($employee) {
                    $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                        ->where('leaves.employee_id', '=', $employee->id);
                })
                ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                ->select('leave_types.id', 'leave_types.title', DB::raw('
                    CASE
                        WHEN leave_types.title = "Paid Leave" THEN employees.paid_leave_balance
                        ELSE (leave_types.days - COALESCE(SUM(leaves.total_leave_days), 0))
                    END AS days'))
                ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                ->get();

            return response()->json([
                'employee' => $employee,
                'employeesId' => $employeesId,
                'branches' => $branches,
                'departments' => $departments,
                'designations' => $designations,
                'documents' => $documents,
                'leaves' => $leaves,
            ]);
        // }catch (\Exception $e) {
        //     return redirect()->back()->with('error', $e->getMessage());
        // }
    }

    /**
     * @OA\Get(
     *     path="/api/employees",
     *     operationId="getEmployees",
     *     tags={"Employees"},
     *     summary="Fetch all employees",
     *     description="Get a list of all employees accessible by the authenticated user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function getEmployees(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated.'
            ], 401);
        }

        // Check if user has the 'Manage Employee' permission
        if ($user->getAllPermissions()->pluck('name')->contains('Manage Employee')) {
            if ($user->type === 'employee') {
                $employees = Employee::where('user_id', $user->id)->get();
            } else {
                $employees = Employee::where('created_by', $user->creatorId())->get();
            }

            return response()->json([
                'success' => true,
                'data' => $employees
            ], 200);
        }

        return response()->json([
            'success' => false,
            'error' => 'Permission denied.'
        ], 403);
    }

    public function store(Request $request)
    {
        if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Create Employee')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'dob' => 'required',
                    'gender' => 'required',
                    'phone' => 'required',
                    'address' => 'required',
                    'email' => 'required|unique:users',
                    'password' => 'required',
                    'department_id' => 'required',
                    'designation_id' => 'required',
                    'shift_start' => 'required',
                    // 'document.*' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,zip|max:20480',
                ]
            );
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $objUser = User::find(\Auth::user()->creatorId());
            $total_employee = $objUser->countEmployees();
            $plan = Plan::find($objUser->plan);

            if ($total_employee < $plan->max_employees || $plan->max_employees == -1) {
                $user = User::create(
                    [
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'password' => Hash::make($request['password']),
                        'type' => 'employee',
                        'lang' => 'en',
                        'created_by' => \Auth::user()->creatorId(),
                    ]
                );
                $user->save();
                $user->assignRole('Employee');
            } else {
                return response()->json(['error' => 'Your employee limit is over, Please upgrade plan.'], 403);
            }


            $document_implode = !empty($request->document) ? implode(',', array_keys($request->document)) : null;

            $employee = Employee::create(
                [
                    'user_id' => $user->id,
                    'name' => $request['name'],
                    'dob' => $request['dob'],
                    'gender' => $request['gender'],
                    'phone' => $request['phone'],
                    'address' => $request['address'],
                    'email' => $request['email'],
                    'password' => Hash::make($request['password']),
                    'employee_id' => $this->employeeNumber(),
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'designation_id' => $request['designation_id'],
                    'company_doj' => $request['company_doj'],
                    'documents' => $document_implode,
                    'account_holder_name' => $request['account_holder_name'],
                    'account_number' => $request['account_number'],
                    'bank_name' => $request['bank_name'],
                    'bank_identifier_code' => $request['bank_identifier_code'],
                    'branch_location' => $request['branch_location'],
                    'tax_payer_id' => $request['tax_payer_id'],
                    'shift_start' => $request['shift_start'],
                    'created_by' => \Auth::user()->creatorId(),
                ]
            );

            if ($request->hasFile('document')) {
                foreach ($request->document as $key => $document) {
                    $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('document')[$key]->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $dir = storage_path('uploads/document/');
                    $image_path = $dir . $filenameWithExt;

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $path = $request->file('document')[$key]->storeAs('uploads/document/', $fileNameToStore);
                    EmployeeDocument::create(
                        [
                            'employee_id' => $employee['employee_id'],
                            'document_id' => $key,
                            'document_value' => $fileNameToStore,
                            'created_by' => \Auth::user()->creatorId(),
                        ]
                    );
                }
            }

            $settings = Utility::settings();
            if ($settings['employee_create'] == 1) {
                $user->type = 'employee';
                $user->password = $request['password'];
                try {
                    Mail::to($user->email)->send(new UserCreate($user));
                } catch (\Exception $e) {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }

                return response()->json(['success' => 'Employee successfully created.' . (isset($smtp_error) ? $smtp_error : '')], 201);
            }

            return response()->json(['success' => 'Employee successfully created.'], 201);
        } else {
            return response()->json(['error' => 'Permission denied.'], 403);
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Edit Employee')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'dob' => 'required',
                    'gender' => 'required',
                    'phone' => 'required',
                    'address' => 'required',
                    'shift_start' => 'required',
                    // 'document.*' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,zip|max:20480',
                ]
            );

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            $employee->update(
                [
                    'name' => $request['name'],
                    'dob' => $request['dob'],
                    'gender' => $request['gender'],
                    'phone' => $request['phone'],
                    'address' => $request['address'],
                    'shift_start' => $request['shift_start'],
                ]
            );

            if ($request->hasFile('document')) {
                foreach ($request->document as $key => $document) {
                    $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('document')[$key]->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $dir = storage_path('uploads/document/');
                    $image_path = $dir . $filenameWithExt;

                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }

                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $path = $request->file('document')[$key]->storeAs('uploads/document/', $fileNameToStore);
                    EmployeeDocument::updateOrCreate(
                        [
                            'employee_id' => $employee['employee_id'],
                            'document_id' => $key,
                        ],
                        [
                            'document_value' => $fileNameToStore,
                            'created_by' => \Auth::user()->creatorId(),
                        ]
                    );
                }
            }

            return response()->json(['success' => 'Employee successfully updated.'], 200);
        } else {
            return response()->json(['error' => 'Permission denied.'], 403);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/employees/{id}",
     *     operationId="deleteEmployee",
     *     tags={"Employee"},
     *     summary="Delete an employee",
     *     description="Delete an employee record by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="Employee successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Employee not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Delete Employee')) {
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            $employee->delete();

            return response()->json(['success' => 'Employee successfully deleted.'], 200);
        } else {
            return response()->json(['error' => 'Permission denied.'], 403);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/employees/import",
     *     operationId="importEmployees",
     *     tags={"Employee"},
     *     summary="Import employees from a CSV file",
     *     description="Imports employee data from a CSV file and creates new employees or updates existing ones.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="file", type="file", format="binary", description="CSV file containing employee data")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Employees successfully imported",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="msg", type="string", example="Record successfully imported")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid file type or file is missing")
     *         )
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied")
     *         )
     *     )
     * )
     */
    public function import(Request $request)
    {
        $rules = ['file' => 'required|mimes:csv,txt'];
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $employees = (new EmployeesImport())->toArray(request()->file('file'))[0];
        $totalCustomer = count($employees) - 1;
        $errorArray = [];

        for ($i = 1; $i <= count($employees) - 1; $i++) {
            $employee = $employees[$i];
            $employeeByEmail = Employee::where('email', $employee[5])->first();
            $userByEmail = User::where('email', $employee[5])->first();

            if (!empty($employeeByEmail) && !empty($userByEmail)) {
                $employeeData = $employeeByEmail;
            } else {
                $user = new User();
                $user->name = $employee[0];
                $user->email = $employee[5];
                $user->password = Hash::make($employee[6]);
                $user->type = 'employee';
                $user->lang = 'en';
                $user->created_by = \Auth::user()->creatorId();
                $user->save();
                $user->assignRole('Employee');

                $employeeData = new Employee();
                $employeeData->employee_id = $this->employeeNumber();
                $employeeData->user_id = $user->id;
            }

            $employeeData->name = $employee[0];
            $employeeData->dob = $employee[1];
            $employeeData->gender = $employee[2];
            $employeeData->phone = $employee[3];
            $employeeData->address = $employee[4];
            $employeeData->email = $employee[5];
            $employeeData->password = Hash::make($employee[6]);
            $employeeData->branch_id = $employee[8];
            $employeeData->department_id = $employee[9];
            $employeeData->designation_id = $employee[10];
            $employeeData->company_doj = $employee[11];
            $employeeData->account_holder_name = $employee[12];
            $employeeData->account_number = $employee[13];
            $employeeData->bank_name = $employee[14];
            $employeeData->bank_identifier_code = $employee[15];
            $employeeData->branch_location = $employee[16];
            $employeeData->tax_payer_id = $employee[17];
            $employeeData->created_by = \Auth::user()->creatorId();

            if (empty($employeeData)) {
                $errorArray[] = $employeeData;
            } else {
                $employeeData->save();
            }
        }

        $data = [];
        if (empty($errorArray)) {
            $data['status'] = 'success';
            $data['msg'] = __('Record successfully imported');
        } else {
            $data['status'] = 'error';
            $data['msg'] = count($errorArray) . ' ' . __('Record import failed out of ' . $totalCustomer . ' records');
        }

        return response()->json($data, $data['status'] === 'success' ? 200 : 400);
    }

    /**
     * @OA\Get(
     *     path="/api/employees/designations",
     *     operationId="getDesignations",
     *     tags={"Employee"},
     *     summary="Get designations by department",
     *     description="Retrieve a list of designations based on the given department ID",
     *     @OA\Parameter(
     *         name="department_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of designations",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Manager")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Department ID is required")
     *         )
     *     )
     * )
     */
    public function json(Request $request)
    {
        $designations = Designation::where('department_id', $request->department_id)->get()->pluck('name', 'id')->toArray();
        return response()->json(['data' => $designations]);
    }

    /**
     * @OA\Get(
     *     path="/api/employees/profile",
     *     operationId="getEmployeeProfile",
     *     tags={"Employee"},
     *     summary="Get employee profiles",
     *     description="Retrieve employee profiles based on filters.",
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
     *         name="designation",
     *         in="query",
     *         description="Filter by designation ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of employee profiles and filter options",
     *         @OA\JsonContent(
     *             @OA\Property(property="employees", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com")
     *             )),
     *             @OA\Property(property="branches", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Headquarters")
     *             )),
     *             @OA\Property(property="departments", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="HR")
     *             )),
     *             @OA\Property(property="designations", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Manager")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied")
     *         )
     *     )
     * )
     */

    public function profile(Request $request)
    {
        if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Manage Employee Profile')) {
            $employees = Employee::where('created_by', \Auth::user()->creatorId());
            if (!empty($request->branch)) {
                $employees->where('branch_id', $request->branch);
            }
            if (!empty($request->department)) {
                $employees->where('department_id', $request->department);
            }
            if (!empty($request->designation)) {
                $employees->where('designation_id', $request->designation);
            }
            $employees = $employees->get();

            $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branches->prepend('All', '');

            $departments = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments->prepend('All', '');

            $designations = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations->prepend('All', '');

            return response()->json([
                'employees' => $employees,
                'branches' => $branches,
                'departments' => $departments,
                'designations' => $designations,
            ]);
        } else {
            return response()->json(['error' => __('Permission denied.')], 403);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/employees/profile/{id}",
     *     operationId="getEmployeeProfileById",
     *     tags={"Employee"},
     *     summary="Get employee profile by ID",
     *     description="Retrieve detailed employee profile information by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee profile details",
     *         @OA\JsonContent(
     *             @OA\Property(property="employee", type="object", description="Detailed employee information"),
     *             @OA\Property(property="employeesId", type="string", description="Formatted employee ID"),
     *             @OA\Property(property="branches", type="object", description="Branch details"),
     *             @OA\Property(property="departments", type="object", description="Department details"),
     *             @OA\Property(property="designations", type="object", description="Designation details"),
     *             @OA\Property(property="documents", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="document_name", type="string", example="resume.pdf")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Employee not found")
     *         )
     *     )
     * )
     */
    public function profileShow($id)
    {
        if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Show Employee Profile')) {
            // $empId = \Crypt::decrypt($id);
            $empId = $id;
            $employee = Employee::find($empId);
            $documents = DucumentUpload::whereIn(
                    'role', [
                              $userRole->id,
                              0,
                          ]
                )->where('created_by', \Auth::user()->creatorId())->get();
            $branches = Branch::where('id', $employee->branch_id)->first()->pluck('name');
            $departments = Department::where('id', $employee->department_id)->first()->pluck('name');
            $designations = Designation::where('id', $employee->designation_id)->first()->pluck('name');

            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            $employeesId = \Auth::user()->employeeIdFormat($employee->employee_id);

            return response()->json([
                'employee' => $employee,
                'employeesId' => $employeesId,
                'branches' => $branches,
                'departments' => $departments,
                'designations' => $designations,
                'documents' => $documents,
            ]);
        } else {
            return response()->json(['error' => __('Permission denied.')], 403);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/employees/{id}",
     *     operationId="getEmployeeDetails",
     *     tags={"Employee"},
     *     summary="Get employee details by ID",
     *     description="Retrieve detailed employee information, including documents, leaves, and other related data.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee details",
     *         @OA\JsonContent(
     *             @OA\Property(property="employee", type="object", description="Detailed employee information"),
     *             @OA\Property(property="employeesId", type="string", description="Formatted employee ID"),
     *             @OA\Property(property="branches", type="string", description="Branch name"),
     *             @OA\Property(property="departments", type="string", description="Department name"),
     *             @OA\Property(property="designations", type="string", description="Designation name"),
     *             @OA\Property(property="documents", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="document_name", type="string")
     *             )),
     *             @OA\Property(property="leaves", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="days", type="integer")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Employee not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Show Employee')) {
            // $empId = \Crypt::decrypt($id);
            $empId = $id;
            $employee = Employee::find($empId)->makeHidden('password');
            $user = User::find($employee->user_id)->makeHidden('password');
            $userRole = $user->roles->first();

            $documents = DucumentUpload::whereIn(
                    'role', [
                        $userRole->id,
                        0,
                    ]
                )->where('created_by', \Auth::user()->creatorId())->get();

            // Get the name directly from the first result
            $branches = Branch::where('id', $employee->branch_id)->value('name');
            $departments = Department::where('id', $employee->department_id)->value('name');
            $designations = Designation::where('id', $employee->designation_id)->value('name');

            $employeesId = $user->employeeIdFormat($employee->id);

            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            $leaves = LeaveType::leftJoin('employees', function($join) use ($empId) {
                    $join->on('employees.id', '=', DB::raw($empId));
                })
                ->leftJoin('leaves', function($join) use ($employee) {
                    $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                        ->where('leaves.employee_id', '=', $employee->id);
                })
                ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                ->select('leave_types.id', 'leave_types.title', DB::raw('
                    CASE
                        WHEN leave_types.title = "Paid Leave" THEN employees.paid_leave_balance
                        ELSE (leave_types.days - COALESCE(SUM(leaves.total_leave_days), 0))
                    END AS days'))
                ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                ->get();

            return response()->json([
                'employee' => $employee,
                'employeesId' => $employeesId,
                'branches' => $branches,
                'departments' => $departments,
                'designations' => $designations,
                'documents' => $documents,
                'leaves' => $leaves,
            ]);
        } else {
            return response()->json(['error' => __('Permission denied.')], 403);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/employees/employee-number",
     *     operationId="getEmployeeNumber",
     *     tags={"Employee"},
     *     summary="Generate new employee number",
     *     description="Retrieve the next available employee number based on the latest record.",
     *     @OA\Response(
     *         response=200,
     *         description="Employee number",
     *         @OA\JsonContent(
     *             @OA\Property(property="employee_number", type="integer", example=1)
     *         )
     *     )
     * )
     */
    public function employeeNumber()
    {
        $latest = Employee::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return response()->json(['employee_number' => 1]);
        }

        return response()->json(['employee_number' => $latest->id + 1]);
    }

    /**
     * @OA\Get(
     *     path="/api/employees/last-login",
     *     operationId="getLastLogin",
     *     tags={"Employee"},
     *     summary="Get last login information of users",
     *     description="Retrieve last login details of users.",
     *     @OA\Response(
     *         response=200,
     *         description="List of users and their last login times",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="users",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="last_login_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )

     */
    public function lastLogin()
    {
        $users = User::where('created_by', \Auth::user()->creatorId())->get();
        return response()->json(['users' => $users]);
    }

    /**
     * @OA\Post(
     *     path="/api/employees-by-branch",
     *     operationId="getEmployeesByBranch",
     *     tags={"Employee"},
     *     summary="Get employees by branch",
     *     description="Retrieve a list of employees based on the given branch ID.",
     *     @OA\Parameter(
     *         name="branch",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of employees",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\AdditionalProperties(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Branch ID is required")
     *         )
     *     )
     * )
     */
    public function employeeJson(Request $request)
    {
        $employees = Employee::where('branch_id', $request->branch)->get()->pluck('name', 'id')->toArray();
        return response()->json(['data' => $employees]);
    }

}
