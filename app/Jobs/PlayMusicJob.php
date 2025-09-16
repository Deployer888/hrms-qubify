<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class PlayMusicJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $employeeId;

    /**
     * Create a new job instance.
     */
    public function __construct($employeeId)
    {
        $this->employeeId = $employeeId;
        Log::info("PlayMusicJob created for employee ID: {$employeeId}");
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("Executing PlayMusicJob for employee ID: {$this->employeeId}");
        $url = url('/play-music/' . $this->employeeId);
    
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                Log::info("HTTP request to {$url} successful");
            } else {
                Log::error("HTTP request to {$url} failed with status {$response->status()}");
            }
        } catch (\Exception $e) {
            Log::error("HTTP request to {$url} failed with exception: {$e->getMessage()}");
        }
    }
}
