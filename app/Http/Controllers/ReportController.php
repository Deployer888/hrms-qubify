<?php

namespace App\Http\Controllers;

use App\Models\AccountList;
use App\Models\AttendanceEmployee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Holiday;
use App\Models\PaySlip;
use App\Models\TimeSheet;
use App\Models\Termination;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use App\Helpers\Helper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use DB; 
use PhpOffice\PhpSpreadsheet\Writer\Html;

class ReportController extends Controller
{
    public function incomeVsExpense(Request $request)
    {
        if(\Auth::user()->can('Manage Report'))
        {
            $deposit = Deposit::where('created_by', \Auth::user()->creatorId());

            $labels       = $data = [];
            $expenseCount = $incomeCount = 0;
            if(!empty($request->start_month) && !empty($request->end_month))
            {

                $start = strtotime($request->start_month);
                $end   = strtotime($request->end_month);

                $currentdate = $start;
                $month       = [];
                while($currentdate <= $end)
                {
                    $month = date('m', $currentdate);
                    $year  = date('Y', $currentdate);

                    $depositFilter = Deposit::where('created_by', \Auth::user()->creatorId())->whereMonth('date', $month)->whereYear('date', $year)->get();

                    $depositsTotal = 0;
                    foreach($depositFilter as $deposit)
                    {
                        $depositsTotal += $deposit->amount;
                    }
                    $incomeData[] = $depositsTotal;
                    $incomeCount  += $depositsTotal;

                    $expenseFilter = Expense::where('created_by', \Auth::user()->creatorId())->whereMonth('date', $month)->whereYear('date', $year)->get();
                    $expenseTotal  = 0;
                    foreach($expenseFilter as $expense)
                    {
                        $expenseTotal += $expense->amount;
                    }
                    $expenseData[] = $expenseTotal;
                    $expenseCount  += $expenseTotal;

                    $labels[]    = date('M Y', $currentdate);
                    $currentdate = strtotime('+1 month', $currentdate);

                }

                $filter['startDateRange'] = date('M-Y', strtotime($request->start_month));
                $filter['endDateRange']   = date('M-Y', strtotime($request->end_month));

            }
            else
            {
                for($i = 0; $i < 6; $i++)
                {
                    $month = date('m', strtotime("-$i month"));
                    $year  = date('Y', strtotime("-$i month"));

                    $depositFilter = Deposit::where('created_by', \Auth::user()->creatorId())->whereMonth('date', $month)->whereYear('date', $year)->get();

                    $depositTotal = 0;
                    foreach($depositFilter as $deposit)
                    {
                        $depositTotal += $deposit->amount;
                    }

                    $incomeData[] = $depositTotal;
                    $incomeCount  += $depositTotal;

                    $expenseFilter = Expense::where('created_by', \Auth::user()->creatorId())->whereMonth('date', $month)->whereYear('date', $year)->get();
                    $expenseTotal  = 0;
                    foreach($expenseFilter as $expense)
                    {
                        $expenseTotal += $expense->amount;
                    }
                    $expenseData[] = $expenseTotal;
                    $expenseCount  += $expenseTotal;

                    $labels[] = date('M Y', strtotime("-$i month"));
                }
                $filter['startDateRange'] = date('M-Y');
                $filter['endDateRange']   = date('M-Y', strtotime("-5 month"));

            }

            $incomeArr['name'] = __('Income');
            $incomeArr['data'] = $incomeData;

            $expenseArr['name'] = __('Expense');
            $expenseArr['data'] = $expenseData;

            $data[] = $incomeArr;
            $data[] = $expenseArr;

            // dd(json_encode($data));

            return view('report.income_expense', compact('labels', 'data', 'incomeCount', 'expenseCount', 'filter'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function leave(Request $request)
    {

        if(\Auth::user()->can('Manage Report'))
        {

            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('All', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All', '');

            $filterYear['branch']        = __('All');
            $filterYear['department']    = __('All');
            $filterYear['type']          = __('Monthly');
            $filterYear['dateYearRange'] = date('M-Y');
            $employees                   = Employee::where('created_by', \Auth::user()->creatorId());
            if(!empty($request->branch))
            {
                $employees->where('branch_id', $request->branch);
                $filterYear['branch'] = !empty(Branch::find($request->branch)) ? Branch::find($request->branch)->name : '';
            }
            if(!empty($request->department))
            {
                $employees->where('department_id', $request->department);
                $filterYear['department'] = !empty(Department::find($request->department)) ? Department::find($request->department)->name : '';
            }


            $employees = $employees->get();

            $leaves        = [];
            $totalApproved = $totalReject = $totalPending = 0;
            foreach($employees as $employee)
            {

                $employeeLeave['id']          = $employee->id;
                $employeeLeave['employee_id'] = $employee->employee_id;
                $employeeLeave['employee']    = $employee->name;

                $approved = Leave::where('employee_id', $employee->id)->where('status', 'Approve');
                $reject   = Leave::where('employee_id', $employee->id)->where('status', 'Reject');
                $pending  = Leave::where('employee_id', $employee->id)->where('status', 'Pending');

                if($request->type == 'monthly' && !empty($request->month))
                {
                    $month = date('m', strtotime($request->month));
                    $year  = date('Y', strtotime($request->month));

                    $approved->whereMonth('applied_on', $month)->whereYear('applied_on', $year);
                    $reject->whereMonth('applied_on', $month)->whereYear('applied_on', $year);
                    $pending->whereMonth('applied_on', $month)->whereYear('applied_on', $year);

                    $filterYear['dateYearRange'] = date('M-Y', strtotime($request->month));
                    $filterYear['type']          = __('Monthly');

                }
                elseif(!isset($request->type))
                {
                    $month     = date('m');
                    $year      = date('Y');
                    $monthYear = date('Y-m');

                    $approved->whereMonth('applied_on', $month)->whereYear('applied_on', $year);
                    $reject->whereMonth('applied_on', $month)->whereYear('applied_on', $year);
                    $pending->whereMonth('applied_on', $month)->whereYear('applied_on', $year);

                    $filterYear['dateYearRange'] = date('M-Y', strtotime($monthYear));
                    $filterYear['type']          = __('Monthly');
                }


                if($request->type == 'yearly' && !empty($request->year))
                {
                    $approved->whereYear('applied_on', $request->year);
                    $reject->whereYear('applied_on', $request->year);
                    $pending->whereYear('applied_on', $request->year);


                    $filterYear['dateYearRange'] = $request->year;
                    $filterYear['type']          = __('Yearly');
                }

                $approved = $approved->count();
                $reject   = $reject->count();
                $pending  = $pending->count();

                $totalApproved += $approved;
                $totalReject   += $reject;
                $totalPending  += $pending;

                $employeeLeave['approved'] = $approved;
                $employeeLeave['reject']   = $reject;
                $employeeLeave['pending']  = $pending;


                $leaves[] = $employeeLeave;
            }

            $starting_year = date('Y', strtotime('-5 year'));
            $ending_year   = date('Y', strtotime('+5 year'));

            $filterYear['starting_year'] = $starting_year;
            $filterYear['ending_year']   = $ending_year;

            $filter['totalApproved'] = $totalApproved;
            $filter['totalReject']   = $totalReject;
            $filter['totalPending']  = $totalPending;


            return view('report.leave', compact('department', 'branch', 'leaves', 'filterYear', 'filter'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function employeeLeave(Request $request, $employee_id, $status, $type, $month, $year)
    {
        if(\Auth::user()->can('Manage Report'))
        {
            $leaveTypes = LeaveType::where('created_by', \Auth::user()->creatorId())->get();
            $leaves     = [];
            foreach($leaveTypes as $leaveType)
            {
                $leave        = new Leave();
                $leave->title = $leaveType->title;
                $totalLeave   = Leave::where('employee_id', $employee_id)->where('status', $status)->where('leave_type_id', $leaveType->id);
                if($type == 'yearly')
                {
                    $totalLeave->whereYear('applied_on', $year);
                }
                else
                {
                    $m = date('m', strtotime($month));
                    $y = date('Y', strtotime($month));

                    $totalLeave->whereMonth('applied_on', $m)->whereYear('applied_on', $y);
                }
                $totalLeave = $totalLeave->count();

                $leave->total = $totalLeave;
                $leaves[]     = $leave;
            }

            $leaveData = Leave::where('employee_id', $employee_id)->where('status', $status);
            if($type == 'yearly')
            {
                $leaveData->whereYear('applied_on', $year);
            }
            else
            {
                $m = date('m', strtotime($month));
                $y = date('Y', strtotime($month));

                $leaveData->whereMonth('applied_on', $m)->whereYear('applied_on', $y);
            }


            $leaveData = $leaveData->get();


            return view('report.leaveShow', compact('leaves', 'leaveData'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }


    }

    public function accountStatement(Request $request)
    {
        if(\Auth::user()->can('Manage Report'))
        {
            $accountList = AccountList::where('created_by', \Auth::user()->creatorId())->get()->pluck('account_name', 'id');
            $accountList->prepend('All', '');

            $filterYear['account'] = __('All');
            $filterYear['type']    = __('Income');


            if($request->type == 'expense')
            {
                $accountData = Expense::orderBy('id');
                $accounts    = Expense::select('account_lists.id', 'account_lists.account_name')->leftjoin('account_lists', 'expenses.account_id', '=', 'account_lists.id')->groupBy('expenses.account_id')->selectRaw('sum(amount) as total');

                if(!empty($request->start_month) && !empty($request->end_month))
                {
                    $start = strtotime($request->start_month);
                    $end   = strtotime($request->end_month);
                }
                else
                {
                    $start = strtotime(date('Y-m'));
                    $end   = strtotime(date('Y-m', strtotime("-5 month")));
                }

                $currentdate = $start;

                while($currentdate <= $end)
                {
                    $data['month'] = date('m', $currentdate);
                    $data['year']  = date('Y', $currentdate);

                    $accountData->Orwhere(
                        function ($query) use ($data){
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );

                    $accounts->Orwhere(
                        function ($query) use ($data){
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );

                    $currentdate = strtotime('+1 month', $currentdate);
                }

                $filterYear['startDateRange'] = date('M-Y', $start);
                $filterYear['endDateRange']   = date('M-Y', $end);

                if(!empty($request->account))
                {
                    $accountData->where('account_id', $request->account);
                    $accounts->where('account_lists.id', $request->account);

                    $filterYear['account'] = !empty(AccountList::find($request->account)) ? Department::find($request->account)->account_name : '';
                }

                $accounts->where('expenses.created_by', \Auth::user()->creatorId());

                $filterYear['type'] = __('Expense');
            }
            else
            {
                $accountData = Deposit::orderBy('id');
                $accounts    = Deposit::select('account_lists.id', 'account_lists.account_name')->leftjoin('account_lists', 'deposits.account_id', '=', 'account_lists.id')->groupBy('deposits.account_id')->selectRaw('sum(amount) as total');

                if(!empty($request->start_month) && !empty($request->end_month))
                {

                    $start = strtotime($request->start_month);
                    $end   = strtotime($request->end_month);

                }
                else
                {
                    $start = strtotime(date('Y-m'));
                    $end   = strtotime(date('Y-m', strtotime("-5 month")));

                }


                $currentdate = $start;

                while($currentdate <= $end)
                {
                    $data['month'] = date('m', $currentdate);
                    $data['year']  = date('Y', $currentdate);

                    $accountData->Orwhere(
                        function ($query) use ($data){
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );
                    $currentdate = strtotime('+1 month', $currentdate);

                    $accounts->Orwhere(
                        function ($query) use ($data){
                            $query->whereMonth('date', $data['month'])->whereYear('date', $data['year']);
                        }
                    );
                    $currentdate = strtotime('+1 month', $currentdate);
                }

                $filterYear['startDateRange'] = date('M-Y', $start);
                $filterYear['endDateRange']   = date('M-Y', $end);

                if(!empty($request->account))
                {
                    $accountData->where('account_id', $request->account);
                    $accounts->where('account_lists.id', $request->account);

                    $filterYear['account'] = !empty(AccountList::find($request->account)) ? Department::find($request->account)->account_name : '';

                }
                $accounts->where('deposits.created_by', \Auth::user()->creatorId());


            }

            $accountData->where('created_by', \Auth::user()->creatorId());
            $accountData = $accountData->get();

            $accounts = $accounts->get();


            return view('report.account_statement', compact('accountData', 'accountList', 'accounts', 'filterYear'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payroll(Request $request)
    {

        if(\Auth::user()->can('Manage Report'))
        {
            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('All', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All', '');

            $filterYear['branch']     = __('All');
            $filterYear['department'] = __('All');
            $filterYear['type']       = __('Monthly');

            $payslips = PaySlip::select('pay_slips.*', 'employees.name')->leftjoin('employees', 'pay_slips.employee_id', '=', 'employees.id')->where('pay_slips.created_by', \Auth::user()->creatorId());


            if($request->type == 'monthly' && !empty($request->month))
            {

                $payslips->where('salary_month', $request->month);

                $filterYear['dateYearRange'] = date('M-Y', strtotime($request->month));
                $filterYear['type']          = __('Monthly');
            }
            elseif(!isset($request->type))
            {
                $month = date('Y-m');

                $payslips->where('salary_month', $month);

                $filterYear['dateYearRange'] = date('M-Y', strtotime($month));
                $filterYear['type']          = __('Monthly');
            }


            if($request->type == 'yearly' && !empty($request->year))
            {
                $startMonth = $request->year . '-01';
                $endMonth   = $request->year . '-12';
                $payslips->where('salary_month', '>=', $startMonth)->where('salary_month', '<=', $endMonth);

                $filterYear['dateYearRange'] = $request->year;
                $filterYear['type']          = __('Yearly');
            }


            if(!empty($request->branch))
            {
                $payslips->where('employees.branch_id', $request->branch);

                $filterYear['branch'] = !empty(Branch::find($request->branch)) ? Branch::find($request->branch)->name : '';
            }

            if(!empty($request->department))
            {
                $payslips->where('employees.department_id', $request->department);

                $filterYear['department'] = !empty(Department::find($request->department)) ? Department::find($request->department)->name : '';
            }

            $payslips = $payslips->get();

            $totalBasicSalary = $totalNetSalary = $totalAllowance = $totalCommision = $totalLoan = $totalSaturationDeduction = $totalOtherPayment = $totalOverTime = 0;

            foreach($payslips as $payslip)
            {
                $totalBasicSalary += $payslip->basic_salary;
                $totalNetSalary   += $payslip->net_payble;

                $allowances = json_decode($payslip->allowance);
                foreach($allowances as $allowance)
                {
                    $totalAllowance += $allowance->amount;

                }

                $commisions = json_decode($payslip->commission);
                foreach($commisions as $commision)
                {
                    $totalCommision += $commision->amount;

                }

                $loans = json_decode($payslip->loan);
                foreach($loans as $loan)
                {
                    $totalLoan += $loan->amount;
                }

                $saturationDeductions = json_decode($payslip->saturation_deduction);
                foreach($saturationDeductions as $saturationDeduction)
                {
                    $totalSaturationDeduction += $saturationDeduction->amount;
                }

                $otherPayments = json_decode($payslip->other_payment);
                foreach($otherPayments as $otherPayment)
                {
                    $totalOtherPayment += $otherPayment->amount;
                }

                $overtimes = json_decode($payslip->overtime);
                foreach($overtimes as $overtime)
                {
                    $days  = $overtime->number_of_days;
                    $hours = $overtime->hours;
                    $rate  = $overtime->rate;

                    $totalOverTime += ($rate * $hours) * $days;
                }


            }

            $filterData['totalBasicSalary']         = $totalBasicSalary;
            $filterData['totalNetSalary']           = $totalNetSalary;
            $filterData['totalAllowance']           = $totalAllowance;
            $filterData['totalCommision']           = $totalCommision;
            $filterData['totalLoan']                = $totalLoan;
            $filterData['totalSaturationDeduction'] = $totalSaturationDeduction;
            $filterData['totalOtherPayment']        = $totalOtherPayment;
            $filterData['totalOverTime']            = $totalOverTime;


            $starting_year = date('Y', strtotime('-5 year'));
            $ending_year   = date('Y', strtotime('+5 year'));

            $filterYear['starting_year'] = $starting_year;
            $filterYear['ending_year']   = $ending_year;

            return view('report.payroll', compact('payslips', 'filterData', 'branch', 'department', 'filterYear'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function timesheet(Request $request)
    {
        if(\Auth::user()->can('Manage Report'))
        {
            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('All', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All', '');

            $filterYear['branch']     = __('All');
            $filterYear['department'] = __('All');

            $timesheets       = TimeSheet::select('time_sheets.*', 'employees.name')->leftjoin('employees', 'time_sheets.employee_id', '=', 'employees.id')->where('time_sheets.created_by', \Auth::user()->creatorId());
            $timesheetFilters = TimeSheet::select('time_sheets.*', 'employees.name')->groupBy('employee_id')->selectRaw('sum(hours) as total')->leftjoin('employees', 'time_sheets.employee_id', '=', 'employees.id')->where('time_sheets.created_by', \Auth::user()->creatorId());


            if(!empty($request->start_date) && !empty($request->end_date))
            {
                $timesheets->where('date', '>=', $request->start_date);
                $timesheets->where('date', '<=', $request->end_date);

                $timesheetFilters->where('date', '>=', $request->start_date);
                $timesheetFilters->where('date', '<=', $request->end_date);

                $filterYear['start_date'] = $request->start_date;
                $filterYear['end_date']   = $request->end_date;
            }
            else
            {

                $filterYear['start_date'] = date('Y-m-01');
                $filterYear['end_date']   = date('Y-m-t');

                $timesheets->where('date', '>=', $filterYear['start_date']);
                $timesheets->where('date', '<=', $filterYear['end_date']);

                $timesheetFilters->where('date', '>=', $filterYear['start_date']);
                $timesheetFilters->where('date', '<=', $filterYear['end_date']);
            }

            if(!empty($request->branch))
            {
                $timesheets->where('branch_id', $request->branch);
                $timesheetFilters->where('branch_id', $request->branch);
                $filterYear['branch'] = !empty(Branch::find($request->branch)) ? Branch::find($request->branch)->name : '';
            }
            if(!empty($request->department))
            {
                $timesheets->where('department_id', $request->department);
                $timesheetFilters->where('department_id', $request->department);

                $filterYear['department'] = !empty(Department::find($request->department)) ? Department::find($request->department)->name : '';
            }

            $timesheets = $timesheets->get();

            $timesheetFilters = $timesheetFilters->get();

            $totalHours = 0;
            foreach($timesheetFilters as $timesheetFilter)
            {
                $totalHours += $timesheetFilter->hours;
            }
            $filterYear['totalHours']    = $totalHours;
            $filterYear['totalEmployee'] = count($timesheetFilters);


            return view('report.timesheet', compact('timesheets', 'branch', 'department', 'filterYear', 'timesheetFilters'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function monthlyAttendance(Request $request)
    {
        if (\Auth::user()->can('Manage Report')) 
        {
            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('All', '');
    
            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All', '');
            
            $data['branch'] = __('All');
            $data['department'] = __('All');
            
            $todayDate = Carbon::today()->toDateString();
            
            // Set up month and dates
            $statuses = ['Absent', 'Holiday', 'Week-End', 'Leave'];
            $todayDate = Carbon::today()->toDateString();
            $todayMonth = Carbon::today()->month;
            $todayYear = Carbon::today()->year;
        
            // Retrieve employees based on branch and department filters
            $employees = Employee::where('is_active', 1)->where('created_by', \Auth::user()->creatorId());
            
            if (isset($_GET['month']) && $todayYear.'-'.$todayMonth != $_GET['month']){
                $employees = Employee::where('is_active', 1)->where(function($query) {
                                $query->where(DB::raw('YEAR(company_doj)'), '<', now()->year)
                                      ->orWhere(function($q) {
                                          $q->where(DB::raw('YEAR(company_doj)'), '=', now()->year)
                                            ->where(DB::raw('MONTH(company_doj)'), '<', now()->month);
                                      });
                            })
                            ->where('created_by', \Auth::user()->creatorId());
            }
            
            if (!empty($request->branch)) {
                $employees->where('branch_id', $request->branch);
                $data['branch'] = Branch::find($request->branch)->name ?? '';
            }
            if (!empty($request->department)) {
                $employees->where('department_id', $request->department);
                $data['department'] = Department::find($request->department)->name ?? '';
            }
            $employees = $employees->get();
            
            $month = !empty($request->month) ? date('m', strtotime($request->month)) : date('m');
            $year = !empty($request->month) ? date('Y', strtotime($request->month)) : date('Y');
            $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $dates = range(1, $num_of_days);
            
            $totalDaysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
            $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
            $todayDate =Carbon::today(); // Get today's date
            $currentMonth = $todayDate->month; // Get the current month
            $currentYear = $todayDate->year; // Get the current year
            
            if ($month == $currentMonth && $year == $currentYear) {
                $endDay = $todayDate->day - 1;
            } else {
                $endDay = date('t', mktime(0, 0, 0, $month, 1, $year));
            }
            
            $startDay = 1;
            $dates = range($startDay, $endDay);
            $requiredHours = count($dates) * 8; 
            
            $filter_year = request()->input('year', date('Y'));
            
            $employeeTerminated = Termination::whereMonth('termination_date', $month)
                                 ->whereYear('termination_date', $year)
                                 ->get();
            $totalWeekends = 0;
            foreach ($employees as $employee) {
                $attendances = [];
                $employeePresent = $employeeLeave = $employeeLate = $employeeEarlyLeave = $employeeOverTime = $totalPresent = $totalLeave = $totalHolidays = $daysShortOf8Hours = $totalAbsent = $totalOverTime = $totalEarlyleave = $totalLate = 0;
                $totalWorkHours = 0;
                $shortHours = 0;
            
                // Employee joining date logic
                $joiningDate = $employee->company_doj;
                $joiningTimestamp = strtotime($joiningDate);
                $joiningDay = (int) date('d', $joiningTimestamp);
                $joiningMonth = (int) date('m', $joiningTimestamp);
                $joiningYear = (int) date('Y', $joiningTimestamp);
            
                // Check if employee is terminated or resigned
                $terminationDate = $employeeTerminated->where('employee_id', $employee->id)->first();
                $resignationDate = $employee->date_of_exit;
            
                // Determine the last working day for the employee
                if ($terminationDate) {
                    $lastWorkingDay = strtotime($terminationDate->termination_date);
                } elseif ($resignationDate) {
                    $lastWorkingDay = strtotime($resignationDate);
                } else {
                    $lastWorkingDay = strtotime(sprintf('%s-%s-%02d', $year, $month, $totalDaysInMonth));
                }
            
                // Adjust eligible days in the current month for mid-joining or mid-termination
                $isMidMonthJoining = ($joiningYear == $year && $joiningMonth == $month);
                $eligibleDaysInMonth = $isMidMonthJoining ? $totalDaysInMonth - ($joiningDay - 1) : $totalDaysInMonth;
            
                foreach ($dates as $day) {
                    $date = sprintf('%s-%s-%02d', $year, $month, $day);
                    $currentDateTimestamp = strtotime($date);
    
                    // Skip days before joining
                    if ($isMidMonthJoining && $day < $joiningDay) {
                        $attendances[$day] = 'N/A';
                        continue;
                    }
            
                    // Skip days after termination/resignation
                    elseif ($currentDateTimestamp > $lastWorkingDay) {
                        $attendances[$day] = 'N/A';
                        continue;
                    }
            
                    $dayOfWeek = date('N', strtotime($date));
                    $status = '';
                    $backgroundColor = 'FFFFFF';
                    $workTime = 0;
            
                    if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                        $status = 'Week-End';
                        $backgroundColor = '051C4B';
                        $totalWeekends++;
                        $workTime = 8;
                    } else {
                        $holiday = Holiday::where('date', $date)->first();
                        $leave = Leave::where('employee_id', $employee->id)
                            ->where('start_date', '<=', $date)
                            ->where('end_date', '>=', $date)
                            ->whereIn('status', ['Approve', 'Pending'])
                            ->first();
                        $attendancesForDay = AttendanceEmployee::where('employee_id', $employee->id)
                            ->whereDate('date', $date)
                            ->get();
            
                        if ($holiday) {
                            $status = 'Holiday';
                            $backgroundColor = '00B8D9';
                            $totalHolidays++;
                            $workTime = 8;
                        } elseif ($leave) {
                            if($leave->leavetype == 'full'){
                                $status = 'Full-Day';
                                $backgroundColor = 'FFAB00';
                                $totalLeave++;
                                $workTime = 8;
                            }
                            elseif($leave->leavetype == 'half'){
                                $status = 'Half-Day';
                                $backgroundColor = 'FFAB00';
                                $totalLeave += 0.5;
                                $workTime = 4;
                                goto restAttendance;
                            }
                            else{
                                $status = 'Short-Day';
                                $backgroundColor = 'FFAB00';
                                $totalLeave += 0.25 ;
                                $workTime = 6;
                                goto restAttendance;
                            }
                        } elseif ($attendancesForDay->isNotEmpty()) {
                            restAttendance:

                            foreach ($attendancesForDay as $attendance) {
                                if ($attendance->clock_in && $attendance->clock_out) {
                                    $clockInTime = strtotime($attendance->clock_in);
                                    $clockOutTime = strtotime($attendance->clock_out);
                            
                                    if ($clockOutTime > $clockInTime) {
                                        // Remove seconds for precise calculation
                                        $clockInTime = strtotime(date('Y-m-d H:i:00', $clockInTime));
                                        $clockOutTime = strtotime(date('Y-m-d H:i:00', $clockOutTime));
                            
                                        // Add the difference in seconds to total time
                                        $workTime += ($clockOutTime - $clockInTime);
                                    }
                                }
                            }

                            $totalMinutes = floor($workTime / 60); // Convert total seconds to total minutes
                            $hours = floor($totalMinutes / 60);    // Extract total hours
                            $minutes = $totalMinutes % 60;         // Extract remaining minutes
                            
                            $workTime = $workTime / 3600;
                            
                            if($status == 'Half-Day'){
                                $timeFormat = sprintf('%02d:%02d Hrs', $hours, $minutes);
                                if ($workTime < 4) {
                                    $daysShortOf8Hours++;
                                    $shortHours += ceil(4 - $workTime);
                                }
                                $backgroundColor = $workTime < 4 ? '#FF5630' : '#36B37E';
                                $status = "<span class='badge text-white' style='background: " . $backgroundColor . "'>P ($timeFormat) - <span class='badge badge-warning text-white'>Half-Day Leave</span></span>";
                                $totalPresent++;
                            }
                            elseif($status == 'Short-Day'){
                                $timeFormat = sprintf('%02d:%02d Hrs', $hours, $minutes);
                                if ($workTime < 6) {
                                    $daysShortOf8Hours++;
                                    $shortHours += ceil(6 - $workTime);
                                }
                                $backgroundColor = $workTime < 6 ? '#FF5630' : '#36B37E';
                                $status = "<span class='badge text-white' style='background: " . $backgroundColor . "'>P ($timeFormat) - <span class='badge badge-warning text-white'>Short-Day Leave</span></span>";
                                $totalPresent++;
                            }
                            else{
                                $timeFormat = sprintf('%02d:%02d Hrs', $hours, $minutes);
                                if ($workTime < 8) {
                                    $daysShortOf8Hours++;
                                    $shortHours += ceil(8 - $workTime);
                                }
                                $backgroundColor = $workTime < 8 ? '#FF5630' : '#36B37E';
                                $status = "<span class='badge text-white' style='background: " . $backgroundColor . "'>P ($timeFormat)</span>";
                                $totalPresent++;
                            }
                        } else {
                            $status = 'Absent';
                            $backgroundColor = 'FF5630';
                            $totalAbsent++;
                        }
                    }
            
                    $totalWorkHours += $workTime;
                    $attendances[$day] = $status;
                }
                
                $employeesAttendance[] = [
                    'name' => $employee->name,
                    'attendance' => $attendances,
                    'present' => $employeePresent,
                    'leave' => $employeeLeave,
                    'late' => $employeeLate,
                    'early_leave' => $employeeEarlyLeave,
                    'overtime' => $employeeOverTime,
                    'backgroundColor' => $backgroundColor,
                ];

            }
            
            if (!in_array($status, $statuses)) $totalPresent++;
    
            // Summary data
            $data['totalOvertime'] = $totalOverTime;
            $data['totalEarlyLeave'] = $totalEarlyleave;
            $data['totalLate'] = $totalLate;
            $data['totalPresent'] = $totalPresent;
            $data['totalLeave'] = $totalLeave;
            $data['curMonth'] = $month . '-' . $year;
    
            return view('report.monthlyAttendance', compact('employeesAttendance', 'branch', 'department', 'dates', 'data'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /* short-days issue */
    public function exportCsv($filter_month, $branch, $department)
    {
        $data['branch'] = __('All');
        $data['department'] = __('All');
        
        $todayDate = Carbon::today()->toDateString();
        
        // Set up month and dates
        $currentDate = strtotime($filter_month);
        $month = date('m', $currentDate);
        $year = date('Y', $currentDate);
        $data['curMonth'] = date('M-Y', $currentDate);
        $todayMonth = Carbon::today()->month;
        $todayYear = Carbon::today()->year;
    
        // Retrieve employees based on branch and department filters
        $employees = Employee::where('is_active', 1)->where('created_by', \Auth::user()->creatorId());
        
        if (isset($filter_month) && $todayYear.'-'.$todayMonth != $filter_month){
            $employees = Employee::where('is_active', 1)->where(function($query) {
                            $query->where(DB::raw('YEAR(company_doj)'), '<', now()->year)
                                  ->orWhere(function($q) {
                                      $q->where(DB::raw('YEAR(company_doj)'), '=', now()->year)
                                        ->where(DB::raw('MONTH(company_doj)'), '<', now()->month);
                                  });
                        })
                        ->where('created_by', \Auth::user()->creatorId());
        }
        
        if ($branch != 0) {
            $employees->where('branch_id', $branch);
            $data['branch'] = Branch::find($branch)->name ?? '';
        }
        if ($department != 0) {
            $employees->where('department_id', $department);
            $data['department'] = Department::find($department)->name ?? '';
        }
        $employees = $employees->get();
        
        $totalDaysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
        $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
        $todayDate =Carbon::today(); // Get today's date
        $currentMonth = $todayDate->month; // Get the current month
        $currentYear = $todayDate->year; // Get the current year
        
        if ($month == $currentMonth && $year == $currentYear) {
            $endDay = $todayDate->day - 1;
        } else {
            $endDay = date('t', mktime(0, 0, 0, $month, 1, $year));
        }
        // Calculate date range for the last 7 days excluding today
        // $endDay = date('t', mktime(0, 0, 0, $month, 1, $year));
        $startDay = 1;
        $dates = range($startDay, $endDay);
        $requiredHours = count($dates) * 8; 
    
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Set headers
        $columns = array_merge(['Employee'], array_map(function ($day) use ($month, $year) {
            return sprintf('%02d-%02d-%04d', $day, $month, $year);
        }, $dates));
        $sheet->fromArray($columns, NULL, 'A1');
    
        // Auto-size all columns to fit content
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnID = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    
        // Make the first row and first column bold
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:A' . $sheet->getHighestRow())->getFont()->setBold(true);
    
        // Freeze the first row and first column
        $sheet->freezePane('B2');
    
        // Initialize summary variables
        $employeeSummaries = [];
    
        $row = 2; // Data starts from row 2
       
        $filter_year = request()->input('year', date('Y'));
        $employeeTerminated = Termination::whereMonth('termination_date', $filter_month)
                                 ->whereYear('termination_date', $filter_year)
                                 ->get();

        foreach ($employees as $employee) {
            $attendances = [$employee->name];
            $totalPresent = $totalAbsent = $totalLeave = $totalHolidays = $totalWeekends = $daysShortOf8Hours = 0;
            $totalWorkHours = 0;
            $shortHours = 0;
        
            // Employee joining date logic
            $joiningDate = $employee->company_doj;
            $joiningTimestamp = strtotime($joiningDate);
            $joiningDay = (int) date('d', $joiningTimestamp);
            $joiningMonth = (int) date('m', $joiningTimestamp);
            $joiningYear = (int) date('Y', $joiningTimestamp);
        
            // Check if employee is terminated or resigned
            $terminationDate = $employeeTerminated->where('employee_id', $employee->id)->first();
            $resignationDate = $employee->date_of_exit;
        
            // Determine the last working day for the employee
            if ($terminationDate) {
                $lastWorkingDay = strtotime($terminationDate->termination_date);
            } elseif ($resignationDate) {
                $lastWorkingDay = strtotime($resignationDate);
            } else {
                $lastWorkingDay = strtotime(sprintf('%s-%s-%02d', $year, $month, $totalDaysInMonth));
            }
        
            // Adjust eligible days in the current month for mid-joining or mid-termination
            $isMidMonthJoining = ($joiningYear == $year && $joiningMonth == $month);
            $eligibleDaysInMonth = $isMidMonthJoining ? $totalDaysInMonth - ($joiningDay - 1) : $totalDaysInMonth;
        
            foreach ($dates as $day) {
                $backgroundColor = '';
                $date = sprintf('%s-%s-%02d', $year, $month, $day);
                $currentDateTimestamp = strtotime($date);

                // Skip days before joining
                if ($isMidMonthJoining && $day < $joiningDay) {
                    $attendances[$day] = 'N/A';
                    $cell = $sheet->getCellByColumnAndRow(count($attendances), $row);
                    continue;
                }
        
                // Skip days after termination/resignation
                elseif ($currentDateTimestamp > $lastWorkingDay) {
                    $attendances[$day] = 'N/A';
                    $cell = $sheet->getCellByColumnAndRow(count($attendances), $row);
                    continue;
                }
        
                $dayOfWeek = date('N', strtotime($date));
                $status = '';
                $backgroundColor = 'FFFFFF';
                $workTime = 0;
        
                if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                    $status = 'Week-End';
                    $backgroundColor = '051C4B';
                    $totalWeekends++;
                    $workTime = 8;
                } else {
                    $holiday = Holiday::where('date', $date)->first();
                    $leave = Leave::where('employee_id', $employee->id)
                        ->where('start_date', '<=', $date)
                        ->where('end_date', '>=', $date)
                        ->whereIn('status', ['Approve', 'Pending'])
                        ->first();
                    $attendancesForDay = AttendanceEmployee::where('employee_id', $employee->id)
                        ->whereDate('date', $date)
                        ->get();
        
                    if ($holiday) {
                        $status = 'Holiday';
                        $backgroundColor = '00B8D9';
                        $totalHolidays++;
                        $workTime = 8;
                    } elseif ($leave) {
                        if($leave->leavetype == 'full'){
                            $status = 'Full-Day';
                            $backgroundColor = 'FFAB00';
                            $totalLeave++;
                            $workTime = 8;
                        }
                        elseif($leave->leavetype == 'half'){
                            $status = 'Half-Day';
                            $backgroundColor = 'FFAB00';
                            $totalLeave += 0.5;
                            $workTime = 4;
                            goto restAttendance;
                        }
                        else{
                            $status = 'Short-Day';
                            $backgroundColor = 'FFAB00';
                            $totalLeave += 0.25 ;
                            $workTime = 6;
                            goto restAttendance;
                        }
                    } elseif ($attendancesForDay->isNotEmpty()) {
                        restAttendance:
                        foreach ($attendancesForDay as $attendance) {
                            if ($attendance->clock_in && $attendance->clock_out) {
                                $clockInTime = strtotime($attendance->clock_in);
                                $clockOutTime = strtotime($attendance->clock_out);
                        
                                if ($clockOutTime > $clockInTime) {
                                    // Remove seconds for precise calculation
                                    $clockInTime = strtotime(date('Y-m-d H:i:00', $clockInTime));
                                    $clockOutTime = strtotime(date('Y-m-d H:i:00', $clockOutTime));
                        
                                    // Add the difference in seconds to total time
                                    $workTime += ($clockOutTime - $clockInTime);
                                }
                            }
                        }

                        $totalMinutes = floor($workTime / 60); // Convert total seconds to total minutes
                        $hours = floor($totalMinutes / 60);    // Extract total hours
                        $minutes = $totalMinutes % 60;         // Extract remaining minutes
                        
                        $workTime = $workTime / 3600;
                        $workTime = $workTime > 8 ? 8 : $workTime ;
                        if($status == 'Half-Day'){
                            $timeFormat = sprintf('%02d:%02d Hrs', $hours, $minutes);
                            if ($workTime < 4) {
                                $daysShortOf8Hours++;
                                $shortHours += ceil(4 - $workTime);
                            }
                            $backgroundColor = $workTime < 4 ? 'FF5630' : '36B37E';
                            $status = "P ($timeFormat) - Half-Day Leave";
                            $totalPresent += 0.5;
                        }
                        elseif($status == 'Short-Day'){
                            $timeFormat = sprintf('%02d:%02d Hrs', $hours, $minutes);
                            if ($workTime < 6) {
                                $daysShortOf8Hours++;
                                $shortHours += ceil(6 - $workTime);
                            }
                            $backgroundColor = $workTime < 6 ? 'FF5630' : '36B37E';
                            $status = "P ($timeFormat) - Short-Day Leave";
                            $totalPresent += 0.75;
                        }
                        else{
                            $timeFormat = sprintf('%02d:%02d Hrs', $hours, $minutes);
                            if ($workTime < 8) {
                                $daysShortOf8Hours++;
                                $shortHours += ceil(8 - $workTime);
                            }
                            $backgroundColor = $workTime < 8 ? 'FF5630' : '36B37E';
                            $status = "P ($timeFormat)";
                            $totalPresent++;
                        }
                        
                    } else {
                        $status = 'Absent';
                        $backgroundColor = 'FF5630';
                        $totalAbsent++;
                    }
                }
        
                $totalWorkHours += $workTime;
                $attendances[$day] = $status;
        
                // Apply background and text color
                $cell = $sheet->getCellByColumnAndRow(count($attendances), $row);
                $sheet->getStyle($cell->getCoordinate())->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($backgroundColor);
                if ($backgroundColor !== 'FFFFFF') {
                    $sheet->getStyle($cell->getCoordinate())->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
                }
            }
        
            $sheet->fromArray($attendances, NULL, 'A' . $row);
            $row++;
        
            // Salary calculation adjusted for mid-month joiners and terminations
            $dailyRate = $employee->salary > 0 ? round($employee->salary / $totalDaysInMonth, 2) : 0;
        
            $hourlyRate = round($dailyRate / 8, 2);
            $hourlyDeduction = round($shortHours * $hourlyRate, 2);
            $absentDeduction = round($totalAbsent * $dailyRate, 2);
        
            $deductions = round(($absentDeduction == '' ? '0' : $absentDeduction) + $hourlyDeduction, 2);
            $eligibleSalary = round($dailyRate * ($totalPresent + $totalAbsent + $totalLeave + $totalHolidays + $totalWeekends));
            $finalSalary = round($eligibleSalary - $absentDeduction - $hourlyDeduction, 2);
        
            // Store summary
            $employeeSummaries[] = [
                'Employee' => $employee->name,
                'Present Days' => $totalPresent,
                'Absent Days' => $totalAbsent,
                'Leave Days' => $totalLeave,
                'Holiday Days' => $totalHolidays,
                'Weekend Days' => $totalWeekends,
                'Days Short of 8 Hours' => $daysShortOf8Hours,
                'Total Work Hours' => round($totalWorkHours, 2),
                'Short Hours' => $shortHours,
                'Salary' => $employee->salary,
                'Total Eligible Salary' => round($eligibleSalary),
                'Amount Deducted for Absent Days' => $absentDeduction,
                'Amount Deducted for Short Hours' => $hourlyDeduction,
                'Final Calculated Salary' => $finalSalary,
            ];
        }
        
        
        /*
        // To View the salary structure on browser
        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Add summary table to sheet
        $summaryStartRow = $row + 2;
        $sheet->fromArray(['Employee', 'Present Days', 'Absent Days', 'Leaves', 'Holidays', 'Weekends', 'Days Short of 8 Hours', 'Working Hours', 'Short Hours', 'Salary', 'Total Eligible Salary', 'Deduction for Absent Days', 'Deduction for Short Hours', 'Final Calculated Salary'], NULL, "A$summaryStartRow");
        $sheet->getStyle("A$summaryStartRow:M$summaryStartRow")->getFont()->setBold(true);
    
        $row = $summaryStartRow + 1;
        foreach ($employeeSummaries as $summary) {
            $sheet->fromArray(array_values($summary), NULL, "A$row");
            $row++;
        }
    
        // Center-align all columns and apply border
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]
        ]);
    
        // Use Html writer to generate HTML output
        $writer = new Html($spreadsheet);
    
        // Capture HTML output
        ob_start();
        $writer->save('php://output');
        $htmlContent = ob_get_clean();
    
        // Return the HTML content in the view
        return view('attendance_report.attendance_report', compact('htmlContent'));
        */
        
        
        // Add summary table to sheet
        $summaryStartRow = $row + 2;
        $sheet->fromArray(['Employee', 'Present Days', 'Absent Days', 'Leave Days', 'Holiday Days', 'Weekend Days', 'Days Short of 8 Hours', 'Total Work Hours', 'Short Hours', 'Salary', 'Total Eligible Salary', 'Deduction for Absent Days', 'Deduction for Short Hours', 'Final Calculated Salary', ''], NULL, "A$summaryStartRow");
        $sheet->getStyle("A$summaryStartRow:M$summaryStartRow")->getFont()->setBold(true);
    
        $row = $summaryStartRow + 1;
        foreach ($employeeSummaries as $summary) {
            $sheet->fromArray(array_values($summary), NULL, "A$row");
            $row++;
        }
    
        // Center-align all columns and apply border
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]
        ]);
    
        // Finalize file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Attendance_Report_' . $data['curMonth'] . '.xlsx';
        $filePath = storage_path($fileName);
        $writer->save($filePath);
    
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    /* short-days issue */
    
}
