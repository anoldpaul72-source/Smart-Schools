<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Logs & Reports | Smart-Results</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 20px; color: #333; }
        .container { max-width: 900px; margin: 30px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        h2 { color: #0056b3; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; text-transform: uppercase; text-align: center; font-size: 20px; letter-spacing: 0.5px; }
        .nav-links { display: flex; justify-content: space-between; margin-bottom: 25px; font-size: 14px; background: #e9ecef; padding: 10px 14px; border-radius: 4px; align-items: center; }
        .nav-links a { font-weight: bold; text-decoration: none; color: #0056b3; }
        
        .filter-box { background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 25px; }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        label { font-weight: bold; font-size: 13px; color: #475569; }
        select, input { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; background: white; }
        .btn-filter { background-color: #0f172a; color: white; padding: 10px 16px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 14px; transition: background 0.2s; }
        .btn-filter:hover { background-color: #1e293b; }
        
        .date-section { margin-bottom: 35px; }
        .date-header { background: #e2e8f0; padding: 10px 15px; font-weight: bold; font-size: 15px; border-radius: 4px; color: #1e293b; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px 15px; text-align: left; font-size: 14px; }
        th { background-color: #f1f5f9; color: #1e293b; text-transform: uppercase; font-size: 12px; width: 50%; }
        
        .status-badge { font-weight: bold; padding: 4px 10px; border-radius: 4px; font-size: 12px; text-transform: uppercase; display: inline-block; }
        .Present { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .Absent { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .Permission, .Late { background-color: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }

        @media print {
            .nav-links, .filter-box, .btn-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            .container { box-shadow: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📊 {{ __('Attendance History') }}</h2>

    <div class="nav-links">
        <span>{{ __('Institution') }}: <b>{{ $schoolName }}</b></span>
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('teacher.attendance') }}" style="color: #16a34a; font-weight: bold;">📝 {{ __('Take Attendance') }}</a>
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

    <div class="filter-box">
        <form method="GET" action="{{ route('teacher.attendance.history') }}">
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="class_name">Classroom Structure:</label>
                    <select name="class_name" id="class_name" required>
                        <option value="">-- Choose Class --</option>
                        @foreach($assignedClasses as $class)
                            <option value="{{ $class }}" {{ $selectedClass === $class ? 'selected' : '' }}>
                                {{ $class }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="start_date">From Date:</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" required>
                </div>
                <div class="filter-group">
                    <label for="end_date">To Date:</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" required>
                </div>
                <button type="submit" class="btn-filter">🔍 Generate Report</button>
            </div>
        </form>
    </div>

    @if(!empty($selectedClass))
        @if(count($attendanceRecords) > 0)
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #475569; margin: 0; font-size: 16px;">Logs for Class {{ $selectedClass }}</h3>
                <button onclick="window.print()" class="btn-filter btn-print" style="background-color: #0056b3;">🖨️ Print Logs</button>
            </div>

            @foreach($attendanceRecords as $date => $studentsList)
                <div class="date-section">
                    <div class="date-header">
                        <span>📅 {{ date('d-M-Y', strtotime($date)) }}</span>
                        <span style="font-size: 13px; font-weight: normal;">Total Checked: {{ count($studentsList) }}</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th style="text-align: center; width: 160px;">Roll Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentsList as $st)
                                <tr>
                                    <td><b>{{ $st['student_name'] }}</b></td>
                                    <td style="text-align: center;">
                                        <span class="status-badge {{ $st['status'] }}">
                                            {{ $st['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <div style="text-align: center; color: #94a3b8; padding: 30px; border: 1px dashed #cbd5e1; background: #f8fafc; border-radius: 6px;">
                No attendance signatures found within the selected dates.
            </div>
        @endif
    @endif
</div>

</body>
</html>
