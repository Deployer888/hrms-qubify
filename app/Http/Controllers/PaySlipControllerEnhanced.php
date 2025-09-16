<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PaySlip;
use App\Models\User;
use App\Models\Utility;
use App\Mail\PayslipSend;
use App\Http\Requests\PaySlipRequest;
use App\Services\PaySlipErrorHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaySlipControllerEnhanced extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Pay Slip')) {
            try {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get();
                $month = [
                    '01' => 'January',
                    '02' => 'February',
                    '03' => 'March',
                    '04' => 'April',
                    '05' => 'May',
                    '06' => 'June',
                    '07' => 'July',
                    '08' => 'August',
                    '09' => 'September',
                    '10' => 'October',
                    '11' => 'November',
                    '12' => 'December',
                ];
                $year = [];
                for ($i = date('Y'); $i >= (date('Y') - 5); $i--) {
                    $year[$i] = $i;
                }

                return view('payslip.index', compact('employees', 'month', 'year'));
            } catch (\Exception $e) {
                $errorResponse = PaySlipErrorHandler::handleDataLoadError($e);
                return redirect()->back()->with('error', $errorResponse['message']);
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(PaySlipRequest $request)
    {
        if (\Auth::user()->can('Create Pay Slip')) {
            $month = $request->month;
            $year = $request->year;
            $salaryMonth = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

            try {
                // Get all active employees for the current creator
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())
                    ->where('is_active', true)
                    ->get();

                if ($employees->isEmpty()) {
                    return redirect()->route('payslip.index')->with('error', __('No active employees found.'));
                }

                // Validate employee salary configurations
                $salaryErrors = PaySlipErrorHandler::validateEmployeeSalaryConfig($employees);
                if (!empty($salaryErrors)) {
                    return redirect()->route('payslip.index')->with('error', implode(' ', $salaryErrors));
                }

                // Check for existing payslips for this month
                $existingPayslips = PaySlip::where('salary_month', $salaryMonth)
                    ->whereIn('employee_id', $employees->pluck('id'))
                    ->exists();

                if ($existingPayslips) {
                    return redirect()->route('payslip.index')->with('error', 
                        __('Payslips for this month already exist. Please delete existing payslips first.'));
                }

                $createdCount = 0;
                $warnings = [];

                DB::beginTransaction();

                foreach ($employees as $employee) {
                    try {
                        // Validate attendance data and collect warnings
                        $attendanceWarnings = PaySlipErrorHandler::validateAttendanceData($employee, $month, $year);
                        $warnings = array_merge($warnings, $attendanceWarnings);

                        // Calculate attendance for the month
                        $attendanceData = $employee->calculateAttendanceForMonth($month, $year);
                        
                        // Calculate salary components
                        $basicSalary = $employee->basic_salary ?? $employee->salary ?? 0;
                        $hra = $employee->hra ?? 0;
                        $specialAllowance = $employee->special_allowance ?? 0;
                        $otherAllowance = $employee->other_allowance ?? 0;
                        $tds = $employee->tds ?? 0;
                        $otherDeduction = $employee->other_deduction ?? 0;

                        // Calculate pro-rated salary based on attendance
                        $workingDays = $attendanceData['total_working_days'];
                        $payableDays = $attendanceData['actual_payable_days'];
                        $lossOfPayDays = $attendanceData['loss_of_pay_days'];

                        $salaryRatio = $workingDays > 0 ? ($payableDays / $workingDays) : 1;

                        $proRatedBasicSalary = $basicSalary * $salaryRatio;
                        $proRatedHra = $hra * $salaryRatio;
                        $proRatedSpecialAllowance = $specialAllowance * $salaryRatio;
                        $proRatedOtherAllowance = $otherAllowance * $salaryRatio;

                        // Calculate total earnings and deductions
                        $totalEarnings = $proRatedBasicSalary + $proRatedHra + $proRatedSpecialAllowance + $proRatedOtherAllowance;
                        $totalDeduction = $tds + $otherDeduction;
                        $netPayable = $totalEarnings - $totalDeduction;

                        // Create payslip record
                        PaySlip::create([
                            'employee_id' => $employee->id,
                            'net_payble' => round($netPayable, 2),
                            'basic_salary' => round($proRatedBasicSalary, 2),
                            'salary_month' => $salaryMonth,
                            'status' => 0, // Unpaid
                            'allowance' => round($proRatedOtherAllowance, 2),
                            'commission' => 0,
                            'loan' => 0,
                            'saturation_deduction' => round($otherDeduction, 2),
                            'other_payment' => 0,
                            'overtime' => 0,
                            'created_by' => \Auth::user()->creatorId(),
                            'actual_payable_days' => $payableDays,
                            'total_working_days' => $workingDays,
                            'loss_of_pay_days' => $lossOfPayDays,
                            'hra' => round($proRatedHra, 2),
                            'tds' => $tds,
                            'special_allowance' => round($proRatedSpecialAllowance, 2),
                            'total_earnings' => round($totalEarnings, 2),
                            'total_deduction' => round($totalDeduction, 2)
                        ]);

                        $createdCount++;
                    } catch (\Exception $e) {
                        Log::error("Payslip creation error for employee {$employee->id}: " . $e->getMessage());
                        // Continue with other employees instead of failing completely
                    }
                }

                DB::commit();

                // Log successful operation
                PaySlipErrorHandler::logSuccess('payslip_generation', [
                    'month' => $month,
                    'year' => $year,
                    'employees_processed' => $createdCount
                ]);

                $message = __('Payslips created successfully for :count employees.', ['count' => $createdCount]);
                
                if (!empty($warnings)) {
                    $message .= ' Warnings: ' . implode(' ', array_slice($warnings, 0, 3));
                    if (count($warnings) > 3) {
                        $message .= ' and ' . (count($warnings) - 3) . ' more.';
                    }
                }

                return redirect()->route('payslip.index')->with('success', $message);

            } catch (\Exception $e) {
                DB::rollBack();
                $errorResponse = PaySlipErrorHandler::handleGenerationError($e, $request);
                return redirect()->route('payslip.index')->with('error', $errorResponse['message']);
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function search_json(Request $request)
    {
        try {
            $month = $request->month ?? date('m');
            $year = $request->year ?? date('Y');
            $salaryMonth = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

            $currentUser = \Auth::user();
            
            // Build base query
            $query = PaySlip::with(['employee.department', 'employee.designation'])
                ->where('salary_month', $salaryMonth);

            // Filter based on user role
            if ($currentUser->type == 'employee') {
                // For employees, show only their own payslips
                $employee = Employee::where('user_id', $currentUser->id)->first();
                if (!$employee) {
                    return response()->json([
                        'draw' => intval($request->draw),
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => []
                    ]);
                }
                $query->where('employee_id', $employee->id);
            } else {
                // For admin/HR, show payslips for their organization
                $query->whereHas('employee', function($q) use ($currentUser) {
                    $q->where('created_by', $currentUser->creatorId());
                });
            }

            // Get total count before filtering
            $totalRecords = $query->count();

            // Apply search filter if provided
            if (!empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->whereHas('employee', function($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                      ->orWhere('employee_id', 'like', "%{$searchValue}%")
                      ->orWhere('email', 'like', "%{$searchValue}%");
                });
            }

            // Get filtered count
            $filteredRecords = $query->count();

            // Apply pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            
            if ($length != -1) {
                $query->offset($start)->limit($length);
            }

            // Apply ordering
            $orderColumn = $request->order[0]['column'] ?? 0;
            $orderDir = $request->order[0]['dir'] ?? 'asc';
            
            $columns = ['employee.name', 'employee.employee_id', 'basic_salary', 'net_payble', 'status'];
            if (isset($columns[$orderColumn])) {
                if (strpos($columns[$orderColumn], '.') !== false) {
                    $query->join('employees', 'pay_slips.employee_id', '=', 'employees.id')
                          ->orderBy(str_replace('employee.', 'employees.', $columns[$orderColumn]), $orderDir)
                          ->select('pay_slips.*');
                } else {
                    $query->orderBy($columns[$orderColumn], $orderDir);
                }
            }

            $payslips = $query->get();

            // Format data for DataTables
            $data = [];
            foreach ($payslips as $payslip) {
                $employee = $payslip->employee;
                if (!$employee) continue;

                // Generate action buttons based on user permissions
                $actions = '';
                
                if ($currentUser->can('Show Pay Slip')) {
                    $actions .= '<a href="' . route('payslip.showemployee', $payslip->id) . '" class="btn btn-sm btn-primary" title="View" data-bs-toggle="tooltip"><i class="fa fa-eye"></i></a> ';
                }
                
                if ($currentUser->can('Show Pay Slip')) {
                    $actions .= '<a href="' . route('payslip.payslipPdf', $payslip->id) . '" class="btn btn-sm btn-info" title="Payslip PDF" target="_blank" data-bs-toggle="tooltip"><i class="fa fa-file-pdf"></i></a> ';
                }
                
                if ($payslip->status == 0 && $currentUser->can('Edit Pay Slip')) {
                    $actions .= '<a href="' . route('payslip.paysalary', [$payslip->employee_id, $salaryMonth]) . '" class="btn btn-sm btn-success" title="Pay Now" data-bs-toggle="tooltip"><i class="fa fa-money"></i></a> ';
                }
                
                if ($currentUser->can('Edit Pay Slip')) {
                    $actions .= '<a href="' . route('payslip.editemployee', $payslip->id) . '" class="btn btn-sm btn-warning" title="Edit" data-bs-toggle="tooltip"><i class="fa fa-edit"></i></a> ';
                }
                
                if ($currentUser->can('Delete Pay Slip')) {
                    $actions .= '<a href="' . route('payslip.delete', $payslip->id) . '" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm(\'Are you sure?\')" data-bs-toggle="tooltip"><i class="fa fa-trash"></i></a>';
                }

                $statusBadge = $payslip->status == 1 
                    ? '<span class="badge badge-success">Paid</span>' 
                    : '<span class="badge badge-warning">Unpaid</span>';

                $data[] = [
                    $employee->name ?? 'N/A',
                    $employee->employee_id ?? 'N/A',
                    $employee->department->name ?? 'N/A',
                    number_format($payslip->basic_salary, 2),
                    number_format($payslip->net_payble, 2),
                    $statusBadge,
                    $actions
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            $errorResponse = PaySlipErrorHandler::handleDataLoadError($e, $request);
            return response()->json([
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $errorResponse['message']
            ], 500);
        }
    }

    public function updateEmployee(PaySlipRequest $request, $payslipId)
    {
        if (\Auth::user()->can('Edit Pay Slip')) {
            try {
                $payslip = PaySlip::findOrFail($payslipId);
                
                $payslip->update($request->only([
                    'basic_salary', 'net_payble', 'allowance', 'commission', 
                    'loan', 'saturation_deduction', 'other_payment', 'overtime'
                ]));

                // Log successful operation
                PaySlipErrorHandler::logSuccess('payslip_update', [
                    'payslip_id' => $payslipId,
                    'updated_fields' => array_keys($request->only([
                        'basic_salary', 'net_payble', 'allowance', 'commission', 
                        'loan', 'saturation_deduction', 'other_payment', 'overtime'
                    ]))
                ]);

                return redirect()->route('payslip.index')->with('success', __('Payslip updated successfully.'));
            } catch (\Exception $e) {
                $errorResponse = PaySlipErrorHandler::handleUpdateError($e, $payslipId, $request);
                return redirect()->route('payslip.index')->with('error', $errorResponse['message']);
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bulkpayment(PaySlipRequest $request, $date)
    {
        if (\Auth::user()->can('Edit Pay Slip')) {
            try {
                $employeeIds = $request->employee ?? [];
                
                if (empty($employeeIds)) {
                    return redirect()->back()->with('error', __('Please select at least one employee.'));
                }

                $updatedCount = PaySlip::where('salary_month', $date)
                    ->whereIn('employee_id', $employeeIds)
                    ->where('status', 0)
                    ->update(['status' => 1]);

                // Log successful operation
                PaySlipErrorHandler::logSuccess('bulk_payment', [
                    'date' => $date,
                    'employee_ids' => $employeeIds,
                    'updated_count' => $updatedCount
                ]);

                return redirect()->route('payslip.index')->with('success', 
                    __('Bulk payment completed for :count payslips.', ['count' => $updatedCount]));
            } catch (\Exception $e) {
                $errorResponse = PaySlipErrorHandler::handleBulkPaymentError($e, $request);
                return redirect()->route('payslip.index')->with('error', $errorResponse['message']);
            }
        } else {
            return redirect()->route('payslip.index')->with('error', __('Permission denied.'));
        }
    }

    // Add other methods with similar error handling...
    // (paysalary, bulk_pay_create, showemployee, editemployee, pdf, payslipPdf, send, destroy)
}