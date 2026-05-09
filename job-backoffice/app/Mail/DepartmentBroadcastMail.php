<?php

namespace App\Mail;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DepartmentBroadcastMail extends Mailable
{
    public function __construct(
        protected Employee $managerEmployee,
        protected Department $department,
        protected User $recipient,
        protected array $payload
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            to: $this->recipient->email,
            subject: ($this->payload['type_label'] ?? 'Department Notification') . ': ' . ($this->payload['title'] ?? 'Update'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.department-broadcast',
            with: [
                'managerEmployee' => $this->managerEmployee,
                'department' => $this->department,
                'recipient' => $this->recipient,
                'payload' => $this->payload,
            ],
        );
    }
}
