<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Member Contributions Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .container {
            padding: 30px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo-cell {
            width: 60px;
            vertical-align: middle;
        }
        .logo-cell img {
            width: 50px;
            height: auto;
        }
        .title-cell {
            vertical-align: middle;
            text-align: right;
        }
        .title-cell h1 {
            margin: 0;
            color: #0f172a;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-cell p {
            margin: 3px 0 0 0;
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 0;
            font-size: 11px;
        }
        .meta-table td strong {
            color: #0f172a;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="container">

        <!-- Top Header with Logo -->
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('images/logo.png') }}" alt="ROSSET-SWA Logo">
                </td>
                <td class="title-cell">
                    <h1>ROSSET-SWA</h1>
                    <p>Member Benevolence Contributions Report</p>
                </td>
            </tr>
        </table>

        <!-- Member & Report Metadata Box -->
        <div class="meta-box">
            <table class="meta-table">
                <tr>
                    <td><strong>Member Name:</strong> {{ $memberName }}</td>
                    <td class="text-right"><strong>Generated At:</strong> {{ $generatedAt }}</td>
                </tr>
                <tr>
                    <td><strong>Membership No:</strong> {{ $membershipNumber }}</td>
                    <td class="text-right"><strong>Status:</strong> {{ $memberStatus }}</td>
                </tr>
                <tr>
                    <td><strong>Phone:</strong> {{ $phone }}</td>
                    <td class="text-right"><strong>Email:</strong> {{ $email }}</td>
                </tr>
                <tr>
                    <td><strong>School:</strong> {{ $school }}</td>
                    <td class="text-right"><strong>Total Records:</strong> {{ $contributionHistory->count() }}</td>
                </tr>
            </table>
        </div>

        <!-- Contribution History Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;" class="text-center">#</th>
                    <th>Date & Time</th>
                    <th>Transaction Number</th>
                    <th>Case Number</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributionHistory as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('d/m/Y g:ia') : 'N/A' }}</td>
                        <td>{{ $transaction->reference_number ?? 'N/A' }}</td>
                        <td>{{ $transaction->case_number ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="color: #64748b; padding: 20px;">
                            No contribution records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer Note -->
        <div class="footer">
            <p>This is an electronically generated official document from the ROSSET-SWA Member Portal.</p>
        </div>

    </div>

</body>
</html>