<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Transactions Records</title>
    <style>
        @page {
            margin: 15px 18px 18px 18px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #000000;
            margin: 0;
            padding: 0;
            line-height: 1.15;
        }

        .container {
            padding: 0;
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
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .header-section h2 {
            margin: 2px 0 0 0;
            color: #000000;
            font-size: 10px;
            font-weight: bold;
        }

        .report-title {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 7px 0;
        }

        .summary {
            text-align: right;
            padding-bottom: 5px;
            margin-bottom: 7px;
        }

        .summary-label {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .summary-amount {
            font-size: 11px;
            font-weight: bold;
            margin-top: 1px;
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

        .data-table th,
        .data-table td {
            border: 1px solid #000000;
            padding: 3px 4px;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #ffffff;
            color: #000000;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            font-weight: bold;
            text-align: center;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        .data-table td {
            font-size: 7.5px;
            color: #000000;
        }

        .col-reference {
            width: 24%;
        }

        .col-amount {
            width: 14%;
            text-align: right;
        }

        .col-phone {
            width: 18%;
        }

        .col-type {
            width: 18%;
        }

        .col-date {
            width: 26%;
        }

        .reference {
            font-family: monospace;
            font-size: 7px;
        }

        .amount {
            font-family: monospace;
            font-weight: bold;
            text-align: right;
            white-space: nowrap;
        }

        .phone {
            font-family: monospace;
            font-size: 7px;
        }

        .transaction-type {
            text-transform: uppercase;
            font-size: 7px;
        }

        .date-time {
            font-family: monospace;
            font-size: 7px;
            white-space: nowrap;
        }

        .empty-state {
            color: #000000;
            padding: 12px !important;
            text-align: center !important;
        }

        .footer {
            position: fixed;
            bottom: -5px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 6px;
            color: #000000;
        }

        .footer p {
            margin: 1px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-section"> <img src="{{ public_path('images/logo.png') }}" alt="ROSSET-SWA Logo">
            <h1> Rongo Sub County Teachers Welfare Association </h1>
            <h2> (ROSSET-SWA) </h2>
        </div>
        <div class="report-title"> TRANSACTIONS RECORDS </div>
        <div class="summary">
            <div class="summary-label"> TOTAL SUCCESSFUL TRANSACTION AMOUNT </div>
            <div class="summary-amount"> KSH {{ number_format($totalAmount, 2) }} </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-reference"> Reference </th>
                    <th class="col-amount"> Amount </th>
                    <th class="col-phone"> Phone </th>
                    <th class="col-type"> Type </th>
                    <th class="col-date"> Date / Time </th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td class="reference"> {{ $tx->reference_number ?? '—' }} </td>
                        <td class="amount"> {{ number_format($tx->amount ?? 0, 2) }} </td>
                        <td class="phone"> {{ $tx->phone_number ?? '—' }} </td>
                        <td class="transaction-type"> {{ str_replace('_', ' ', $tx->type ?? '—') }} </td>
                        <td class="date-time">
                            {{ $tx->created_at ? $tx->created_at->setTimezone('Africa/Nairobi')->format('d-m-y g:i A') : '—' }}
                        </td>
                </tr> @empty <tr>
                        <td colspan="5" class="empty-state"> No transaction records found. </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="footer">
            <p> Generated By: {{ $generatedBy }} &nbsp;&nbsp;|&nbsp;&nbsp; Generated At: {{ $generatedAt }} </p>
            <p> This is an electronically generated official document from the ROSSET-SWA system. </p>
        </div>
    </div>
</body>

</html>
