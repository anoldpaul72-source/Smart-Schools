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
    </style>
</head>
<body>

<div class="container">
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
                            <th style="padding: 7px 8px;">{{ __('Tarehe') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentMarks as $rm)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 6px 8px; font-weight: bold;">{{ $rm->student ? $rm->student->student_name : '-' }}</td>
                                <td style="padding: 6px 8px;">{{ $rm->student ? $rm->student->class_name : '-' }}</td>
                                <td style="padding: 6px 8px; color: #0369a1; font-weight: bold;">{{ $rm->subject ? $rm->subject->subject_name : '-' }}</td>
                                <td style="padding: 6px 8px;">{{ $rm->term }}</td>
                                <td style="padding: 6px 8px; font-weight: bold; color: {{ $rm->marks < 40 ? '#dc2626' : '#16a34a' }};">{{ $rm->marks }}</td>
                                <td style="padding: 6px 8px; font-weight: bold;">{{ $rm->grade }}</td>
                                <td style="padding: 6px 8px; color: #64748b;">{{ $rm->exam_date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
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
</script>

</body>
</html>
