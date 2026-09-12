<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Revenue Statement - {{ $schoolName }} ({{ $projectYear }})</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            color: #000000;
            margin: 0;
            padding: 25px;
            font-size: 12px;
        }

        .header-box {
            text-align: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .gov-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .school-title {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f766e;
            margin: 4px 0;
            letter-spacing: 0.5px;
        }

        .doc-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 11px;
            background: #f8fafc;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000000;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }

        th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10.5px;
        }

        .total-row td {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 12px;
            border-top: 2px solid #000000;
        }

        .signatures {
            margin-top: 45px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            text-align: center;
            page-break-inside: avoid;
        }

        .sig-line {
            border-top: 1px dashed #000000;
            margin-top: 40px;
            padding-top: 5px;
            font-weight: bold;
            font-size: 11px;
        }

        .no-print {
            margin-bottom: 15px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="no-print" style="display: flex; justify-content: space-between; align-items: center; background: #0f172a; color: white; padding: 10px 16px; border-radius: 6px;">
    <span style="font-weight: bold;">🖨️ {{ __('Official Institutional Revenue Statement') }}</span>
    <div style="display: flex; gap: 10px;">
        <button onclick="window.print()" style="background: #0d9488; color: white; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-weight: bold;">
            {{ __('Print / Save as PDF') }}
        </button>
        <button onclick="window.close()" style="background: #475569; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
            {{ __('Close') }}
        </button>
    </div>
</div>

<div class="header-box">
    <div class="gov-title">{{ __('THE UNITED REPUBLIC OF TANZANIA') }}</div>
    <div class="gov-title">{{ __("PRESIDENT'S OFFICE - REGIONAL ADMINISTRATION AND LOCAL GOVERNMENT") }}</div>
    <div class="school-title">{{ strtoupper($schoolName) }}</div>
    <div class="doc-title">{{ __('OFFICIAL STATEMENT OF INSTITUTIONAL PROJECT REVENUES & NON-FEE COLLECTIONS') }}</div>
</div>

<div class="meta-info">
    <div><b>{{ __('Academic / Financial Year') }}:</b> {{ $projectYear }}</div>
    <div><b>{{ __('Category Filter') }}:</b> {{ $category ? __($category) : __('All Project Categories') }}</div>
    <div><b>{{ __('Generated On') }}:</b> {{ date('d M Y, H:i') }}</div>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 30px; text-align: center;">#</th>
            <th style="width: 75px;">{{ __('Date') }}</th>
            <th style="width: 130px;">{{ __('Category') }}</th>
            <th>{{ __('Project / Revenue Source') }}</th>
            <th style="width: 120px;">{{ __('Payer / Customer') }}</th>
            <th style="width: 80px;">{{ __('Receipt #') }}</th>
            <th style="width: 80px;">{{ __('Method') }}</th>
            <th style="width: 100px; text-align: right;">{{ __('Amount (TZS)') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($incomes as $index => $inc)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $inc->payment_date ? $inc->payment_date->format('d/m/Y') : '-' }}</td>
                <td><b>{{ __($inc->category) }}</b></td>
                <td>
                    {{ $inc->source_title }}
                    @if($inc->notes)
                        <div style="font-size: 9.5px; color: #555; font-style: italic;">{{ __('Note') }}: {{ $inc->notes }}</div>
                    @endif
                </td>
                <td>{{ $inc->payer_name ?: '-' }}</td>
                <td>{{ $inc->receipt_number ?: '-' }}</td>
                <td>{{ __($inc->payment_method) }}</td>
                <td style="text-align: right; font-weight: bold;">
                    {{ number_format($inc->amount, 2) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">
                    {{ __('No revenue records found for the selected criteria.') }}
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="7" style="text-align: right; text-transform: uppercase;">
                {{ __('GRAND TOTAL REVENUE (TZS)') }}:
            </td>
            <td style="text-align: right; color: #0f766e; font-size: 13px;">
                {{ number_format($totalAmount, 2) }}
            </td>
        </tr>
    </tfoot>
</table>

<div class="signatures">
    <div>
        <div class="sig-line">{{ __('Prepared By (Bursar / Accountant)') }}</div>
        <div style="font-size: 10px; color: #555; margin-top: 3px;">{{ __('Signature & Date') }}</div>
    </div>
    <div>
        <div class="sig-line">{{ __('Audited By (Internal Auditor / Master)') }}</div>
        <div style="font-size: 10px; color: #555; margin-top: 3px;">{{ __('Signature & Date') }}</div>
    </div>
    <div>
        <div class="sig-line">{{ __('Approved By (Headmaster / Headmistress)') }}</div>
        <div style="font-size: 10px; color: #555; margin-top: 3px;">{{ __('Signature & Official Stamp') }}</div>
    </div>
</div>

</body>
</html>
