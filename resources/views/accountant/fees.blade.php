<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Ledger & Configurations | Smart-Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 20px;
            color: #333333;
        }

        .container {
            max-width: 950px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #0f766e;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            text-transform: uppercase;
            text-align: center;
            font-size: 20px;
            letter-spacing: 0.5px;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .nav-links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            font-size: 14px;
            background: #e9ecef;
            padding: 10px 14px;
            border-radius: 4px;
            align-items: center;
        }

        .nav-links a {
            font-weight: bold;
            text-decoration: none;
            color: #0f766e;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        @media (max-width: 768px) {
            .panel-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            height: fit-content;
        }

        h3 {
            margin-top: 0;
            color: #334155;
            font-size: 15px;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 6px;
            letter-spacing: 0.3px;
        }

        label {
            font-weight: bold;
            font-size: 13px;
            color: #475569;
            display: block;
            margin-top: 10px;
            margin-bottom: 4px;
        }

        select, input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
            background-color: white;
            font-family: inherit;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #0f766e;
        }

        .btn {
            background-color: #0f172a;
            color: white;
            padding: 11px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            width: 100%;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            transition: background 0.2s;
        }

        .btn:hover {
            background-color: #1e293b;
        }

        .btn-accent {
            background-color: #0d9488;
        }

        .btn-accent:hover {
            background-color: #0f766e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-size: 12px;
            text-transform: uppercase;
        }

        .badge {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
        }

        .cleared {
            background-color: #dcfce7;
            color: #15803d;
        }

        .owing {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .debug-box {
            background-color: #fffbeb;
            border: 1px dashed #f59e0b;
            padding: 14px;
            color: #b45309;
            font-size: 13px;
            margin-top: 15px;
            border-radius: 4px;
        }

        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media print {
            .nav-links, .no-print, form, button, .btn {
                display: none !important;
            }
            .panel-grid {
                display: block !important;
            }
            .card {
                background: white !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                width: 100% !important;
            }
            body {
                background: white;
                margin: 0;
            }
            .container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📊 {{ __('Financial Ledger and Accounts Statement') }}</h2>

    <div class="nav-links">
        <span>{{ __('Campus') }}: <b>{{ $schoolName }}</b></span>
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('accountant.collect_payment') }}" style="font-weight: bold; color: #0d9488;">
                ➕ {{ __('Collect Payment') }}
            </a>
            <a href="{{ route('home') }}">{{ __('Home') }}</a>

            <!-- Language Switcher -->
            <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 2px 4px; gap: 4px;">
                <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#0f766e' : '#64748b' }}; background: {{ app()->getLocale() == 'en' ? '#ccfbf1' : 'transparent' }};">🇬🇧 EN</a>
                <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#0f766e' : '#64748b' }}; background: {{ app()->getLocale() == 'sw' ? '#ccfbf1' : 'transparent' }};">🇹🇿 SW</a>
            </div>

            <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                @csrf
                <button type="submit" class="no-print" style="color: #ef4444; font-weight: bold; text-decoration: none; padding: 5px 12px; border: 1px solid #fecaca; background-color: #fee2e2; border-radius: 4px; cursor: pointer; font-size: 13px;">
                    {{ __('Logout') }} ↩
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="panel-grid">
        <!-- LEFT PANEL: Manage Fee Configuration Structure -->
        <div class="card no-print">
            <h3>⚙️ Config Required Fee</h3>
            <form method="POST" action="{{ route('accountant.fee_structure.store') }}">
                @csrf
                <label for="config_class">Class Template:</label>
                <select name="config_class" id="config_class" required>
                    <option value="">-- Choose Class --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class }}">{{ $class }}</option>
                    @endforeach
                </select>

                <label for="total_amount">Required Amount (TZS):</label>
                <input type="number" name="total_amount" id="total_amount" placeholder="e.g. 400000" min="0" step="0.01" required>

                <label for="academic_year">Academic Year:</label>
                <input type="number" name="academic_year" id="academic_year" value="{{ date('Y') }}" required>

                <button type="submit" class="btn">Save Configuration</button>
            </form>
        </div>

        <!-- RIGHT PANEL: View Ledger Roster Statement Filtering -->
        <div class="card" style="width: 100%;">
            <h3>🔍 Look Up Accounts Statement</h3>
            <form method="GET" action="{{ route('accountant.fees') }}" class="no-print">
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 2;">
                        <label for="filter_class">Target Class Structure:</label>
                        <select name="filter_class" id="filter_class" required>
                            <option value="">-- Choose Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class }}" {{ $filterClass === $class ? 'selected' : '' }}>
                                    {{ $class }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label for="filter_year">Year:</label>
                        <input type="number" name="filter_year" id="filter_year" value="{{ $filterYear }}" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-accent">Generate Financial Statement</button>
            </form>

            @if(!empty($filterClass))
                <div style="margin-top: 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; background: #e2e8f0; padding: 10px 14px; border-radius: 4px;">
                        <span style="font-size: 14px; font-weight: bold; color: #1e293b;">
                            Roster: Class {{ $filterClass }} (Year: {{ $filterYear }})
                        </span>
                        <span style="font-size: 13px; font-weight: bold; color: #0f766e;">
                            Class Mandatory Fee: {{ number_format($targetFeeRequired, 2) }} TZS
                        </span>
                    </div>

                    @if(count($ledger) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Full Student Name</th>
                                    <th>Paid (TZS)</th>
                                    <th>Balance Due (TZS)</th>
                                    <th style="text-align: center;">Account State</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ledger as $row)
                                    <tr>
                                        <td><b>{{ $row['student_name'] }}</b></td>
                                        <td style="color: #16a34a; font-weight: bold;">
                                            {{ number_format($row['total_paid'], 2) }}
                                        </td>
                                        <td style="color: #ef4444; font-weight: bold;">
                                            {{ number_format($row['balance'], 2) }}
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge {{ strtolower($row['status']) }}">
                                                {{ $row['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="margin-top: 15px; overflow: hidden;">
                            <button onclick="window.print()" class="btn no-print" style="background-color: #0d9488; max-width: 180px; float: right; margin-top: 0;">
                                🖨️ Print Report
                            </button>
                        </div>
                    @else
                        <div class="debug-box">
                            ⚠️ <b>Notice:</b> Hakuna rekodi za wanafunzi zilizopatikana kwenye darasa la <b>"{{ $filterClass }}"</b> kwa shule ya <b>"{{ $schoolName }}"</b>.
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

</body>
</html>
