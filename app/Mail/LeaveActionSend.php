<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class LeaveActionSend extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($leave)
    {
        $this->leave = $leave;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->approvedBy(Auth::user());
        return $this->view('email.leave_action_send')->with(['leave'=>$this->leave,'approved_by'=>$data])->subject('Regarding the Approval or Rejection of Leave.');
    }
    
    private function approvedBy($user)
    {   $data = [];
        if ($user && $user->type == 'hr')
        {
            $data['by'] = "HR Department";
            $data['name'] = $user->name;
            # code...
        }
        elseif ($user->type == 'employee')
        {
            $data['by'] = "Team Leader";
            $data['name'] = $user->name;
            # code...
        }
        else
        {
            $data['by'] = "CEO";
            $data['name'] = $user->name;
        }
        return $data;
    }
}
