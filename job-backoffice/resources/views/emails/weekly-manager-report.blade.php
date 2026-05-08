@component('emails.layout', ['headerTitle' => 'Weekly Team Report', 'headerSubtitle' => 'Manager dashboard summary'])
    <h2>Hello {{ $manager->user?->name }},</h2>

    <p>Here's your team's summary for the week of <strong>{{ now()->startOfWeek()->format('M d') }}</strong> to <strong>{{ now()->endOfWeek()->format('M d, Y') }}</strong>.</p>

    <div class="info-box">
        <strong>Team Overview</strong>
        <div class="info-row">
            <div class="info-label">Team Members:</div>
            <div class="info-value">{{ $teamSize ?? 0 }}</div>
        </div>
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
            <div class="info-label">Overdue Tasks:</div>
            <div class="info-value">{{ $overdueCount ?? 0 }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Leave Requests:</div>
            <div class="info-value">{{ $pendingLeaves ?? 0 }} pending</div>
        </div>
        <div class="info-row">
            <div class="info-label">Average Attendance:</div>
            <div class="info-value">{{ $avgAttendance ?? 0 }}%</div>
        </div>
    </div>

    <div class="button-wrapper">
        <a href="{{ url('/manager/dashboard') }}" class="button">View Manager Dashboard</a>
    </div>

    <p>Review your team's performance and take necessary actions to keep projects on track.</p>

    <div class="divider"></div>

    <p style="font-size: 12px; color: #9ca3af;">
        This is an automated team report from Karaaj EMS. You will receive this every Friday.
    </p>
@endcomponent
