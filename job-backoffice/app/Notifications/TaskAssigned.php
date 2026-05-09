<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Mail\Mailable;
use App\Models\EmployeeTask;

class TaskAssigned extends Notification
{
    protected EmployeeTask $task;

    public function __construct(EmployeeTask $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'task.assigned',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'manager_employee_id' => $this->task->manager_employee_id,
            'priority' => $this->task->priority,
            'due_date' => $this->task->due_date?->toDateString(),
        ];
    }

    public function toMail($notifiable)
    {
        return new class($this->task, $notifiable) extends Mailable {
            public function __construct(
                protected EmployeeTask $task,
                protected $employee
            ) {}

            public function build()
            {
                return $this->to($this->employee->email)
                    ->subject('New Task Assigned: ' . $this->task->title)
                    ->view('emails.task-assigned')
                    ->with([
                        'task' => $this->task,
                        'employee' => $this->employee, // This is User object
                        'manager' => $this->task->manager, // This is Employee object
                        'company' => $this->task->manager?->department?->company,
                    ])
                    ->from(config('mail.from.address'), config('mail.from.name'));
            }
        };
    }
}

