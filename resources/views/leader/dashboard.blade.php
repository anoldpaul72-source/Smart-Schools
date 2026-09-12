<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Dashboard - NECTA Broadsheet Format</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 15px;
            font-size: 11px;
            color: #000000;
        }

        .filter-panel {
            background: #0f172a;
            color: white;
            padding: 12px 18px;
            display: flex;
            gap: 14px;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .filter-panel label {
            font-size: 13px;
            font-weight: bold;
            color: #cbd5e1;
        }

        .filter-panel select,
        .filter-panel button,
        .filter-panel .btn-timetable {
            padding: 7px 14px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            font-size: 13px;
            font-weight: bold;
            font-family: inherit;
        }

        .filter-panel select {
            background-color: white;
            color: #0f172a;
        }

        .btn-generate {
            background: #059669 !important;
            color: white !important;
            border: none !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-generate:hover {
            background: #047857 !important;
        }

        .btn-print {
            background: #10b981 !important;
            color: white !important;
            border: none !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background: #059669 !important;
        }

        .btn-bulk-sms {
            background: #059669 !important;
            color: white !important;
            border: none !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            transition: background 0.15s;
        }

        .btn-bulk-sms:hover {
            background: #047857 !important;
        }

        .btn-table-sms {
            background-color: #059669;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: bold;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            transition: background-color 0.15s;
        }

        .btn-table-sms:hover {
            background-color: #047857;
        }

        /* SMS Modal Backdrop & Card */
        .sms-modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.65);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .sms-modal-card {
            background: #ffffff;
            width: 92%;
            max-width: 520px;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            font-size: 13px;
            color: #1e293b;
        }

        .sms-preview-card {
            background: #f1f5f9;
            border: 1px dashed #94a3b8;
            border-radius: 8px;
            padding: 12px;
            font-family: monospace;
            font-size: 12px;
            color: #0f172a;
            white-space: pre-line;
            line-height: 1.4;
            margin: 10px 0;
        }

        @page {
            size: A4 landscape;
            margin: 4mm 4mm 4mm 4mm;
        }

        @media print {
            html, body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                font-family: Arial, sans-serif !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .filter-panel, .btn-table-sms, .sms-modal-backdrop, .no-print, .th-sms, .td-sms, .alert-banner, [class*="alert"], button, .logout-link {
                display: none !important;
            }

            .broadsheet-container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            .system-header {
                font-size: 11px !important;
                margin-bottom: 1px !important;
            }

            .school-header {
                font-size: 12px !important;
                margin-bottom: 1px !important;
            }

            .exam-header {
                font-size: 10px !important;
                margin-bottom: 6px !important;
            }

            .top-summary-grid {
                margin-bottom: 6px !important;
                gap: 8px !important;
            }

            .graph-box, .mini-table, .gpa-box {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .main-table-container {
                overflow: visible !important;
                width: 100% !important;
                border: 0.5px solid #000 !important;
                margin-top: 5px !important;
            }

            .broadsheet-table {
                width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
            }

            .broadsheet-table th, .broadsheet-table td {
                border: 0.5px solid #000 !important;
                padding: 2px 0.5px !important;
                font-size: 7.5px !important;
                line-height: 1.1 !important;
                text-align: center !important;
            }

            .broadsheet-table th {
                background-color: #e0f2fe !important;
                font-size: 7px !important;
                font-weight: 800 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .th-index, .td-index { width: 22px !important; }
            .th-reg, .td-reg { width: 56px !important; font-size: 6.5px !important; }
            .th-name, .student-name-left { width: 95px !important; font-size: 6.8px !important; padding-left: 2px !important; white-space: normal !important; overflow: hidden !important; text-overflow: ellipsis !important; }
            .th-sex, .td-sex { width: 14px !important; }
            .th-sub-mrk, .td-sub-mrk { width: 14px !important; }
            .th-sub-grd, .td-sub-grd { width: 12px !important; }
            .th-total, .td-total { width: 22px !important; font-size: 7px !important; font-weight: bold !important; }
            .th-avrg, .td-avrg { width: 24px !important; font-size: 7px !important; font-weight: bold !important; }
            .th-agrd, .td-agrd { width: 15px !important; font-size: 7px !important; font-weight: bold !important; }
            .th-pts, .td-pts { width: 15px !important; font-size: 7px !important; font-weight: bold !important; }
            .th-dvsn, .td-dvsn { width: 18px !important; font-size: 7px !important; font-weight: bold !important; }
            .th-rank, .td-rank { width: 18px !important; font-size: 7px !important; font-weight: bold !important; }

            thead {
                display: table-header-group !important;
            }

            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .subject-breakdown-box {
                margin-top: 15px !important;
                page-break-before: auto !important;
            }
            .subject-necta-table th, .subject-necta-table td {
                padding: 2px 2px !important;
                font-size: 8px !important;
                border: 0.5px solid #000 !important;
            }
        }

        .btn-timetable {
            background: #2563eb !important;
            color: white !important;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none !important;
        }

        .btn-timetable:hover {
            background: #1d4ed8 !important;
        }

        .btn-home {
            background: #475569 !important;
            color: white !important;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none !important;
        }

        .logout-link {
            margin-left: auto;
            color: #f87171;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .broadsheet-container {
            width: 100%;
            max-width: 1550px;
            margin: 0 auto;
            background: white;
            padding: 16px;
            border: 2px solid #000;
            box-sizing: border-box;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .system-header {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            color: #0284c7;
            text-transform: uppercase;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }

        .school-header {
            text-align: center;
            font-weight: 800;
            font-size: 16px;
            color: #b91c1c;
            text-transform: uppercase;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }

        .exam-header {
            text-align: center;
            font-weight: 800;
            font-size: 13px;
            color: #16a34a;
            text-transform: uppercase;
            margin-bottom: 18px;
            letter-spacing: 0.3px;
        }

        .top-summary-grid {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 16px;
            margin-bottom: 20px;
            align-items: start;
        }

        @media (max-width: 992px) {
            .top-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        .graph-box {
            border: 1px solid #cbd5e1;
            height: 190px;
            padding: 10px;
            text-align: center;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .graph-title {
            font-weight: 800;
            color: #1e293b;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 4px;
        }

        .bar-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            height: 115px;
            padding: 0 10px;
        }

        .division-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 18%;
        }

        .bars-flex {
            display: flex;
            gap: 4px;
            align-items: flex-end;
            width: 100%;
            height: 95px;
            border-bottom: 2px solid #64748b;
            padding-bottom: 2px;
        }

        .g-bar-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            width: 50%;
            height: 100%;
        }

        .bar-value {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 2px;
            color: #334155;
        }

        .g-bar {
            width: 100%;
            border-radius: 3px 3px 0 0;
            transition: all 0.3s ease;
            box-shadow: 1px -1px 3px rgba(0,0,0,0.15);
        }

        .g-bar.female {
            background: linear-gradient(to top, #ec4899, #f472b6);
            border: 1px solid #db2777;
        }

        .g-bar.male {
            background: linear-gradient(to top, #0284c7, #38bdf8);
            border: 1px solid #0369a1;
        }

        .div-label {
            font-weight: 800;
            font-size: 11px;
            margin-top: 5px;
            padding: 2px 6px;
            background: #f1f5f9;
            border-radius: 4px;
            width: 80%;
            text-align: center;
        }

        .summary-tables {
            display: flex;
            gap: 15px;
            align-items: start;
            flex-wrap: wrap;
        }

        .mini-table {
            border-collapse: collapse;
            background: white;
        }

        .mini-table th, .mini-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: center;
            font-size: 10px;
        }

        .mini-table th {
            background-color: #e0f2fe;
            font-weight: bold;
        }

        .f-row { color: #db2777; font-weight: bold; }
        .m-row { color: #0284c7; font-weight: bold; }
        .ttl-row { font-weight: bold; background-color: #f1f5f9; }

        .gpa-box {
            border: 1px solid #000;
            background: #fef2f2;
            padding: 10px 16px;
            text-align: center;
            min-width: 130px;
            border-radius: 4px;
        }

        .gpa-title {
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            color: #475569;
        }

        .gpa-flex {
            display: flex;
            justify-content: space-around;
            margin-top: 6px;
            font-size: 11px;
        }

        .main-table-container {
            overflow-x: auto;
            width: 100%;
            border: 1px solid #000;
            margin-top: 15px;
        }

        .broadsheet-table {
            width: 100%;
            border-collapse: collapse;
        }

        .broadsheet-table th, .broadsheet-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            font-size: 11px;
            text-align: center;
        }

        .broadsheet-table th {
            background-color: #e0f2fe;
            color: #000;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        .student-name-left {
            text-align: left !important;
            padding-left: 6px !important;
            text-transform: uppercase;
            font-weight: bold !important;
            color: #0f172a;
        }

        .fail-score {
            color: #dc2626;
            font-weight: bold;
        }

        /* NECTA Subject Breakdown Styling */
        .subject-breakdown-box {
            margin-top: 25px;
            width: 100%;
            background: white;
        }

        .subject-breakdown-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
            margin-bottom: 6px;
        }

        .subject-necta-table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
        }

        .subject-necta-table th, .subject-necta-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            text-align: center;
            font-size: 10px;
        }

        .subject-necta-table th {
            background-color: #ffffff;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
        }

        .sub-title-td {
            font-weight: bold;
            vertical-align: middle;
            text-transform: uppercase;
            font-size: 11px;
        }

        @media print {
            .filter-panel { display: none !important; }
            body { margin: 0; padding: 0; background: white; }
            .broadsheet-container { border: none; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body>

<!-- Flash Message Alerts -->
@if(session('success'))
    <div class="alert-banner" style="background: #ecfdf5; border: 1.5px solid #10b981; color: #065f46; padding: 12px 18px; border-radius: 8px; margin-bottom: 15px; font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 20px;">✅</span>
        <div style="flex: 1;">{{ session('success') }}</div>
    </div>
@endif
@if(session('error'))
    <div class="alert-banner" style="background: #fef2f2; border: 1.5px solid #ef4444; color: #991b1b; padding: 12px 18px; border-radius: 8px; margin-bottom: 15px; font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 20px;">⚠️</span>
        <div style="flex: 1;">{{ session('error') }}</div>
    </div>
@endif

<!-- Top Navigation & Filter Bar -->
<div class="filter-panel">
    <form method="GET" action="{{ route('leader.dashboard') }}" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <label>{{ __('Class') }}:</label>
        <select name="class">
            @foreach($availableClasses as $cls)
                <option value="{{ $cls }}" {{ $selectedClass === $cls ? 'selected' : '' }}>{{ $cls }}</option>
            @endforeach
        </select>

        <label>{{ __('Assessment Type') }}:</label>
        <select name="exam_type">
            @foreach(['Weekly Test', 'Monthly Test', 'Terminal Examination', 'Annual Examination'] as $etype)
                <option value="{{ $etype }}" {{ $selectedExam === $etype ? 'selected' : '' }}>{{ __($etype) }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn-generate">🔍 {{ __('Generate Broadsheet') }}</button>
    </form>

    <button type="button" class="btn-print" onclick="window.print();">🖨️ {{ __('Print Broadsheet') }}</button>

    <button type="button" class="btn-bulk-sms" onclick="openLeaderBulkSmsModal()" title="{{ __('Tuma Matokeo ya Darasa Hili kwa Wazazi kwa SMS') }}">
        <span>📱</span> <span>{{ __('Tuma SMS kwa Wazazi') }}</span>
    </button>

    @if(auth()->user()->role === 'Academic Master' || auth()->user()->isAdmin() || auth()->user()->isTeacher() || auth()->user()->teacherAssignments()->exists())
        <a href="{{ route('teacher.marks') }}" class="btn-timetable" style="background: #2563eb !important; color: white !important; border: none !important;" title="{{ __('Ingiza Alama za Wanafunzi') }}">
            📝 {{ __('Marks Entry') }}
        </a>
        <a href="{{ route('teacher.attendance') }}" class="btn-timetable" style="background: #059669 !important; color: white !important; border: none !important;" title="{{ __('Mahudhurio ya Wanafunzi') }}">
            📋 {{ __('Attendance') }}
        </a>
    @endif

    <a href="{{ route('timetable.index') }}" class="btn-timetable">
        📅 {{ __('School Timetable') }}
    </a>

    <a href="{{ route('home') }}" class="btn-home">
        🏠 {{ __('Home') }}
    </a>

    <!-- Language Switcher -->
    <div style="display: inline-flex; align-items: center; background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 2px 4px; gap: 4px;">
        <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#38bdf8' : '#94a3b8' }}; background: {{ app()->getLocale() == 'en' ? '#0f172a' : 'transparent' }};">🇬🇧 EN</a>
        <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#38bdf8' : '#94a3b8' }}; background: {{ app()->getLocale() == 'sw' ? '#0f172a' : 'transparent' }};">🇹🇿 SW</a>
    </div>

    <form method="POST" action="{{ route('logout') }}" style="margin-left: auto;">
        @csrf
        <button type="submit" class="logout-link">🚪 {{ __('Logout') }}</button>
    </form>
</div>

<!-- Main Broadsheet Document -->
<div class="broadsheet-container">
    <div class="system-header">THE UNITED REPUBLIC OF TANZANIA</div>
    <div class="system-header">THE PRIME MINISTER'S OFFICE, REGIONAL ADMINSTRATION AND LOCAL GOVERNMENT</div>
    <div class="school-header">{{ strtoupper($schoolName) }}</div>
    <div class="exam-header">{{ strtoupper($selectedClass) }} — {{ strtoupper($selectedExam) }} SUMMARY REPORT (MONTH: {{ strtoupper($examMonthName) }})</div>

    <!-- Grading Scale Key / Vigezo vya Madaraja -->
    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; flex-wrap: wrap; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 14px; margin-bottom: 14px; font-size: 11px;">
        <span style="font-weight: 800; color: #1e293b; text-transform: uppercase; display: inline-flex; align-items: center; gap: 4px;">
            <span>📐</span> {{ __('Grading Scale:') }}
        </span>
        <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; border-radius: 4px; padding: 2px 8px; font-weight: 700;">
            A: 75 – 100 <small style="font-weight: normal; opacity: 0.85;">(Excellent)</small>
        </span>
        <span style="background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; border-radius: 4px; padding: 2px 8px; font-weight: 700;">
            B: 60 – 74 <small style="font-weight: normal; opacity: 0.85;">(Very Good)</small>
        </span>
        <span style="background: #fef9c3; color: #a16207; border: 1px solid #fde047; border-radius: 4px; padding: 2px 8px; font-weight: 700;">
            C: 45 – 59 <small style="font-weight: normal; opacity: 0.85;">(Good)</small>
        </span>
        <span style="background: #ffedd5; color: #c2410c; border: 1px solid #fdba74; border-radius: 4px; padding: 2px 8px; font-weight: 700;">
            D: 30 – 44 <small style="font-weight: normal; opacity: 0.85;">(Pass)</small>
        </span>
        <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 4px; padding: 2px 8px; font-weight: 700;">
            F: 0 – 29 <small style="font-weight: normal; opacity: 0.85;">(Fail)</small>
        </span>
    </div>

    <!-- Top Summary Grid: Division Chart & Summary Mini Tables -->
    <div class="top-summary-grid">
        <!-- Division Distribution Graph -->
        <div class="graph-box">
            <div class="graph-title">📊 Graph of Division Distribution</div>
            
            <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 6px; font-size: 10px; font-weight: bold;">
                <div style="display: flex; align-items: center; gap: 4px;">
                    <div style="width: 12px; height: 12px; background: linear-gradient(to top, #ec4899, #f472b6); border: 1px solid #db2777; border-radius: 2px;"></div>
                    <span>Female (F)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 4px;">
                    <div style="width: 12px; height: 12px; background: linear-gradient(to top, #0284c7, #38bdf8); border: 1px solid #0369a1; border-radius: 2px;"></div>
                    <span>Male (M)</span>
                </div>
            </div>

            <div class="bar-container">
                @php
                    $divNames = ['I', 'II', 'III', 'IV', '0'];
                    $lblColors = ['I'=>'#16a34a', 'II'=>'#2563eb', 'III'=>'#ca8a04', 'IV'=>'#ea580c', '0'=>'#dc2626'];
                @endphp

                @foreach($divNames as $dName)
                    <div class="division-group">
                        <div class="bars-flex">
                            <!-- Female Bar -->
                            <div class="g-bar-wrapper">
                                <span class="bar-value">{{ $divCounters['F'][$dName] > 0 ? $divCounters['F'][$dName] : '' }}</span>
                                <div class="g-bar female" style="height: {{ max(($divCounters['F'][$dName] / $maxStudents) * 75, 3) }}%;"></div>
                                <span style="font-size: 8px; color: #64748b; font-weight: bold; margin-top: 2px;">F</span>
                            </div>
                            <!-- Male Bar -->
                            <div class="g-bar-wrapper">
                                <span class="bar-value">{{ $divCounters['M'][$dName] > 0 ? $divCounters['M'][$dName] : '' }}</span>
                                <div class="g-bar male" style="height: {{ max(($divCounters['M'][$dName] / $maxStudents) * 75, 3) }}%;"></div>
                                <span style="font-size: 8px; color: #64748b; font-weight: bold; margin-top: 2px;">M</span>
                            </div>
                        </div>
                        <div class="div-label" style="color: {{ $lblColors[$dName] }}; border-left: 3px solid {{ $lblColors[$dName] }};">
                            {{ $dName }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Summary Tables & Registered Count Box -->
        <div class="summary-tables">
            <!-- Division Summary Table -->
            <table class="mini-table">
                <thead>
                    <tr>
                        <th rowspan="2">SEX</th>
                        <th colspan="5">DIVISION SUMMARY</th>
                        <th rowspan="2">TOTAL</th>
                    </tr>
                    <tr>
                        <th>I</th><th>II</th><th>III</th><th>IV</th><th>0</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="f-row">
                        <td>F</td>
                        <td>{{ $divCounters['F']['I'] }}</td>
                        <td>{{ $divCounters['F']['II'] }}</td>
                        <td>{{ $divCounters['F']['III'] }}</td>
                        <td>{{ $divCounters['F']['IV'] }}</td>
                        <td>{{ $divCounters['F']['0'] }}</td>
                        <td>{{ $genderTotals['F'] }}</td>
                    </tr>
                    <tr class="m-row">
                        <td>M</td>
                        <td>{{ $divCounters['M']['I'] }}</td>
                        <td>{{ $divCounters['M']['II'] }}</td>
                        <td>{{ $divCounters['M']['III'] }}</td>
                        <td>{{ $divCounters['M']['IV'] }}</td>
                        <td>{{ $divCounters['M']['0'] }}</td>
                        <td>{{ $genderTotals['M'] }}</td>
                    </tr>
                    <tr class="ttl-row">
                        <td>TTL</td>
                        <td>{{ $totalDivI }}</td>
                        <td>{{ $totalDivII }}</td>
                        <td>{{ $totalDivIII }}</td>
                        <td>{{ $totalDivIV }}</td>
                        <td>{{ $totalDiv0 }}</td>
                        <td>{{ count($studentsData) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Average Grades Overview Table -->
            <table class="mini-table">
                <thead>
                    <tr>
                        <th rowspan="2">SEX</th>
                        <th colspan="5">AVERAGE GRADES OVERVIEW</th>
                        <th rowspan="2">TOTAL</th>
                    </tr>
                    <tr>
                        <th>A</th><th>B</th><th>C</th><th>D</th><th>F</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="f-row">
                        <td>F</td>
                        <td>{{ $gpaCounters['F']['A'] }}</td>
                        <td>{{ $gpaCounters['F']['B'] }}</td>
                        <td>{{ $gpaCounters['F']['C'] }}</td>
                        <td>{{ $gpaCounters['F']['D'] }}</td>
                        <td>{{ $gpaCounters['F']['F'] }}</td>
                        <td>{{ $genderTotals['F'] }}</td>
                    </tr>
                    <tr class="m-row">
                        <td>M</td>
                        <td>{{ $gpaCounters['M']['A'] }}</td>
                        <td>{{ $gpaCounters['M']['B'] }}</td>
                        <td>{{ $gpaCounters['M']['C'] }}</td>
                        <td>{{ $gpaCounters['M']['D'] }}</td>
                        <td>{{ $gpaCounters['M']['F'] }}</td>
                        <td>{{ $genderTotals['M'] }}</td>
                    </tr>
                    <tr class="ttl-row">
                        <td>TTL</td>
                        <td>{{ $gpaCounters['F']['A'] + $gpaCounters['M']['A'] }}</td>
                        <td>{{ $gpaCounters['F']['B'] + $gpaCounters['M']['B'] }}</td>
                        <td>{{ $gpaCounters['F']['C'] + $gpaCounters['M']['C'] }}</td>
                        <td>{{ $gpaCounters['F']['D'] + $gpaCounters['M']['D'] }}</td>
                        <td>{{ $gpaCounters['F']['F'] + $gpaCounters['M']['F'] }}</td>
                        <td>{{ count($studentsData) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Registered Badge Box -->
            <div class="gpa-box">
                <div class="gpa-title">REGISTERED</div>
                <div style="font-size: 22px; font-weight: 800; color: #0284c7; margin: 4px 0;">
                    {{ count($studentsData) }}
                </div>
                <div class="gpa-flex">
                    <span class="f-row">F:{{ $genderTotals['F'] }}</span>
                    <span class="m-row">M:{{ $genderTotals['M'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- The Grand NECTA Broadsheet Table -->
    <div class="main-table-container">
        <table class="broadsheet-table">
            <thead>
                <tr>
                    <th rowspan="2" class="th-index" style="width: 45px;">INDEX</th>
                    <th rowspan="2" class="th-reg" style="width: 120px;">REG NUMBER</th>
                    <th rowspan="2" class="th-name" style="text-align: left; padding-left: 6px;">NAME OF STUDENTS</th>
                    <th rowspan="2" class="th-sex" style="width: 35px;">SEX</th>

                    @foreach($subjects as $subject)
                        <th colspan="2">{{ $subject->subject_name }}</th>
                    @endforeach

                    <th rowspan="2" class="th-total" style="width: 55px;">TOTAL</th>
                    <th rowspan="2" class="th-avrg" style="width: 55px;">AVRG</th>
                    <th rowspan="2" class="th-agrd" style="width: 45px;">A.GRD</th>
                    <th rowspan="2" class="th-pts" style="width: 45px;">PTS</th>
                    <th rowspan="2" class="th-dvsn" style="width: 45px;">DVSN</th>
                    <th rowspan="2" class="th-rank" style="width: 45px;">RANK</th>
                    <th rowspan="2" class="th-sms" style="width: 55px;">SMS</th>
                </tr>
                <tr>
                    @foreach($subjects as $subject)
                        <th class="th-sub-mrk">MRK</th>
                        <th class="th-sub-grd">GRD</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if(count($studentsData) > 0)
                    @php $counter = 1; @endphp
                    @foreach($studentsData as $student)
                        @php
                            $indexNum = str_pad($counter++, 4, '0', STR_PAD_LEFT);
                            $sexColor = ($student['sex'] === 'F') ? '#db2777' : '#0284c7';
                        @endphp
                        <tr>
                            <td class="td-index">{{ $indexNum }}</td>
                            <td class="td-reg" style="font-weight: bold; color: #475569;">{{ $student['reg_number'] }}</td>
                            <td class="student-name-left">{{ $student['student_name'] }}</td>
                            <td class="td-sex" style="font-weight: bold; color: {{ $sexColor }};">{{ $student['sex'] }}</td>

                            @foreach($subjects as $subject)
                                @php
                                    $score = $student['scores'][$subject->id] ?? null;
                                    $gData = app(\App\Http\Controllers\LeaderController::class)->getGradeInfo($score);
                                    $failStyle = in_array($gData['G'], ['F', 'D']) ? 'fail-score' : '';
                                @endphp
                                <td class="td-sub-mrk {{ $failStyle }}">{{ $score !== null ? $score : '-' }}</td>
                                <td class="td-sub-grd" style="font-weight: bold; color: {{ $gData['C'] }};">{{ $gData['G'] }}</td>
                            @endforeach

                            <td class="td-total" style="font-weight: bold; background: #f8fafc;">{{ $student['total'] }}</td>
                            <td class="td-avrg" style="font-weight: bold; background: #f0f9ff; color: #0284c7;">
                                {{ $student['count'] > 0 ? $student['average'] : '-' }}
                            </td>
                            <td class="td-agrd" style="font-weight: bold;">
                                {{ $student['count'] > 0 ? app(\App\Http\Controllers\LeaderController::class)->getGradeInfo($student['average'])['G'] : '-' }}
                            </td>
                            <td class="td-pts" style="font-weight: bold; color: #d97706;">{{ $student['points'] }}</td>
                            <td class="td-dvsn" style="font-weight: bold; color: #16a34a;">{{ $student['division'] }}</td>
                            <td class="td-rank" style="font-weight: bold; background: #f0fdf4; color: #166534; font-size: 12px;">
                                {{ $student['rank'] }}
                            </td>
                            <td class="td-sms">
                                <button type="button" class="btn-table-sms" onclick="openLeaderSingleSmsModal({{ $student['id'] }}, '{{ addslashes($student['student_name']) }}', '{{ $student['parent_phone'] ?? '' }}', '{{ $selectedExam }}')" title="{{ __('Tuma SMS kwa Mzazi') }}">
                                    📱 SMS
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ (count($subjects) * 2) + 11 }}" style="padding: 30px; text-align: center; font-style: italic; color: #94a3b8;">
                            ❌ No academic records found for this class on the selected assessment type.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Subjects Performance Summary Table -->
    <div class="subject-breakdown-box">
        <div class="subject-breakdown-title">SUBJECTS SUMMARY</div>
        <table class="subject-necta-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 130px;">SUBJECT'S</th>
                    <th rowspan="2" style="width: 30px;">SEX</th>
                    <th rowspan="2" style="width: 35px;">REG</th>
                    <th rowspan="2" style="width: 35px;">ABS</th>
                    <th rowspan="2" style="width: 35px;">CSE</th>
                    <th rowspan="2" style="width: 35px;">CD</th>
                    <th colspan="5">GRADE'S</th>
                    <th rowspan="2" style="width: 40px;">A-D</th>
                    <th rowspan="2" style="width: 45px;">(A-D)%</th>
                    <th rowspan="2" style="width: 40px;">AVG</th>
                    <th rowspan="2" style="width: 55px;">GPA</th>
                    <th rowspan="2" style="width: 35px;">PSN</th>
                </tr>
                <tr>
                    <th style="width: 25px;">A</th>
                    <th style="width: 25px;">B</th>
                    <th style="width: 25px;">C</th>
                    <th style="width: 25px;">D</th>
                    <th style="width: 25px;">F</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subjectStats as $subId => $st)
                    <!-- Female Row -->
                    <tr class="f-row">
                        <td rowspan="3" class="sub-title-td" style="color:#000;">{{ $st['name'] }}</td>
                        <td>F</td>
                        <td>{{ $st['reg']['F'] }}</td>
                        <td>{{ $st['abs']['F'] > 0 ? '-'.$st['abs']['F'] : '0' }}</td>
                        <td>{{ $st['sat']['F'] }}</td>
                        <td>{{ $st['sat']['F'] }}</td>
                        <td>{{ $st['grades']['F']['A'] }}</td>
                        <td>{{ $st['grades']['F']['B'] }}</td>
                        <td>{{ $st['grades']['F']['C'] }}</td>
                        <td>{{ $st['grades']['F']['D'] }}</td>
                        <td>{{ $st['grades']['F']['F'] }}</td>
                        <td>{{ $st['ad_pass']['F'] }}</td>
                        <td>{{ $st['ad_pct']['F'] }}%</td>
                        <td rowspan="3" style="vertical-align: middle; font-weight: bold; color: #000;">{{ $st['avg'] }}</td>
                        <td rowspan="3" style="vertical-align: middle; font-weight: bold; color: #000;">{{ $st['gpa'] > 0 ? sprintf("%.4f", $st['gpa']) : '-' }}</td>
                        <td rowspan="3" style="vertical-align: middle; font-weight: bold; color: #166534; background: #f0fdf4;">{{ $st['psn'] }}</td>
                    </tr>
                    <!-- Male Row -->
                    <tr class="m-row">
                        <td>M</td>
                        <td>{{ $st['reg']['M'] }}</td>
                        <td>{{ $st['abs']['M'] > 0 ? '-'.$st['abs']['M'] : '0' }}</td>
                        <td>{{ $st['sat']['M'] }}</td>
                        <td>{{ $st['sat']['M'] }}</td>
                        <td>{{ $st['grades']['M']['A'] }}</td>
                        <td>{{ $st['grades']['M']['B'] }}</td>
                        <td>{{ $st['grades']['M']['C'] }}</td>
                        <td>{{ $st['grades']['M']['D'] }}</td>
                        <td>{{ $st['grades']['M']['F'] }}</td>
                        <td>{{ $st['ad_pass']['M'] }}</td>
                        <td>{{ $st['ad_pct']['M'] }}%</td>
                    </tr>
                    <!-- Total Row -->
                    <tr class="ttl-row" style="color:#000;">
                        <td>T</td>
                        <td>{{ $st['reg']['T'] }}</td>
                        <td>{{ $st['abs']['T'] > 0 ? '-'.$st['abs']['T'] : '0' }}</td>
                        <td>{{ $st['sat']['T'] }}</td>
                        <td>{{ $st['sat']['T'] }}</td>
                        <td>{{ $st['grades']['T']['A'] }}</td>
                        <td>{{ $st['grades']['T']['B'] }}</td>
                        <td>{{ $st['grades']['T']['C'] }}</td>
                        <td>{{ $st['grades']['T']['D'] }}</td>
                        <td>{{ $st['grades']['T']['F'] }}</td>
                        <td>{{ $st['ad_pass']['T'] }}</td>
                        <td>{{ $st['ad_pct']['T'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL YA BULK SMS (KWA WAZAZI WOTE WA DARASA HILI KWENYE BROADSHEET) -->
<div id="leaderBulkSmsModal" class="sms-modal-backdrop">
    <div class="sms-modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 24px;">📱</span>
                <div>
                    <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a;">{{ __('Tuma Ripoti ya Matokeo kwa SMS (Bulk SMS)') }}</h3>
                    <p style="margin: 2px 0 0 0; font-size: 11.5px; color: #64748b;">Kutuma kwa wazazi wote wa darasa hili mara moja</p>
                </div>
            </div>
            <button type="button" onclick="closeLeaderBulkSmsModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>
        </div>

        <form method="POST" action="{{ route('sms.send_bulk') }}" id="leaderBulkSmsForm" onsubmit="handleLeaderBulkSubmit()">
            @csrf
            <input type="hidden" name="class_name" value="{{ $selectedClass }}">
            <input type="hidden" name="term" value="{{ $selectedExam }}">

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 14px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="font-weight: 600; color: #64748b;">Darasa:</span>
                    <strong style="color: #0f172a; font-size: 14px;">{{ $selectedClass }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="font-weight: 600; color: #64748b;">Aina ya Mtihani:</span>
                    <strong style="color: #0284c7; font-size: 14px;">{{ $selectedExam }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-weight: 600; color: #64748b;">Wanafunzi Waliosajiliwa:</span>
                    <strong style="color: #059669; font-size: 14px;">{{ count($studentsData) }} wanafunzi</strong>
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: bold; font-size: 12px; color: #475569; text-transform: uppercase;">{{ __('Mfano wa Ujumbe Utakaotumwa (SMS Preview):') }}</label>
                <div class="sms-preview-card">
MZAZI WA [JINA LA MWANAFUNZI] ({{ $selectedClass }})
Ripoti: {{ $selectedExam }} - {{ $schoolName }}
Matokeo: Kiswahili: 82(A), Maths: 68(B), English: 75(B), Physics: 64(C), Bio: 80(A)
Wastani: 73.2% (Daraja: B)
Mahudhurio: 96% | Ada Inayodaiwa: 0 TZS
Kazi nzuri na hongera.
                </div>
                <div style="font-size: 11.5px; color: #64748b; line-height: 1.4;">
                    ℹ️ Ujumbe utatumwa kwa namba za wazazi zilizohifadhiwa. Ikiwa unatumia mfumo bila API keys au majaribio, ujumbe utahifadhiwa kwenye <strong>Simulated Mode</strong>.
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                <button type="button" onclick="closeLeaderBulkSmsModal()" style="padding: 9px 16px; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 6px; font-weight: bold; cursor: pointer; color: #475569;">
                    Ghairi
                </button>
                <button type="submit" id="btnLeaderBulkSubmit" style="padding: 9px 20px; background: #059669; color: #ffffff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <span>📱</span>
                    <span>{{ __('Thibitisha & Tuma SMS Sasa') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL YA SINGLE SMS (MWANAFUNZI MMOJA KWENYE BROADSHEET) -->
<div id="leaderSingleSmsModal" class="sms-modal-backdrop">
    <div class="sms-modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 24px;">✉️</span>
                <div>
                    <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a;">{{ __('Tuma Ripoti kwa Mzazi') }}</h3>
                    <p style="margin: 2px 0 0 0; font-size: 11.5px; color: #64748b;">Ujumbe wa matokeo ya mwanafunzi binafsi</p>
                </div>
            </div>
            <button type="button" onclick="closeLeaderSingleSmsModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>
        </div>

        <form method="POST" action="{{ route('sms.send_single') }}" id="leaderSingleSmsForm">
            @csrf
            <input type="hidden" name="student_id" id="leader_single_student_id">
            <input type="hidden" name="term" id="leader_single_term" value="{{ $selectedExam }}">

            <div style="margin-bottom: 14px; background: #f8fafc; padding: 10px 14px; border-radius: 6px; border: 1px solid #e2e8f0;">
                <label style="display: block; font-weight: bold; font-size: 12px; margin-bottom: 4px; color: #64748b;">Mwanafunzi:</label>
                <div id="leader_single_student_name" style="font-size: 15px; font-weight: 800; color: #0f172a;">-</div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 6px; color: #334155;">
                    {{ __('Namba ya Simu ya Mzazi (Tanzania):') }} <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" name="phone" id="leader_single_phone" required placeholder="k.m. 0712345678 au +255..." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Namba hii itahifadhiwa pia kwenye taarifa za mwanafunzi huyu.</div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                <button type="button" onclick="closeLeaderSingleSmsModal()" style="padding: 9px 16px; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 6px; font-weight: bold; cursor: pointer; color: #475569;">Ghairi</button>
                <button type="submit" style="padding: 9px 20px; background: #059669; color: #ffffff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    ✉️ {{ __('Tuma SMS Sasa') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openLeaderBulkSmsModal() {
    const modal = document.getElementById('leaderBulkSmsModal');
    if (modal) modal.style.display = 'flex';
}

function closeLeaderBulkSmsModal() {
    const modal = document.getElementById('leaderBulkSmsModal');
    if (modal) modal.style.display = 'none';
}

function handleLeaderBulkSubmit() {
    const btn = document.getElementById('btnLeaderBulkSubmit');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>⏳</span> <span>Inatuma SMS... Tafadhali subiri</span>';
    }
}

function openLeaderSingleSmsModal(studentId, studentName, phone, term) {
    document.getElementById('leader_single_student_id').value = studentId;
    document.getElementById('leader_single_student_name').innerText = studentName;
    document.getElementById('leader_single_phone').value = phone || '';
    if (term) {
        document.getElementById('leader_single_term').value = term;
    }
    const modal = document.getElementById('leaderSingleSmsModal');
    if (modal) modal.style.display = 'flex';
}

function closeLeaderSingleSmsModal() {
    const modal = document.getElementById('leaderSingleSmsModal');
    if (modal) modal.style.display = 'none';
}

window.addEventListener('click', function(event) {
    const bulkModal = document.getElementById('leaderBulkSmsModal');
    const singleModal = document.getElementById('leaderSingleSmsModal');
    if (event.target === bulkModal) closeLeaderBulkSmsModal();
    if (event.target === singleModal) closeLeaderSingleSmsModal();
});
</script>
</body>
</html>
