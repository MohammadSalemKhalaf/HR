<?php

namespace App\Mail;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DailyEmployeeReportMail extends Mailable
{
    public function __construct(
        protected Employee $employee,
        protected AttendanceRecord $record,
        protected array $reportData,
        protected $manager
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            to: $this->manager?->email,
            subject: 'Daily Work Report - ' . ($this->employee->user?->name ?? 'Employee'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-employee-report',
            with: [
                'employee' => $this->employee,
                'record' => $this->record,
                'reportData' => $this->reportData,
                'manager' => $this->manager,
            ],
        );
    }
}
