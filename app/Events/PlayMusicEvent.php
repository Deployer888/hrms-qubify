<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayMusicEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $empId;

    /**
     * Create a new event instance.
     */
    public function __construct($empId)
    {
        $this->empId = $empId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Update the database to trigger the music
        DB::table('employees')->update([
            'employee_id' => $this->empId,
            'isBirthday' => 0,
        ]);
        
        return new Channel('music-trigger');
        
        return [
            new PrivateChannel('channel-name'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'message' => 'Time to celebrate!',
        ];
    }
}
