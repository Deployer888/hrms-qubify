<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateEmployeeClockOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:update-clockout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update clock_out time for employees with clock_out time of 00:00:00';
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDateTime = Carbon::now();
        
        DB::table('attendance_employees')
            ->where('clock_out', '00:00:00')
            ->update(['clock_out' => $currentDateTime->format('H:i:s')]);
            // ->update(['overtime' => $currentDateTime->format('H:i:s')]);

        $this->info('Employee clock_out times have been updated.');
    }
}
