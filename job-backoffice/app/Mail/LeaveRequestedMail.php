<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use App\Models\Leave;

class LeaveRequestedMail extends Mailable
{
    public function __construct(
        protected Leave $leave,
        protected $manager
    ) {}

    public function envelope(): Envelope
    {
        $employeeName = $this->leave->employee->user?->name ?? 'Team Member';

        return new Envelope(
            from: config('mail.from.address'),
            to: $this->manager->email,
            subject: 'New Leave Request from ' . $employeeName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leave-requested',
            with: [
                'leave' => $this->leave,
                'manager' => $this->manager,
            ],
        );
    }
}
