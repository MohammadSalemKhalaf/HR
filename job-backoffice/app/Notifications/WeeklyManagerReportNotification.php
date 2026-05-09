<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Mail\Mailable;
use App\Models\Employee;

/**
 * Weekly Manager Report Notification
 * Scheduled to run every Friday at 5 PM
 * Provides team overview and key metrics
 * Future: Integrate with AI insights and recommendations
 */
class WeeklyManagerReportNotification extends Notification
{
    protected Employee $manager;
    protected array $reportData;

    public function __construct(Employee $manager, array $reportData = [])
    {
        $this->manager = $manager;
        $this->reportData = $reportData;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'report.weekly_manager',
            'title' => 'Your Weekly Team Report',
            'team_size' => $this->reportData['teamSize'] ?? 0,
            'tasks_completed' => $this->reportData['tasksCompleted'] ?? 0,
            'pending_leaves' => $this->reportData['pendingLeaves'] ?? 0,
            'week_ending' => now()->endOfWeek()->toDateString(),
        ];
    }

    public function toMail($notifiable)
    {
        return new class($this->manager, $this->reportData) extends Mailable {
            public function __construct(
                protected Employee $manager,
                protected array $reportData
            ) {}

            public function build()
            {
                return $this->subject('Team Report: ' . now()->format('M d - ') . now()->addDays(7)->format('M d, Y'))
                    ->view('emails.weekly-manager-report')
                    ->with([
                        'manager' => $this->manager,
                        'teamSize' => $this->reportData['teamSize'] ?? 0,
                        'tasksAssigned' => $this->reportData['tasksAssigned'] ?? 0,
                        'tasksCompleted' => $this->reportData['tasksCompleted'] ?? 0,
                        'tasksPending' => $this->reportData['tasksPending'] ?? 0,
                        'overdueCount' => $this->reportData['overdueCount'] ?? 0,
                        'pendingLeaves' => $this->reportData['pendingLeaves'] ?? 0,
                        'avgAttendance' => $this->reportData['avgAttendance'] ?? 0,
                    ])
                    ->from(config('mail.from.address'), config('mail.from.name') . ' - Reports');
            }
        };
    }
}
