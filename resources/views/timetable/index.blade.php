<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Timetable - {{ $selectedClass }} | Smart-Results</title>
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
            background: #0284c7;
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
            background: #0369a1;
        }

        .nav-links a {
            font-weight: bold;
            text-decoration: none;
            color: #0284c7;
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
            color: #0284c7;
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
            .filter-form, .nav-links, .print-btn, .btn-edit, .btn-add { display: none !important; }
            th { background-color: #eaeaea !important; -webkit-print-color-adjust: exact; }
            .break-cell { background-color: #f5f5f5 !important; -webkit-print-color-adjust: exact; }
            .slot-box { background: none !important; border: none !important; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <div>
            <h2>🗓️ {{ __('CLASS SCHEDULE') }}: {{ strtoupper($selectedClass) }}</h2>
            <small style="color: #64748b; font-weight: bold; font-size: 13px;">{{ __('Campus') }}: {{ $schoolName }}</small>
        </div>

        <div class="filter-form">
            <form method="GET" action="{{ route('timetable.index') }}" style="display:inline;">
                <select name="class_name" onchange="this.form.submit()">
                    @foreach($classes as $cls)
                        <option value="{{ $cls }}" {{ $selectedClass === $cls ? 'selected' : '' }}>
                            {{ $cls }}
                        </option>
                    @endforeach
                </select>
            </form>

            <div class="nav-links" style="display:flex; align-items:center; gap:8px;">
                @if($isAcademic)
                    <form method="POST" action="{{ route('timetable.auto_generate') }}" style="display:inline;" onsubmit="return confirm('Generate automatic timetable for all classes? This will update the schedule without teacher clashes.');">
                        @csrf
                        <button type="submit" class="btn-auto">⚡ {{ __('Auto-Generate Timetable') }}</button>
                    </form>
                @else
                    <button type="button" class="btn-auto" onclick="alert('Please login as Academic Master, Head of School, or Admin to auto-generate timetables.')">⚡ {{ __('Auto-Generate Timetable') }}</button>
                @endif
                <a href="{{ route('home') }}">{{ __('Home') }}</a>

                <!-- Language Switcher -->
                <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 2px 4px; gap: 4px; margin-left: 6px;">
                    <a href="{{ route('lang.switch', 'en') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'en' ? '#0284c7' : '#64748b' }}; background: {{ app()->getLocale() == 'en' ? '#e0f2fe' : 'transparent' }};">🇬🇧 EN</a>
                    <a href="{{ route('lang.switch', 'sw') }}" style="font-size: 11px; font-weight: bold; text-decoration: none; padding: 2px 6px; border-radius: 10px; color: {{ app()->getLocale() == 'sw' ? '#0284c7' : '#64748b' }}; background: {{ app()->getLocale() == 'sw' ? '#e0f2fe' : 'transparent' }};">🇹🇿 SW</a>
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

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>{{ __('Day') }}</th>
                    <th>{{ __('Period') }} 1<br><small>{{ $periodSlots[1] }}</small></th>
                    <th>{{ __('Period') }} 2<br><small>{{ $periodSlots[2] }}</small></th>
                    <th>{{ __('Period') }} 3<br><small>{{ $periodSlots[3] }}</small></th>
                    <th>{{ __('Period') }} 4<br><small>{{ $periodSlots[4] }}</small></th>
                    <th style="width: 32px;">{{ __('TEA BREAK') }}<br><small>10:40 - 11:10</small></th>
                    <th>{{ __('Period') }} 5<br><small>{{ $periodSlots[5] }}</small></th>
                    <th>{{ __('Period') }} 6<br><small>{{ $periodSlots[6] }}</small></th>
                    <th>{{ __('Period') }} 7<br><small>{{ $periodSlots[7] }}</small></th>
                    <th style="width: 32px;">{{ __('LUNCH BREAK') }}<br><small>01:10 - 02:00</small></th>
                    <th>{{ __('Period') }} 8<br><small>{{ $periodSlots[8] }}</small></th>
                    <th>{{ __('Period') }} 9<br><small>{{ $periodSlots[9] }}</small></th>
                </tr>
            </thead>
            <tbody>
                @foreach($days as $day)
                    <tr>
                        <td class="day-column">{{ __($day) }}</td>

                        {{-- Period 1 to 4 --}}
                        @for($p = 1; $p <= 4; $p++)
                            @include('timetable.partials.cell', ['day' => $day, 'p' => $p])
                        @endfor

                        {{-- Tea Break --}}
                        <td class="break-cell">B<br>R<br>E<br>A<br>K</td>

                        {{-- Period 5 to 7 --}}
                        @for($p = 5; $p <= 7; $p++)
                            @include('timetable.partials.cell', ['day' => $day, 'p' => $p])
                        @endfor

                        {{-- Lunch Break --}}
                        <td class="break-cell">L<br>U<br>N<br>C<br>H</td>

                        {{-- Period 8 to 9 --}}
                        @for($p = 8; $p <= 9; $p++)
                            @include('timetable.partials.cell', ['day' => $day, 'p' => $p])
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button onclick="window.print()" class="print-btn">🖨️ Print Timetable</button>
    <div style="clear: both;"></div>
</div>

<!-- Modal for Editing/Adding Slot -->
@if($isAcademic)
<div id="slotModal" class="modal">
    <div class="modal-content">
        <div class="modal-header" id="modalTitle">Edit Timetable Slot</div>
        
        <form method="POST" action="{{ route('timetable.save_slot') }}" id="saveSlotForm">
            @csrf
            <input type="hidden" name="class_name" value="{{ $selectedClass }}">
            <input type="hidden" name="day_of_week" id="modalDay">
            <input type="hidden" name="period_number" id="modalPeriod">

            <div class="form-group">
                <label for="modalSubject">Subject:</label>
                <select name="subject_id" id="modalSubject" required>
                    <option value="">-- Choose Subject --</option>
                    @foreach($allSubjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="modalTeacher">Assigned Teacher:</label>
                <select name="teacher_id" id="modalTeacher" required>
                    <option value="">-- Choose Teacher --</option>
                    @foreach($allTeachers as $tch)
                        <option value="{{ $tch->id }}">{{ $tch->name ?: $tch->username }}</option>
                    @endforeach
                </select>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-save">Save Slot</button>
                <button type="button" class="btn-delete" id="modalDeleteBtn" onclick="submitDelete()">Delete</button>
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
            </div>
        </form>

        <form method="POST" action="{{ route('timetable.delete_slot') }}" id="deleteSlotForm" style="display: none;">
            @csrf
            <input type="hidden" name="class_name" value="{{ $selectedClass }}">
            <input type="hidden" name="day_of_week" id="delDay">
            <input type="hidden" name="period_number" id="delPeriod">
        </form>
    </div>
</div>
@endif

<script>
    function handleEdit(day, period, subId, teachId) {
        @if($isAcademic)
            openModal(day, period, subId, teachId);
        @else
            alert('Please login as Academic Master, Head of School, or Admin to edit timetable slots.');
        @endif
    }

    @if($isAcademic)
    function openModal(day, period, subId, teachId) {
        document.getElementById('modalDay').value = day;
        document.getElementById('modalPeriod').value = period;
        document.getElementById('modalSubject').value = subId || '';
        document.getElementById('modalTeacher').value = teachId || '';

        document.getElementById('delDay').value = day;
        document.getElementById('delPeriod').value = period;

        document.getElementById('modalTitle').textContent = (subId ? 'Edit' : 'Add') + ' Slot: ' + day + ' (Period ' + period + ')';
        document.getElementById('modalDeleteBtn').style.display = subId ? 'inline-block' : 'none';

        document.getElementById('slotModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('slotModal').style.display = 'none';
    }

    function submitDelete() {
        if (confirm('Clear this timetable slot?')) {
            document.getElementById('deleteSlotForm').submit();
        }
    }

    window.onclick = function(event) {
        const modal = document.getElementById('slotModal');
        if (event.target === modal) {
            closeModal();
        }
    };
    @endif
</script>

</body>
</html>
