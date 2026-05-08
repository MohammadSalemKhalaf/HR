<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Karaaj HR-SaaS' }}</title>
    <style>
        * { margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header-logo {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 5px;
        }
        .header-subtitle {
            font-size: 13px;
            opacity: 0.85;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 20px;
        }
        .description {
            color: #555;
            margin-bottom: 25px;
            font-size: 14px;
            line-height: 1.7;
        }
        .card {
            background: #f8f9fa;
            border-left: 4px solid #0284c7;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .card.success {
            border-left-color: #10b981;
            background: #f0fdf4;
        }
        .card.warning {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }
        .card.danger {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        .card.info {
            border-left-color: #0284c7;
            background: #f0f9ff;
        }
        .info-row {
            display: table;
            width: 100%;
            margin: 12px 0;
            font-size: 14px;
        }
        .info-label {
            display: table-cell;
            width: 100px;
            font-weight: 600;
            color: #0f172a;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            color: #555;
            padding-left: 20px;
            word-break: break-word;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.high {
            background: #fecaca;
            color: #991b1b;
        }
        .badge.medium {
            background: #fcd34d;
            color: #78350f;
        }
        .badge.low {
            background: #d1fae5;
            color: #065f46;
        }
        .badge.success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge.rejected {
            background: #fecaca;
            color: #991b1b;
        }
        .badge.pending {
            background: #e0e7ff;
            color: #3730a3;
        }
        .button-wrapper {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 40px;
            background-color: #0284c7;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0369a1;
        }
        .button.success {
            background-color: #10b981;
        }
        .button.success:hover {
            background-color: #059669;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 25px 0;
        }
        .footer {
            background-color: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            color: #6b7280;
            font-size: 12px;
            margin: 8px 0;
        }
        .footer-link {
            color: #0284c7;
            text-decoration: none;
        }
        .timestamp {
            color: #9ca3af;
            font-size: 12px;
            text-align: right;
            margin-top: 15px;
        }
        .company-info {
            font-size: 13px;
            color: #555;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        @media (max-width: 600px) {
            .container { padding: 10px; }
            .content { padding: 20px; }
            .header { padding: 30px 20px; }
            .footer { padding: 20px; }
            .info-label { width: 80px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <div class="header-logo">Karaaj HR-SaaS</div>
                <div class="header-subtitle">{{ $headerSubtitle ?? 'Employee Management System' }}</div>
            </div>

            <div class="content">
                {{ $slot }}
            </div>

            <div class="footer">
                <div class="footer-text">
                    <strong>Karaaj HR-SaaS</strong><br>
                    Professional Employee Management System
                </div>
                <div class="footer-text">
                    © {{ date('Y') }} Karaaj. All rights reserved.
                </div>
                <div class="footer-text">
                    <a href="{{ url('/') }}" class="footer-link">Visit Portal</a> •
                    <a href="{{ url('/') }}" class="footer-link">Preferences</a>
                </div>
                <div class="footer-text" style="margin-top: 15px; opacity: 0.7;">
                    This is an automated email. Please do not reply directly to this message.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
