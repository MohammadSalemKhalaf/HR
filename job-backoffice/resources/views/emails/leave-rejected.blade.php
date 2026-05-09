@component('emails.layouts.app', ['headerSubtitle' => 'Leave Request Rejected'])
    <div class="greeting">Hello {{ $leave->employee->user->name }},</div>

    <div class="description">
        Unfortunately, your leave request has been <strong>rejected</strong> by your manager.
        Please review the details below.
    </div>

    <div class="card danger">
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
            <div class="info-label">Rejected By:</div>
            <div class="info-value">{{ $leave->approvedBy?->name ?? 'Manager' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value"><span class="badge rejected">✗ REJECTED</span></div>
        </div>
    </div>

    <div class="button-wrapper">
        <a href="{{ url('/employee/leaves') }}" class="button">Submit New Request</a>
    </div>

    <div class="description" style="font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 20px;">
        You can submit a new leave request with different dates or contact your manager directly for more information
        about the rejection. We're here to help!
    </div>

    <div class="timestamp">
        {{ now()->format('M d, Y g:i A') }}
    </div>
@endcomponent
