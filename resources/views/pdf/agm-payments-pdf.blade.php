<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $year }} AGM Payments Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .container {
            padding: 20px;
        }
        /* Centered Hierarchical Header */
        .header-section {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header-section img {
            width: 45px;
            height: auto;
            margin-bottom: 4px;
        }
        .header-section h1 {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-section h2 {
            margin: 2px 0 0 0;
            color: #334155;
            font-size: 11px;
            font-weight: 600;
        }
        .header-section p {
            margin: 3px 0 0 0;
            color: #2EA3F2;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        /* Generated Timestamp (Top Right) */
        .timestamp-box {
            text-align: right;
            font-size: 8px;
            font-style: italic;
            color: #64748b;
            margin-bottom: 8px;
        }
        /* Data Table Styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        /* Footer */
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="container">

        <!-- Centered Header -->
        <div class="header-section">
            <img src="{{ public_path('images/logo.png') }}" alt="ROSSET-SWA Logo">
            <h1>Rongo Sub County Teachers Welfare Association</h1>
            <h2>(ROSSET-SWA)</h2>
            <p>{{ $year }} AGM Payments Report</p>
        </div>

        <!-- Generated At Timestamp Right Above Table -->
        <div class="timestamp-box">
            Generated at: {{ $generatedAt }}
        </div>

        <!-- Payments Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 25%;">Member Number</th>
                    <th style="width: 45%;">Name</th>
                    <th style="width: 22%;" class="text-right">Amount (Ksh)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $index => $payment)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $payment->user->membership_number ?? 'N/A' }}</td>
                        <td>
                            {{ 
                                trim(
                                    ($payment->user->salutation ?? '') . ' ' . 
                                    ($payment->user->first_name ?? '') . ' ' . 
                                    ($payment->user->last_name ?? '')
                                ) ?: 'N/A' 
                            }}
                        </td>
                        <td class="text-right">{{ number_format($payment->amount) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="color: #64748b; padding: 20px;">
                            No payment records found for the {{ $year }} AGM.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>ROSSET-SWA Official Financial Records &bull; Page 1 of 1</p>
        </div>

    </div>

</body>
</html>