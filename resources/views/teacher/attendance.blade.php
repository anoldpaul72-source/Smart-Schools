<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Daily Student Attendance | Smart-Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 20px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #0056b3;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 12px;
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
        }

        .selection-box {
            background: #f8fafc;
            padding: 18px 20px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            margin-bottom: 25px;
        }

        select {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 14px;
            min-width: 220px;
            background: white;
        }

        select:focus {
            outline: none;
            border-color: #0056b3;
        }

        .btn-select {
            background-color: #0f172a;
            color: white;
            padding: 8px 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            margin-left: 8px;
            transition: background 0.2s;
        }

        .btn-select:hover {
            background-color: #1e293b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #0056b3;
            color: white;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
        }

        .radio-group label {
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .present { color: #16a34a; }
        .absent { color: #ef4444; }
        .permission { color: #ea580c; }

        .btn-save {
            background-color: #16a34a;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }

        .btn-save:hover {
            background-color: #15803d;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
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

        .debug-notice {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            color: #b45309;
            padding: 10px 14px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📋 {{ __('STUDENT ROLL CALL PROTOCOL') }}</h2>

    <div class="nav-links">
        <span>{{ __('Institution') }}: <b>{{ $schoolName }}</b></span>
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('teacher.attendance.history') }}" style="font-weight: bold; color: #0056b3;">📊 {{ __('Attendance History') }}</a>
            <a href="{{ route('teacher.marks') }}" style="color: #475569;">➕ {{ __('Enter Marks') }}</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                @csrf
                <button type="submit" style="background: none; border: none; color: red; font-weight: bold; cursor: pointer; padding: 0; font-size: 14px;">{{ __('Logout') }}</button>
            </form>

            <!-- Language Switcher -->
            <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 2px 4px; gap: 4px;">
                <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#0056b3' : '#64748b' }}; background: {{ app()->getLocale() == 'en' ? '#e0f2fe' : 'transparent' }};">🇬🇧 EN</a>
                <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#0056b3' : '#64748b' }}; background: {{ app()->getLocale() == 'sw' ? '#e0f2fe' : 'transparent' }};">🇹🇿 SW</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    @if(!$isPrivileged && $assignedClasses->isEmpty())
        <div class="debug-notice">
            ⚠️ <b>Debug Info:</b> No classes found for Teacher ID <b>{{ $teacher->id }}</b> in the <code>teacher_assignments</code> table. Please assign a class to this teacher in your database.
        </div>
    @endif

    <div class="selection-box">
        <form method="GET" action="{{ route('teacher.attendance') }}" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <label for="class_name" style="font-size: 14px; margin: 0; white-space: nowrap;"><b>{{ __('Select Target Class') }}:</b> </label>
            <select name="class_name" id="class_name" required style="margin: 0;">
                <option value="">-- {{ __('Choose Class') }} --</option>
                @foreach($assignedClasses as $class)
                    <option value="{{ $class }}" {{ $selectedClass === $class ? 'selected' : '' }}>
                        {{ $class }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-select" style="margin: 0;">{{ __('Load Classroom') }}</button>
        </form>
    </div>

    @if(!empty($selectedClass))
        <form method="POST" action="{{ route('teacher.attendance.store') }}">
            @csrf
            <input type="hidden" name="class_name" value="{{ $selectedClass }}">

            <h3 style="color: #475569; font-size: 15px; margin-top: 15px;">
                {{ __('Student Roster') }}: {{ __('Class') }} {{ $selectedClass }} ({{ __('Date') }}: {{ date('d-M-Y') }})
            </h3>
            
            @if($students->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">S/N</th>
                            <th>{{ __('Full Student Name') }}</th>
                            <th>{{ __('Attendance Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                            @php
                                $status = $existingAttendance->get($student->id)?->status ?? 'Present';
                            @endphp
                            <tr>
                                <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                                <td><b>{{ $student->student_name }}</b></td>
                                <td>
                                    <div class="radio-group">
                                        <label class="present">
                                            <input type="radio" name="status[{{ $student->id }}]" value="Present" {{ $status === 'Present' ? 'checked' : '' }}> {{ __('Present') }}
                                        </label>
                                        <label class="absent">
                                            <input type="radio" name="status[{{ $student->id }}]" value="Absent" {{ $status === 'Absent' ? 'checked' : '' }}> {{ __('Absent') }}
                                        </label>
                                        <label class="permission">
                                            <input type="radio" name="status[{{ $student->id }}]" value="Permission" {{ ($status === 'Permission' || $status === 'Late') ? 'checked' : '' }}> {{ __('Permission') }}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <button type="submit" class="btn-save">{{ __('Save Today\'s Attendance') }}</button>
            @else
                <div class="debug-notice" style="margin-top: 15px;">
                    ⚠️ <b>Debug Info:</b> Found 0 students matching Class: <b>"{{ $selectedClass }}"</b> and School: <b>"{{ $schoolName }}"</b>. Verify that student profiles exactly match these values in the database.
                </div>
            @endif
        </form>
    @endif
</div>

</body>
</html>
