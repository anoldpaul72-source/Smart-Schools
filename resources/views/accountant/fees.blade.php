<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Finance Ledger & Accounts Statement') }} | Smart-Results</title>
    <style>
        :root {
            --primary: #0f766e;
            --primary-hover: #115e59;
            --primary-light: #ccfbf1;
            --secondary: #0f172a;
            --border: #cbd5e1;
            --bg-page: #f8fafc;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-page);
            margin: 20px;
            color: #333333;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 26px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        h2 {
            color: var(--primary);
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            text-transform: uppercase;
            text-align: center;
            font-size: 21px;
            letter-spacing: 0.5px;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .nav-links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
            background: #f1f5f9;
            padding: 10px 16px;
            border-radius: 8px;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        .nav-links a {
            font-weight: bold;
            text-decoration: none;
            color: var(--primary);
        }

        /* Tabs Navigation */
        .tabs-header {
            display: flex;
            gap: 10px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 22px;
        }

        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            font-size: 14px;
            font-weight: 700;
            color: #64748b;
            text-decoration: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s ease;
        }

        .tab-btn:hover {
            color: var(--primary);
            background: #f0fdfa;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }

        .tab-btn.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
            background: #f0fdfa;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }

        /* KPI / Summary Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 14px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            border-left: 4px solid var(--primary);
        }

        .kpi-card.farm { border-left-color: #16a34a; }
        .kpi-card.vendor { border-left-color: #f59e0b; }
        .kpi-card.frame { border-left-color: #0284c7; }
        .kpi-card.other { border-left-color: #8b5cf6; }

        .kpi-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .kpi-value {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        @media (max-width: 900px) {
            .panel-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            height: fit-content;
        }

        h3 {
            margin-top: 0;
            color: #334155;
            font-size: 15px;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 8px;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        label {
            font-weight: bold;
            font-size: 13px;
            color: #475569;
            display: block;
            margin-top: 10px;
            margin-bottom: 4px;
        }

        select, input, textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13px;
            box-sizing: border-box;
            background-color: white;
            font-family: inherit;
        }

        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.15);
        }

        .btn {
            background-color: #0f172a;
            color: white;
            padding: 11px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            width: 100%;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #1e293b;
        }

        .btn-accent {
            background-color: var(--primary);
        }

        .btn-accent:hover {
            background-color: var(--primary-hover);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            border-radius: 6px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #e2e8f0;
            padding: 9px 12px;
            text-align: left;
            font-size: 13px;
        }

        th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .badge {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-farm { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-vendor { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-frame { background-color: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
        .badge-other { background-color: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }

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
            border-radius: 6px;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
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
            .nav-links, .tabs-header, .no-print, form, button, .btn {
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
                border: none;
            }
        }
    </style>
</head>
<body>

@include('layouts.sidebar')

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">
        <button type="button" class="sidebar-toggle-btn" onclick="toggleSidebar()" title="{{ __('Toggle Sidebar') }}">
            ☰ {{ __('Menu') }}
        </button>
        <div style="font-size: 13px; color: #64748b; font-weight: 700;">Smart-Schools &bull; {{ __('Finance Desk') }}</div>
    </div>

    <h2>📊 {{ __('Financial Ledger and Accounts Statement') }}</h2>

    <!-- Navigation Header -->
    <div class="nav-links">
        <span>🏫 {{ __('Campus') }}: <b>{{ $schoolName }}</b></span>
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('accountant.collect_payment') }}" style="font-weight: bold; color: var(--primary);">
                ➕ {{ __('Collect Student Fee') }}
            </a>
            <a href="{{ route('home') }}">🏠 {{ __('Home') }}</a>

            <!-- Language Switcher -->
            <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 2px 4px; gap: 4px;">
                <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#0f766e' : '#64748b' }}; background: {{ app()->getLocale() == 'en' ? '#ccfbf1' : 'transparent' }};">🇬🇧 EN</a>
                <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#0f766e' : '#64748b' }}; background: {{ app()->getLocale() == 'sw' ? '#ccfbf1' : 'transparent' }};">🇹🇿 SW</a>
            </div>

            <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                @csrf
                <button type="submit" class="no-print" style="color: #ef4444; font-weight: bold; text-decoration: none; padding: 5px 12px; border: 1px solid #fecaca; background-color: #fee2e2; border-radius: 4px; cursor: pointer; font-size: 13px;">
                    🚪 {{ __('Logout') }}
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

    <!-- Main Tabs -->
    <div class="tabs-header no-print">
        <a href="{{ route('accountant.fees', ['tab' => 'fees']) }}" class="tab-btn {{ $activeTab === 'fees' ? 'active' : '' }}">
            💰 {{ __('Student Fees & Structure') }}
        </a>
        <a href="{{ route('accountant.fees', ['tab' => 'projects', 'project_year' => $projectYear]) }}" class="tab-btn {{ $activeTab === 'projects' ? 'active' : '' }}">
            🌾 {{ __('School Projects & Revenues') }}
        </a>
    </div>

    @if($activeTab === 'projects')
        <!-- ========================================== -->
        <!-- TAB 2: MAPATO YA MIRADI YA SHULE           -->
        <!-- ========================================== -->

        <!-- KPI Summary Cards -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-title">💰 {{ __('Total Project Revenues') }} ({{ $projectYear }})</div>
                <div class="kpi-value" style="color: var(--primary);">{{ number_format($totalProjectRevenue, 2) }} <span style="font-size: 12px; font-weight: normal;">TZS</span></div>
            </div>
            <div class="kpi-card farm">
                <div class="kpi-title">🌽 {{ __('Farm Produce Sales') }}</div>
                <div class="kpi-value" style="color: #16a34a;">{{ number_format($farmRevenue, 2) }} <span style="font-size: 12px; font-weight: normal;">TZS</span></div>
            </div>
            <div class="kpi-card vendor">
                <div class="kpi-title">🍲 {{ __('Food Vendor / Canteen Levy') }}</div>
                <div class="kpi-value" style="color: #d97706;">{{ number_format($vendorRevenue, 2) }} <span style="font-size: 12px; font-weight: normal;">TZS</span></div>
            </div>
            <div class="kpi-card frame">
                <div class="kpi-title">🏪 {{ __('Commercial Stalls Rent') }}</div>
                <div class="kpi-value" style="color: #0284c7;">{{ number_format($frameRevenue, 2) }} <span style="font-size: 12px; font-weight: normal;">TZS</span></div>
            </div>
            @if($otherRevenue > 0)
            <div class="kpi-card other">
                <div class="kpi-title">🏢 {{ __('Other Institutional Projects') }}</div>
                <div class="kpi-value" style="color: #7c3aed;">{{ number_format($otherRevenue, 2) }} <span style="font-size: 12px; font-weight: normal;">TZS</span></div>
            </div>
            @endif
        </div>

        <div class="panel-grid">
            <!-- LEFT PANEL: Fomu ya Kurekodi Mapato ya Mradi -->
            <div class="card no-print">
                <h3>➕ {{ __('Record Project Revenue') }}</h3>
                <form method="POST" action="{{ route('accountant.project_income.store') }}">
                    @csrf
                    <input type="hidden" name="academic_year" value="{{ $projectYear }}">

                    <label for="category">{{ __('Project Category') }}:</label>
                    <select name="category" id="category" required>
                        <option value="">-- {{ __('Choose Category') }} --</option>
                        @foreach($categories as $catKey => $catLabel)
                            <option value="{{ $catKey }}">{{ __($catLabel) }}</option>
                        @endforeach
                    </select>

                    <label for="source_title">{{ __('Source / Project Title') }}:</label>
                    <input type="text" name="source_title" id="source_title" placeholder="{{ __('e.g. Corn Harvest 30 Bags, Stall No. 4, Hall Hire') }}" required>

                    <label for="payer_name">{{ __('Payer / Customer Name') }}:</label>
                    <input type="text" name="payer_name" id="payer_name" placeholder="{{ __('e.g. John Doe or Mama Ashura') }}">

                    <label for="amount">{{ __('Amount Received (TZS)') }}:</label>
                    <input type="number" name="amount" id="amount" placeholder="e.g. 150000" min="1" step="0.01" required>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label for="payment_date">{{ __('Date') }}:</label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div>
                            <label for="receipt_number">{{ __('Receipt Number') }}:</label>
                            <input type="text" name="receipt_number" id="receipt_number" placeholder="REC-001">
                        </div>
                    </div>

                    <label for="payment_method">{{ __('Payment Method') }}:</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="Cash">{{ __('Cash') }}</option>
                        <option value="NMB Bank">NMB Bank</option>
                        <option value="CRDB Bank">CRDB Bank</option>
                        <option value="M-Pesa">M-Pesa</option>
                        <option value="Tigo Pesa">Tigo Pesa</option>
                        <option value="Airtel Money">Airtel Money</option>
                        <option value="Bank Transfer">{{ __('Bank Transfer') }}</option>
                    </select>

                    <label for="notes">{{ __('Additional Notes (Optional)') }}:</label>
                    <textarea name="notes" id="notes" rows="2" placeholder="{{ __('Any notes regarding this revenue or transaction...') }}"></textarea>

                    <button type="submit" class="btn btn-accent">💾 {{ __('Save Project Revenue') }}</button>
                </form>
            </div>

            <!-- RIGHT PANEL: Jedwali la Historia na Vichujio vya Mapato ya Miradi -->
            <div class="card" style="width: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <h3 style="border: none; margin: 0; padding: 0;">📋 {{ __('Project Revenue Records') }}</h3>

                    <a href="{{ route('accountant.project_income.print', ['year' => $projectYear, 'category' => $projectCategory]) }}" target="_blank" class="no-print" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: #0f172a; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 13px;">
                        🖨️ {{ __('Print Official Statement') }}
                    </a>
                </div>

                <!-- Filter Bar -->
                <form method="GET" action="{{ route('accountant.fees') }}" class="no-print" style="display: flex; gap: 10px; align-items: flex-end; background: #ffffff; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 15px; flex-wrap: wrap;">
                    <input type="hidden" name="tab" value="projects">

                    <div style="flex: 2; min-width: 180px;">
                        <label style="margin: 0 0 4px 0;">{{ __('Filter by Category') }}:</label>
                        <select name="project_category" onchange="this.form.submit()">
                            <option value="">-- {{ __('All Project Categories') }} --</option>
                            @foreach($categories as $catKey => $catLabel)
                                <option value="{{ $catKey }}" {{ $projectCategory === $catKey ? 'selected' : '' }}>{{ __($catLabel) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 110px;">
                        <label style="margin: 0 0 4px 0;">{{ __('Year') }}:</label>
                        <input type="number" name="project_year" value="{{ $projectYear }}" onchange="this.form.submit()">
                    </div>

                    <button type="submit" class="btn btn-accent" style="margin: 0; width: auto; padding: 9px 16px;">🔍 {{ __('Filter') }}</button>
                </form>

                @if($projectIncomes->count() > 0)
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Source / Project Title') }}</th>
                                    <th>{{ __('Payer / Customer Name') }}</th>
                                    <th style="text-align: right;">{{ __('Amount (TZS)') }}</th>
                                    <th>{{ __('Payment & Receipt') }}</th>
                                    <th class="no-print" style="text-align: center;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projectIncomes as $inc)
                                    <tr>
                                        <td>{{ $inc->payment_date ? $inc->payment_date->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = 'badge-other';
                                                if ($inc->category === 'Mauzo ya Mazao' || $inc->category === 'Farm Produce Sales') $badgeClass = 'badge-farm';
                                                elseif ($inc->category === 'Ushuru wa Mama Ntilie' || $inc->category === 'Food Vendor / Canteen Levy') $badgeClass = 'badge-vendor';
                                                elseif ($inc->category === 'Kodi za Fremu' || $inc->category === 'Commercial Stalls Rent') $badgeClass = 'badge-frame';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ __($categories[$inc->category] ?? $inc->category) }}</span>
                                        </td>
                                        <td>
                                            <b>{{ $inc->source_title }}</b>
                                            @if($inc->notes)
                                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">{{ $inc->notes }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $inc->payer_name ?: '-' }}</td>
                                        <td style="text-align: right; color: #16a34a; font-weight: bold; font-size: 14px;">
                                            {{ number_format($inc->amount, 2) }}
                                        </td>
                                        <td>
                                            <div style="font-size: 12px; font-weight: 600;">{{ __($inc->payment_method) }}</div>
                                            @if($inc->receipt_number)
                                                <span style="font-size: 11px; color: #64748b;">#{{ $inc->receipt_number }}</span>
                                            @endif
                                        </td>
                                        <td class="no-print" style="text-align: center;">
                                            <form method="POST" action="{{ route('accountant.project_income.destroy', $inc->id) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this revenue record?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444; font-size: 16px;" title="{{ __('Delete') }}">
                                                    🗑️
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div style="margin-top: 15px;">
                        {{ $projectIncomes->appends(['tab' => 'projects', 'project_year' => $projectYear, 'project_category' => $projectCategory])->links() }}
                    </div>
                @else
                    <div class="debug-box">
                        💡 <b>{{ __('Notice') }}:</b> {{ __('No project revenue records found for year') }} <b>{{ $projectYear }}</b> @if($projectCategory) ({{ __('Category') }}: <b>{{ __($categories[$projectCategory] ?? $projectCategory) }}</b>) @endif. {{ __('Use the form on the left to record new revenue.') }}
                    </div>
                @endif
            </div>
        </div>

    @else
        <!-- ========================================== -->
        <!-- TAB 1: ADA ZA WANAFUNZI (STUDENT FEES)     -->
        <!-- ========================================== -->
        <div class="panel-grid">
            <!-- LEFT PANEL: Manage Fee Configuration Structure -->
            <div class="card no-print">
                <h3>⚙️ {{ __('Configure Required Fee') }}</h3>
                <form method="POST" action="{{ route('accountant.fee_structure.store') }}">
                    @csrf
                    <label for="config_class">{{ __('Class Template') }}:</label>
                    <select name="config_class" id="config_class" required>
                        <option value="">-- {{ __('Choose Class') }} --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class }}">{{ $class }}</option>
                        @endforeach
                    </select>

                    <label for="total_amount">{{ __('Required Amount (TZS)') }}:</label>
                    <input type="number" name="total_amount" id="total_amount" placeholder="e.g. 400000" min="0" step="0.01" required>

                    <label for="academic_year">{{ __('Academic Year') }}:</label>
                    <input type="number" name="academic_year" id="academic_year" value="{{ date('Y') }}" required>

                    <button type="submit" class="btn">💾 {{ __('Save Configuration') }}</button>
                </form>
            </div>

            <!-- RIGHT PANEL: View Ledger Roster Statement Filtering -->
            <div class="card" style="width: 100%;">
                <h3>🔍 {{ __('Look Up Accounts Statement') }}</h3>
                <form method="GET" action="{{ route('accountant.fees') }}" class="no-print">
                    <input type="hidden" name="tab" value="fees">
                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 2;">
                            <label for="filter_class">{{ __('Target Class Structure') }}:</label>
                            <select name="filter_class" id="filter_class" required>
                                <option value="">-- {{ __('Choose Class') }} --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class }}" {{ $filterClass === $class ? 'selected' : '' }}>
                                        {{ $class }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label for="filter_year">{{ __('Year') }}:</label>
                            <input type="number" name="filter_year" id="filter_year" value="{{ $filterYear }}" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-accent">📊 {{ __('Generate Financial Statement') }}</button>
                </form>

                @if(!empty($filterClass))
                    <div style="margin-top: 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; background: #e2e8f0; padding: 10px 14px; border-radius: 6px;">
                            <span style="font-size: 14px; font-weight: bold; color: #1e293b;">
                                {{ __('Roster') }}: {{ $filterClass }} ({{ __('Year') }}: {{ $filterYear }})
                            </span>
                            <span style="font-size: 13px; font-weight: bold; color: var(--primary);">
                                {{ __('Class Mandatory Fee') }}: {{ number_format($targetFeeRequired, 2) }} TZS
                            </span>
                        </div>

                        @if(count($ledger) > 0)
                            <table>
                                <thead>
                                    <tr>
                                        <th>{{ __('Full Student Name') }}</th>
                                        <th>{{ __('Paid (TZS)') }}</th>
                                        <th>{{ __('Balance Due (TZS)') }}</th>
                                        <th style="text-align: center;">{{ __('Account State') }}</th>
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
                                                    {{ __($row['status']) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div style="margin-top: 15px; overflow: hidden;">
                                <button onclick="window.print()" class="btn no-print" style="background-color: var(--primary); max-width: 180px; float: right; margin-top: 0;">
                                    🖨️ {{ __('Print Report') }}
                                </button>
                            </div>
                        @else
                            <div class="debug-box">
                                ⚠️ <b>{{ __('Notice') }}:</b> {{ __('No student records found for this class.') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

</body>
</html>
