<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Transactions Records</title>
    <style>
        @page {
            margin: 15px 18px 30px 18px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 7.5px;
            color: #000000;
            margin: 0;
            padding: 0;
            line-height: 1.15;
            position: relative;
        }

        /* Watermark styling */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 220px;
            height: auto;
            opacity: 0.08;
            z-index: -1000;
        }

        .container {
            padding: 0;
            position: relative;
            z-index: 1;
        }

        .header-section {
            text-align: center;
            padding-bottom: 7px;
            margin-bottom: 8px;
        }

        .header-section img {
            width: 36px;
            height: auto;
            margin-bottom: 3px;
        }

        .header-section h1 {
            margin: 0;
            color: #000000;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .header-section h2 {
            margin: 2px 0 0 0;
            color: #000000;
            font-size: 9.5px;
            font-weight: bold;
        }

        .report-title {
            text-align: center;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 5px 0;
        }

        /* Generated At positioned on the far right right above the table */
        .table-meta-top {
            text-align: right;
            font-size: 5.5px;
            font-style: italic;
            color: #333333;
            margin-bottom: 2px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        /* Thinner, lighter, visible borders */
        .data-table th,
        .data-table td {
            border: 0.5px solid #555555;
            padding: 3px 5px;
            vertical-align: middle;
            text-align: center;
        }

        .data-table th {
            background-color: #f8f8f8;
            color: #000000;
            font-size: 6.5px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            font-weight: bold;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        /* Unified data styling matching date/monospace text formatting */
        .data-table td {
            font-family: monospace;
            font-size: 6.5px;
            color: #111111;
        }

        /* Specific alignments and overrides */
        .col-type,
        .transaction-type,
        .col-member,
        .col-description {
            text-align: left !important;
        }

        .col-description {
            text-transform: none !important;
        }

        .amount {
            font-weight: bold;
        }

        .transaction-type {
            text-transform: uppercase;
        }

        .empty-state {
            color: #000000;
            padding: 12px !important;
            text-align: center !important;
            font-family: Helvetica, Arial, sans-serif !important;
        }
    </style>
</head>

<body>
    <!-- Centered Watermark Logo -->
    <img src="{{ public_path('images/logo.png') }}" alt="Watermark" class="watermark">

    <div class="container">
        <div class="header-section">
            <img src="{{ public_path('images/logo.png') }}" alt="ROSSET-SWA Logo">
            <h1> Rongo Sub County Teachers Welfare Association </h1>
            <h2> (ROSSET-SWA) </h2>
        </div>
        <div class="report-title"> TRANSACTIONS RECORDS </div>
        
        <!-- Generated At placed on the far right right above the table -->
        <div class="table-meta-top">
            Generated At: {{ $generatedAt }}
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th class="col-member">Member</th>
                    <th>Mem No</th>
                    <th>Reference</th>
                    <th>Amount (Ksh)</th>
                    <th>Phone</th>
                    <th class="col-type">Type</th>
                    <th class="col-description">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    @php
                        $parsedDate = $tx->created_at ? $tx->created_at->setTimezone('Africa/Nairobi') : null;
                        
                        // Normalise phone number back from 2547XXXXXXXX / 2541XXXXXXXX to 07XXXXXXXX / 01XXXXXXXX
                        $rawPhone = $tx->phone_number ?? '';
                        if (str_starts_with($rawPhone, '254')) {
                            $normalizedPhone = '0' . substr($rawPhone, 3);
                        } else {
                            $normalizedPhone = $rawPhone ?: '—';
                        }
                    @endphp
                    <tr>
                      <td style="white-space: nowrap;">{{ $parsedDate ? $parsedDate->format('d-M-y') : '—' }}</td>
                      <td style="white-space: nowrap;">{{ $parsedDate ? $parsedDate->format('h:i A') : '—' }}</td>
                        <td class="col-member">
                            @if ($tx->user)
                                {{ trim(($tx->user->first_name ?? '') . ' ' . ($tx->user->last_name ?? '')) }}
                            @else
                                <span style="font-style: italic;">Unknown Member</span>
                            @endif
                        </td>
                        <td>{{ $tx->user?->membership_number ?? '—' }}</td>
                        <td>{{ $tx->reference_number ?? '—' }}</td>
                        <td class="amount">{{ number_format($tx->amount ?? 0, 0) }}</td>
                        <td>{{ $normalizedPhone }}</td>
                        <td class="transaction-type">{{ str_replace('_', ' ', $tx->type ?? '—') }}</td>
                        <td class="col-description" style="text-transform: none;">{{ $tx->description ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-state"> No transaction records found. </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>