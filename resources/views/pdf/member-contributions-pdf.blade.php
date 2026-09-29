<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Member Contributions Report</title>
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
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-section img {
            width: 50px;
            height: auto;
            margin-bottom: 6px;
        }
        .header-section h1 {
            margin: 0;
            color: #0f172a;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-section h2 {
            margin: 3px 0 0 0;
            color: #334155;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .header-section p {
            margin: 3px 0 0 0;
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        /* Professional Member Details Box */
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 15px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 6px;
            font-size: 10px;
            vertical-align: top;
        }
        .meta-table td strong {
            color: #0f172a;
        }
        /* Data Table Styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        /* Footer & Generated Timestamp */
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        .footer p {
            margin: 2px 0;
        }
    </style>
</head>
<body>

    <div class="container">

        <!-- Centered Hierarchical Header with Logo and Full Association Name -->
        <div class="header-section">
            <img src="{{ public_path('images/logo.png') }}" alt="ROSSET-SWA Logo">
            <h1>Rongo Sub County Teachers Welfare Association</h1>
            <h2>(ROSSET-SWA)</h2>
            <p>Member Benevolence Contributions Report</p>
        </div>

        <!-- Member Details Box (Single Row Layout) -->
        <div class="meta-box">
            <table class="meta-table">
                <tr>
                    <td style="width: 45%;"><strong>Name:</strong> {{ $memberName }}</td>
                    <td style="width: 30%;"><strong>Membership No:</strong> {{ $membershipNumber }}</td>
                    <td style="width: 25%;"><strong>TSC No:</strong> {{ $tscNumber }}</td>
                </tr>
            </table>
        </div>

        <!-- Contribution History Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">#</th>
                    <th>Date</th>
                    <th>Case No.</th>
                    <th>Affected Member Name</th>
                    <th>Affected Member No.</th>
                    <th>Amount</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributionHistory as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $transaction->paid_at ? $transaction->paid_at->setTimezone('Africa/Nairobi')->format('d-m-Y g:i a') : 'N/A' }}</td>
                        <td>{{ $transaction->case_number ?? 'N/A' }}</td>
                        <td>
                            {{
                                trim(
                                    optional($transaction->benevolenceCase?->member)->salutation . ' ' .
                                    optional($transaction->benevolenceCase?->member)->first_name . ' ' .
                                    optional($transaction->benevolenceCase?->member)->last_name
                                ) ?: 'N/A'
                            }}
                        </td>
                        <td>
                            {{
                                optional($transaction->benevolenceCase?->member)->tsc_number
                                ??
                                optional($transaction->benevolenceCase?->member)->membership_number
                                ??
                                'N/A'
                            }}
                        </td>
                        <td>KSH {{ number_format($transaction->amount ?? 0) }}</td>
                        <td>{{ $transaction->reference_number ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="color: #64748b; padding: 20px;">
                            No contribution records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer with Generated Timestamp -->
        <div class="footer">
            <p>Generated At {{ $generatedAt }}</p>
            {{-- <p>This is an electronically generated official document from the ROSSET-SWA Member Portal.</p> --}}
        </div>

    </div>

</body>
</html>