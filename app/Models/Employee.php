<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Traits\BalanceCalculatorTrait;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory, Notifiable, BalanceCalculatorTrait;
    protected $fillable = [
        'user_id',
        'name',
        'dob',
        'gender',
        'phone',
        'address',
        'email',
        'password',
        'employee_id',
        'branch_id',
        'office_id',
        'department_id',
        'designation_id',
        'company_doj',
        'date_of_exit',
        'documents',
        'account_holder_name',
        'account_number',
        'bank_name',
        'bank_identifier_code',
        'branch_location',
        'tax_payer_id',
        'salary_type',
        'salary',
        'shift_start',
        'created_by',
        'is_team_leader',
        'team_leader_id',
        'is_probation',
        'clock_in_pin',
        'paid_leave_balance',
    ];

    protected $casts = [
        'company_doj' => 'date',
        'date_of_exit' => 'date',
        'dob' => 'date',
        'paid_leave_balance' => 'decimal:2',
    ];
    
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function documents()
    {
        return $this->hasMany('App\Models\EmployeeDocument', 'employee_id', 'employee_id')->get();
    }

    public function salary_type()
    {
        return $this->hasOne('App\Models\PayslipType', 'id', 'salary_type')->pluck('name')->first();
    }

    public function get_net_salary()
    {

        //allowance
        $allowances      = Allowance::where('employee_id', '=', $this->id)->get();
        $total_allowance = 0;
        foreach ($allowances as $allowance) {
            $total_allowance = $allowance->amount + $total_allowance;
        }

        //commission
        $commissions      = Commission::where('employee_id', '=', $this->id)->get();

        $total_commission = 0;
        foreach ($commissions as $commission) {
            $total_commission = $commission->amount + $total_commission;
        }

        //Loan
        $loans      = Loan::where('employee_id', '=', $this->id)->get();
        $total_loan = 0;
        foreach ($loans as $loan) {
            $total_loan = $loan->amount + $total_loan;
        }

        //Saturation Deduction
        $saturation_deductions      = SaturationDeduction::where('employee_id', '=', $this->id)->get();
        $total_saturation_deduction = 0;
        foreach ($saturation_deductions as $saturation_deduction) {
            $total_saturation_deduction = $saturation_deduction->amount + $total_saturation_deduction;
        }

        //OtherPayment
        $other_payments      = OtherPayment::where('employee_id', '=', $this->id)->get();
        $total_other_payment = 0;
        foreach ($other_payments as $other_payment) {
            $total_other_payment = $other_payment->amount + $total_other_payment;
        }

        //Overtime
        $over_times      = Overtime::where('employee_id', '=', $this->id)->get();
        $total_over_time = 0;
        foreach ($over_times as $over_time) {
            $total_work      = $over_time->number_of_days * $over_time->hours;
            $amount          = $total_work * $over_time->rate;
            $total_over_time = $amount + $total_over_time;
        }


        //Net Salary Calculate
        $advance_salary = $total_allowance + $total_commission - $total_loan - $total_saturation_deduction + $total_other_payment + $total_over_time;

        $employee       = Employee::where('id', '=', $this->id)->first();

        $net_salary     = (!empty($employee->salary) ? $employee->salary : 0) + $advance_salary;

        return $net_salary;
    }

    public static function allowance($id)
    {

        //allowance
        $allowances      = Allowance::where('employee_id', '=', $id)->get();
        $total_allowance = 0;
        foreach ($allowances as $allowance) {
            $total_allowance = $allowance->amount + $total_allowance;
        }

        $allowance_json = json_encode($allowances);

        return $allowance_json;
    }

    public static function commission($id)
    {
        //commission
        $commissions      = Commission::where('employee_id', '=', $id)->get();
        $total_commission = 0;

        foreach ($commissions as $commission) {
            $total_commission = $commission->amount + $total_commission;
        }
        $commission_json = json_encode($commissions);

        return $commission_json;
    }

    public static function loan($id)
    {
        //Loan
        $loans      = Loan::where('employee_id', '=', $id)->get();
        $total_loan = 0;
        foreach ($loans as $loan) {
            $total_loan = $loan->amount + $total_loan;
        }
        $loan_json = json_encode($loans);

        return $loan_json;
    }

    public static function saturation_deduction($id)
    {
        //Saturation Deduction
        $saturation_deductions      = SaturationDeduction::where('employee_id', '=', $id)->get();
        $total_saturation_deduction = 0;
        foreach ($saturation_deductions as $saturation_deduction) {
            $total_saturation_deduction = $saturation_deduction->amount + $total_saturation_deduction;
        }
        $saturation_deduction_json = json_encode($saturation_deductions);

        return $saturation_deduction_json;
    }

    public static function other_payment($id)
    {
        //OtherPayment
        $other_payments      = OtherPayment::where('employee_id', '=', $id)->get();
        $total_other_payment = 0;
        foreach ($other_payments as $other_payment) {
            $total_other_payment = $other_payment->amount + $total_other_payment;
        }
        $other_payment_json = json_encode($other_payments);

        return $other_payment_json;
    }

    public static function overtime($id)
    {
        //Overtime
        $over_times      = Overtime::where('employee_id', '=', $id)->get();
        $total_over_time = 0;
        foreach ($over_times as $over_time) {
            $total_work      = $over_time->number_of_days * $over_time->hours;
            $amount          = $total_work * $over_time->rate;
            $total_over_time = $amount + $total_over_time;
        }
        $over_time_json = json_encode($over_times);

        return $over_time_json;
    }

    public static function employee_id()
    {
        $employee = Employee::latest()->first();

        return !empty($employee) ? $employee->id + 1 : 1;
    }

    public function teamLeader()
    {
        return $this->belongsTo(Employee::class, 'team_leader_id');
    }

    public function getTeamLeaderNameAndId()
    {
        return $this->teamLeader()->select('id', 'name', 'email')->first();
    }

    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'branch_id');
    }

    public function phone()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'phone');
    }

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function designation()
    {
        return $this->hasOne('App\Models\Designation', 'id', 'designation_id');
    }

    public function salaryType()
    {
        return $this->hasOne('App\Models\PayslipType', 'id', 'salary_type');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function paySlip()
    {
        return $this->hasOne('App\Models\PaySlip', 'id', 'employee_id');
    }
    
    public function resignation()
    {
        return $this->hasOne('App\Models\Resignation', 'employee_id', 'id');
    }
    
    public function termination()
    {
        return $this->hasOne('App\Models\Termination', 'employee_id', 'id');
    }

    public function employeeLeaves()
    {
        return $this->hasMany('App\Models\Leave', 'employee_id', 'id')
            ->select('leaves.*', 'leave_types.title as category')
            ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
            ->whereYear('leaves.created_at', now()->year)
            ->orderBy('leaves.applied_on','DESC');
    }

    public function attendanceEmployees()
    {
        return $this->hasOne('App\Models\AttendanceEmployee', 'employee_id', 'id')
                ->whereDate('date', '=', now()->toDateString())->orderBy('id','ASC');
    }  

    public function attendance($date = null)
    {
        return $this->hasMany('App\Models\AttendanceEmployee', 'employee_id', 'id')
                    ->orderBy('clock_in', 'ASC'); // Order by ID (optional)
    }


    public function present_status($employee_id, $data)
    {
        return AttendanceEmployee::where('employee_id', $employee_id)->where('date', $data)->first();
    }
    public static function employee_name($name)
    {

        $employee = Employee::where('id', $name)->first();
        if (!empty($employee)) {
            return $employee->name;
        }
    }


    public static function login_user($name)
    {
        $user = User::where('id', $name)->first();
        return $user->name;
    }

    public static function employee_salary($salary)
    {
        $employee = Employee::where("salary", $salary)->first();
        if ($employee->salary == '0' || $employee->salary == '0.0') {
            return "-";
        } else {
            return $employee->salary;
        }
    }

    public function employeeIdFormat($number)
    {
        $settings = Utility::settings();
        
        // return $settings["employee_prefix"] . sprintf("%01d", $number);
        return $settings["employee_prefix"] . $number;
    }

    public function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }
    
    public function acknowledges()
    {
        return $this->hasMany(Acknowledge::class, 'emp_id');
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    /**
     * Get all location records for the employee.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(EmployeeLocation::class);
    }

    /**
     * Get the latest location record for the employee.
     */
    public function latestLocation()
    {
        return $this->hasOne(EmployeeLocation::class)->latest('time');
    }

    /**
     * Get today's location records for the employee.
     */
    public function todayLocations()
    {
        return $this->locations()->whereDate('time', today());
    }

    /**
     * Get all leave accrual ledger entries for the employee.
     */
    public function leaveAccrualLedger(): HasMany
    {
        return $this->hasMany(LeaveAccrualLedger::class);
    }

    /**
     * Get the adjusted date of joining (max of actual DOJ and 2024-04-01).
     */
    public function getAdjustedDojAttribute(): Carbon
    {
        $minDate = Carbon::parse('2024-04-01');
        $actualDoj = $this->company_doj ? Carbon::parse($this->company_doj) : $minDate;
        
        return $actualDoj->gt($minDate) ? $actualDoj : $minDate;
    }

    /**
     * Get the accrual start date (3 months after adjusted DOJ).
     */
    public function getAccrualStartDateAttribute(): Carbon
    {
        return $this->adjusted_doj->copy()->addMonths(3);
    }

    /**
     * Check if employee is active on the last day of a given month.
     */
    public function isActiveOnLastDayOfMonth(Carbon $month): bool
    {
        $lastDayOfMonth = $month->copy()->endOfMonth();
        
        // Check if employee was active on that date
        if (!$this->is_active) {
            return false;
        }
        
        // Check if employee had not exited by then
        if ($this->date_of_exit && Carbon::parse($this->date_of_exit)->lt($lastDayOfMonth)) {
            return false;
        }
        
        // Check if employee had joined by then
        if ($this->company_doj && Carbon::parse($this->company_doj)->gt($lastDayOfMonth)) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the current paid leave balance using real-time calculation.
     */
    public function getPaidLeaveBalanceAttribute($value): float
    {
        // Use real-time calculation instead of stored value
        return $this->getCurrentPaidLeaveBalance();
    }

    /**
     * Get expected paid leave balance based on DOJ and accrual rules.
     */
    public function getExpectedPaidLeaveBalance(): float
    {
        $calculator = app(\App\Services\AccrualCalculator::class);
        $now = Carbon::now('Asia/Kolkata');
        
        return $calculator->calculateExpectedBalance($this, $now);
    }

    /**
     * Get balance discrepancy (expected - actual).
     */
    public function getBalanceDiscrepancy(): float
    {
        $expected = $this->getExpectedPaidLeaveBalance();
        $actual = $this->getTotalAccruedLeave();
        
        return round($expected - $actual, 2);
    }

    /**
     * Get missing accrual months for this employee.
     */
    public function getMissingAccrualMonths(): array
    {
        $calculator = app(\App\Services\AccrualCalculator::class);
        $now = Carbon::now('Asia/Kolkata');
        
        // Get expected eligible months using company_doj (calculator will handle adjustment)
        $adjustedDoj = $calculator->calculateAdjustedDoj($this->company_doj);
        $expectedMonths = $calculator->getEligibleMonths($adjustedDoj, $now);
        
        // Get existing accrual months (both cron and backfill are valid)
        $existingMonths = $this->leaveAccrualLedger()
            ->whereIn('source', ['cron', 'backfill'])
            ->pluck('year_month')
            ->toArray();
        
        return array_diff($expectedMonths, $existingMonths);
    }

    /**
     * Check if employee's balance is correct.
     */
    public function isBalanceCorrect(): bool
    {
        $discrepancy = abs($this->getBalanceDiscrepancy());
        $missingMonths = $this->getMissingAccrualMonths();
        
        return $discrepancy < 0.01 && empty($missingMonths);
    }

    /**
     * Get detailed balance breakdown with validation.
     */
    public function getDetailedBalanceBreakdown(): array
    {
        $breakdown = $this->getBalanceBreakdown();
        
        return array_merge($breakdown, [
            'expected_balance' => $this->getExpectedPaidLeaveBalance(),
            'balance_discrepancy' => $this->getBalanceDiscrepancy(),
            'missing_months' => $this->getMissingAccrualMonths(),
            'is_correct' => $this->isBalanceCorrect(),
            'doj' => $this->company_doj,
            'adjusted_doj' => $this->adjusted_doj,
            'accrual_start_month' => $this->accrual_start_month,
        ]);
    }

}
