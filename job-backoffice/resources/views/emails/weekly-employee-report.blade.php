@component('emails.layout', ['headerTitle' => 'Weekly Employee Report', 'headerSubtitle' => 'Your weekly activity summary'])
    <h2>Hello {{ $employee->user?->name }},</h2>

    <p>Here's your weekly summary from <strong>{{ now()->startOfWeek()->format('M d') }}</strong> to <strong>{{ now()->endOfWeek()->format('M d, Y') }}</strong>.</p>

    <div class="info-box">
        <strong>This Week's Activity</strong>
        <div class="info-row">
            <div class="info-label">Tasks Assigned:</div>
            <div class="info-value">{{ $tasksAssigned ?? 0 }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tasks Completed:</div>
            <div class="info-value">{{ $tasksCompleted ?? 0 }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Pending Tasks:</div>
            <div class="info-value">{{ $tasksPending ?? 0 }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Attendance Days:</div>
            <div class="info-value">{{ $attendanceDays ?? 0 }}/5</div>
        </div>
        <div class="info-row">
            <div class="info-label">Leave Days:</div>
            <div class="info-value">{{ $leaveDays ?? 0 }}</div>
        </div>
    </div>

    <div class="button-wrapper">
        <a href="{{ url('/employee/dashboard') }}" class="button">View Full Dashboard</a>
    </div>

    <p>Keep up the excellent work! Your manager has visibility into your progress.</p>

    <div class="divider"></div>

    <p style="font-size: 12px; color: #9ca3af;">
        This is an automated weekly report from Karaaj EMS. You will receive this every Friday.
    </p>
@endcomponent
