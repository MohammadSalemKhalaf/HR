<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header-subtitle {
            margin-top: 8px;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .content h2 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 20px;
            color: #0f172a;
        }
        .content p {
            margin: 0 0 15px 0;
            line-height: 1.6;
            color: #555;
        }
        .content ul {
            margin: 15px 0;
            padding-left: 20px;
            color: #555;
            line-height: 1.8;
        }
        .content li {
            margin: 8px 0;
        }
        .button-wrapper {
            margin: 25px 0;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #10b981;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #059669;
        }
        .info-box {
            background-color: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .info-box strong {
            color: #0c4a6e;
        }
        .info-row {
            display: table;
            width: 100%;
            margin: 10px 0;
        }
        .info-label {
            display: table-cell;
            width: 120px;
            font-weight: 600;
            color: #0f172a;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            color: #555;
            padding-left: 15px;
        }
        .priority-high {
            display: inline-block;
            background-color: #fecaca;
            color: #991b1b;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .priority-medium {
            display: inline-block;
            background-color: #fcd34d;
            color: #78350f;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .priority-low {
            display: inline-block;
            background-color: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            font-size: 12px;
            color: #6b7280;
        }
        .footer a {
            color: #0284c7;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <h1>{{ $headerTitle ?? 'Karaaj - HR Management' }}</h1>
                <div class="header-subtitle">{{ $headerSubtitle ?? 'Employee Management System' }}</div>
            </div>

            <div class="content">
                {{ $slot }}
            </div>

            <div class="footer">
                <p><strong>Karaaj HR Management System</strong></p>
                <p>© {{ date('Y') }} Karaaj. All rights reserved.</p>
                <p>This is an automated email from Karaaj EMS. Please do not reply directly.</p>
            </div>
        </div>
    </div>
</body>
</html>
