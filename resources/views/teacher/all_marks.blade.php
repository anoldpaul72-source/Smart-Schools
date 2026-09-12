<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Results Report - View All Marks | Smart-Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 20px;
            color: #333333;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .report-header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px double #0056b3;
            padding-bottom: 15px;
        }

        .report-header h1 {
            margin: 0;
            font-size: 24px;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-header h2 {
            margin: 5px 0 0 0;
            font-size: 17px;
            color: #0056b3;
            text-transform: uppercase;
        }

        .report-header h3 {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #475569;
            text-transform: uppercase;
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
            flex-wrap: wrap;
            gap: 10px;
        }

        .nav-links a {
            font-weight: bold;
            text-decoration: none;
        }

        .filter-box {
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-box label {
            font-weight: bold;
            font-size: 13px;
            color: #334155;
        }

        .filter-box select {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 13px;
            background: white;
            cursor: pointer;
        }

        .btn-filter {
            background-color: #0f172a;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-filter:hover {
            background-color: #1e293b;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-enter-marks {
            background-color: #16a34a;
            color: white !important;
            padding: 8px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print {
            background-color: #0284c7;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background-color: #0369a1;
        }

        .btn-bulk-sms {
            background-color: #059669;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-bulk-sms:hover {
            background-color: #047857;
        }

        .btn-group-sms {
            background: #059669;
            color: white;
            border: none;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-group-sms:hover {
            background: #047857;
        }

        .btn-student-sms {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-student-sms:hover {
            background: #dbeafe;
        }

        .btn-edit-mark {
            background: #ffffff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            border-radius: 4px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
        }

        .btn-edit-mark:hover {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);
        }

        /* Modal Styles */
        .sms-modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .sms-modal-content {
            background: #ffffff;
            width: 90%;
            max-width: 520px;
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .sms-preview-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #059669;
            border-radius: 6px;
            padding: 12px 14px;
            font-family: monospace;
            font-size: 12px;
            color: #334155;
            white-space: pre-line;
            line-height: 1.45;
            margin: 14px 0;
        }

        .report-section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }

        .group-header {
            background: #f1f5f9;
            border-left: 4px solid #0056b3;
            padding: 10px 14px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .group-header h3 {
            margin: 0;
            color: #0f172a;
            font-size: 16px;
            text-transform: uppercase;
        }

        .group-count {
            background: #0056b3;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 15px;
            font-size: 13px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 10px 8px;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        tr:hover {
            background-color: #f1f5f9;
        }

        .grade-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
            font-size: 12px;
            min-width: 22px;
            text-align: center;
        }

        .grade-A { background-color: #16a34a; }
        .grade-B { background-color: #0284c7; }
        .grade-C { background-color: #ca8a04; }
        .grade-D { background-color: #ea580c; }
        .grade-F { background-color: #dc2626; }

        .score-cell {
            font-weight: bold;
            font-size: 14px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }

        .empty-state h3 {
            margin-bottom: 8px;
            color: #334155;
        }

        @page {
            size: A4 portrait;
            margin: 8mm 8mm 8mm 8mm;
        }

        @media print {
            body { background: white !important; margin: 0 !important; padding: 0 !important; font-size: 11px !important; }
            .container { box-shadow: none !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
            .nav-links, .filter-box, .btn-print, .btn-enter-marks, .btn-bulk-sms, .btn-group-sms, .group-count, .th-sms, .td-sms, .btn-student-sms, .sms-modal-backdrop, .no-print, .grading-scale-box, .flash-message, [class*="alert"] {
                display: none !important;
            }
            .report-header { margin-bottom: 12px !important; }
            .report-section {
                margin-bottom: 25px !important;
                page-break-inside: auto !important;
                break-inside: auto !important;
            }
            thead {
                display: table-header-group !important;
            }
            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            th { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact !important; font-size: 11px !important; padding: 6px 6px !important; }
            td { font-size: 11.5px !important; padding: 6px 6px !important; }
            .grade-badge { border: 1px solid #333 !important; color: black !important; background: transparent !important; }
            .group-header { border-left: 4px solid #0056b3 !important; background-color: #f8fafc !important; -webkit-print-color-adjust: exact !important; padding: 6px 10px !important; }
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
        <div style="font-size: 13px; color: #64748b; font-weight: 700;">Smart-Schools &bull; {{ __('Academic Marks Report') }}</div>
    </div>

    <div class="report-header">
        <h1>{{ $schoolName }}</h1>
        <h2>{{ __('ACADEMIC RESULTS REPORT') }}</h2>
        <h3>{{ __('Instructor') }}: {{ $teacher->name ?: $teacher->username }}</h3>
    </div>

    <div class="nav-links">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('teacher.marks') }}" style="color: #0056b3;">⬅ {{ __('Enter Marks') }}</a>
            <a href="{{ route('teacher.timetable') }}" style="color: #0284c7;">🗓️ {{ __('My Timetable') }}</a>
            <a href="{{ route('teacher.attendance') }}" style="color: #16a34a;">📝 {{ __('Take Attendance') }}</a>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #dc2626; font-weight: bold; cursor: pointer; padding: 0; font-size: 14px;">{{ __('Logout') }}</button>
            </form>

            <!-- Language Switcher -->
            <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 2px 4px; gap: 4px;">
                <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#0284c7' : '#64748b' }}; background: {{ app()->getLocale() == 'en' ? '#e0f2fe' : 'transparent' }};">🇬🇧 EN</a>
                <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#0284c7' : '#64748b' }}; background: {{ app()->getLocale() == 'sw' ? '#e0f2fe' : 'transparent' }};">🇹🇿 SW</a>
            </div>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">✅</span>
            <div style="flex: 1;">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">⚠️</span>
            <div style="flex: 1;">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Filter Form -->
    <div class="filter-box">
        <form method="GET" action="{{ route('teacher.marks.all') }}" style="display: contents;">
            <div class="filter-group">
                <label for="filter_term">{{ __('Exam Assessment') }}:</label>
                <select name="filter_term" id="filter_term">
                    <option value="">-- {{ __('All Assessment Types') }} --</option>
                    @foreach($allTerms as $t)
                        <option value="{{ $t }}" {{ $selectedTerm === $t ? 'selected' : '' }}>{{ __($t) }}</option>
                    @endforeach
                </select>

                <label for="filter_class">{{ __('Class') }}:</label>
                <select name="filter_class" id="filter_class">
                    <option value="">-- {{ __('All Classes') }} --</option>
                    @foreach($allClasses as $c)
                        <option value="{{ $c }}" {{ $selectedClass === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn-filter">🔍 {{ __('Filter') }}</button>
            </div>
        </form>

        <div class="action-buttons">
            <button type="button" class="btn-bulk-sms" onclick="openBulkSmsModal('{{ $selectedClass }}', '{{ $selectedTerm }}')">
                📱 {{ __('Send Bulk SMS to Parents') }}
            </button>
            <a href="{{ route('teacher.marks') }}" class="btn-enter-marks">➕ {{ __('Enter New Marks') }}</a>
            <button type="button" class="btn-print" onclick="window.print()">🖨️ {{ __('Print Report') }}</button>
        </div>
    </div>

    <!-- Grading Scale Key / Vigezo vya Madaraja -->
    <div class="grading-scale-box no-print" style="display: flex; align-items: center; justify-content: flex-start; gap: 8px; flex-wrap: wrap; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px; margin-bottom: 22px; font-size: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <span style="font-weight: 800; color: #334155; text-transform: uppercase;">📊 {{ __('Vigezo vya Madaraja:') }}</span>
        <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; border-radius: 4px; padding: 2px 8px; font-weight: 700;">A: 75 – 100 <small>(Excellent)</small></span>
        <span style="background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; border-radius: 4px; padding: 2px 8px; font-weight: 700;">B: 60 – 74 <small>(Very Good)</small></span>
        <span style="background: #fef9c3; color: #a16207; border: 1px solid #fde047; border-radius: 4px; padding: 2px 8px; font-weight: 700;">C: 45 – 59 <small>(Good)</small></span>
        <span style="background: #ffedd5; color: #c2410c; border: 1px solid #fdba74; border-radius: 4px; padding: 2px 8px; font-weight: 700;">D: 30 – 44 <small>(Pass)</small></span>
        <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 4px; padding: 2px 8px; font-weight: 700;">F: 0 – 29 <small>(Fail)</small></span>
    </div>

    <!-- Marks Report Listing -->
    @if(empty($groupedMarks))
        <div class="empty-state">
            <h3>{{ __('Hakuna alama zilizopatikana!') }}</h3>
            <p>{{ __('Hakuna kumbukumbu za matokeo zilizopatikana kulingana na vigezo ulivyochagua.') }}</p>
            <a href="{{ route('teacher.marks') }}" class="btn-enter-marks" style="margin-top: 15px;">➕ {{ __('Weka Alama Mpya') }}</a>
        </div>
    @else
        @foreach($groupedMarks as $groupKey => $group)
            <div class="report-section">
                <div class="group-header">
                    <div>
                        @php
                            $groupExamDate = $group['info']['exam_date'] ?? null;
                            if (!$groupExamDate && !empty($group['students'])) {
                                foreach ($group['students'] as $st) {
                                    if (!empty($st->exam_date)) {
                                        $groupExamDate = $st->exam_date;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        <h3>📚 {{ $group['info']['class_name'] }} &bull; {{ $group['info']['subject_name'] }} &bull; {{ __($group['info']['term']) }}@if(!empty($groupExamDate)) &bull; {{ $groupExamDate }}@endif</h3>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" class="btn-group-sms" onclick="openBulkSmsModal('{{ $group['info']['class_name'] }}', '{{ $group['info']['term'] }}')" title="Tuma SMS kwa darasa hili">
                            📱 {{ __('Tuma SMS kwa Darasa Hili') }}
                        </button>
                        <span class="group-count">{{ count($group['students']) }} {{ __('Students') }}</span>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th style="width: 110px;">{{ __('Reg Number') }}</th>
                            <th>{{ __('Student Name') }}</th>
                            <th style="width: 60px; text-align: center;">{{ __('Sex') }}</th>
                            <th style="width: 75px; text-align: center;">{{ __('Score') }}</th>
                            <th style="width: 55px; text-align: center;">{{ __('Grade') }}</th>
                            <th>{{ __('Remarks') }}</th>
                            <th class="th-sms" style="width: 140px; text-align: center;">📱 {{ __('SMS to Parent') }}</th>
                            <th style="width: 80px; text-align: center;" class="no-print">✏️ {{ __('Edit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group['students'] as $idx => $mark)
                            @php
                                $student = $mark->student;
                                $parentPhone = $student ? $student->effective_parent_phone : null;
                            @endphp
                            <tr>
                                <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $idx + 1 }}</td>
                                <td><b>{{ $student ? $student->reg_number : 'N/A' }}</b></td>
                                <td>{{ $student ? $student->student_name : 'Unknown' }}</td>
                                <td style="text-align: center; font-weight: bold; color: {{ ($student && $student->sex == 'F') ? '#db2777' : '#0284c7' }};">
                                    {{ $student ? $student->sex : '-' }}
                                </td>
                                @php
                                    [$rowGrade, $rowRemarks] = \App\Models\Mark::calculateGrade((float)$mark->marks);
                                @endphp
                                <td style="text-align: center;" class="score-cell">{{ number_format($mark->marks, 0) }}%</td>
                                <td style="text-align: center;">
                                    <span class="grade-badge grade-{{ $rowGrade }}">{{ $rowGrade }}</span>
                                </td>
                                <td>{{ __($rowRemarks) }}</td>
                                <td class="td-sms" style="text-align: center;">
                                    @if($student)
                                        @if($parentPhone)
                                            <div style="font-size: 11px; font-weight: bold; color: #047857; margin-bottom: 4px;">
                                                📞 {{ $parentPhone }}
                                            </div>
                                            <button type="button" class="btn-student-sms" onclick="openSingleSmsModal({{ $student->id }}, '{{ addslashes($student->student_name) }}', '{{ $parentPhone }}', '{{ $group['info']['term'] }}')">
                                                ✉️ {{ __('Send SMS') }}
                                            </button>
                                        @else
                                            <span style="color: #94a3b8; font-size: 11px; display: block; margin-bottom: 3px;">{{ __('No Phone') }}</span>
                                            <button type="button" class="btn-student-sms" style="border-color: #f59e0b; color: #b45309; background: #fffbeb;" onclick="openSingleSmsModal({{ $student->id }}, '{{ addslashes($student->student_name) }}', '', '{{ $group['info']['term'] }}')">
                                                ➕ {{ __('Add & Send') }}
                                            </button>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="text-align: center;" class="no-print">
                                    <button type="button" class="btn-edit-mark" onclick="openEditMarkModal({{ $mark->id }}, '{{ addslashes($student ? $student->student_name : 'Mwanafunzi') }}', '{{ addslashes($student ? $student->reg_number : 'N/A') }}', '{{ addslashes($group['info']['subject_name']) }}', '{{ $mark->marks }}', '{{ $mark->exam_date ?: date('Y-m-d') }}', '{{ addslashes($group['info']['term']) }}')">
                                        ✏️ {{ __('Edit') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

</div>

<!-- 1. MODAL YA BULK SMS (KUTUMA KWA DARASA ZIMA) -->
<div id="bulkSmsModal" class="sms-modal-backdrop">
    <div class="sms-modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 24px;">📱</span>
                <h3 style="margin: 0; font-size: 18px; color: #0f172a;">{{ __('Tuma Ripoti kwa SMS (Bulk SMS)') }}</h3>
            </div>
            <button type="button" onclick="closeBulkSmsModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #94a3b8;">&times;</button>
        </div>

        <form method="POST" action="{{ route('teacher.marks.send_bulk_sms') }}" id="bulkSmsForm" onsubmit="handleBulkSubmit()">
            @csrf
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 5px; color: #334155;">{{ __('Chagua Darasa:') }} <span style="color: #dc2626;">*</span></label>
                <select name="class_name" id="modal_class_name" required style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Chagua Darasa --</option>
                    @foreach($allClasses as $c)
                        <option value="{{ $c }}" {{ $selectedClass === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 5px; color: #334155;">{{ __('Aina ya Mtihani / Muhula:') }}</label>
                <select name="term" id="modal_term" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    @foreach($allTerms as $t)
                        <option value="{{ $t }}" {{ $selectedTerm === $t ? 'selected' : '' }}>{{ __($t) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: bold; font-size: 12px; color: #64748b; text-transform: uppercase;">{{ __('Mfano wa Ujumbe Utakaotumwa (SMS Preview):') }}</label>
                <div class="sms-preview-card">
MZAZI WA JUMA HAMISI (Form 4)
Ripoti: Annual Examination - {{ $schoolName }}
Matokeo: Kiswahili: 82(A), Maths: 68(B), English: 75(B), Physics: 64(C), Bio: 80(A)
Wastani: 73.2% (Daraja: B)
Mahudhurio: 96% | Ada Inayodaiwa: 0 TZS
Kazi nzuri na hongera.
                </div>
                <div style="font-size: 11.5px; color: #64748b; line-height: 1.4;">
                    ℹ️ Ujumbe utatumwa kwa wazazi wote wa darasa hili walio na namba za simu. Huduma inatumia <strong>Beem Africa SMS Gateway</strong>.
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                <button type="button" onclick="closeBulkSmsModal()" style="padding: 9px 16px; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 6px; font-weight: bold; cursor: pointer; color: #475569;">Ghairi</button>
                <button type="submit" id="btnBulkSubmit" style="padding: 9px 20px; background: #059669; color: #ffffff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <span>📱</span>
                    <span>{{ __('Thibitisha & Tuma SMS Sasa') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL YA SINGLE SMS (MWANAFUNZI MMOJA) -->
<div id="singleSmsModal" class="sms-modal-backdrop">
    <div class="sms-modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 24px;">✉️</span>
                <h3 style="margin: 0; font-size: 18px; color: #0f172a;">{{ __('Tuma Ripoti kwa Mzazi') }}</h3>
            </div>
            <button type="button" onclick="closeSingleSmsModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #94a3b8;">&times;</button>
        </div>

        <form method="POST" action="{{ route('teacher.marks.send_single_sms') }}" id="singleSmsForm">
            @csrf
            <input type="hidden" name="student_id" id="single_student_id">
            <input type="hidden" name="term" id="single_term">

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 4px; color: #64748b;">{{ __('Mwanafunzi:') }}</label>
                <div id="single_student_name" style="font-size: 16px; font-weight: 800; color: #0f172a;">-</div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 6px; color: #334155;">{{ __('Namba ya Simu ya Mzazi (Tanzania):') }} <span style="color: #dc2626;">*</span></label>
                <input type="text" name="phone" id="single_phone" required placeholder="k.m. 0712345678 au +255..." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Namba hii itahifadhiwa pia kwenye taarifa za mwanafunzi huyu.</div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                <button type="button" onclick="closeSingleSmsModal()" style="padding: 9px 16px; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 6px; font-weight: bold; cursor: pointer; color: #475569;">Ghairi</button>
                <button type="submit" style="padding: 9px 20px; background: #1d4ed8; color: #ffffff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    ✉️ {{ __('Tuma SMS Sasa') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL YA KUHARIRI ALAMA (EDIT MARK) -->
<div id="editMarkModal" class="sms-modal-backdrop">
    <div class="sms-modal-content" style="max-width: 480px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 24px;">✏️</span>
                <div>
                    <h3 style="margin: 0; font-size: 18px; color: #0f172a;">{{ __('Edit Student Marks') }}</h3>
                    <p style="margin: 2px 0 0 0; font-size: 11.5px; color: #64748b;">{{ __('Update student marks and examination date') }}</p>
                </div>
            </div>
            <button type="button" onclick="closeEditMarkModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; line-height: 1;">&times;</button>
        </div>

        <form method="POST" id="editMarkForm" action="">
            @csrf
            @method('PUT')

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="font-size: 12px; color: #64748b; font-weight: 600;">{{ __('Student:') }}</span>
                    <strong id="edit_student_name" style="font-size: 13.5px; color: #0f172a;">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="font-size: 12px; color: #64748b; font-weight: 600;">{{ __('Registration Number:') }}</span>
                    <span id="edit_reg_number" style="font-size: 12.5px; font-weight: 700; color: #475569;">-</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-size: 12px; color: #64748b; font-weight: 600;">{{ __('Subject & Assessment:') }}</span>
                    <span id="edit_subject_term" style="font-size: 12px; font-weight: 700; color: #0284c7;">-</span>
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 6px; color: #334155;">
                    {{ __('New Score (0 - 100):') }} <span style="color: #dc2626;">*</span>
                </label>
                <input type="number" step="0.5" min="0" max="100" name="marks" id="edit_score" required
                       oninput="updateGradePreview(this.value)"
                       placeholder="{{ __('Enter score e.g. 78') }}"
                       style="width: 100%; padding: 10px 12px; border: 2px solid #cbd5e1; border-radius: 6px; font-size: 16px; font-weight: bold; box-sizing: border-box;">
            </div>

            <!-- Live Grade Preview Pill -->
            <div id="gradePreviewBox" style="background: #f1f5f9; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 12px; font-weight: 600; color: #475569;">{{ __('Assigned Grade:') }}</span>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span id="previewGradeBadge" class="grade-badge grade-A" style="font-size: 13px; padding: 3px 10px;">A</span>
                    <span id="previewRemarks" style="font-size: 12.5px; font-weight: 700; color: #334155;">Excellent</span>
                </div>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: bold; font-size: 13px; margin-bottom: 6px; color: #334155;">
                    {{ __('Examination Date:') }}
                </label>
                <input type="date" name="exam_date" id="edit_exam_date"
                       style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                <button type="button" onclick="confirmDeleteMark()" style="padding: 9px 14px; background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer;">
                    🗑️ {{ __('Delete Marks') }}
                </button>

                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="closeEditMarkModal()" style="padding: 9px 16px; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 6px; font-weight: bold; cursor: pointer; color: #475569;">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" style="padding: 9px 20px; background: #0284c7; color: #ffffff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                        💾 {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </form>

        <form id="deleteMarkHiddenForm" method="POST" action="" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
function openBulkSmsModal(className, term) {
    if (className) {
        document.getElementById('modal_class_name').value = className;
    }
    if (term) {
        document.getElementById('modal_term').value = term;
    }
    document.getElementById('bulkSmsModal').style.display = 'flex';
}

function closeBulkSmsModal() {
    document.getElementById('bulkSmsModal').style.display = 'none';
}

function handleBulkSubmit() {
    const btn = document.getElementById('btnBulkSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span>⏳</span> <span>Inatuma SMS... Tafadhali subiri</span>';
}

function openSingleSmsModal(studentId, studentName, phone, term) {
    document.getElementById('single_student_id').value = studentId;
    document.getElementById('single_student_name').innerText = studentName;
    document.getElementById('single_phone').value = phone || '';
    document.getElementById('single_term').value = term || 'Annual Examination';
    document.getElementById('singleSmsModal').style.display = 'flex';
}

function closeSingleSmsModal() {
    document.getElementById('singleSmsModal').style.display = 'none';
}

function openEditMarkModal(markId, studentName, regNumber, subjectName, currentScore, examDate, term) {
    document.getElementById('edit_student_name').textContent = studentName;
    document.getElementById('edit_reg_number').textContent = regNumber;
    document.getElementById('edit_subject_term').textContent = subjectName + ' (' + term + ')';
    document.getElementById('edit_score').value = currentScore;
    document.getElementById('edit_exam_date').value = examDate || '';

    var baseUrl = "{{ url('teacher/marks') }}";
    document.getElementById('editMarkForm').action = baseUrl + '/' + markId;
    document.getElementById('deleteMarkHiddenForm').action = baseUrl + '/' + markId;

    updateGradePreview(currentScore);
    document.getElementById('editMarkModal').style.display = 'flex';
}

function closeEditMarkModal() {
    document.getElementById('editMarkModal').style.display = 'none';
}

function updateGradePreview(score) {
    var val = parseFloat(score);
    var grade = '-';
    var remarks = '-';
    var badgeClass = 'grade-F';

    if (!isNaN(val)) {
        if (val >= 75) {
            grade = 'A';
            remarks = 'Excellent';
            badgeClass = 'grade-A';
        } else if (val >= 60) {
            grade = 'B';
            remarks = 'Very Good';
            badgeClass = 'grade-B';
        } else if (val >= 45) {
            grade = 'C';
            remarks = 'Good';
            badgeClass = 'grade-C';
        } else if (val >= 30) {
            grade = 'D';
            remarks = 'Pass';
            badgeClass = 'grade-D';
        } else {
            grade = 'F';
            remarks = 'Fail';
            badgeClass = 'grade-F';
        }
    }

    var badge = document.getElementById('previewGradeBadge');
    badge.textContent = grade;
    badge.className = 'grade-badge ' + badgeClass;
    document.getElementById('previewRemarks').textContent = remarks;
}

function confirmDeleteMark() {
    if (confirm("{{ __('Are you sure you want to delete these marks?') }}")) {
        document.getElementById('deleteMarkHiddenForm').submit();
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const bulkModal = document.getElementById('bulkSmsModal');
    const singleModal = document.getElementById('singleSmsModal');
    const editModal = document.getElementById('editMarkModal');
    if (event.target === bulkModal) {
        closeBulkSmsModal();
    }
    if (event.target === singleModal) {
        closeSingleSmsModal();
    }
    if (event.target === editModal) {
        closeEditMarkModal();
    }
}
</script>

</body>
</html>

