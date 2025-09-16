<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    private $fromEmail;
    private $fromName;
    private $update;

    /**
     * Create a new message instance.
     */
    public function __construct($leave, $fromEmail, $fromName, $update = null)
    {
        $this->leave = $leave;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->update = $update;
    }

    public function build()
    {
        $subject='Leave Request';
        if($this->update != null){
            $subject='Updated Leave Request';
        }
        return $this->view('email.leave_request_send')
                    ->from($this->fromEmail, $this->fromName)
                    ->subject($subject);
    }

}
