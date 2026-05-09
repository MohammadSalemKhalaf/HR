<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Mail\Mailable;
use App\Models\EmployeeTask;

class TaskCompleted extends Notification
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
            'type' => 'task.completed',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'employee_id' => $this->task->employee_id,
            'completed_at' => $this->task->completed_at?->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        return new class($this->task, $notifiable) extends Mailable {
            public function __construct(
                protected EmployeeTask $task,
                protected $manager
            ) {}

            public function build()
            {
                return $this->to($this->task->manager->user->email)
                    ->subject('Task Completed: ' . $this->task->title)
                    ->view('emails.task-completed')
                    ->with([
                        'task' => $this->task,
                        'manager' => $this->task->manager, // Employee object
                        'employee' => $this->task->employee, // Employee object
                    ])
                    ->from(config('mail.from.address'), config('mail.from.name'));
            }
        };
    }
}
