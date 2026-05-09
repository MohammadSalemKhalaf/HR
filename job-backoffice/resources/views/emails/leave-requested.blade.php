@component('emails.layouts.app', ['headerSubtitle' => 'New Leave Request'])
    <div class="greeting">Hello {{ $manager->name }},</div>

    <div class="description">
        <strong>{{ $leave->employee->user->name }}</strong> from your team has submitted a leave request that requires your approval.
    </div>

    <div class="card info">
        <div class="info-row">
            <div class="info-label">Employee:</div>
            <div class="info-value">{{ $leave->employee->user->name }}</div>
        </div>
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
            <div class="info-value"><strong>{{ $leave->days_count }} days</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value"><span class="badge pending">⏳ PENDING APPROVAL</span></div>
        </div>
    </div>

    <div class="button-wrapper">
        <a href="{{ url('/manager/leaves') }}" class="button">Review & Approve</a>
    </div>

    <div class="description" style="font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 20px;">
        Please review this leave request and take action within 24 hours. You can approve or reject the request
        in your manager dashboard. Your decision will be communicated to the employee automatically.
    </div>

    <div class="timestamp">
        {{ now()->format('M d, Y g:i A') }}
    </div>
@endcomponent
