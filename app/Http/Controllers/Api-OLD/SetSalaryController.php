<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\AllowanceOption;
use App\Models\Commission;
use App\Models\DeductionOption;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanOption;
use App\Models\OtherPayment;
use App\Models\Overtime;
use App\Models\PayslipType;
use App\Models\SaturationDeduction;
use Illuminate\Http\Request;

class SetSalaryController extends Controller
{
    /**
 * @OA\Get(
 *     path="/setsalary",
 *     summary="Retrieve a list of employees with set salary permissions",
 *     tags={"Set Salary"},
 *     description="Returns a list of employees created by the authenticated user, provided the user has the 'Manage Set Salary' permission.",
 *     operationId="getEmployeesWithSetSalary",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Employees retrieved successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=true
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Employees retrieved successfully."
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(
 *                         property="id",
 *                         type="integer",
 *                         example=1
 *                     ),
 *                     @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         example="John Doe"
 *                     ),
 *                     @OA\Property(
 *                         property="email",
 *                         type="string",
 *                         example="john.doe@example.com"
 *                     ),
 *                     @OA\Property(
 *                         property="created_by",
 *                         type="integer",
 *                         example=1
 *                     ),
 *                     @OA\Property(
 *                         property="salary",
 *                         type="number",
 *                         format="float",
 *                         example=50000
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Permission denied.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Permission denied."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Something went wrong. Please try again later.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Something went wrong. Please try again later."
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Internal Server Error"
 *             )
 *         )
 *     )
 * )
 */
    public function index()
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Manage Set Salary')) {
                $employees = Employee::where('created_by', \Auth::user()->creatorId())->get();

                return response()->json([
                    'success' => true,
                    'message' => __('Employees retrieved successfully.'),
                    'data' => $employees
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Permission denied.')
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again later.'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Get(
 *     path="/setsalary-edit/{id}",
 *     summary="Retrieve employee salary data for editing",
 *     tags={"Set Salary"},
 *     description="Fetches salary-related data for an employee, including allowances, commissions, loans, and other payments.",
 *     operationId="editEmployeeSalary",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Employee ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Employee salary data retrieved successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=true
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Employee salary data retrieved successfully."
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="employee",
 *                     type="object",
 *                     @OA\Property(
 *                         property="id",
 *                         type="integer",
 *                         example=1
 *                     ),
 *                     @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         example="John Doe"
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Permission denied.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Permission denied."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Something went wrong. Please try again later.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Something went wrong. Please try again later."
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Internal Server Error"
 *             )
 *         )
 *     )
 * )
 */
    public function edit($id)
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Edit Set Salary')) {

                $payslip_type      = PayslipType::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
                $allowance_options = AllowanceOption::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
                $loan_options      = LoanOption::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
                $deduction_options = DeductionOption::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');

                if (\Auth::user()->type == 'employee') {
                    $currentEmployee      = Employee::where('user_id', \Auth::user()->id)->first();
                    $allowances           = Allowance::where('employee_id', $currentEmployee->id)->get();
                    $commissions          = Commission::where('employee_id', $currentEmployee->id)->get();
                    $loans                = Loan::where('employee_id', $currentEmployee->id)->get();
                    $saturationdeductions = SaturationDeduction::where('employee_id', $currentEmployee->id)->get();
                    $otherpayments        = OtherPayment::where('employee_id', $currentEmployee->id)->get();
                    $overtimes            = Overtime::where('employee_id', $currentEmployee->id)->get();
                    $employee             = Employee::where('user_id', \Auth::user()->id)->first();

                    return response()->json([
                        'success' => true,
                        'message' => __('Employee salary data retrieved successfully.'),
                        'data' => [
                            'employee' => $employee,
                            'payslip_type' => $payslip_type,
                            'allowance_options' => $allowance_options,
                            'commissions' => $commissions,
                            'loan_options' => $loan_options,
                            'overtimes' => $overtimes,
                            'otherpayments' => $otherpayments,
                            'saturationdeductions' => $saturationdeductions,
                            'loans' => $loans,
                            'deduction_options' => $deduction_options,
                            'allowances' => $allowances,
                        ]
                    ], 200);
                } else {
                    $allowances           = Allowance::where('employee_id', $id)->get();
                    $commissions          = Commission::where('employee_id', $id)->get();
                    $loans                = Loan::where('employee_id', $id)->get();
                    $saturationdeductions = SaturationDeduction::where('employee_id', $id)->get();
                    $otherpayments        = OtherPayment::where('employee_id', $id)->get();
                    $overtimes            = Overtime::where('employee_id', $id)->get();
                    $employee             = Employee::find($id);

                    return response()->json([
                        'success' => true,
                        'message' => __('Employee salary data retrieved successfully.'),
                        'data' => [
                            'employee' => $employee,
                            'payslip_type' => $payslip_type,
                            'allowance_options' => $allowance_options,
                            'commissions' => $commissions,
                            'loan_options' => $loan_options,
                            'overtimes' => $overtimes,
                            'otherpayments' => $otherpayments,
                            'saturationdeductions' => $saturationdeductions,
                            'loans' => $loans,
                            'deduction_options' => $deduction_options,
                            'allowances' => $allowances,
                        ]
                    ], 200);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Permission denied.')
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again later.'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Get(
 *     path="/setsalary-show/{id}",
 *     summary="Retrieve detailed employee salary data",
 *     tags={"Set Salary"},
 *     description="Fetches detailed salary-related data for a specific employee by ID.",
 *     operationId="showEmployeeSalaryDetails",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Employee ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Employee salary details retrieved successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=true
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Employee salary details retrieved successfully."
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="employee",
 *                     type="object",
 *                     @OA\Property(
 *                         property="id",
 *                         type="integer",
 *                         example=1
 *                     ),
 *                     @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         example="John Doe"
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Permission denied.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Permission denied."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Something went wrong. Please try again later.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Something went wrong. Please try again later."
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Internal Server Error"
 *             )
 *         )
 *     )
 * )
 */
    public function show($id)
    {
        try {
            $payslip_type      = PayslipType::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
            $allowance_options = AllowanceOption::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
            $loan_options      = LoanOption::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');
            $deduction_options = DeductionOption::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');

            if (\Auth::user()->type == 'employee') {
                $currentEmployee      = Employee::where('user_id', \Auth::user()->id)->first();
                $allowances           = Allowance::where('employee_id', $currentEmployee->id)->get();
                $commissions          = Commission::where('employee_id', $currentEmployee->id)->get();
                $loans                = Loan::where('employee_id', $currentEmployee->id)->get();
                $saturationdeductions = SaturationDeduction::where('employee_id', $currentEmployee->id)->get();
                $otherpayments        = OtherPayment::where('employee_id', $currentEmployee->id)->get();
                $overtimes            = Overtime::where('employee_id', $currentEmployee->id)->get();
                $employee             = Employee::where('user_id', \Auth::user()->id)->first();

                return response()->json([
                    'success' => true,
                    'message' => __('Employee salary details retrieved successfully.'),
                    'data' => [
                        'employee' => $employee,
                        'payslip_type' => $payslip_type,
                        'allowance_options' => $allowance_options,
                        'commissions' => $commissions,
                        'loan_options' => $loan_options,
                        'overtimes' => $overtimes,
                        'otherpayments' => $otherpayments,
                        'saturationdeductions' => $saturationdeductions,
                        'loans' => $loans,
                        'deduction_options' => $deduction_options,
                        'allowances' => $allowances,
                    ]
                ], 200);
            } else {
                $allowances           = Allowance::where('employee_id', $id)->get();
                $commissions          = Commission::where('employee_id', $id)->get();
                $loans                = Loan::where('employee_id', $id)->get();
                $saturationdeductions = SaturationDeduction::where('employee_id', $id)->get();
                $otherpayments        = OtherPayment::where('employee_id', $id)->get();
                $overtimes            = Overtime::where('employee_id', $id)->get();
                $employee             = Employee::find($id);

                return response()->json([
                    'success' => true,
                    'message' => __('Employee salary details retrieved successfully.'),
                    'data' => [
                        'employee' => $employee,
                        'payslip_type' => $payslip_type,
                        'allowance_options' => $allowance_options,
                        'commissions' => $commissions,
                        'loan_options' => $loan_options,
                        'overtimes' => $overtimes,
                        'otherpayments' => $otherpayments,
                        'saturationdeductions' => $saturationdeductions,
                        'loans' => $loans,
                        'deduction_options' => $deduction_options,
                        'allowances' => $allowances,
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again later.'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Put(
 *     path="/setsalary-update/{id}",
 *     summary="Update an employee's salary",
 *     tags={"Set Salary"},
 *     description="Updates the salary details for a specific employee. This endpoint requires validation for salary type and salary amount.",
 *     operationId="updateEmployeeSalary",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Employee ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="salary_type",
 *                 type="string",
 *                 example="Monthly"
 *             ),
 *             @OA\Property(
 *                 property="salary",
 *                 type="number",
 *                 format="float",
 *                 example=50000.00
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Employee Salary Updated successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=true
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Employee Salary Updated successfully."
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="id",
 *                     type="integer",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="salary_type",
 *                     type="string",
 *                     example="Monthly"
 *                 ),
 *                 @OA\Property(
 *                     property="salary",
 *                     type="number",
 *                     format="float",
 *                     example=50000.00
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Validation error."
 *             ),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "salary_type": {"The salary type field is required."},
 *                     "salary": {"The salary field is required."}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Something went wrong. Please try again later.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Something went wrong. Please try again later."
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Internal Server Error"
 *             )
 *         )
 *     )
 * )
 */
    public function employeeUpdateSalary(Request $request, $id)
    {
        try {
            $validator = \Validator::make(
                $request->all(), [
                    'salary_type' => 'required',
                    'salary' => 'required',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Validation error.'),
                    'errors' => $validator->errors()
                ], 422);
            }

            $employee = Employee::findOrFail($id);

            $employee->fill($request->all())->save();

            return response()->json([
                'success' => true,
                'message' => __('Employee Salary Updated successfully.'),
                'data' => $employee
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again later.'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Get(
 *     path="/setsalary-salary",
 *     summary="Retrieve logged-in employee's salary details",
 *     tags={"Set Salary"},
 *     description="Fetches the salary details of the logged-in employee. This endpoint is only accessible by users with the 'employee' type.",
 *     operationId="getEmployeeSalaryDetails",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Employee salary details retrieved successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=true
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Employee salary details retrieved successfully."
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(
 *                         property="id",
 *                         type="integer",
 *                         example=1
 *                     ),
 *                     @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         example="John Doe"
 *                     ),
 *                     @OA\Property(
 *                         property="email",
 *                         type="string",
 *                         example="john.doe@example.com"
 *                     ),
 *                     @OA\Property(
 *                         property="salary",
 *                         type="number",
 *                         format="float",
 *                         example=50000.00
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Something went wrong. Please try again later.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Something went wrong. Please try again later."
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Internal Server Error"
 *             )
 *         )
 *     )
 * )
 */
    public function employeeSalary()
    {
        try {
            if (\Auth::user()->type == "employee") {
                $employees = Employee::where('user_id', \Auth::user()->id)->get();

                return response()->json([
                    'success' => true,
                    'message' => __('Employee salary details retrieved successfully.'),
                    'data' => $employees
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again later.'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Get(
 *     path="/setsalary-basic-salary/{id}",
 *     summary="Retrieve an employee's basic salary details",
 *     tags={"Set Salary"},
 *     description="Fetches the basic salary details and payslip types for a specific employee by ID.",
 *     operationId="getEmployeeBasicSalary",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Employee ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Employee basic salary details retrieved successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=true
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Employee basic salary details retrieved successfully."
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="employee",
 *                     type="object",
 *                     @OA\Property(
 *                         property="id",
 *                         type="integer",
 *                         example=1
 *                     ),
 *                     @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         example="John Doe"
 *                     ),
 *                     @OA\Property(
 *                         property="email",
 *                         type="string",
 *                         example="john.doe@example.com"
 *                     ),
 *                     @OA\Property(
 *                         property="salary",
 *                         type="number",
 *                         format="float",
 *                         example=50000.00
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="payslip_type",
 *                     type="object",
 *                     example={
 *                         "1": "Monthly",
 *                         "2": "Bi-weekly"
 *                     }
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Employee not found.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Employee not found."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Something went wrong. Please try again later.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=false
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Something went wrong. Please try again later."
 *             ),
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Internal Server Error"
 *             )
 *         )
 *     )
 * )
 */
    public function employeeBasicSalary($id)
    {
        try {
            $payslip_type = PayslipType::where('created_by', \Auth::user()->creatorId())->pluck('name', 'id');

            $employee = Employee::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => __('Employee basic salary details retrieved successfully.'),
                'data' => [
                    'employee' => $employee,
                    'payslip_type' => $payslip_type
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again later.'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
