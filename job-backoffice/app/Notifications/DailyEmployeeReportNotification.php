<?php

namespace App\Notifications;

use App\Mail\DailyEmployeeReportMail;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Notifications\Notification;

class DailyEmployeeReportNotification extends Notification
{
    public function __construct(
        protected Employee $employee,
        protected AttendanceRecord $record,
        protected array $reportData,
        protected $manager
    ) {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'attendance.daily_report',
            'title' => 'Daily Employee Report',
            'employee_name' => $this->reportData['employee_name'] ?? $this->employee->user?->name,
            'company_name' => $this->reportData['company_name'] ?? $this->employee->company?->name,
            'department_name' => $this->reportData['department_name'] ?? $this->employee->department?->name,
            'date' => $this->reportData['date'] ?? now()->toDateString(),
            'summary_line' => $this->reportData['summary_line'] ?? null,
            'attendance_status' => $this->reportData['attendance_status'] ?? null,
        ];
    }

    public function toMail($notifiable)
    {
        return new DailyEmployeeReportMail($this->employee, $this->record, $this->reportData, $this->manager);
    }
}
