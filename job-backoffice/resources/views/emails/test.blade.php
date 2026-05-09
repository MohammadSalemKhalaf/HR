@component('emails.layout', ['headerTitle' => 'Test Email', 'headerSubtitle' => 'SMTP Configuration Verification'])
    <h2>Hello {{ $username }},</h2>

    <p>This is a test email from <strong>Karaaj EMS</strong> to verify that your email notification system is working correctly.</p>

    <div class="info-box">
        <strong>Email Test Details</strong>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">✓ SMTP Connection Successful</div>
        </div>
        <div class="info-row">
            <div class="info-label">Sent To:</div>
            <div class="info-value">{{ auth()->user()->email }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Timestamp:</div>
            <div class="info-value">{{ now()->format('M d, Y H:i:s A') }}</div>
        </div>
    </div>

    <p style="margin-top: 20px;">If you received this email, your email notification system is working properly. You will now receive notifications for:</p>

    <ul style="margin: 15px 0;">
        <li>Task assignments and completions</li>
        <li>Leave request approvals and rejections</li>
        <li>Weekly activity reports</li>
        <li>Important HR announcements</li>
    </ul>

    <div class="divider"></div>

    <p style="font-size: 12px; color: #9ca3af;">
        This is an automated test email from Karaaj EMS. If you did not request this test, please ignore it.
    </p>
@endcomponent
