<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Benevolence Case Report - {{ $case->case_number }}</title>
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
        /* Professional Meta Info Box */
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
        /* Summary Statistics Block */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .stats-table td {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: center;
            font-size: 9px;
            width: 25%;
        }
        .stats-table td strong {
            display: block;
            font-size: 11px;
            color: #0f172a;
            margin-top: 2px;
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
            <p>Benevolence Case Report &amp; Contribution Summary</p>
        </div>

        <!-- Case Metadata Box -->
        <div class="meta-box">
            <table class="meta-table">
                <tr>
                    <td style="width: 50%;"><strong>Case No:</strong> {{ $case->case_number }}</td>
                    <td style="width: 50%;"><strong>Category:</strong> {{ $case->category?->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>
                        <strong>Affected Member:</strong> 
                        {{ 
                            trim(
                                optional($case->member)->salutation . ' ' . 
                                optional($case->member)->first_name . ' ' . 
                                optional($case->member)->last_name
                            ) ?: 'N/A' 
                        }}
                    </td>
                    <td>
                        <strong>Member / TSC No:</strong> 
                        {{ optional($case->member)->tsc_number ?? optional($case->member)->membership_number ?? 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Status:</strong> {{ ucfirst($case->status) }}</td>
                    <td><strong>Date Logged:</strong> {{ $case->created_at ? $case->created_at->setTimezone('Africa/Nairobi')->format('d-m-Y g:i a') : 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Summary Statistics Row -->
        <table class="stats-table">
            <tr>
                <td>
                    Total Collected
                    <strong>KSH {{ number_format($totalAmountCollected) }}</strong>
                </td>
                <td>
                    Contributors
                    <strong>{{ $totalContributorsCount }} / {{ $totalSystemMembers }} ({{ $contributionPercentage }}%)</strong>
                </td>
                <td>
                    Target Amount
                    <strong>KSH {{ number_format($case->target_amount ?? 0) }}</strong>
                </td>
                <td>
                    Case Progress
                    <strong>{{ $case->target_amount > 0 ? round(($totalAmountCollected / $case->target_amount) * 100, 1) : 0 }}%</strong>
                </td>
            </tr>
        </table>

        <!-- Contributions History Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;" class="text-center">#</th>
                    <th>Date</th>
                    <th>Contributor Name</th>
                    <th>Membership / TSC No.</th>
                    <th>Amount (Ksh)</th>
                    <th>Payment Channel</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributions as $index => $contribution)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $contribution->created_at ? $contribution->created_at->setTimezone('Africa/Nairobi')->format('d-m-Y g:i a') : 'N/A' }}</td>
                        <td>
                            {{ 
                                trim(
                                    optional($contribution->user)->salutation . ' ' . 
                                    optional($contribution->user)->first_name . ' ' . 
                                    optional($contribution->user)->last_name
                                ) ?: 'N/A' 
                            }}
                        </td>
                        <td>
                            {{ 
                                optional($contribution->user)->tsc_number 
                                ?? optional($contribution->user)->membership_number 
                                ?? 'N/A' 
                            }}
                        </td>
                        <td>{{ number_format($contribution->amount ?? 0) }}</td>
                        <td>{{ $contribution->payment_channel ?? 'N/A' }}</td>
                        <td>{{ $contribution->reference_number ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="color: #64748b; padding: 20px;">
                            No contribution records found for this case.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer with Generated Timestamp -->
        <div class="footer">
            <p>Generated At {{ $generatedAt }}</p>
        </div>

    </div>

</body>
</html>