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

        @media print {
            body { background: white; margin: 0; padding: 0; font-size: 11px; }
            .container { box-shadow: none; max-width: 100%; padding: 0; }
            .nav-links, .filter-box, .btn-print, .btn-enter-marks { display: none !important; }
            th { background-color: #eaeaea !important; -webkit-print-color-adjust: exact; }
            .grade-badge { border: 1px solid #333; color: black !important; background: transparent !important; }
            .group-header { border-left-color: #333 !important; background-color: #eee !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="container">
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
                        <h3>📚 {{ $group['info']['class_name'] }} &bull; {{ $group['info']['subject_name'] }} &bull; {{ __($group['info']['term']) }}</h3>
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
                            <th style="width: 95px; text-align: center;">{{ __('Exam Date') }}</th>
                            <th style="width: 75px; text-align: center;">{{ __('Score') }}</th>
                            <th style="width: 55px; text-align: center;">{{ __('Grade') }}</th>
                            <th>{{ __('Remarks') }}</th>
                            <th style="width: 140px; text-align: center;">📱 {{ __('SMS kwa Mzazi') }}</th>
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
                                <td style="text-align: center; color: #475569;">{{ $mark->exam_date ?: '-' }}</td>
                                <td style="text-align: center;" class="score-cell">{{ number_format($mark->marks, 0) }}%</td>
                                <td style="text-align: center;">
                                    <span class="grade-badge grade-{{ $mark->grade }}">{{ $mark->grade }}</span>
                                </td>
                                <td>{{ __($mark->remarks) }}</td>
                                <td style="text-align: center;">
                                    @if($student)
                                        @if($parentPhone)
                                            <div style="font-size: 11px; font-weight: bold; color: #047857; margin-bottom: 4px;">
                                                📞 {{ $parentPhone }}
                                            </div>
                                            <button type="button" class="btn-student-sms" onclick="openSingleSmsModal({{ $student->id }}, '{{ addslashes($student->student_name) }}', '{{ $parentPhone }}', '{{ $group['info']['term'] }}')">
                                                ✉️ {{ __('Tuma SMS') }}
                                            </button>
                                        @else
                                            <span style="color: #94a3b8; font-size: 11px; display: block; margin-bottom: 3px;">{{ __('Hana Namba') }}</span>
                                            <button type="button" class="btn-student-sms" style="border-color: #f59e0b; color: #b45309; background: #fffbeb;" onclick="openSingleSmsModal({{ $student->id }}, '{{ addslashes($student->student_name) }}', '', '{{ $group['info']['term'] }}')">
                                                ➕ {{ __('Weka & Tuma') }}
                                            </button>
                                        @endif
                                    @else
                                        -
                                    @endif
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

// Close modals when clicking outside
window.onclick = function(event) {
    const bulkModal = document.getElementById('bulkSmsModal');
    const singleModal = document.getElementById('singleSmsModal');
    if (event.target === bulkModal) {
        closeBulkSmsModal();
    }
    if (event.target === singleModal) {
        closeSingleSmsModal();
    }
}
</script>

</body>
</html>

