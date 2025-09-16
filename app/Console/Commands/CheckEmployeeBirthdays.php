<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Employee;

class CheckEmployeeBirthdays extends Command
{
    protected $signature = 'check:employee-birthdays';
    protected $description = 'Check for employee birthdays and set birthday flag';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentDate = Carbon::now()->format('m-d');

        $employees = Employee::whereRaw('DATE_FORMAT(dob, "%m-%d") = ?', [$currentDate])->get();
        
        if(count($employees) > 0) {
            $employees = Employee::all();

            foreach ($employees as $employee) {
                $employee->isBirthday = true;
                $employee->save();
            }
        }
    }
}

