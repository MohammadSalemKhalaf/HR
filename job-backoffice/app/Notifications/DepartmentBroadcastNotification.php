<?php

namespace App\Notifications;

use App\Mail\DepartmentBroadcastMail;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Notifications\Notification;

class DepartmentBroadcastNotification extends Notification
{
    public function __construct(
        protected Employee $managerEmployee,
        protected Department $department,
        protected array $payload
    ) {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'department.broadcast',
            'title' => $this->payload['title'] ?? 'Department Notification',
            'message' => $this->payload['message'] ?? '',
            'notification_type' => $this->payload['type'] ?? 'general_announcement',
            'notification_type_label' => $this->payload['type_label'] ?? 'General Announcement',
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'sender_name' => $this->managerEmployee->user?->name,
            'recipient_name' => $notifiable->name ?? null,
        ];
    }

    public function toMail($notifiable)
    {
        return new DepartmentBroadcastMail($this->managerEmployee, $this->department, $notifiable, $this->payload);
    }
}
