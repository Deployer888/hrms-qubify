<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\User;

class DeactivateEmployees extends Command
{
    protected $signature = 'employees:deactivate';
    protected $description = 'Deactivate employees who have a date_of_exit set.';

    public function handle()
    {
        // Update employees who have a date_of_exit not null
        $employees = Employee::whereNotNull('date_of_exit')->get();

        foreach ($employees as $employee) {
            $employee->is_active = 0;
            $employee->save();

            $user = User::where('id', $employee->user_id)->first();
            if ($user) {
                $user->is_active = 0;
                $user->save();
            }
        }

        $this->info('Inactive employees have been updated.');
    }
}
