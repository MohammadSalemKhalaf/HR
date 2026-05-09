<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\EmployeeTask;
use Carbon\Carbon;

class DailyEmployeeReportService
{
    public function build(Employee $employee, AttendanceRecord $record): array
    {
        $checkInAt = $record->check_in_at;
        $checkOutAt = $record->check_out_at;
        $workdayStart = Carbon::parse($record->attendance_date->toDateString() . ' ' . config('ems.attendance.workday_start', '09:00'));
        $workdayEnd = Carbon::parse($record->attendance_date->toDateString() . ' ' . config('ems.attendance.workday_end', '17:00'));

        $lateMinutes = 0;
        if ($checkInAt && $checkInAt->greaterThan($workdayStart)) {
            $lateMinutes = $workdayStart->diffInMinutes($checkInAt);
        }

        $workedMinutes = 0;
        if ($checkInAt && $checkOutAt) {
            $workedMinutes = max(0, $checkInAt->diffInMinutes($checkOutAt));
        }

        $expectedShiftMinutes = $workdayStart->diffInMinutes($workdayEnd);
        $attendanceStatus = 'Present';

        if ($lateMinutes > 0) {
            $attendanceStatus = 'Late';
        } elseif ($checkOutAt && $workedMinutes > 0 && $workedMinutes < $expectedShiftMinutes) {
            $attendanceStatus = 'Left Early';
        }

        $tasks = EmployeeTask::query()
            ->where('employee_id', $employee->id)
            ->get(['title', 'status']);

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'completed')->count();
        $pendingTasks = max(0, $totalTasks - $completedTasks);

        return [
            'employee_name' => $employee->user?->name ?? 'Employee',
            'company_name' => $employee->company?->name ?? 'N/A',
            'department_name' => $employee->department?->name ?? 'N/A',
            'date' => $record->attendance_date?->format('F d, Y') ?? now()->format('F d, Y'),
            'check_in_time' => $checkInAt?->format('h:i A') ?? 'N/A',
            'check_out_time' => $checkOutAt?->format('h:i A') ?? 'N/A',
            'worked_minutes' => $workedMinutes,
            'worked_hours' => $this->formatMinutes($workedMinutes),
            'late_minutes' => $lateMinutes,
            'late_label' => $lateMinutes > 0 ? $lateMinutes . ' minutes' : 'On time',
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'completed_task_titles' => $tasks->where('status', 'completed')->pluck('title')->values()->all(),
            'attendance_status' => $attendanceStatus,
            'summary_line' => "Completed {$completedTasks} out of {$totalTasks} tasks today",
        ];
    }

    private function formatMinutes(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours <= 0 && $remainingMinutes <= 0) {
            return '0m';
        }

        if ($hours <= 0) {
            return $remainingMinutes . 'm';
        }

        return $hours . 'h ' . $remainingMinutes . 'm';
    }
}
