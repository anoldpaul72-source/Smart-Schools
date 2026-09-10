@extends('layouts.app')

@section('title', __('STUDENT PROGRESS REPORT') . ' | Smart-Results')

@section('no_global_header', true)

@section('styles')
<style>
    body {
        background-color: #f1f5f9 !important;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .parent-portal-wrapper {
        max-width: 1040px;
        margin: 20px auto 40px auto;
        padding: 0 15px;
    }

    /* Top Bar Card */
    .parent-top-card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        padding: 14px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .parent-welcome-text {
        font-size: 14px;
        color: #475569;
    }

    .parent-welcome-text strong {
        color: #0f172a;
        font-weight: 800;
        letter-spacing: 0.3px;
    }

    .top-actions-group {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    /* Language Switcher */
    .parent-lang-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .parent-lang-toggle a {
        text-decoration: none;
        color: #64748b;
        transition: color 0.15s;
    }

    .parent-lang-toggle a.active-lang {
        color: #0284c7;
        font-weight: 800;
    }

    .parent-lang-toggle a:hover {
        color: #0284c7;
    }

    /* Download PDF Button */
    .btn-download-report {
        background-color: #0284c7;
        color: #ffffff !important;
        border: none;
        border-radius: 6px;
        padding: 9px 18px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s, transform 0.1s;
        box-shadow: 0 1px 2px rgba(2, 132, 199, 0.2);
    }

    .btn-download-report:hover {
        background-color: #0369a1;
        transform: translateY(-1px);
    }

    /* Logout Link */
    .btn-parent-logout {
        background: none;
        border: none;
        color: #dc2626;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        padding: 4px;
        transition: opacity 0.2s;
    }

    .btn-parent-logout:hover {
        text-decoration: underline;
        opacity: 0.85;
    }

    /* Filter Card */
    .parent-filter-card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        padding: 14px 24px;
        margin-top: 16px;
    }

    .filter-inner-form {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .filter-label {
        font-size: 14px;
        font-weight: 700;
        color: #334155;
    }

    .filter-select {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 8px 36px 8px 12px;
        font-size: 14px;
        font-family: inherit;
        color: #1e293b;
        background-color: #ffffff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 16px;
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        outline: none;
        min-width: 200px;
    }

    .filter-select:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
    }

    .btn-view-report {
        background-color: #1e293b;
        color: #ffffff !important;
        border: none;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-view-report:hover {
        background-color: #0f172a;
    }

    /* Main Big Title */
    .report-main-title {
        font-size: 28px;
        font-weight: 900;
        color: #1e293b;
        text-align: center;
        margin: 28px 0 22px 0;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* Main Student Report Card */
    .student-main-report-card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        border: 1px solid #e2e8f0;
        padding: 30px 35px;
        margin-bottom: 30px;
    }

    .student-name-title {
        font-size: 24px;
        font-weight: 900;
        color: #0284c7;
        margin: 0 0 8px 0;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .student-meta-details {
        font-size: 14px;
        color: #475569;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .student-meta-details .highlight {
        color: #0284c7;
        font-weight: 800;
    }

    .meta-divider {
        color: #94a3b8;
        font-weight: 300;
    }

    .report-solid-divider {
        height: 3px;
        background-color: #0284c7;
        width: 100%;
        margin-top: 14px;
        margin-bottom: 24px;
        border-radius: 2px;
    }

    /* No Marks Notice */
    .no-marks-state {
        text-align: center;
        color: #94a3b8;
        font-style: italic;
        font-size: 14.5px;
        padding: 30px 0 25px 0;
    }

    /* Academic Marks Table */
    .marks-results-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0 25px 0;
    }

    .marks-results-table th {
        background-color: #f8fafc;
        color: #334155;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 11px 14px;
        border-bottom: 2px solid #e2e8f0;
        text-align: left;
    }

    .marks-results-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #1e293b;
    }

    .marks-results-table tbody tr:hover {
        background-color: #fafbfc;
    }

    .grade-badge {
        display: inline-block;
        font-weight: 800;
        font-size: 13px;
        padding: 2px 10px;
        border-radius: 4px;
        text-align: center;
    }

    .grade-a { background: #dcfce7; color: #15803d; }
    .grade-b { background: #e0f2fe; color: #0369a1; }
    .grade-c { background: #fef9c3; color: #854d0e; }
    .grade-d { background: #ffedd5; color: #9a3412; }
    .grade-f { background: #fee2e2; color: #b91c1c; }

    /* ATTENDANCE RECORD PER SOMO (USER REQUEST) */
    .attendance-per-subject-box {
        margin-top: 30px;
        border-top: 1px solid #e2e8f0;
        padding-top: 24px;
    }

    .attendance-box-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 16px;
    }

    .attendance-header-info h3 {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .attendance-header-info p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    .overall-rate-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 6px 14px;
        border-radius: 8px;
    }

    .overall-rate-badge .rate-txt {
        font-size: 18px;
        font-weight: 900;
        color: #16a34a;
    }

    .overall-rate-badge .sessions-txt {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }

    .subject-att-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 13.5px;
    }

    .subject-att-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 10px 12px;
        border-bottom: 1px solid #e2e8f0;
        border-top: 1px solid #e2e8f0;
    }

    .subject-att-table td {
        padding: 11px 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .subject-att-table tr:hover td {
        background: #fcfdfe;
    }

    .att-count-present {
        display: inline-block;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12.5px;
    }

    .att-count-absent {
        display: inline-block;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12.5px;
    }

    .absent-has {
        background: #fee2e2;
        color: #dc2626;
    }

    .absent-none {
        background: #f1f5f9;
        color: #94a3b8;
    }

    .progress-bar-container {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 140px;
    }

    .bar-bg {
        flex: 1;
        height: 7px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.4s ease;
    }

    .fill-good { background: #16a34a; }
    .fill-mid  { background: #eab308; }
    .fill-low  { background: #ef4444; }

    .rate-percent-num {
        font-size: 12.5px;
        font-weight: 800;
        color: #334155;
        min-width: 38px;
        text-align: right;
    }

    .status-badge-chip {
        font-size: 11.5px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        display: inline-block;
        text-align: center;
        white-space: nowrap;
    }

    .chip-green  { background: #dcfce7; color: #15803d; }
    .chip-blue   { background: #e0f2fe; color: #0369a1; }
    .chip-yellow { background: #fef9c3; color: #854d0e; }
    .chip-red    { background: #fee2e2; color: #b91c1c; }

    /* FINANCIAL STATUS BOX (EXACT MATCH TO SCREENSHOT) */
    .financial-status-card {
        border: 1.5px dashed #93c5fd;
        background: #f8fafc;
        border-radius: 8px;
        padding: 16px 22px;
        margin-top: 26px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .fin-left-details {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }

    .fin-title-heading {
        font-size: 13.5px;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
    }

    .badge-account-status {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.4px;
        padding: 4px 10px;
        border-radius: 4px;
        display: inline-block;
        text-transform: uppercase;
    }

    .status-owing-badge {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .status-cleared-badge {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .fin-right-columns {
        display: flex;
        align-items: center;
        gap: 28px;
        flex-wrap: wrap;
    }

    .fin-metric-column {
        text-align: right;
    }

    .metric-label-top {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 2px;
    }

    .metric-value-bottom {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .metric-val-paid {
        color: #16a34a;
    }

    .metric-val-due {
        color: #dc2626;
    }

    /* Print Styles for PDF Generation */
    @media print {
        header, footer, .parent-top-card, .parent-filter-card, .btn-download-report, .parent-lang-toggle, .btn-parent-logout {
            display: none !important;
        }

        body {
            background: #ffffff !important;
            padding: 0 !important;
        }

        .parent-portal-wrapper {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .student-main-report-card {
            border: 1px solid #ccc !important;
            box-shadow: none !important;
            padding: 20px !important;
        }

        .financial-status-card {
            border: 1.5px dashed #999 !important;
            background: #fdfdfd !important;
        }
    }
</style>
@endsection

@section('content')
<div class="parent-portal-wrapper">

    @if(!$selectedStudent)
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 40px; text-align: center; margin-top: 30px;">
            <div style="font-size: 44px; margin-bottom: 12px;">👪</div>
            <h3 style="font-size: 18px; font-weight: 800; color: #1e293b;">{{ __('No Students Linked to Your Account') }}</h3>
            <p style="color: #64748b; font-size: 14px; margin-top: 8px;">
                {{ __('Please contact your school administrator to link your parent account to your student profile.') }}
            </p>
        </div>
    @else
        <!-- TOP BAR (CARD 1) -->
        <div class="parent-top-card">
            <div class="parent-welcome-text">
                {{ __('Logged in as Parent:') }} <strong>{{ strtoupper(Auth::user()->name) }}</strong>
            </div>

            <div class="top-actions-group">
                <!-- Language Switcher -->
                <div class="parent-lang-toggle" title="{{ __('Choose Language') }}">
                    <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'active-lang' : '' }}">🇬🇧 EN</a>
                    <span style="color: #cbd5e1;">|</span>
                    <a href="{{ route('lang.switch', 'sw') }}" class="{{ app()->getLocale() == 'sw' ? 'active-lang' : '' }}">🇹🇿 SW</a>
                </div>

                <!-- Download Report (PDF) -->
                <button type="button" class="btn-download-report" onclick="window.print()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    {{ __('Download Report (PDF)') }}
                </button>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                    @csrf
                    <button type="submit" class="btn-parent-logout">{{ __('Logout') }}</button>
                </form>
            </div>
        </div>

        <!-- FILTER BAR (CARD 2) -->
        <div class="parent-filter-card">
            @if($children->count() > 1)
                <!-- Quick Child Switcher Pills -->
                <div style="margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span style="font-size: 13px; font-weight: 800; color: #64748b; text-transform: uppercase;">{{ __('Your Children:') }}</span>
                    @foreach($children as $child)
                        <a href="{{ route('parent.reports', ['student_id' => $child->id, 'report_type' => $selectedReportType]) }}"
                           style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 20px; text-decoration: none; font-weight: 700; font-size: 13px; border: 1.5px solid {{ $selectedStudent->id == $child->id ? '#0284c7' : '#cbd5e1' }}; background: {{ $selectedStudent->id == $child->id ? '#f0f9ff' : '#ffffff' }}; color: {{ $selectedStudent->id == $child->id ? '#0284c7' : '#475569' }};">
                            <span>👤 {{ $child->student_name }}</span>
                            <span style="background: {{ $selectedStudent->id == $child->id ? '#0284c7' : '#e2e8f0' }}; color: {{ $selectedStudent->id == $child->id ? '#ffffff' : '#334155' }}; font-size: 11px; padding: 2px 8px; border-radius: 12px; font-weight: 800;">{{ $child->class_name }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            <form method="GET" action="{{ route('parent.reports') }}" class="filter-inner-form">
                @if($availableClasses->count() > 1)
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="filter-label">{{ __('Select Class:') }}</span>
                        <select name="class_name" class="filter-select" onchange="this.form.submit()">
                            @foreach($availableClasses as $cls)
                                <option value="{{ $cls }}" {{ $selectedClass == $cls ? 'selected' : '' }}>
                                    🏫 {{ $cls }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if($children->count() > 1)
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="filter-label">{{ __('Select Student:') }}</span>
                        <select name="student_id" class="filter-select" onchange="this.form.submit()">
                            @foreach($children as $child)
                                <option value="{{ $child->id }}" {{ $selectedStudent->id == $child->id ? 'selected' : '' }}>
                                    {{ $child->student_name }} ({{ $child->class_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                @endif

                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="filter-label">{{ __('Select Report Type:') }}</span>
                    <select name="report_type" class="filter-select" onchange="this.form.submit()">
                        @foreach(['Annual Examination', 'Terminal Examination', 'Midterm Test', 'Monthly Test', 'Weekly Test'] as $type)
                            <option value="{{ $type }}" {{ $selectedReportType == $type ? 'selected' : '' }}>
                                {{ __($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn-view-report">{{ __('View Report') }}</button>
            </form>
        </div>

        <!-- CENTERED HEADING -->
        <h1 class="report-main-title">{{ __('STUDENT PROGRESS REPORT') }}</h1>

        <!-- MAIN STUDENT REPORT (CARD 3) -->
        <div class="student-main-report-card">
            <!-- Student Title & Metadata -->
            <h2 class="student-name-title">{{ strtoupper($selectedStudent->student_name) }}</h2>
            <div class="student-meta-details">
                <span>{{ __('Class:') }} <span class="highlight">{{ $selectedStudent->class_name }}</span></span>
                <span class="meta-divider">|</span>
                <span>{{ __('Assessment:') }} <span class="highlight">{{ $selectedReportType }}</span></span>
                <span class="meta-divider">|</span>
                <span>{{ __('Date Done:') }} <span class="highlight">{{ $dateDone }}</span></span>
            </div>

            <!-- Blue Solid Divider Line -->
            <div class="report-solid-divider"></div>

            <!-- Academic Marks Section -->
            @if($marks->isEmpty())
                <div class="no-marks-state">
                    {{ __('No marks have been recorded for :assessment yet.', ['assessment' => $selectedReportType]) }}
                </div>
            @else
                <div style="overflow-x: auto; margin-bottom: 25px;">
                    <table class="marks-results-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>{{ __('Subject') }}</th>
                                <th style="text-align: center;">{{ __('Marks (/100)') }}</th>
                                <th style="text-align: center;">{{ __('Grade') }}</th>
                                <th>{{ __('Remarks') }}</th>
                                <th>{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($marks as $idx => $m)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td><strong>{{ $m->subject ? $m->subject->subject_name : 'Subject' }}</strong></td>
                                    <td style="text-align: center; font-weight: 800; font-size: 15px;">{{ number_format($m->marks, 1) }}</td>
                                    <td style="text-align: center;">
                                        <span class="grade-badge grade-{{ strtolower($m->grade) }}">{{ $m->grade }}</span>
                                    </td>
                                    <td>{{ $m->remarks ?: 'Good Progress' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($m->exam_date)->format('d M, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f8fafc; font-weight: 800;">
                                <td colspan="2" style="padding: 12px 14px;">{{ __('Average / Overall Grade') }}</td>
                                <td style="text-align: center; color: #0284c7; font-size: 16px;">
                                    {{ $average ? number_format($average, 1) . '%' : 'N/A' }}
                                </td>
                                <td style="text-align: center; font-size: 16px;">
                                    <span class="grade-badge grade-{{ strtolower($overallGrade) }}">{{ $overallGrade }}</span>
                                </td>
                                <td colspan="2" style="font-weight: 600; color: #64748b;">
                                    {{ $overallGrade == 'A' ? 'Excellent Performance' : ($overallGrade == 'B' ? 'Very Good Performance' : 'Satisfactory') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            <!-- ATTENDANCE RECORD FOR EACH SUBJECT (USER EXPLICIT REQUEST) -->
            <div class="attendance-per-subject-box">
                <div class="attendance-box-top">
                    <div class="attendance-header-info">
                        <h3>
                            <span>📅</span> {{ __('Rekodi ya Mahudhurio kwa Kila Somo') }}
                        </h3>
                        <p>{{ __('Fuatilia mahudhurio ya mwanafunzi darasani kwa kila somo katika kipindi hiki') }}</p>
                    </div>

                    <div class="overall-rate-badge">
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: #15803d; text-transform: uppercase;">
                                {{ __('Jumla ya Mahudhurio:') }}
                            </div>
                            <div class="rate-txt">{{ $overallAttendanceRate }}%</div>
                        </div>
                        <div class="sessions-txt">
                            ({{ $totalAttendedSessions }}/{{ $totalPlannedSessions }} {{ __('Vipindi') }})
                        </div>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="subject-att-table">
                        <thead>
                            <tr>
                                <th style="width: 35px; text-align: center;">#</th>
                                <th>{{ __('Somo (Subject)') }}</th>
                                <th style="text-align: center;">{{ __('Vipindi Vilivyofundishwa') }}</th>
                                <th style="text-align: center;">{{ __('Alivyohudhuria (Present)') }}</th>
                                <th style="text-align: center;">{{ __('Alivyokosa (Absent)') }}</th>
                                <th>{{ __('Kiwango cha Mahudhurio') }}</th>
                                <th style="text-align: center;">{{ __('Hali (Status)') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjectAttendances as $index => $item)
                                <tr>
                                    <td style="text-align: center; color: #94a3b8; font-weight: 600;">{{ $index + 1 }}</td>
                                    <td>
                                        <div style="font-weight: 800; color: #1e293b;">
                                            {{ $item['subject_name'] }}
                                        </div>
                                    </td>
                                    <td style="text-align: center; font-weight: 700; color: #334155;">
                                        {{ $item['total'] }}
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="att-count-present">{{ $item['present'] }}</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="att-count-absent {{ $item['absent'] > 0 ? 'absent-has' : 'absent-none' }}">
                                            {{ $item['absent'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress-bar-container">
                                            <div class="bar-bg">
                                                <div class="bar-fill {{ $item['rate'] >= 85 ? 'fill-good' : ($item['rate'] >= 70 ? 'fill-mid' : 'fill-low') }}"
                                                     style="width: {{ $item['rate'] }}%;"></div>
                                            </div>
                                            <span class="rate-percent-num">{{ $item['rate'] }}%</span>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($item['rate'] >= 90)
                                            <span class="status-badge-chip chip-green">{{ __('Nzuri Sana') }}</span>
                                        @elseif($item['rate'] >= 75)
                                            <span class="status-badge-chip chip-blue">{{ __('Nzuri') }}</span>
                                        @elseif($item['rate'] >= 60)
                                            <span class="status-badge-chip chip-yellow">{{ __('Wastani') }}</span>
                                        @else
                                            <span class="status-badge-chip chip-red">{{ __('Inahitaji Uangalizi') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 25px; color: #94a3b8;">
                                        {{ __('Hakuna rekodi za mahudhurio zilizopatikana kwa mwanafunzi huyu.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- FINANCIAL STATUS BOX (EXACT MATCH TO SCREENSHOT) -->
            <div class="financial-status-card">
                <div class="fin-left-details">
                    <div class="fin-title-heading">
                        <span>🗂️</span> {{ __('FINANCIAL STATUS') }} ({{ __('ACADEMIC YEAR:') }} {{ $academicYear }})
                    </div>
                    <div class="badge-account-status {{ $isOwing ? 'status-owing-badge' : 'status-cleared-badge' }}">
                        {{ __('ACCOUNT STATUS:') }} {{ $isOwing ? __('OWING') : __('CLEARED') }}
                    </div>
                </div>

                <div class="fin-right-columns">
                    <div class="fin-metric-column">
                        <div class="metric-label-top">{{ __('TOTAL FEE REQUIRED') }}</div>
                        <div class="metric-value-bottom">{{ number_format($totalFees, 2) }} TZS</div>
                    </div>

                    <div class="fin-metric-column">
                        <div class="metric-label-top">{{ __('TOTAL PAID') }}</div>
                        <div class="metric-value-bottom metric-val-paid">{{ number_format($paidAmount, 2) }} TZS</div>
                    </div>

                    <div class="fin-metric-column">
                        <div class="metric-label-top">{{ __('BALANCE DUE') }}</div>
                        <div class="metric-value-bottom metric-val-due">{{ number_format($remainingBalance, 2) }} TZS</div>
                    </div>
                </div>
            </div>

        </div>
    @endif

</div>
@endsection
