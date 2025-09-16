<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class UpdatePaidLeave extends Command
{
    protected $signature = 'update:paidleave';
    protected $description = 'Add 1.5 days of paid leave every month and reset yearly';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $employees = DB::table('employees')->where('is_active', 1)->where('is_probation', 0)->get();
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->month;
        $currentYear = $currentDate->year;

        foreach ($employees as $employee) {
            $joiningDate = Carbon::parse($employee->company_doj);
            $joiningMonth = $joiningDate->month;
            $joiningYear = $joiningDate->year;

            // Add 1.5 leave days if the joining month is less than the current month
            // or if it's the same month and the current date is greater than or equal to the joining date
            /*if (
                $joiningYear < $currentYear || 
                ($joiningYear == $currentYear && $joiningMonth < $currentMonth) || 
                ($joiningYear == $currentYear && $joiningMonth == $currentMonth && $currentDate->day >= $joiningDate->day)
            ) {*/ 
                $employee = \App\Models\Employee::find($employee->id);
                if ($employee) {
                    // Check if current date is 3 months greater than $employee->company_doj
                    $companyDoj = \Carbon\Carbon::parse($employee->company_doj);
                    $threeMonthsLater = $companyDoj->addMonths(3);
            
                    if ($currentDate->greaterThanOrEqualTo($threeMonthsLater) && $employee->paid_leave_balance <= 30) {
                        $paid_leave_balance = $employee->paid_leave_balance + 1.5;
                        if($paid_leave_balance >= 30) $paid_leave_balance = 30;
                        $employee->paid_leave_balance = $paid_leave_balance;
                        $employee->save();
                    }
                }
            // }


            // Subtract the leave taken
            /*$leaves = DB::table('leaves')
                        ->where('employee_id', $employee->id)
                        ->where('leave_type_id', 3)
                        ->get();

            $totalLeaveDays = 0;

            foreach ($leaves as $leave) {
                $period = CarbonPeriod::create($leave->start_date, $leave->end_date);

                foreach ($period as $date) {
                    if (!$date->isWeekend()) {
                        $totalLeaveDays++;
                    }
                }
            }

            // Reset leave days to 0
            DB::table('employees')
                ->where('id', $employee->id)
                ->decrement('paid_leave_balance', $totalLeaveDays);
            */
            
            
            // Reset leaves yearly
            // if ($currentMonth == $joiningMonth && $currentDate->day == $joiningDate->day) {
            /*if ($currentDate->month == 1 && $currentDate->day == 1) {
                DB::table('employees')
                    ->where('id', $employee->id)
                    ->update(['paid_leave_balance' => 0]);
            }*/

            // Reset leaves yearly
            // if ($currentMonth == $joiningMonth && $currentDate->day == $joiningDate->day) {
            if ($currentDate->month == 1 && $currentDate->day == 1) {
                /*DB::table('employees')
                    ->where('id', $employee->id)
                    ->where('is_active', 1)
                    ->where('is_probation', 0)
                    ->update(['paid_leave_balance' => 0]);*/
                
                /*DB::table('leave_types')
                    ->update([
                        'Casual Leave' => 5,
                        'Sick Leave' => 5,
                        'Maternity Leaves' => 182,
                        'Paternity Leaves' => 15,
                        'Bereavement leave' => 3,
                        'Birthday leave' => 1,
                        ]);*/
            }
        }
    }
}
