<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Mail\Mailable;
use App\Models\Employee;

/**
 * Weekly Employee Report Notification
 * Scheduled to run every Friday at 5 PM
 * Future: Integrate with AI summary generation
 */
class WeeklyEmployeeReportNotification extends Notification
{
    protected Employee $employee;
    protected array $reportData;

    public function __construct(Employee $employee, array $reportData = [])
    {
        $this->employee = $employee;
        $this->reportData = $reportData;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'report.weekly_employee',
            'title' => 'Your Weekly Summary Report',
            'tasks_completed' => $this->reportData['tasksCompleted'] ?? 0,
            'attendance_rate' => $this->reportData['attendanceRate'] ?? 0,
            'week_ending' => now()->endOfWeek()->toDateString(),
        ];
    }

    public function toMail($notifiable)
    {
        return new class($this->employee, $this->reportData) extends Mailable {
            public function __construct(
                protected Employee $employee,
                protected array $reportData
            ) {}

            public function build()
            {
                return $this->subject('Weekly Report: ' . now()->format('M d - ') . now()->addDays(7)->format('M d, Y'))
                    ->view('emails.weekly-employee-report')
                    ->with([
                        'employee' => $this->employee,
                        'tasksAssigned' => $this->reportData['tasksAssigned'] ?? 0,
                        'tasksCompleted' => $this->reportData['tasksCompleted'] ?? 0,
                        'tasksPending' => $this->reportData['tasksPending'] ?? 0,
                        'attendanceDays' => $this->reportData['attendanceDays'] ?? 0,
                        'leaveDays' => $this->reportData['leaveDays'] ?? 0,
                    ])
                    ->from(config('mail.from.address'), config('mail.from.name') . ' - Reports');
            }
        };
    }
}
