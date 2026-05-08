<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Models\Leave;
use App\Mail\LeaveRequestedMail;

class LeaveRequested extends Notification
{
    protected $leave;

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
            'type' => 'leave.requested',
            'leave_id' => $this->leave->id,
            'employee_name' => $this->leave->employee->user?->name,
            'leave_type' => $this->leave->type,
            'start_date' => $this->leave->start_date?->toDateString(),
            'end_date' => $this->leave->end_date?->toDateString(),
        ];
    }

    public function toMail($notifiable)
    {
        return new LeaveRequestedMail($this->leave, $notifiable);
    }
}
