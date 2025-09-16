<?php

namespace App\Listeners;

use App\Events\EmployeeBirthday;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\PlayMusicJob;
use Illuminate\Support\Facades\Log;

class PlayMusicAfterCheckIn
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmployeeBirthday $event)
    {
        Log::info("PlayMusicAfterCheckIn listener handling event for employee ID: {$event->employeeId}");
        PlayMusicJob::dispatch($event->employeeId);
        Log::info("Dispatched PlayMusicJob for employee ID: {$event->employeeId}");
    }
}
