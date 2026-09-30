<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $schoolName }} - {{ __('School Timetable') }} ({{ $selectedClass }}) | Smart-Schools</title>
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
            color: #2563eb;
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

        .filter-form {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-form select {
            padding: 8px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-auto {
            background: #2563eb;
            color: white !important;
            border: none;
            padding: 9px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-auto:hover {
            background: #1d4ed8;
        }

        .nav-links a {
            font-weight: bold;
            text-decoration: none;
            color: #2563eb;
            font-size: 14px;
            margin-left: 10px;
        }

        .alert {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
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
            font-style: italic;
            letter-spacing: 2px;
            width: 28px;
            padding: 4px 2px;
            font-size: 11px;
            line-height: 1.6;
        }

        .slot-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 6px 4px;
            border-radius: 4px;
            min-height: 58px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .slot-subject {
            font-weight: bold;
            color: #166534;
            font-size: 12px;
        }

        .slot-teacher {
            color: #64748b;
            font-size: 10px;
            margin-top: 2px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-edit {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 3px;
            cursor: pointer;
            margin-top: 3px;
            align-self: center;
        }

        .btn-edit:hover {
            background-color: #2563eb;
        }

        .btn-add {
            background-color: #f8fafc;
            color: #64748b;
            border: 1px dashed #cbd5e1;
            padding: 4px 8px;
            font-size: 11px;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn-add:hover {
            background-color: #e2e8f0;
            color: #0f172a;
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

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 24px 28px;
            border-radius: 8px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: left;
        }

        .modal-header {
            font-weight: bold;
            font-size: 16px;
            color: #2563eb;
            margin-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #475569;
        }

        .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 14px;
        }

        .modal-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 22px;
        }

        .btn-save {
            background: #16a34a;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-delete {
            background: #dc2626;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-cancel {
            background: #64748b;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        @media print {
            body { background: white; margin: 0; padding: 0; }
            .container { box-shadow: none; max-width: 100%; padding: 0; }
            .filter-form, .nav-links, .print-btn, .btn-edit, .btn-add, .teachers-registry-box, .no-print { display: none !important; }
            th { background-color: #eaeaea !important; -webkit-print-color-adjust: exact; }
            .break-cell { background-color: #f5f5f5 !important; -webkit-print-color-adjust: exact; }
            .slot-box { background: none !important; border: none !important; }
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
        <div style="font-size: 13px; color: #64748b; font-weight: 700;">Smart-Schools &bull; {{ __('School Timetable') }}</div>
    </div>

    <div class="header-section">
        <div>
            <div style="font-size: 17px; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span>🏫 {{ strtoupper($schoolName) }}</span>
                @if($isAcademic)
                    <button type="button" onclick="openSettingsModal()" class="no-print" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 3px 9px; border-radius: 6px; font-size: 11px; font-weight: 800; cursor: pointer; text-transform: none; letter-spacing: 0;">
                        ✏️ {{ __('Edit School / Periods / Breaks') }}
                    </button>
                @endif
            </div>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <h2 style="margin: 0;">🗓️ {{ __('CLASS SCHEDULE') }}: {{ strtoupper($selectedClass) }}</h2>
                @if($isALevel)
                    <span style="background: #fdf4ff; color: #86198f; border: 1.5px solid #d946ef; padding: 3px 10px; border-radius: 12px; font-weight: 800; font-size: 11px;">
                        🎓 {{ __('A-Level (Advance Schedule)') }}
                    </span>
                @else
                    <span style="background: #f0fdf4; color: #166534; border: 1.5px solid #22c55e; padding: 3px 10px; border-radius: 12px; font-weight: 800; font-size: 11px;">
                        📚 {{ __('O-Level Schedule') }}
                    </span>
                @endif
            </div>
            <small style="color: #64748b; font-weight: bold; font-size: 12px; display: block; margin-top: 4px;">
                {{ __('School') }}: <b>{{ $schoolName }}</b> &bull; {{ __('Periods per Day') }}: <b>{{ $periodsPerDay ?? 10 }}</b> &bull; {{ __('Breakfast') }}: <b>{{ $breakfastTime ?? '11:20 - 11:40' }}</b> &bull; {{ __('Lunch') }}: <b>{{ $lunchTime ?? '14:20 - 15:00' }}</b>
            </small>
        </div>

        <div class="filter-form">
            <form method="GET" action="{{ route('timetable.index') }}" style="display:inline-flex; align-items:center; gap:8px; flex-wrap:wrap;">
                @if(!empty($canSwitchSchool) && isset($allSchools) && count($allSchools) > 1)
                    <select name="school_name" onchange="this.form.submit()" title="{{ __('Select School') }}" style="border-color: #3b82f6; background: #eff6ff; color: #1e3a8a;">
                        @foreach($allSchools as $sch)
                            <option value="{{ $sch }}" {{ $schoolName === $sch ? 'selected' : '' }}>
                                🏫 {{ $sch }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="hidden" name="school_name" value="{{ $schoolName }}">
                @endif

                <select name="class_name" onchange="this.form.submit()">
                    @foreach($classes as $cls)
                        @php
                            $cIsAdv = \App\Models\Student::isClassALevel($cls);
                        @endphp
                        <option value="{{ $cls }}" {{ $selectedClass === $cls ? 'selected' : '' }}>
                            {{ $cls }} {{ $cIsAdv ? '🎓 (' . __('Advance') . ')' : '📚 (' . __('O-Level') . ')' }}
                        </option>
                    @endforeach
                </select>
            </form>

            <div class="nav-links" style="display:flex; align-items:center; gap:8px; flex-wrap: wrap;">
                @if($isAcademic)
                    <button type="button" onclick="openSettingsModal()" style="background: #0f766e; color: white; border: none; padding: 9px 14px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold; display: inline-flex; align-items: center; gap: 5px;">
                        ⚙️ {{ __('Vipindi & Mapumziko') }}
                    </button>
                    <form method="POST" action="{{ route('timetable.auto_generate') }}" style="display:inline;" onsubmit="return confirm('{{ __('Are you sure you want the system to generate a new school timetable from the registered teachers and their assigned subjects?') }}');">
                        @csrf
                        <input type="hidden" name="school_name" value="{{ $schoolName }}">
                        <button type="submit" class="btn-auto">⚡ {{ __('Generate Kutoka kwa Walimu & Masomo') }}</button>
                    </form>
                @else
                    <button type="button" class="btn-auto" onclick="alert('{{ __('Please login as Academic Master, Head of School, or Admin to auto-generate timetables.') }}')">⚡ {{ __('Auto-Generate Timetable') }}</button>
                @endif
                <button type="button" onclick="toggleTeachersRegistry()" style="background: #f8fafc; color: #0f172a; border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold;">
                    👨‍🏫 {{ __('Walimu & Masomo') }} ({{ isset($allTeachers) ? $allTeachers->count() : 0 }})
                </button>
                <a href="{{ route('home') }}">{{ __('Home') }}</a>

                <!-- Language Switcher -->
                <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 2px 4px; gap: 4px; margin-left: 6px;">
                    <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#2563eb' : '#64748b' }}; background: {{ app()->getLocale() == 'en' ? '#eff6ff' : 'transparent' }};">🇬🇧 EN</a>
                    <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#2563eb' : '#64748b' }}; background: {{ app()->getLocale() == 'sw' ? '#eff6ff' : 'transparent' }};">🇹🇿 SW</a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <!-- Registered Teachers & Their Assigned Subjects Box -->
    <div id="teachersRegistryBox" class="teachers-registry-box" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 18px; margin-bottom: 18px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 10px;">
            <div>
                <span style="font-weight: 800; font-size: 14px; color: #0f172a;">
                    👨‍🏫 {{ __('Walimu Waliosajiliwa na Masomo Wanayofundisha') }} — {{ $schoolName }}
                </span>
                <span style="font-size: 12px; color: #475569; margin-left: 6px;">
                    ({{ __('Darasa') }}: <b style="color: #2563eb;">{{ $selectedClass }}</b>)
                </span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                @if(auth()->check() && auth()->user()->role === 'Admin')
                    <a href="{{ route('admin.users', ['role' => 'Teacher']) }}" style="font-size: 12px; font-weight: bold; color: #2563eb; text-decoration: none; background: #eff6ff; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 4px;">
                        ➕ {{ __('Sajili / Hariri Walimu & Masomo') }}
                    </a>
                @endif
            </div>
        </div>

        @if(isset($classAssignments) && $classAssignments->isNotEmpty())
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @foreach($classAssignments as $asg)
                    <div style="background: #ffffff; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; border-radius: 6px; padding: 6px 10px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                        <span style="font-weight: 700; color: #166534;">📚 {{ $asg->subject ? $asg->subject->subject_name : 'Subject' }}</span>
                        <span style="color: #94a3b8;">&bull;</span>
                        <span style="color: #0f172a; font-weight: 600;">👤 {{ $asg->teacher ? ($asg->teacher->name ?: $asg->teacher->username) : 'Teacher' }}</span>
                    </div>
                @endforeach
            </div>
        @elseif(isset($schoolAssignments) && $schoolAssignments->isNotEmpty())
            <div style="font-size: 12px; color: #475569; margin-bottom: 6px;">
                ℹ️ {{ __('Walimu waliosajiliwa shuleni na masomo yao (hutumika kutengeneza ratiba):') }}
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @foreach($schoolAssignments as $asg)
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6; border-radius: 6px; padding: 6px 10px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <span style="font-weight: 700; color: #1e40af;">📚 {{ $asg->subject ? $asg->subject->subject_name : 'Subject' }}</span>
                        <span style="color: #94a3b8;">&bull;</span>
                        <span style="color: #0f172a; font-weight: 600;">👤 {{ $asg->teacher ? ($asg->teacher->name ?: $asg->teacher->username) : 'Teacher' }}</span>
                        <span style="color: #64748b; font-size: 11px;">({{ $asg->class_name }})</span>
                    </div>
                @endforeach
            </div>
        @else
            <div style="color: #b91c1c; font-size: 12px; font-weight: bold;">
                ⚠️ {{ __('Hakuna walimu waliopangiwa masomo bado. Tafadhali nenda User Management uwasajili walimu na masomo wanayofundisha.') }}
            </div>
        @endif
    </div>

    @php
        $totalPeriods = $periodsPerDay ?? 10;
        $bAfter = $breakfastAfterPeriod ?? 5;
        $lAfter = $lunchAfterPeriod ?? 9;
        $bTime = $breakfastTime ?? '11:20 - 11:40';
        $lTime = $lunchTime ?? '14:20 - 15:00';
    @endphp

    <!-- Break Times & Periods Bar -->
    <div class="timetable-legend" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 10px; margin-bottom: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; font-size: 12px; align-items: center;">
        <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
            <span>🏫 <b>{{ __('School') }}:</b> {{ $schoolName }}</span>
            <span style="color: #cbd5e1;">|</span>
            <span>🔢 <b>{{ __('Periods per Day') }}:</b> {{ $totalPeriods }}</span>
            <span style="color: #cbd5e1;">|</span>
            <span style="color: #0369a1; font-weight: 800;">⏰ <b>{{ __('Muda wa Mapumziko') }}:</b></span>
            <span>☕ <b>{{ __('Breakfast') }}:</b> {{ $bTime }}</span>
            <span style="color: #cbd5e1;">|</span>
            <span>🍱 <b>{{ __('Lunch') }}:</b> {{ $lTime }}</span>
        </div>
        @if($isAcademic)
            <button type="button" onclick="openSettingsModal()" class="no-print" style="background: #ffffff; color: #0f766e; border: 1px solid #0d9488; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer;">
                ⚙️ {{ __('Edit Periods, Breakfast & Lunch Time') }}
            </button>
        @endif
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>{{ __('Day') }}</th>
                    @for($p = 1; $p <= $totalPeriods; $p++)
                        <th>{{ __('Period') }} {{ $p }}<br><small>{{ $periodSlots[$p] ?? '' }}</small></th>
                        @if($bAfter > 0 && $p === $bAfter && $p < $totalPeriods)
                            <th style="width: 32px; background-color: #fef9c3; color: #854d0e;">{{ __('BREAKFAST') }}<br><small>{{ $bTime }}</small></th>
                        @endif
                        @if($lAfter > 0 && $p === $lAfter && $p < $totalPeriods)
                            <th style="width: 32px; background-color: #ffedd5; color: #9a3412;">{{ __('LUNCH BREAK') }}<br><small>{{ $lTime }}</small></th>
                        @endif
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($days as $day)
                    <tr>
                        <td class="day-column">{{ __($day) }}</td>

                        @for($p = 1; $p <= $totalPeriods; $p++)
                            @include('timetable.partials.cell', ['day' => $day, 'p' => $p])

                            @if($bAfter > 0 && $p === $bAfter && $p < $totalPeriods)
                                <td class="break-cell" style="background-color: #fef9c3; color: #854d0e;" title="{{ $bTime }}">B<br>R<br>E<br>A<br>K<br>F<br>A<br>S<br>T</td>
                            @endif

                            @if($lAfter > 0 && $p === $lAfter && $p < $totalPeriods)
                                <td class="break-cell" style="background-color: #ffedd5; color: #9a3412;" title="{{ $lTime }}">L<br>U<br>N<br>C<br>H</td>
                            @endif
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button onclick="window.print()" class="print-btn">🖨️ {{ __('Print Timetable') }}</button>
    <div style="clear: both;"></div>
</div>

<!-- Modal for Editing/Adding Slot -->
@if($isAcademic)
<div id="slotModal" class="modal">
    <div class="modal-content" style="max-width: 460px;">
        <div class="modal-header" id="modalTitle">{{ __('Edit Timetable Slot') }}</div>
        
        <form method="POST" action="{{ route('timetable.save_slot') }}" id="saveSlotForm">
            @csrf
            <input type="hidden" name="school_name" value="{{ $schoolName }}">
            <input type="hidden" name="class_name" value="{{ $selectedClass }}">
            <input type="hidden" name="day_of_week" id="modalDay">
            <input type="hidden" name="period_number" id="modalPeriod">
            <input type="hidden" name="slot_type" id="modalSlotType" value="subject">

            <!-- Slot Type Toggle: Subject vs Event -->
            <div class="form-group">
                <label>{{ __('Slot Type') }}:</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <button type="button" id="btnTypeSubject" onclick="setSlotType('subject')"
                            style="padding: 8px 10px; border-radius: 6px; border: 2px solid #2563eb; background: #eff6ff; color: #1d4ed8; font-weight: 800; font-size: 13px; cursor: pointer;">
                        📚 {{ __('Subject') }}
                    </button>
                    <button type="button" id="btnTypeEvent" onclick="setSlotType('event')"
                            style="padding: 8px 10px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f8fafc; color: #475569; font-weight: 800; font-size: 13px; cursor: pointer;">
                        🏆 {{ __('Event / Activity') }}
                    </button>
                </div>
            </div>

            <!-- Subject Selector (shown when slot_type == 'subject') -->
            <div class="form-group" id="subjectGroupBox">
                <label for="modalSubject">{{ __('Subject') }}:</label>
                <select name="subject_id" id="modalSubject" onchange="onModalSubjectChange(this.value)">
                    <option value="">-- {{ __('Choose Subject') }} --</option>
                    @foreach($allSubjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Event Input (shown when slot_type == 'event') -->
            <div class="form-group" id="eventGroupBox" style="display: none;">
                <label for="modalEventName">🏆 {{ __('Event / Activity Name') }}:</label>
                <input type="text" name="event_name" id="modalEventName"
                       placeholder="{{ __('e.g. Sports and Games, Debate, Religion, Self Study...') }}"
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; box-sizing: border-box;">

                <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 6px;">
                    <span style="font-size: 11px; color: #64748b; width: 100%; font-weight: bold;">⚡ {{ __('Quick Choose Event') }}:</span>
                    @foreach(['Sports and Games', 'Debate', 'Religion', 'General Cleaning', 'Self Study / Prep', 'Clubs & Societies'] as $presetEvent)
                        <button type="button" onclick="document.getElementById('modalEventName').value = '{{ $presetEvent }}'"
                                style="background: #fffbeb; color: #92400e; border: 1px solid #fcd34d; border-radius: 12px; padding: 3px 9px; font-size: 11px; font-weight: 700; cursor: pointer;">
                            {{ __($presetEvent) }}
                        </button>
                    @endforeach
                </div>

                <label style="display: flex; align-items: center; gap: 7px; margin-top: 12px; font-size: 12px; color: #0f172a; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="apply_all_classes" id="modalApplyAllClasses" value="1">
                    <span>{{ __('Apply this event to all classes in this school for this period') }}</span>
                </label>
            </div>

            <div class="form-group">
                <label for="modalTeacher" id="modalTeacherLabel">{{ __('Assigned Teacher') }}:</label>
                <select name="teacher_id" id="modalTeacher">
                    <option value="" id="modalTeacherPlaceholder">-- {{ __('Choose Teacher') }} --</option>
                    @foreach($allTeachers as $tch)
                        @php
                            $isClassTeacher = in_array($tch->id, $assignedTeacherIds ?? []);
                            $tSubjects = $tch->teacherAssignments
                                ? $tch->teacherAssignments->map(fn($a) => $a->subject ? $a->subject->subject_name : null)->filter()->unique()->implode(', ')
                                : '';
                        @endphp
                        <option value="{{ $tch->id }}">
                            {{ $tch->name ?: $tch->username }}{{ $tSubjects ? ' — [' . $tSubjects . ']' : '' }} {{ $isClassTeacher ? '★' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-save">{{ __('Save Slot') }}</button>
                <button type="button" class="btn-delete" id="modalDeleteBtn" onclick="submitDelete()">{{ __('Delete') }}</button>
                <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('Cancel') }}</button>
            </div>
        </form>

        <form method="POST" action="{{ route('timetable.delete_slot') }}" id="deleteSlotForm" style="display: none;">
            @csrf
            <input type="hidden" name="school_name" value="{{ $schoolName }}">
            <input type="hidden" name="class_name" value="{{ $selectedClass }}">
            <input type="hidden" name="day_of_week" id="delDay">
            <input type="hidden" name="period_number" id="delPeriod">
        </form>
    </div>
</div>

<!-- Modal for Editing School Timetable Settings (School Name, Periods per Day, Breakfast Time, Lunch Time) -->
<div id="settingsModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">⚙️ {{ __('Timetable Settings') }} — {{ $schoolName }}</div>

        <form method="POST" action="{{ route('timetable.save_settings') }}">
            @csrf
            <input type="hidden" name="school_name" value="{{ $schoolName }}">
            <input type="hidden" name="class_name" value="{{ $selectedClass }}">

            <div class="form-group">
                <label for="newSchoolName">🏫 {{ __('School Name') }}:</label>
                <input type="text" name="new_school_name" id="newSchoolName" value="{{ $schoolName }}" required
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label for="periodsPerDay">🔢 {{ __('Periods per Day') }}:</label>
                    <select name="periods_per_day" id="periodsPerDay" required>
                        @for($cnt = 4; $cnt <= 14; $cnt++)
                            <option value="{{ $cnt }}" {{ ($periodsPerDay ?? 10) == $cnt ? 'selected' : '' }}>
                                {{ $cnt }} {{ __('Periods') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="form-group">
                    <label for="periodDuration">⏱️ {{ __('Minutes per Period') }}:</label>
                    <input type="number" name="period_duration" id="periodDuration" min="20" max="120" value="{{ $periodDuration ?? 40 }}" required
                           style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>
            </div>

            <div class="form-group">
                <label for="classStartTime">🌅 {{ __('First Period Start Time') }} (e.g. 08:00):</label>
                <input type="text" name="class_start_time" id="classStartTime" value="{{ $classStartTime ?? '08:00' }}" placeholder="08:00" required
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label for="breakfastTime">☕ {{ __('Breakfast Time') }}:</label>
                    <input type="text" name="breakfast_time" id="breakfastTime" value="{{ $breakfastTime ?? '11:20 - 11:40' }}" placeholder="11:20 - 11:40" required
                           style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div class="form-group">
                    <label for="breakfastAfterPeriod">{{ __('After Period') }}:</label>
                    <select name="breakfast_after_period" id="breakfastAfterPeriod">
                        <option value="0">-- {{ __('No Breakfast Break') }} --</option>
                        @for($p = 1; $p <= 12; $p++)
                            <option value="{{ $p }}" {{ ($breakfastAfterPeriod ?? 5) == $p ? 'selected' : '' }}>
                                {{ __('After Period') }} {{ $p }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label for="lunchTime">🍱 {{ __('Lunch Time') }}:</label>
                    <input type="text" name="lunch_time" id="lunchTime" value="{{ $lunchTime ?? '14:20 - 15:00' }}" placeholder="14:20 - 15:00" required
                           style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div class="form-group">
                    <label for="lunchAfterPeriod">{{ __('After Period') }}:</label>
                    <select name="lunch_after_period" id="lunchAfterPeriod">
                        <option value="0">-- {{ __('No Lunch Break') }} --</option>
                        @for($p = 1; $p <= 13; $p++)
                            <option value="{{ $p }}" {{ ($lunchAfterPeriod ?? 9) == $p ? 'selected' : '' }}>
                                {{ __('After Period') }} {{ $p }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-save">💾 {{ __('Save Settings') }}</button>
                <button type="button" class="btn-cancel" onclick="closeSettingsModal()">{{ __('Cancel') }}</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    const subjectTeacherMap = @json($subjectTeacherDefaultMap ?? []);

    function toggleTeachersRegistry() {
        const box = document.getElementById('teachersRegistryBox');
        if (box) {
            box.style.display = (box.style.display === 'none') ? 'block' : 'none';
        }
    }

    function onModalSubjectChange(subId) {
        if (subId && subjectTeacherMap[subId]) {
            const teacherSelect = document.getElementById('modalTeacher');
            if (teacherSelect) {
                teacherSelect.value = subjectTeacherMap[subId];
            }
        }
    }

    function setSlotType(type) {
        document.getElementById('modalSlotType').value = type;
        const isEvent = (type === 'event');

        document.getElementById('subjectGroupBox').style.display = isEvent ? 'none' : 'block';
        document.getElementById('eventGroupBox').style.display = isEvent ? 'block' : 'none';

        document.getElementById('modalSubject').required = !isEvent;
        document.getElementById('modalEventName').required = isEvent;
        document.getElementById('modalTeacher').required = !isEvent;

        document.getElementById('modalTeacherLabel').textContent = isEvent
            ? '{{ __("Supervisor Teacher (Optional)") }}:'
            : '{{ __("Assigned Teacher") }}:';
        document.getElementById('modalTeacherPlaceholder').textContent = isEvent
            ? '-- {{ __("No Teacher / Optional") }} --'
            : '-- {{ __("Choose Teacher") }} --';

        const btnSub = document.getElementById('btnTypeSubject');
        const btnEv = document.getElementById('btnTypeEvent');

        if (isEvent) {
            btnEv.style.border = '2px solid #d97706';
            btnEv.style.background = '#fffbeb';
            btnEv.style.color = '#92400e';

            btnSub.style.border = '1px solid #cbd5e1';
            btnSub.style.background = '#f8fafc';
            btnSub.style.color = '#475569';
        } else {
            btnSub.style.border = '2px solid #2563eb';
            btnSub.style.background = '#eff6ff';
            btnSub.style.color = '#1d4ed8';

            btnEv.style.border = '1px solid #cbd5e1';
            btnEv.style.background = '#f8fafc';
            btnEv.style.color = '#475569';
        }
    }

    function handleEdit(day, period, subId, teachId, slotType = 'subject', eventName = '') {
        @if($isAcademic)
            openModal(day, period, subId, teachId, slotType, eventName);
        @else
            alert('{{ __("Please login as Academic Master, Head of School, or Admin to edit timetable slots.") }}');
        @endif
    }

    @if($isAcademic)
    function openModal(day, period, subId, teachId, slotType = 'subject', eventName = '') {
        document.getElementById('modalDay').value = day;
        document.getElementById('modalPeriod').value = period;
        document.getElementById('modalSubject').value = subId || '';
        document.getElementById('modalEventName').value = eventName || '';
        document.getElementById('modalApplyAllClasses').checked = false;
        document.getElementById('modalTeacher').value = teachId || (subId && subjectTeacherMap[subId] ? subjectTeacherMap[subId] : '');

        setSlotType(slotType || 'subject');

        document.getElementById('delDay').value = day;
        document.getElementById('delPeriod').value = period;

        const hasExisting = Boolean(subId || eventName);
        document.getElementById('modalTitle').textContent = (hasExisting ? '{{ __("Edit") }}' : '{{ __("Add") }}') + ' {{ __("Slot") }}: ' + day + ' ({{ __("Period") }} ' + period + ')';
        document.getElementById('modalDeleteBtn').style.display = hasExisting ? 'inline-block' : 'none';

        document.getElementById('slotModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('slotModal').style.display = 'none';
    }

    function openSettingsModal() {
        document.getElementById('settingsModal').style.display = 'flex';
    }

    function closeSettingsModal() {
        document.getElementById('settingsModal').style.display = 'none';
    }

    function submitDelete() {
        if (confirm('{{ __("Clear this timetable slot?") }}')) {
            document.getElementById('deleteSlotForm').submit();
        }
    }

    window.onclick = function(event) {
        const slotModal = document.getElementById('slotModal');
        const settingsModal = document.getElementById('settingsModal');
        if (event.target === slotModal) {
            closeModal();
        }
        if (event.target === settingsModal) {
            closeSettingsModal();
        }
    };
    @endif
</script>

</body>
</html>
