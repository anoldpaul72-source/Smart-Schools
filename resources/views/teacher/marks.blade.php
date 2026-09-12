<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Panel - Enter Marks | Smart-Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 20px;
            color: #333333;
        }

        .container {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #0056b3;
            margin-top: 0;
            text-align: center;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
            background: #e9ecef;
            padding: 10px 14px;
            border-radius: 4px;
            align-items: center;
        }

        .nav-links a {
            font-weight: bold;
            text-decoration: none;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 14px;
            color: #334155;
        }

        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
            background-color: white;
            font-family: inherit;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #0056b3;
        }

        button.btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #0056b3;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 25px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }

        button.btn-submit:hover {
            background-color: #004085;
        }

        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .bulk-box {
            background: #f0fdf4;
            border: 1px dashed #22c55e;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .bulk-title {
            margin-top: 0;
            color: #16a34a;
            text-transform: uppercase;
            font-size: 13px;
            font-weight: bold;
        }

        .bulk-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-download {
            margin: 0;
            background-color: #16a34a;
            color: white;
            border: none;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-download:hover {
            background-color: #15803d;
        }

        .btn-upload {
            text-align: center;
            text-decoration: none;
            background-color: #ea580c;
            color: white;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-upload:hover {
            background-color: #c2410c;
        }

        .btn-edit-mark {
            background: #ffffff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            border-radius: 4px;
            padding: 3px 8px;
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
        }

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
            max-width: 480px;
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .grade-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
            font-size: 12px;
            min-width: 20px;
            text-align: center;
        }
        .grade-A { background-color: #16a34a; }
        .grade-B { background-color: #0284c7; }
        .grade-C { background-color: #ca8a04; }
        .grade-D { background-color: #ea580c; }
        .grade-F { background-color: #dc2626; }
    </style>
</head>
<body>

@include('layouts.sidebar')

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">
        <button type="button" class="sidebar-toggle-btn" onclick="toggleSidebar()" title="{{ __('Toggle Sidebar') }}">
            ☰ {{ __('Menu') }}
        </button>
        <div style="font-size: 13px; color: #64748b; font-weight: 700;">Smart-Schools &bull; {{ __('Teacher Portal') }}</div>
    </div>

    <h2>{{ __('TEACHER PANEL: ENTER MARKS') }}</h2>

    <div class="nav-links">
        <span>{{ __('Logged in') }}: <b>{{ $teacher->name ?: $teacher->username }}</b> ({{ __($teacher->role) }})</span>
        <div style="display: flex; align-items: center; gap: 14px;">
            <a href="{{ route('teacher.timetable') }}" style="color: #0284c7; font-weight: bold; text-decoration: none;">🗓️ {{ __('My Timetable') }}</a>
            <a href="{{ route('teacher.attendance') }}" style="color: #16a34a; font-weight: bold; text-decoration: none;">📝 {{ __('Take Attendance') }}</a>
            <a href="{{ route('teacher.marks.all') }}" style="color: #0056b3; font-weight: bold; text-decoration: none;">{{ __('View All Marks') }}</a>
            @if($isPrivileged)
                <a href="{{ route('leader.dashboard') }}" style="color: #0056b3;">📊 {{ __('View Broadsheet') }}</a>
            @endif
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

    @if(!$isPrivileged)
        @if(!$hasAssignments)
            <div class="alert error" style="text-align: left; background: #fff1f2; border: 1px solid #fda4af; color: #9f1239; margin-bottom: 20px;">
                ⚠️ <b>{{ __('HUJAPANGIWA SOMO AU DARASA BADO!') }}</b><br>
                {{ __('Hauruhusiwi kuingiza au kuona matokeo ya somo lolote mpaka Mkuu wa Shule au Admin akupangie somo na darasa unalofundisha kwenye mfumo.') }}
            </div>
        @else
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; margin-bottom: 18px; font-size: 12px;">
                <b style="color: #0f172a;">📚 {{ __('Masomo & Madarasa Uliyopangiwa Kufundisha') }}:</b>
                <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px;">
                    @foreach($assignments as $asg)
                        <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-weight: bold;">
                            {{ $asg->subject ? $asg->subject->subject_name : 'Subject' }} ({{ $asg->class_name }})
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <!-- Bulk Marks Entry Box -->
    <div class="bulk-box">
        <div class="bulk-title">💡 {{ __('BULK MARKS ENTRY (EXCEL/OFFLINE)') }}</div>
        <p style="margin: 6px 0 10px 0; color: #475569; font-size: 13px;">
            {{ __('Select a Subject and a Class from the form below, then click to download the pre-populated template:') }}
        </p>

        <div class="bulk-buttons">
            <button type="button" onclick="downloadExcelTemplate()" class="btn-download" {{ (!$isPrivileged && !$hasAssignments) ? 'disabled' : '' }}>
                📥 {{ __('Download Template (Excel)') }}
            </button>
            <a href="{{ route('teacher.upload_marks') }}" class="btn-upload">
                📤 {{ __('Upload Completed Template') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    <!-- Grading Scale Reminder -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 20px; font-size: 12px;">
        <div style="font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
            📊 {{ __('Viwango vya Madaraja (Grading Scale):') }}
        </div>
        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
            <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; border-radius: 4px; padding: 2px 7px; font-weight: 700;">A: 75–100</span>
            <span style="background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; border-radius: 4px; padding: 2px 7px; font-weight: 700;">B: 60–74</span>
            <span style="background: #fef9c3; color: #a16207; border: 1px solid #fde047; border-radius: 4px; padding: 2px 7px; font-weight: 700;">C: 45–59</span>
            <span style="background: #ffedd5; color: #c2410c; border: 1px solid #fdba74; border-radius: 4px; padding: 2px 7px; font-weight: 700;">D: 30–44</span>
            <span style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 4px; padding: 2px 7px; font-weight: 700;">F: 0–29</span>
        </div>
    </div>

    <!-- Single Mark Submission Form -->
    <form method="POST" action="{{ route('teacher.marks.store_single') }}">
        @csrf

        <label for="subject_id">{{ __('Select Subject') }}:</label>
        <select name="subject_id" id="subject_id" required {{ (!$isPrivileged && !$hasAssignments) ? 'disabled' : '' }}>
            <option value="">-- {{ __('Select Your Subject') }} --</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                    {{ $subject->subject_name }}
                </option>
            @endforeach
        </select>

        <label for="class_name">{{ __('Select Class') }}:</label>
        <select name="class_name" id="class_name" required {{ (!$isPrivileged && !$hasAssignments) ? 'disabled' : '' }}>
            <option value="">-- {{ __('Select Class First') }} --</option>
            @foreach($myClasses as $class)
                <option value="{{ $class }}" {{ old('class_name') == $class ? 'selected' : '' }}>
                    {{ $class }}
                </option>
            @endforeach
        </select>

        <label for="student_id">{{ __('Select Student Name') }}:</label>
        <select name="student_id" id="student_id" required disabled>
            <option value="">-- {{ __('Choose Class First') }} --</option>
        </select>

        <label for="score">{{ __('Score (0 - 100)') }}:</label>
        <input type="number" step="0.5" min="0" max="100" name="score" id="score" value="{{ old('score') }}" placeholder="{{ __('Enter student score') }}" required {{ (!$isPrivileged && !$hasAssignments) ? 'disabled' : '' }}>

        <label for="term">{{ __('Exam Assessment Type') }}:</label>
        <select name="term" id="term" required {{ (!$isPrivileged && !$hasAssignments) ? 'disabled' : '' }}>
            <option value="">-- {{ __('Select Assessment Type') }} --</option>
            <option value="Weekly Test" {{ old('term') === 'Weekly Test' ? 'selected' : '' }}>{{ __('Weekly Test') }}</option>
            <option value="Monthly Test" {{ old('term') === 'Monthly Test' ? 'selected' : '' }}>{{ __('Monthly Test') }}</option>
            <option value="Midterm Test" {{ old('term') === 'Midterm Test' ? 'selected' : '' }}>{{ __('Midterm Test') }}</option>
            <option value="Terminal Examination" {{ old('term') === 'Terminal Examination' ? 'selected' : '' }}>{{ __('Terminal Examination') }}</option>
            <option value="Annual Examination" {{ old('term') === 'Annual Examination' ? 'selected' : '' }}>{{ __('Annual Examination') }}</option>
        </select>

        <label for="exam_date">{{ __('Exam Date') }}:</label>
        <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date', date('Y-m-d')) }}" required>

        <button type="submit" class="btn-submit" {{ (!$isPrivileged && !$hasAssignments) ? 'disabled style=background-color:#94a3b8;cursor:not-allowed;' : '' }}>{{ __('Submit Marks') }}</button>
    </form>

    @if(isset($recentMarks) && $recentMarks->isNotEmpty())
        <div style="margin-top: 35px; border-top: 2px solid #e2e8f0; padding-top: 20px;">
            <h3 style="font-size: 14px; color: #1e293b; margin-bottom: 12px; text-transform: uppercase; font-weight: bold;">
                📋 {{ __('Your Entered Marks') }}
            </h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px; text-align: left;">
                    <thead>
                        <tr style="background: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                            <th style="padding: 7px 8px;">{{ __('Student Name') }}</th>
                            <th style="padding: 7px 8px;">{{ __('Class') }}</th>
                            <th style="padding: 7px 8px;">{{ __('Subject') }}</th>
                            <th style="padding: 7px 8px;">{{ __('Exam Type') }}</th>
                            <th style="padding: 7px 8px;">{{ __('Score') }}</th>
                            <th style="padding: 7px 8px;">{{ __('Grade') }}</th>
                            <th style="padding: 7px 8px;">{{ __('Date') }}</th>
                            <th style="padding: 7px 8px; text-align: center;">{{ __('Edit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentMarks as $rm)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 6px 8px; font-weight: bold;">{{ $rm->student ? $rm->student->student_name : '-' }}</td>
                                <td style="padding: 6px 8px;">{{ $rm->student ? $rm->student->class_name : '-' }}</td>
                                <td style="padding: 6px 8px; color: #0369a1; font-weight: bold;">{{ $rm->subject ? $rm->subject->subject_name : '-' }}</td>
                                <td style="padding: 6px 8px;">{{ $rm->term }}</td>
                                @php
                                    [$rmGrade] = \App\Models\Mark::calculateGrade((float)$rm->marks);
                                @endphp
                                <td style="padding: 6px 8px; font-weight: bold; color: {{ $rm->marks < 45 ? '#dc2626' : '#16a34a' }};">{{ $rm->marks }}%</td>
                                <td style="padding: 6px 8px; font-weight: bold;">{{ $rmGrade }}</td>
                                <td style="padding: 6px 8px; color: #64748b;">{{ $rm->exam_date }}</td>
                                <td style="padding: 6px 8px; text-align: center;">
                                    <button type="button" class="btn-edit-mark" onclick="openEditMarkModal({{ $rm->id }}, '{{ addslashes($rm->student ? $rm->student->student_name : 'Mwanafunzi') }}', '{{ addslashes($rm->student ? $rm->student->reg_number : 'N/A') }}', '{{ addslashes($rm->subject ? $rm->subject->subject_name : 'Somo') }}', '{{ $rm->marks }}', '{{ $rm->exam_date ?: date('Y-m-d') }}', '{{ addslashes($rm->term) }}')">
                                        ✏️ {{ __('Edit') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Modal ya Kuhariri Alama (Edit Mark) -->
<div id="editMarkModal" class="sms-modal-backdrop">
    <div class="sms-modal-content">
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
    const classSelect = document.getElementById('class_name');
    const studentSelect = document.getElementById('student_id');

    // 1. AJAX Classroom Dependent Filtering Engine
    classSelect.addEventListener('change', function() {
        const selectedClass = this.value;

        if (selectedClass === "") {
            studentSelect.innerHTML = '<option value="">-- Choose Class First --</option>';
            studentSelect.disabled = true;
            return;
        }

        studentSelect.innerHTML = '<option value="">⌛ Loading Students...</option>';
        studentSelect.disabled = false;

        fetch("{{ route('teacher.get_students') }}?class_name=" + encodeURIComponent(selectedClass))
            .then(response => response.json())
            .then(data => {
                studentSelect.innerHTML = '<option value="">-- Select Student --</option>';

                if (data.length > 0) {
                    data.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.student_id;
                        option.textContent = student.student_name + ' (' + student.reg_number + ')';
                        studentSelect.appendChild(option);
                    });
                } else {
                    studentSelect.innerHTML = '<option value="">❌ No students found in this class</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching students:', error);
                studentSelect.innerHTML = '<option value="">❌ Error loading students</option>';
            });
    });

    // If class was pre-selected (e.g. on validation back)
    if (classSelect.value) {
        classSelect.dispatchEvent(new Event('change'));
    }

    // 2. Automated Excel/CSV Spreadsheet Manifest Request
    function downloadExcelTemplate() {
        const cls = classSelect.value;
        const sub = document.getElementById('subject_id').value;

        if (cls === "" || sub === "") {
            alert("❌ Please select both a 'Subject' and a 'Class' from the form below before downloading the template!");
            return;
        }

        window.location.href = "{{ route('teacher.download_template') }}?class_name=" + encodeURIComponent(cls) + '&subject_id=' + sub;
    }

    // 3. Edit Mark Modal Functions
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

    window.onclick = function(event) {
        const editModal = document.getElementById('editMarkModal');
        if (event.target === editModal) {
            closeEditMarkModal();
        }
    }
</script>

</body>
</html>
