@component('emails.layouts.app', ['headerSubtitle' => 'Leave Request Approved'])
    <div class="greeting">Hello {{ $leave->employee->user->name }},</div>

    <div class="description">
        Good news! Your leave request has been <strong>approved</strong> by your manager.
    </div>

    <div class="card success">
        <div class="info-row">
            <div class="info-label">Leave Type:</div>
            <div class="info-value"><strong>{{ ucfirst(str_replace('_', ' ', $leave->type ?? 'Leave')) }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">From:</div>
            <div class="info-value">{{ $leave->start_date?->format('F d, Y') ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">To:</div>
            <div class="info-value">{{ $leave->end_date?->format('F d, Y') ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Duration:</div>
            <div class="info-value"><strong>{{ $leave->start_date && $leave->end_date ? $leave->start_date->diffInDays($leave->end_date) + 1 : 'N/A' }} days</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Approved By:</div>
            <div class="info-value">{{ $leave->approvedBy?->name ?? 'Manager' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value"><span class="badge success">✓ APPROVED</span></div>
        </div>
    </div>

    <div class="button-wrapper">
        <a href="{{ url('/employee/leaves') }}" class="button success">View Leave History</a>
    </div>

    <div class="description" style="font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 20px;">
        Your leave has been officially recorded in the system. You can view your leave balance and approved leave
        dates in your employee dashboard. Enjoy your time off!
    </div>

    <div class="timestamp">
        {{ now()->format('M d, Y g:i A') }}
    </div>
@endcomponent
