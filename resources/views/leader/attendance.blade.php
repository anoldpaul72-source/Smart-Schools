<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Student Attendance Reports') }} | Smart-Schools</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --primary-light: #e0f2fe;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #d97706;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: var(--slate-800);
            line-height: 1.5;
            padding-bottom: 40px;
        }

        .top-navbar {
            background: var(--slate-900);
            color: white;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .top-navbar .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 17px;
            font-weight: 800;
            color: #ffffff;
            text-decoration: none;
        }

        .top-navbar .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-nav {
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-dashboard {
            background: #0284c7;
            color: white;
        }
        .btn-dashboard:hover { background: #0369a1; }

        .btn-print {
            background: #16a34a;
            color: white;
        }
        .btn-print:hover { background: #15803d; }

        .btn-home {
            background: #334155;
            color: white;
        }
        .btn-home:hover { background: #475569; }

        .lang-switch {
            display: inline-flex;
            align-items: center;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 2px 4px;
            gap: 4px;
        }

        .lang-link {
            font-size: 11px;
            font-weight: bold;
            text-decoration: none;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .container {
            max-width: 1380px;
            margin: 24px auto;
            padding: 0 20px;
        }

        /* Printable Official Header */
        .official-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 2px solid var(--slate-800);
        }

        .official-header .gov-title {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: var(--slate-900);
        }

        .official-header .school-title {
            font-size: 20px;
            font-weight: 900;
            color: #b91c1c;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .official-header .report-subtitle {
            font-size: 14px;
            font-weight: 800;
            color: #0369a1;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Report Type Navigation Tabs */
        .tabs-nav {
            display: flex;
            gap: 8px;
            background: white;
            padding: 6px;
            border-radius: 10px;
            border: 1px solid var(--slate-200);
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow-x: auto;
        }

        .tab-btn {
            flex: 1;
            min-width: 140px;
            text-align: center;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13.5px;
            color: var(--slate-600);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .tab-btn:hover {
            color: var(--slate-900);
            background: var(--slate-100);
        }

        .tab-btn.active {
            background: #0284c7;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35);
        }

        /* Filter Box */
        .filter-panel {
            background: white;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .filter-form {
            display: flex;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            flex: 1;
            min-width: 160px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 700;
            color: var(--slate-700);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .form-control {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13.5px;
            color: var(--slate-800);
            background: #ffffff;
            outline: none;
            font-weight: 600;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .btn-filter-submit {
            padding: 10px 22px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13.5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.15s;
        }
        .btn-filter-submit:hover { background: #1e293b; }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-meta h4 {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--slate-500);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }

        .stat-meta .stat-val {
            font-size: 22px;
            font-weight: 900;
            color: var(--slate-900);
            line-height: 1.2;
        }

        .stat-meta .stat-sub {
            font-size: 11px;
            color: var(--slate-500);
            font-weight: 600;
        }

        /* Table Design */
        .report-card {
            background: white;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .table-container {
            overflow-x: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            text-align: left;
        }

        thead tr {
            background: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
        }

        th {
            padding: 10px 12px;
            font-weight: 800;
            color: var(--slate-700);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--slate-200);
            transition: background-color 0.1s;
        }

        tbody tr:hover {
            background-color: #f8fafc;
        }

        td {
            padding: 10px 12px;
            color: var(--slate-800);
            vertical-align: middle;
        }

        /* Status Pills */
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .pill-present {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .pill-absent {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .pill-late {
            background: #ffedd5;
            color: #c2410c;
            border: 1px solid #fdba74;
        }

        .pill-unrecorded {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        /* Percentage Badge */
        .rate-badge {
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 12px;
            display: inline-block;
        }
        .rate-high { background: #dcfce7; color: #166534; }
        .rate-mid  { background: #fef9c3; color: #854d0e; }
        .rate-low  { background: #fee2e2; color: #991b1b; }

        .empty-state {
            padding: 45px 20px;
            text-align: center;
            color: var(--slate-500);
        }

        /* Print Media Styles */
        @page {
            size: A4 landscape;
            margin: 8mm 8mm 8mm 8mm;
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                font-size: 10.5px !important;
            }
            .top-navbar, .tabs-nav, .filter-panel, .no-print {
                display: none !important;
            }
            .container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .report-card {
                border: none !important;
                box-shadow: none !important;
            }
            .stats-grid {
                margin-bottom: 14px !important;
                gap: 8px !important;
            }
            .stat-card {
                padding: 8px 12px !important;
                border: 1px solid #ccc !important;
            }
            .stat-icon { display: none !important; }
            .stat-meta .stat-val { font-size: 16px !important; }
            table {
                font-size: 10px !important;
                border-collapse: collapse !important;
            }
            th, td {
                padding: 5px 6px !important;
                border: 1px solid #cbd5e1 !important;
            }
            th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
            }
            thead { display: table-header-group !important; }
            tr { page-break-inside: avoid !important; break-inside: avoid !important; }
            .pill, .rate-badge {
                border: 1px solid #333 !important;
                color: black !important;
                background: transparent !important;
            }
            .app-main-wrapper { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
        }

        .app-main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 260px);
            transition: margin-left 0.28s cubic-bezier(0.4, 0, 0.2, 1), width 0.28s ease;
        }

        body.sidebar-collapsed .app-main-wrapper {
            margin-left: 0;
            width: 100%;
        }

        @media (max-width: 1023px) {
            .app-main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body>

@include('layouts.sidebar')

<div class="app-main-wrapper" id="appMainWrapper">

<!-- Top Navigation Bar -->
<div class="top-navbar no-print">
    <div style="display:flex; align-items:center; gap:12px;">
        <button type="button" class="sidebar-toggle-btn" onclick="toggleSidebar()" title="{{ __('Toggle Sidebar') }}" style="background:#1e293b; border:1px solid #334155; color:white; border-radius:6px; width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:16px;">
            ☰
        </button>
        <span style="font-size:20px;">📋</span>
        <span style="font-size:15px; font-weight:800; letter-spacing:0.3px;">
            {{ __('School Attendance Reports') }} &bull; {{ $schoolName }}
        </span>
    </div>

    <div class="nav-actions">
        <button type="button" class="btn-nav btn-print" onclick="window.print()">
            🖨️ {{ __('Print Report') }}
        </button>
        <a href="{{ route('leader.dashboard') }}" class="btn-nav btn-dashboard">
            📊 {{ __('Academic Dashboard') }}
        </a>
        <a href="{{ route('home') }}" class="btn-nav btn-home">
            🏠 {{ __('Home') }}
        </a>

        <!-- Language Switcher -->
        <div class="lang-switch">
            <a href="{{ route('lang.switch', 'en') }}" class="lang-link" style="color: {{ app()->getLocale() == 'en' ? '#38bdf8' : '#94a3b8' }}; background: {{ app()->getLocale() == 'en' ? '#0f172a' : 'transparent' }};">🇬🇧 EN</a>
            <a href="{{ route('lang.switch', 'sw') }}" class="lang-link" style="color: {{ app()->getLocale() == 'sw' ? '#38bdf8' : '#94a3b8' }}; background: {{ app()->getLocale() == 'sw' ? '#0f172a' : 'transparent' }};">🇹🇿 SW</a>
        </div>

        <form method="POST" action="{{ route('logout') }}" style="display:inline; margin:0;">
            @csrf
            <button type="submit" style="background:none; border:none; color:#f87171; font-weight:bold; cursor:pointer; font-size:12.5px; margin-left:4px;">
                🚪 {{ __('Logout') }}
            </button>
        </form>
    </div>
</div>

<div class="container">

    <!-- Official Document Header (Visible in print and screen) -->
    <div class="official-header">
        <div class="gov-title">THE UNITED REPUBLIC OF TANZANIA</div>
        <div class="gov-title">THE PRIME MINISTER'S OFFICE, REGIONAL ADMINISTRATION AND LOCAL GOVERNMENT</div>
        <div class="school-title">{{ strtoupper($schoolName) }}</div>
        <div class="report-subtitle">
            {{ strtoupper($selectedClass) }} &bull;
            @if($reportType === 'daily')
                {{ __('DAILY ATTENDANCE REPORT') }} &bull; {{ date('d/m/Y', strtotime($selectedDate)) }}
            @elseif($reportType === 'weekly')
                {{ __('WEEKLY ATTENDANCE REPORT') }} &bull; {{ $summaryStats['week_label'] ?? '' }}
            @elseif($reportType === 'monthly')
                {{ __('MONTHLY ATTENDANCE REPORT') }} &bull; {{ strtoupper($summaryStats['month_name'] ?? '') }}
            @elseif($reportType === 'annual')
                {{ __('ANNUAL ATTENDANCE REPORT') }} &bull; {{ $summaryStats['year'] ?? '' }}
            @endif
            @if($currentSubject)
                &bull; {{ strtoupper($currentSubject->subject_name) }}
            @else
                &bull; {{ __('ALL SUBJECTS / SESSIONS') }}
            @endif
        </div>
    </div>

    <!-- 4 Report Type Tabs -->
    <div class="tabs-nav no-print">
        <a href="{{ route('leader.attendance', array_merge(request()->all(), ['report_type' => 'daily'])) }}" class="tab-btn {{ $reportType === 'daily' ? 'active' : '' }}">
            <span>📅</span>
            <span>{{ __('Daily Attendance') }}</span>
        </a>
        <a href="{{ route('leader.attendance', array_merge(request()->all(), ['report_type' => 'weekly'])) }}" class="tab-btn {{ $reportType === 'weekly' ? 'active' : '' }}">
            <span>📆</span>
            <span>{{ __('Weekly Report') }}</span>
        </a>
        <a href="{{ route('leader.attendance', array_merge(request()->all(), ['report_type' => 'monthly'])) }}" class="tab-btn {{ $reportType === 'monthly' ? 'active' : '' }}">
            <span>🗓️</span>
            <span>{{ __('Monthly Report') }}</span>
        </a>
        <a href="{{ route('leader.attendance', array_merge(request()->all(), ['report_type' => 'annual'])) }}" class="tab-btn {{ $reportType === 'annual' ? 'active' : '' }}">
            <span>📈</span>
            <span>{{ __('Annual Report') }}</span>
        </a>
    </div>

    <!-- Filter Form -->
    <div class="filter-panel no-print">
        <form method="GET" action="{{ route('leader.attendance') }}" class="filter-form">
            <input type="hidden" name="report_type" value="{{ $reportType }}">

            <!-- Class Filter -->
            <div class="form-group">
                <label for="filter_class">{{ __('Class:') }}</label>
                <select name="class_name" id="filter_class" class="form-control">
                    @foreach($availableClasses as $cls)
                        <option value="{{ $cls }}" {{ $selectedClass === $cls ? 'selected' : '' }}>{{ $cls }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Subject Filter -->
            <div class="form-group">
                <label for="filter_subject">{{ __('Subject:') }}</label>
                <select name="subject_id" id="filter_subject" class="form-control">
                    <option value="all" {{ $selectedSubject === 'all' ? 'selected' : '' }}>-- {{ __('All Subjects / Sessions') }} --</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}" {{ (string)$selectedSubject === (string)$sub->id ? 'selected' : '' }}>
                            {{ $sub->subject_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Dynamic Date/Week/Month/Year filters based on active tab -->
            @if($reportType === 'daily')
                <div class="form-group">
                    <label for="filter_date">{{ __('Date:') }}</label>
                    <input type="date" name="date" id="filter_date" value="{{ $selectedDate }}" class="form-control">
                </div>
            @elseif($reportType === 'weekly')
                <div class="form-group">
                    <label for="filter_week">{{ __('Select Week (Any Day):') }}</label>
                    <input type="date" name="week_date" id="filter_week" value="{{ $selectedWeek }}" class="form-control">
                </div>
            @elseif($reportType === 'monthly')
                <div class="form-group">
                    <label for="filter_month">{{ __('Month:') }}</label>
                    <select name="month" id="filter_month" class="form-control">
                        @for($m = 1; $m <= 12; $m++)
                            @php $mDate = Carbon\Carbon::create(2026, $m, 1); @endphp
                            <option value="{{ $m }}" {{ $selectedMonth === $m ? 'selected' : '' }}>
                                {{ $mDate->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter_year">{{ __('Year:') }}</label>
                    <select name="year" id="filter_year" class="form-control">
                        @for($y = (int)date('Y') + 1; $y >= (int)date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            @elseif($reportType === 'annual')
                <div class="form-group">
                    <label for="filter_year_only">{{ __('Year:') }}</label>
                    <select name="year" id="filter_year_only" class="form-control">
                        @for($y = (int)date('Y') + 1; $y >= (int)date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            @endif

            <div>
                <button type="submit" class="btn-filter-submit">
                    🔍 {{ __('Generate Report') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;">🎓</div>
            <div class="stat-meta">
                <h4>{{ __('Registered Students') }}</h4>
                <div class="stat-val">{{ $summaryStats['total_students'] }}</div>
                <div class="stat-sub">{{ $selectedClass }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#dcfce7; color:#16a34a;">✅</div>
            <div class="stat-meta">
                <h4>{{ __('Present') }}</h4>
                <div class="stat-val" style="color:#16a34a;">{{ number_format($summaryStats['present_count']) }}</div>
                <div class="stat-sub">{{ __('Sessions Attended') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2; color:#dc2626;">❌</div>
            <div class="stat-meta">
                <h4>{{ __('Absent') }}</h4>
                <div class="stat-val" style="color:#dc2626;">{{ number_format($summaryStats['absent_count']) }}</div>
                <div class="stat-sub">{{ __('Sessions Missed') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#fef9c3; color:#ca8a04;">📊</div>
            <div class="stat-meta">
                <h4>{{ __('Attendance Rate') }}</h4>
                <div class="stat-val" style="color: {{ $summaryStats['rate_percent'] >= 75 ? '#16a34a' : ($summaryStats['rate_percent'] >= 50 ? '#d97706' : '#dc2626') }};">
                    {{ $summaryStats['rate_percent'] }}%
                </div>
                <div class="stat-sub">{{ __('Class Overall') }}</div>
            </div>
        </div>
    </div>

    <!-- Report Table Container -->
    <div class="report-card">
        <div class="table-container">

            {{-- 1. DAILY ATTENDANCE VIEW --}}
            @if($reportType === 'daily')
                @if(empty($dailyData))
                    <div class="empty-state">
                        <h3>{{ __('No student records found!') }}</h3>
                        <p>{{ __('Please select a class with registered students.') }}</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align:center;">#</th>
                                <th style="width: 120px;">{{ __('Reg Number') }}</th>
                                <th>{{ __('Student Name') }}</th>
                                <th style="width: 65px; text-align:center;">{{ __('Sex') }}</th>
                                <th>{{ __('Subject / Period') }}</th>
                                <th style="width: 130px; text-align:center;">{{ __('Status') }}</th>
                                <th style="width: 170px;">{{ __('Recorded By') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyData as $idx => $row)
                                @php $st = $row['student']; @endphp
                                <tr>
                                    <td style="text-align:center; font-weight:bold; color:#64748b;">{{ $idx + 1 }}</td>
                                    <td><b>{{ $st->reg_number ?: 'N/A' }}</b></td>
                                    <td style="font-weight:600; color:#0f172a;">{{ $st->student_name }}</td>
                                    <td style="text-align:center; font-weight:bold; color: {{ $st->sex == 'F' ? '#db2777' : '#0284c7' }};">
                                        {{ $st->sex ?: '-' }}
                                    </td>
                                    <td>
                                        <span style="font-weight:600; color:#0369a1;">{{ $row['subject_name'] }}</span>
                                        @if($row['period_number'])
                                            <small style="color:#64748b; font-weight:bold;">({{ __('Period') }} {{ $row['period_number'] }})</small>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if($row['status'] === 'Present')
                                            <span class="pill pill-present">✓ {{ __('Present') }}</span>
                                        @elseif($row['status'] === 'Absent')
                                            <span class="pill pill-absent">✕ {{ __('Absent') }}</span>
                                        @elseif($row['status'] === 'Late' || $row['status'] === 'Permission')
                                            <span class="pill pill-late">⏱ {{ __($row['status']) }}</span>
                                        @else
                                            <span class="pill pill-unrecorded">- {{ __('Unrecorded') }}</span>
                                        @endif
                                    </td>
                                    <td style="color:#64748b; font-size:12px;">
                                        {{ $row['recorded_by'] ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            {{-- 2. WEEKLY ATTENDANCE VIEW --}}
            @elseif($reportType === 'weekly')
                @if(empty($weeklyData['students']))
                    <div class="empty-state">
                        <h3>{{ __('No student records found!') }}</h3>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align:center;">#</th>
                                <th style="width: 110px;">{{ __('Reg Number') }}</th>
                                <th>{{ __('Student Name') }}</th>
                                <th style="width: 60px; text-align:center;">{{ __('Sex') }}</th>
                                @foreach($weeklyData['days'] as $dayInfo)
                                    <th style="text-align:center; min-width:85px;">
                                        <div>{{ __($dayInfo['day_name']) }}</div>
                                        <small style="font-weight:normal; color:#64748b;">{{ $dayInfo['day_number'] }}</small>
                                    </th>
                                @endforeach
                                <th style="width: 70px; text-align:center;">{{ __('Present') }}</th>
                                <th style="width: 70px; text-align:center;">{{ __('Absent') }}</th>
                                <th style="width: 85px; text-align:center;">{{ __('Rate %') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weeklyData['students'] as $idx => $row)
                                @php $st = $row['student']; @endphp
                                <tr>
                                    <td style="text-align:center; font-weight:bold; color:#64748b;">{{ $idx + 1 }}</td>
                                    <td><b>{{ $st->reg_number ?: 'N/A' }}</b></td>
                                    <td style="font-weight:600;">{{ $st->student_name }}</td>
                                    <td style="text-align:center; font-weight:bold; color: {{ $st->sex == 'F' ? '#db2777' : '#0284c7' }};">
                                        {{ $st->sex ?: '-' }}
                                    </td>
                                    @foreach($weeklyData['days'] as $dayInfo)
                                        @php $dStatus = $row['days'][$dayInfo['date']] ?? '-'; @endphp
                                        <td style="text-align:center;">
                                            @if($dStatus === 'Present')
                                                <span class="pill pill-present" style="padding:2px 7px; font-size:11px;">✓</span>
                                            @elseif($dStatus === 'Absent')
                                                <span class="pill pill-absent" style="padding:2px 7px; font-size:11px;">✕</span>
                                            @elseif($dStatus === 'Late' || $dStatus === 'Permission')
                                                <span class="pill pill-late" style="padding:2px 6px; font-size:10px;">{{ substr($dStatus, 0, 1) }}</span>
                                            @else
                                                <span style="color:#cbd5e1;">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="text-align:center; font-weight:bold; color:#16a34a;">{{ $row['present_count'] }}</td>
                                    <td style="text-align:center; font-weight:bold; color:#dc2626;">{{ $row['absent_count'] }}</td>
                                    <td style="text-align:center;">
                                        @if($row['rate_percent'] !== null)
                                            <span class="rate-badge {{ $row['rate_percent'] >= 75 ? 'rate-high' : ($row['rate_percent'] >= 50 ? 'rate-mid' : 'rate-low') }}">
                                                {{ $row['rate_percent'] }}%
                                            </span>
                                        @else
                                            <span style="color:#cbd5e1;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            {{-- 3. MONTHLY ATTENDANCE VIEW --}}
            @elseif($reportType === 'monthly')
                @if(empty($monthlyData))
                    <div class="empty-state">
                        <h3>{{ __('No attendance records found for this month!') }}</h3>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align:center;">#</th>
                                <th style="width: 120px;">{{ __('Reg Number') }}</th>
                                <th>{{ __('Student Name') }}</th>
                                <th style="width: 65px; text-align:center;">{{ __('Sex') }}</th>
                                <th style="width: 120px; text-align:center;">{{ __('Sessions Recorded') }}</th>
                                <th style="width: 100px; text-align:center;">{{ __('Present') }}</th>
                                <th style="width: 100px; text-align:center;">{{ __('Absent') }}</th>
                                <th style="width: 90px; text-align:center;">{{ __('Late') }}</th>
                                <th style="width: 110px; text-align:center;">{{ __('Monthly Rate %') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyData as $idx => $row)
                                @php $st = $row['student']; @endphp
                                <tr>
                                    <td style="text-align:center; font-weight:bold; color:#64748b;">{{ $idx + 1 }}</td>
                                    <td><b>{{ $st->reg_number ?: 'N/A' }}</b></td>
                                    <td style="font-weight:600; color:#0f172a;">{{ $st->student_name }}</td>
                                    <td style="text-align:center; font-weight:bold; color: {{ $st->sex == 'F' ? '#db2777' : '#0284c7' }};">
                                        {{ $st->sex ?: '-' }}
                                    </td>
                                    <td style="text-align:center; font-weight:bold; color:#475569;">{{ $row['total_days'] }}</td>
                                    <td style="text-align:center; font-weight:bold; color:#16a34a;">{{ $row['present_count'] }}</td>
                                    <td style="text-align:center; font-weight:bold; color:#dc2626;">{{ $row['absent_count'] }}</td>
                                    <td style="text-align:center; color:#c2410c;">{{ $row['late_count'] }}</td>
                                    <td style="text-align:center;">
                                        @if($row['total_days'] > 0)
                                            <span class="rate-badge {{ $row['rate_percent'] >= 75 ? 'rate-high' : ($row['rate_percent'] >= 50 ? 'rate-mid' : 'rate-low') }}">
                                                {{ $row['rate_percent'] }}%
                                            </span>
                                        @else
                                            <span style="color:#cbd5e1;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            {{-- 4. ANNUAL ATTENDANCE VIEW --}}
            @elseif($reportType === 'annual')
                @if(empty($annualData['students']))
                    <div class="empty-state">
                        <h3>{{ __('No attendance records found for this year!') }}</h3>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align:center;">#</th>
                                <th style="width: 100px;">{{ __('Reg Number') }}</th>
                                <th style="min-width: 170px;">{{ __('Student Name') }}</th>
                                <th style="width: 50px; text-align:center;">{{ __('Sex') }}</th>
                                @foreach($annualData['months'] as $mNum => $mLabel)
                                    <th style="text-align:center; font-size:10.5px;">{{ __($mLabel) }}</th>
                                @endforeach
                                <th style="width: 80px; text-align:center;">{{ __('Present') }}</th>
                                <th style="width: 80px; text-align:center;">{{ __('Total') }}</th>
                                <th style="width: 90px; text-align:center;">{{ __('Annual %') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($annualData['students'] as $idx => $row)
                                @php $st = $row['student']; @endphp
                                <tr>
                                    <td style="text-align:center; font-weight:bold; color:#64748b;">{{ $idx + 1 }}</td>
                                    <td><b>{{ $st->reg_number ?: 'N/A' }}</b></td>
                                    <td style="font-weight:600;">{{ $st->student_name }}</td>
                                    <td style="text-align:center; font-weight:bold; color: {{ $st->sex == 'F' ? '#db2777' : '#0284c7' }};">
                                        {{ $st->sex ?: '-' }}
                                    </td>
                                    @foreach($annualData['months'] as $mNum => $mLabel)
                                        @php $mInfo = $row['months'][$mNum] ?? null; @endphp
                                        <td style="text-align:center; font-size:11px;">
                                            @if($mInfo && $mInfo['rate'] !== null)
                                                <span class="rate-badge {{ $mInfo['rate'] >= 75 ? 'rate-high' : ($mInfo['rate'] >= 50 ? 'rate-mid' : 'rate-low') }}" style="padding:2px 5px; font-size:10.5px;">
                                                    {{ round($mInfo['rate']) }}%
                                                </span>
                                            @else
                                                <span style="color:#cbd5e1;">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="text-align:center; font-weight:bold; color:#16a34a;">{{ $row['year_present'] }}</td>
                                    <td style="text-align:center; font-weight:bold; color:#475569;">{{ $row['year_total'] }}</td>
                                    <td style="text-align:center;">
                                        @if($row['year_total'] > 0)
                                            <span class="rate-badge {{ $row['annual_rate'] >= 75 ? 'rate-high' : ($row['annual_rate'] >= 50 ? 'rate-mid' : 'rate-low') }}">
                                                {{ $row['annual_rate'] }}%
                                            </span>
                                        @else
                                            <span style="color:#cbd5e1;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif

        </div>
    </div>

</div>
</div>

</body>
</html>
