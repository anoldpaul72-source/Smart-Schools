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
                    <h3>📚 {{ $group['info']['class_name'] }} &bull; {{ $group['info']['subject_name'] }} &bull; {{ __($group['info']['term']) }}</h3>
                    <span class="group-count">{{ count($group['students']) }} {{ __('Students') }}</span>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th style="width: 120px;">{{ __('Reg Number') }}</th>
                            <th>{{ __('Student Name') }}</th>
                            <th style="width: 70px; text-align: center;">{{ __('Sex') }}</th>
                            <th style="width: 100px; text-align: center;">{{ __('Exam Date') }}</th>
                            <th style="width: 80px; text-align: center;">{{ __('Score') }}</th>
                            <th style="width: 60px; text-align: center;">{{ __('Grade') }}</th>
                            <th>{{ __('Remarks') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group['students'] as $idx => $mark)
                            <tr>
                                <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $idx + 1 }}</td>
                                <td><b>{{ $mark->student ? $mark->student->reg_number : 'N/A' }}</b></td>
                                <td>{{ $mark->student ? $mark->student->student_name : 'Unknown' }}</td>
                                <td style="text-align: center; font-weight: bold; color: {{ ($mark->student && $mark->student->sex == 'F') ? '#db2777' : '#0284c7' }};">
                                    {{ $mark->student ? $mark->student->sex : '-' }}
                                </td>
                                <td style="text-align: center; color: #475569;">{{ $mark->exam_date ?: '-' }}</td>
                                <td style="text-align: center;" class="score-cell">{{ number_format($mark->marks, 0) }}%</td>
                                <td style="text-align: center;">
                                    <span class="grade-badge grade-{{ $mark->grade }}">{{ $mark->grade }}</span>
                                </td>
                                <td>{{ __($mark->remarks) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

</div>

</body>
</html>
