<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Teaching Schedule - My Timetable | Smart-Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 20px;
            color: #333;
        }

        .container {
            max-width: 1250px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #0284c7;
            margin-top: 0;
            text-transform: uppercase;
            font-size: 20px;
            letter-spacing: 0.5px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .nav-links a {
            font-weight: bold;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-school-tt {
            background-color: #0284c7;
            color: white !important;
            padding: 7px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-school-tt:hover {
            background-color: #0369a1;
        }

        .table-responsive {
            overflow-x: auto;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            font-size: 12px;
            text-align: center;
            min-width: 1050px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 5px;
            vertical-align: middle;
        }

        th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            font-size: 11px;
            line-height: 1.3;
        }

        th small {
            font-weight: normal;
            color: #64748b;
            font-size: 9px;
            display: block;
            margin-top: 2px;
        }

        .day-column {
            background-color: #f8fafc;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            width: 100px;
            font-size: 12px;
        }

        .break-cell {
            background-color: #f1f5f9;
            color: #64748b;
            font-weight: bold;
            font-size: 11px;
            letter-spacing: 2px;
            width: 25px;
            line-height: 1.4;
        }

        .slot-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 6px;
            border-radius: 4px;
            min-height: 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .slot-class {
            font-weight: bold;
            color: #1e40af;
            font-size: 12px;
        }

        .slot-subject {
            color: #475569;
            font-size: 11px;
            margin-top: 3px;
            display: block;
            font-weight: 500;
        }

        .slot-free {
            color: #94a3b8;
            font-size: 11px;
            font-style: italic;
        }

        .print-btn {
            background-color: #0d9488;
            color: white;
            padding: 9px 18px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            float: right;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .print-btn:hover {
            background-color: #0f766e;
        }

        .summary-banner {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .summary-banner b {
            color: #166534;
            font-size: 14px;
        }

        @media print {
            body { background: white; margin: 0; padding: 0; }
            .container { box-shadow: none; max-width: 100%; padding: 0; }
            .nav-links, .print-btn, .btn-school-tt, .summary-banner { display: none !important; }
            th { background-color: #eaeaea !important; -webkit-print-color-adjust: exact; }
            .break-cell { background-color: #f5f5f5 !important; -webkit-print-color-adjust: exact; }
            .slot-box { background: none !important; border: 1px solid #999 !important; }
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
        <div style="font-size: 13px; color: #64748b; font-weight: 700;">Smart-Schools &bull; {{ __('Teacher Schedule') }}</div>
    </div>

    <div class="header-section">
        <div>
            <h2>👨‍🏫 {{ __('MY WEEKLY TEACHING SCHEDULE') }}</h2>
            <small style="color: #64748b; font-weight: bold; font-size: 13px;">
                {{ __('Instructor') }}: <b>{{ $teacher->name ?: $teacher->username }}</b> | {{ __('Campus') }}: {{ $schoolName }}
            </small>
        </div>

        <div class="nav-links">
            <a href="{{ route('teacher.marks') }}" style="color: #0056b3;">⬅ {{ __('Enter Marks') }}</a>
            <a href="{{ route('teacher.attendance') }}" style="color: #16a34a;">📝 {{ __('Take Attendance') }}</a>
            <a href="{{ route('teacher.marks.all') }}" style="color: #0056b3;">📊 {{ __('View All Marks') }}</a>
            <a href="{{ route('timetable.index') }}" class="btn-school-tt">🗓️ {{ __('View School Timetable') }}</a>

            <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #dc2626; font-weight: bold; cursor: pointer; padding: 0; font-size: 13px;">{{ __('Logout') }}</button>
            </form>

            <!-- Language Switcher -->
            <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 2px 4px; gap: 4px;">
                <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#0284c7' : '#64748b' }}; background: {{ app()->getLocale() == 'en' ? '#e0f2fe' : 'transparent' }};">🇬🇧 EN</a>
                <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#0284c7' : '#64748b' }}; background: {{ app()->getLocale() == 'sw' ? '#e0f2fe' : 'transparent' }};">🇹🇿 SW</a>
            </div>
        </div>
    </div>

    <div class="summary-banner">
        <span><b>📌 {{ __('Jumla ya Vipindi') }}:</b> {{ count($slots) }} {{ __('allocated instruction periods per week') }}</span>
        <span style="color: #15803d; font-size: 13px; font-weight: bold;">✅ {{ __('Roster active for academic term') }}</span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 90px;">DAY</th>
                    @for($p = 1; $p <= 4; $p++)
                        <th>
                            PERIOD {{ $p }}
                            <small>{{ $periodSlots[$p] }}</small>
                        </th>
                    @endfor
                    <th class="break-cell">TEA<br>BREAK</th>
                    @for($p = 5; $p <= 7; $p++)
                        <th>
                            PERIOD {{ $p }}
                            <small>{{ $periodSlots[$p] }}</small>
                        </th>
                    @endfor
                    <th class="break-cell">LUNCH<br>BREAK</th>
                    @for($p = 8; $p <= 9; $p++)
                        <th>
                            PERIOD {{ $p }}
                            <small>{{ $periodSlots[$p] }}</small>
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($days as $day)
                    <tr>
                        <td class="day-column">{{ __($day) }}</td>

                        <!-- Periods 1 to 4 -->
                        @for($p = 1; $p <= 4; $p++)
                            <td>
                                @if(isset($teacherMatrix[$day][$p]))
                                    <div class="slot-box">
                                        <span class="slot-class">{{ $teacherMatrix[$day][$p]['class'] }}</span>
                                        <span class="slot-subject">📚 {{ $teacherMatrix[$day][$p]['subject'] }}</span>
                                    </div>
                                @else
                                    <span class="slot-free">-</span>
                                @endif
                            </td>
                        @endfor

                        <td class="break-cell">B<br>R<br>E<br>A<br>K</td>

                        <!-- Periods 5 to 7 -->
                        @for($p = 5; $p <= 7; $p++)
                            <td>
                                @if(isset($teacherMatrix[$day][$p]))
                                    <div class="slot-box">
                                        <span class="slot-class">{{ $teacherMatrix[$day][$p]['class'] }}</span>
                                        <span class="slot-subject">📚 {{ $teacherMatrix[$day][$p]['subject'] }}</span>
                                    </div>
                                @else
                                    <span class="slot-free">-</span>
                                @endif
                            </td>
                        @endfor

                        <td class="break-cell">L<br>U<br>N<br>C<br>H</td>

                        <!-- Periods 8 and 9 -->
                        @for($p = 8; $p <= 9; $p++)
                            <td>
                                @if(isset($teacherMatrix[$day][$p]))
                                    <div class="slot-box">
                                        <span class="slot-class">{{ $teacherMatrix[$day][$p]['class'] }}</span>
                                        <span class="slot-subject">📚 {{ $teacherMatrix[$day][$p]['subject'] }}</span>
                                    </div>
                                @else
                                    <span class="slot-free">-</span>
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button type="button" class="print-btn" onclick="window.print()">🖨️ {{ __('Print My Timetable') }}</button>
</div>

</body>
</html>
