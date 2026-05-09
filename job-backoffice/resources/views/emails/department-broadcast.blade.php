@component('emails.layouts.app', ['headerSubtitle' => $payload['type_label'] ?? 'Department Notification'])
    <div class="greeting">Hello {{ $recipient->name }},</div>

    <div class="description">
        A new <strong>{{ $payload['type_label'] ?? 'department notification' }}</strong> has been sent for the
        <strong>{{ $department->name }}</strong> department by <strong>{{ $managerEmployee->user?->name ?? 'your manager' }}</strong>.
    </div>

    <div class="card info">
        <div class="info-row">
            <div class="info-label">Title:</div>
            <div class="info-value"><strong>{{ $payload['title'] ?? 'Department Notification' }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Type:</div>
            <div class="info-value">
                <span class="badge pending">{{ strtoupper($payload['type_label'] ?? 'General Announcement') }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Department:</div>
            <div class="info-value">{{ $department->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">From:</div>
            <div class="info-value">{{ $managerEmployee->user?->name ?? 'Manager' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Audience:</div>
            <div class="info-value">
                {{ $payload['recipient_mode'] === 'all' ? 'Entire department' : (($payload['recipient_count'] ?? 1) . ' selected employee(s)') }}
            </div>
        </div>
    </div>

    <div class="card info" style="margin-top: 18px;">
        <div style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 12px;">Message</div>
        <div style="color: #334155; white-space: pre-wrap; line-height: 1.8;">{{ $payload['message'] ?? '' }}</div>
    </div>

    <div class="button-wrapper">
        <a href="{{ url('/employee/dashboard') }}" class="button">Open Dashboard</a>
    </div>

    <div class="timestamp">
        {{ now()->format('M d, Y g:i A') }}
    </div>
@endcomponent
