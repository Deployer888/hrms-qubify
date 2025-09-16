<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use App\Models\Commission;
use App\Models\Employee;
use App\Models\Loan;
use App\Mail\InvoiceSend;
use App\Mail\PayslipSend;
use App\Models\OtherPayment;
use App\Models\AttendanceEmployee;
use App\Models\Overtime;
use App\Models\PaySlip;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\SaturationDeduction;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PaySlipController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('Manage Pay Slip') || \Auth::user()->type == 'employee') {
            $employees = Employee::where(
                [
                    'created_by' => \Auth::user()->creatorId(),
                ]
            )->first();

            $month = [
                '01' => 'JAN',
                '02' => 'FEB',
                '03' => 'MAR',
                '04' => 'APR',
                '05' => 'MAY',
                '06' => 'JUN',
                '07' => 'JUL',
                '08' => 'AUG',
                '09' => 'SEP',
                '10' => 'OCT',
                '11' => 'NOV',
                '12' => 'DEC',
            ];

            $year = [
                '2020' => '2020',
                '2021' => '2021',
                '2022' => '2022',
                '2023' => '2023',
                '2024' => '2024',
                '2025' => '2025',
                '2026' => '2026',
                '2027' => '2027',
                '2028' => '2028',
                '2029' => '2029',
                '2030' => '2030',
            ];

            return view('payslip.index', compact('employees', 'month', 'year'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        //
    }
    
    public function store(Request $request)
    {
        // Validate the input data
        $validator = \Validator::make(
            $request->all(),
            [
                'month' => 'required',
                'year' => 'required',
            ]
        );
    
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }
    
        $month = $request->month;
        $year = $request->year;
    
        $formattedMonthYear = $year . '-' . $month;
        $creatorId = \Auth::user()->creatorId();
    
        // Check for already created payslips
        $existingPayslipEmployees = PaySlip::where('salary_month', $formattedMonthYear)
            ->where('created_by', $creatorId)
            ->pluck('employee_id')
            ->toArray();
    
        // Fetch all employees who should have a payslip
        $employees = Employee::where('created_by', $creatorId)
            ->where('company_doj', '<=', date($year . '-' . $month . '-t'))
            ->whereNotIn('id', $existingPayslipEmployees)
            ->get();
    
        // Check if any employee has a salary not set
        $employeeWithoutSalary = Employee::where('created_by', $creatorId)
            ->where('salary', '<=', 0)
            ->exists();
    
        if ($employeeWithoutSalary) {
            return redirect()->route('payslip.index')->with('error', __('Please set employee salary.'));
        }
    
        $numOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
        foreach ($employees as $employee) {
            $employeePresent = $weekend = $holidayCount = $employeeLeave = $lossPays = 0;
    
            // Calculate employee attendance
            for ($day = 1; $day <= $numOfDays; $day++) {
                $date = sprintf('%s-%s-%02d', $year, $month, $day);
                $dayOfWeek = date('N', strtotime($date)); // 1 = Monday, 7 = Sunday
    
                // Check if the day is a holiday
                $isHoliday = Holiday::where('date', $date)->exists();
    
                // Check if the day is a leave
                $isLeave = Leave::where('employee_id', $employee->id)
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->exists();
    
                if ($isHoliday) {
                    $holidayCount++;
                } elseif ($isLeave) {
                    $employeeLeave++;
                } elseif ($dayOfWeek == 6 || $dayOfWeek == 7) {
                    $weekend++;
                } else {
                    // Check attendance
                    $attendanceExists = AttendanceEmployee::where('employee_id', $employee->id)
                        ->whereDate('date', $date)
                        ->exists();
    
                    if ($attendanceExists) {
                        $employeePresent++;
                    } else {
                        $lossPays++;
                    }
                }
            }
    
            $totalWorkingDays = $employeePresent + $lossPays;
            $perDaySalary = $employee->salary / $numOfDays;
            $totalEarnings = ($totalWorkingDays + $weekend + $employeeLeave + $holidayCount) * $perDaySalary;
            $totalDeduction = $lossPays * $perDaySalary;
    
    
    
            // echo "employee = " . $employee->name . "<br>";
            // echo "employeePresent = " . $employeePresent . "<br>";
            // echo "weekend = " . $weekend . "<br>";
            // echo "holidayCount = " . $holidayCount . "<br>";
            // echo "employeeLeave = " . $employeeLeave . "<br>";
            // echo "lossPayDays = " . $lossPays . "<br>";
            
            // echo "totalWorkingDays = " .                $totalWorkingDays = $employeePresent + $lossPays;
            // echo "<br>";                
            // echo "perDaySalary = " .               $perDaySalary = $employee->salary/$numOfDays;
            // echo "<br>";                
            // echo "totalEarnings = " .               $totalEarnings = ($totalWorkingDays + $weekend + $employeeLeave + $holidayCount) * $perDaySalary;
            // echo "<br>";
            // echo "totalDeduction = " .               $totalDeduction = $lossPays * $perDaySalary;
            // echo "<br>";                
            // echo "net_payble = " .               $totalEarnings - $totalDeduction;
            // echo "<br><br><br><br><br><br>"; 
    
    
    
            // Create the payslip record
            $payslip = new PaySlip();
            $payslip->employee_id = $employee->id;
            $payslip->salary_month = $formattedMonthYear;
            $payslip->status = 0;
            $payslip->basic_salary = $employee->salary * 0.4;
            $payslip->hra = $employee->salary * 0.4;
            $payslip->special_allowance = $employee->salary * 0.2;
            $payslip->tds = 0; // Add TDS calculation if required
            $payslip->actual_payable_days = $numOfDays;
            $payslip->total_working_days = $totalWorkingDays;
            $payslip->loss_of_pay_days = $lossPays;
            $payslip->net_payble = $totalEarnings - $totalDeduction;
            $payslip->total_earnings = $totalEarnings;
            $payslip->total_deduction = $totalDeduction;
            $payslip->allowance = Employee::allowance($employee->id);
            $payslip->commission = Employee::commission($employee->id);
            $payslip->loan = Employee::loan($employee->id);
            $payslip->saturation_deduction = Employee::saturation_deduction($employee->id);
            $payslip->other_payment = Employee::other_payment($employee->id);
            $payslip->overtime = Employee::overtime($employee->id);
            $payslip->created_by = $creatorId;
    
            $payslip->save();
    
            // Notification logic
            $this->sendPayslipNotifications($payslip);
        }
    
        return redirect()->route('payslip.index')->with('success', __('Payslip successfully created.'));
    }
    
    /**
     * Send notifications via Slack, Telegram, or Twilio.
     *
     * @param  PaySlip $payslip
     * @return void
     */
    private function sendPayslipNotifications(PaySlip $payslip)
    {
        $setting = Utility::settings(\Auth::user()->creatorId());
        $month = date('M Y', strtotime($payslip->salary_month));
    
        if (isset($setting['monthly_payslip_notification']) && $setting['monthly_payslip_notification']) {
            $msg = __("Payslip generated for") . ' ' . $month . '.';
            Utility::send_slack_msg($msg);
        }
    
        if (isset($setting['telegram_monthly_payslip_notification']) && $setting['telegram_monthly_payslip_notification']) {
            $msg = __("Payslip generated for") . ' ' . $month . '.';
            Utility::send_telegram_msg($msg);
        }
    
        if (isset($setting['twilio_payslip_notification']) && $setting['twilio_payslip_notification']) {
            $employee = Employee::find($payslip->employee_id);
            $msg = __("Payslip generated for") . ' ' . $month . '.';
            if ($employee && $employee->phone) {
                Utility::send_twilio_msg($employee->phone, $msg);
            }
        }
    }

    public function destroy($id)
    {
        $payslip = PaySlip::find($id);
        $payslip->delete();

        return true;
    }

    public function showemployee($paySlip)
    {
        $payslip = PaySlip::find($paySlip);

        return view('payslip.show', compact('payslip'));
    }

    public function search_json(Request $request)
    {

        $formate_month_year = $request->datePicker;
        $validatePaysilp    = PaySlip::where('salary_month', '=', $formate_month_year)->where('created_by', \Auth::user()->creatorId())->get()->toarray();

        $data=[];
        if (empty($validatePaysilp))
         {
            $data=[];
            return $data;
        } 
        else 
        {
            $paylip_employee = PaySlip::select(
                [
                    'employees.id',
                    'employees.employee_id',
                    'employees.name',
                    // 'payslip_types.name as payroll_type',
                    'pay_slips.basic_salary',
                    'pay_slips.net_payble',
                    'pay_slips.id as pay_slip_id',
                    'pay_slips.status',
                    'employees.user_id',
                ]
            )->leftjoin(
                'employees',
                function ($join) use ($formate_month_year) {
                    $join->on('employees.id', '=', 'pay_slips.employee_id');
                    $join->on('pay_slips.salary_month', '=', \DB::raw("'" . $formate_month_year . "'"));
                    $join->leftjoin('payslip_types', 'payslip_types.id', '=', 'employees.salary_type');
                }
            )->where('employees.created_by', \Auth::user()->creatorId())->get();


            foreach ($paylip_employee as $employee) {

                if (Auth::user()->type == 'employee') {
                    if (Auth::user()->id == $employee->user_id) {
                        $tmp   = [];
                        $tmp[] = $employee->id;
                        $tmp[] = $employee->name;
                        // $tmp[] = $employee->payroll_type;
                        $tmp[] = $employee->pay_slip_id;
                        $tmp[] = !empty($employee->employees->salary) ? \Auth::user()->priceFormat($employee->employees->salary) : '-';
                        $tmp[] = !empty($employee->net_payble) ? \Auth::user()->priceFormat($employee->net_payble) : '-';
                        if ($employee->status == 1) {
                            $tmp[] = 'paid';
                        } else {
                            $tmp[] = 'unpaid';
                        }
                        $tmp[]  = !empty($employee->pay_slip_id) ? $employee->pay_slip_id : 0;
                        $data[] = $tmp;
                    }
                } else {

                    $tmp   = [];
                    $tmp[] = $employee->id;
                    $tmp[] = \Auth::user()->employeeIdFormat($employee->employee_id);
                    $tmp[] = $employee->name;
                    // $tmp[] = $employee->payroll_type;
                    $tmp[] = !empty($employee->employees->salary) ? \Auth::user()->priceFormat($employee->employees->salary) : '-';
                    $tmp[] = !empty($employee->net_payble) ? \Auth::user()->priceFormat($employee->net_payble) : '-';
                    if ($employee->status == 1) {
                        $tmp[] = 'Paid';
                    } else {
                        $tmp[] = 'UnPaid';
                    }
                    $tmp[]  = !empty($employee->pay_slip_id) ? $employee->pay_slip_id : 0;
                    $data[] = $tmp;
                }
            }

            return $data;
        }
    }

    public function paysalary($id, $date)
    {
        $employeePayslip = PaySlip::where('employee_id', '=', $id)->where('created_by', \Auth::user()->creatorId())->where('salary_month', '=', $date)->first();
        if (!empty($employeePayslip)) {
            $employeePayslip->status = 1;
            $employeePayslip->save();

            return redirect()->route('payslip.index')->with('success', __('Payslip Payment successfully.'));
        } else {
            return redirect()->route('payslip.index')->with('error', __('Payslip Payment failed.'));
        }
    }

    public function bulk_pay_create($date)
    {
        $Employees       = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->get();
        $unpaidEmployees = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->where('status', '=', 0)->get();

        return view('payslip.bulkcreate', compact('Employees', 'unpaidEmployees', 'date'));
    }

    public function bulkpayment(Request $request, $date)
    {
        $unpaidEmployees = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->where('status', '=', 0)->get();

        foreach ($unpaidEmployees as $employee) {
            $employee->status = 1;
            $employee->save();
        }

        return redirect()->route('payslip.index')->with('success', __('Payslip Bulk Payment successfully.'));
    }

    public function employeepayslip()
    {
        $employees = Employee::where(
            [
                'user_id' => \Auth::user()->id,
            ]
        )->first();

        $payslip = PaySlip::where('employee_id', '=', $employees->id)->get();

        return view('payslip.employeepayslip', compact('payslip'));
    }

    public function pdf($id, $month)
    {
        $payslip  = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
        $employee = Employee::find($payslip->employee_id);

        $employeeID = $employee->employeeIdFormat($employee->employee_id);

        $payslipDetail = Utility::employeePayslipDetail($id);
        
        // echo "<pre>";
        // print_r($payslip);
        // die;
        
        return view('payslip.pdf', compact('payslip', 'employee', 'payslipDetail', 'employeeID'));
    }

    public function send($id, $month)
    {
        $payslip  = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
        $employee = Employee::find($payslip->employee_id);

        $payslip->name  = $employee->name;
        $payslip->email = $employee->email;

        $payslipId    = Crypt::encrypt($payslip->id);
        $payslip->url = route('payslip.payslipPdf', $payslipId);

        $setings = Utility::settings();
        if ($setings['payroll_create'] == 1) {
            try {
                Mail::to($payslip->email)->send(new PayslipSend($payslip));
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Payslip successfully sent.') . (isset($smtp_error) ? $smtp_error : ''));
        }

        return redirect()->back()->with('success', __('Payslip successfully sent.'));
    }

    public function payslipPdf($id)
    {
        $payslipId = Crypt::decrypt($id);

        $payslip  = PaySlip::where('id', $payslipId)->where('created_by', \Auth::user()->creatorId())->first();
        $employee = Employee::find($payslip->employee_id);

        $payslipDetail = Utility::employeePayslipDetail($payslip->employee_id);

        return view('payslip.payslipPdf', compact('payslip', 'employee', 'payslipDetail'));
    }

    public function editEmployee($paySlip)
    {
        $payslip = PaySlip::find($paySlip);

        return view('payslip.salaryEdit', compact('payslip'));
    }

    public function updateEmployee(Request $request, $id)
    {


        if (isset($request->allowance) && !empty($request->allowance)) {
            $allowances   = $request->allowance;
            $allowanceIds = $request->allowance_id;
            foreach ($allowances as $k => $allownace) {
                $allowanceData         = Allowance::find($allowanceIds[$k]);
                $allowanceData->amount = $allownace;
                $allowanceData->save();
            }
        }


        if (isset($request->commission) && !empty($request->commission)) {
            $commissions   = $request->commission;
            $commissionIds = $request->commission_id;
            foreach ($commissions as $k => $commission) {
                $commissionData         = Commission::find($commissionIds[$k]);
                $commissionData->amount = $commission;
                $commissionData->save();
            }
        }

        if (isset($request->loan) && !empty($request->loan)) {
            $loans   = $request->loan;
            $loanIds = $request->loan_id;
            foreach ($loans as $k => $loan) {
                $loanData         = Loan::find($loanIds[$k]);
                $loanData->amount = $loan;
                $loanData->save();
            }
        }


        if (isset($request->saturation_deductions) && !empty($request->saturation_deductions)) {
            $saturation_deductionss   = $request->saturation_deductions;
            $saturation_deductionsIds = $request->saturation_deductions_id;
            foreach ($saturation_deductionss as $k => $saturation_deductions) {

                $saturation_deductionsData         = SaturationDeduction::find($saturation_deductionsIds[$k]);
                $saturation_deductionsData->amount = $saturation_deductions;
                $saturation_deductionsData->save();
            }
        }


        if (isset($request->other_payment) && !empty($request->other_payment)) {
            $other_payments   = $request->other_payment;
            $other_paymentIds = $request->other_payment_id;
            foreach ($other_payments as $k => $other_payment) {
                $other_paymentData         = OtherPayment::find($other_paymentIds[$k]);
                $other_paymentData->amount = $other_payment;
                $other_paymentData->save();
            }
        }


        if (isset($request->rate) && !empty($request->rate)) {
            $rates   = $request->rate;
            $rateIds = $request->rate_id;
            $hourses = $request->hours;

            foreach ($rates as $k => $rate) {
                $overtime        = Overtime::find($rateIds[$k]);
                $overtime->rate  = $rate;
                $overtime->hours = $hourses[$k];
                $overtime->save();
            }
        }


        $payslipEmployee                       = PaySlip::find($request->payslip_id);
        $payslipEmployee->allowance            = Employee::allowance($payslipEmployee->employee_id);
        $payslipEmployee->commission           = Employee::commission($payslipEmployee->employee_id);
        $payslipEmployee->loan                 = Employee::loan($payslipEmployee->employee_id);
        $payslipEmployee->saturation_deduction = Employee::saturation_deduction($payslipEmployee->employee_id);
        $payslipEmployee->other_payment        = Employee::other_payment($payslipEmployee->employee_id);
        $payslipEmployee->overtime             = Employee::overtime($payslipEmployee->employee_id);
        $payslipEmployee->net_payble           = Employee::find($payslipEmployee->employee_id)->get_net_salary();
        $payslipEmployee->save();

        return redirect()->route('payslip.index')->with('success', __('Employee payroll successfully updated.'));
    }
}
