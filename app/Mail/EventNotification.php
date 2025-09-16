<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Event;
use App\Models\Branch;

class EventNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $event;
    public $branch;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
        
        if($event->branch_id > 0)
            $this->branch = Branch::find($event->branch_id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Event Notification: ' . $this->event->title)
                    ->markdown('emails.eventNotification');
    }
}
