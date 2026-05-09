@component('emails.layouts.app', ['headerSubtitle' => 'Daily Attendance & Work Summary'])
    <div class="greeting">Hello {{ $manager->name }},</div>

    <div class="description">
        Report submitted for employee <strong>{{ $reportData['employee_name'] ?? $employee->user?->name }}</strong>.
        Below is the daily work summary for {{ $reportData['date'] ?? now()->format('F d, Y') }}.
    </div>

    <div class="card info">
        <div class="info-row">
            <div class="info-label">Employee:</div>
            <div class="info-value"><strong>{{ $reportData['employee_name'] ?? 'N/A' }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Company:</div>
            <div class="info-value">{{ $reportData['company_name'] ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Department:</div>
            <div class="info-value">{{ $reportData['department_name'] ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date:</div>
            <div class="info-value">{{ $reportData['date'] ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Check-in:</div>
            <div class="info-value">{{ $reportData['check_in_time'] ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Check-out:</div>
            <div class="info-value">{{ $reportData['check_out_time'] ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Worked Hours:</div>
            <div class="info-value"><strong>{{ $reportData['worked_hours'] ?? '0m' }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Late Minutes:</div>
            <div class="info-value">{{ $reportData['late_label'] ?? 'On time' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tasks:</div>
            <div class="info-value">{{ $reportData['summary_line'] ?? 'No task summary available' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Attendance Status:</div>
            <div class="info-value">
                @php($status = strtolower((string) ($reportData['attendance_status'] ?? 'present')))
                @if($status === 'late')
                    <span class="badge warning">⚠ LATE</span>
                @elseif($status === 'left early')
                    <span class="badge danger">⏱ LEFT EARLY</span>
                @else
                    <span class="badge success">✓ PRESENT</span>
                @endif
            </div>
        </div>
    </div>

    <div class="card info" style="margin-top: 18px;">
        <div style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 12px;">Completed Tasks</div>

        @if(!empty($reportData['completed_task_titles']))
            <ul style="margin: 0; padding-left: 18px; color: #334155; line-height: 1.8;">
                @foreach($reportData['completed_task_titles'] as $taskTitle)
                    <li>{{ $taskTitle }}</li>
                @endforeach
            </ul>
        @else
            <div style="color: #64748b; font-size: 14px;">No completed tasks were recorded today.</div>
        @endif
    </div>

    <div class="button-wrapper">
        <a href="{{ url('/manager/attendance') }}" class="button">Review Attendance</a>
    </div>

    <div class="description" style="font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 20px;">
        This report was generated automatically after checkout to keep managers updated with day-end attendance and work progress.
    </div>

    <div class="timestamp">
        {{ now()->format('M d, Y g:i A') }}
    </div>
@endcomponent
