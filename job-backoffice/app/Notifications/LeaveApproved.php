<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Mail\Mailable;
use App\Models\Leave;

class LeaveApproved extends Notification
{
    protected Leave $leave;

    public function __construct(Leave $leave)
    {
        $this->leave = $leave;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'leave.approved',
            'leave_id' => $this->leave->id,
            'title' => 'Your leave request has been approved',
            'start_date' => $this->leave->start_date?->toDateString(),
            'end_date' => $this->leave->end_date?->toDateString(),
        ];
    }

    public function toMail($notifiable)
    {
        return new class($this->leave) extends Mailable {
            public function __construct(
                protected Leave $leave
            ) {}

            public function build()
            {
                return $this->to($this->leave->employee->user->email)
                    ->subject('Leave Request Approved')
                    ->view('emails.leave-approved')
                    ->with([
                        'leave' => $this->leave,
                    ])
                    ->from(config('mail.from.address'), config('mail.from.name'));
            }
        };
    }
}
