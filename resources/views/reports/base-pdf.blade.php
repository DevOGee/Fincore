<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title ?? 'FinCore Report' }}</title>
    <style>
        @page {
            margin: 50px 25px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #4f46e5;
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            color: #6b7280;
            margin: 5px 0 0;
            font-size: 14px;
        }
        .header .period {
            color: #6b7280;
            margin: 5px 0 0;
            font-size: 14px;
            font-weight: 600;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding: 10px 0;
            font-size: 10px;
            color: #6b7280;
        }
        .page-number:after {
            content: "Page " counter(page);
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            color: #4f46e5;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            text-align: left;
            font-weight: 600;
        }
        td {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-bold {
            font-weight: 600;
        }
        .text-success {
            color: #10b981;
        }
        .text-danger {
            color: #ef4444;
        }
        .text-warning {
            color: #f59e0b;
        }
        .kpi-card {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9fafb;
        }
        .kpi-value {
            font-size: 24px;
            font-weight: 600;
            margin: 5px 0;
        }
        .kpi-label {
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .chart-container {
            margin: 20px 0;
            page-break-inside: avoid;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 40px 0 10px;
            display: inline-block;
        }
        .signature-label {
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FinCore</h1>
        <div class="subtitle">{{ $title ?? 'Financial Report' }}</div>
        @if(isset($period))
            <div class="period">{{ $period }}</div>
        @endif
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        <div>Generated on {{ now()->format('F j, Y \a\t g:i A') }} by {{ $user->name }}</div>
        <div class="page-number"></div>
    </div>
</body>
</html>
