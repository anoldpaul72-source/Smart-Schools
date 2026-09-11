@extends('layouts.app')

@section('title', 'Admin Dashboard | Smart-Results')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .icon-school { background: #eff6ff; color: #2563eb; }
    .icon-student { background: #ecfdf5; color: #10b981; }
    .icon-teacher { background: #fef3c7; color: #d97706; }
    .icon-parent { background: #f3e8ff; color: #9333ea; }
    .icon-subject { background: #fee2e2; color: #dc2626; }

    .stat-info h4 {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .stat-info .stat-number {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
    }

    .admin-sections {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    @media (max-width: 900px) {
        .admin-sections {
            grid-template-columns: 1fr;
        }
    }

    .panel-box {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .panel-box h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endsection

@section('content')
<div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800;">{{ __('Admin Panel') }}</h1>
        <p style="color: var(--text-muted); font-size: 14px;">{{ __('Central institutional management & system telemetry') }}</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('leader.dashboard') }}" class="btn" style="background: #0284c7; color: white; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; text-decoration: none;">
            📊 {{ __('Angalia Matokeo ya Wanafunzi') }}
        </a>
        <button type="button" onclick="openAdminBulkSmsModal()" class="btn" style="background: #059669; color: white; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; cursor: pointer; border: none;">
            📱 {{ __('Tuma SMS kwa Wazazi') }}
        </button>
        <a href="{{ route('admin.students') }}" class="btn btn-primary">🎓 {{ __('Manage Students') }}</a>
        <a href="{{ route('admin.users') }}" class="btn btn-outline">👥 {{ __('Manage Users') }}</a>
    </div>
</div>

<!-- Stats Counter Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-school">🏫</div>
        <div class="stat-info">
            <h4>{{ __('Schools') }}</h4>
            <div class="stat-number">{{ $schoolsCount }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-student">🎓</div>
        <div class="stat-info">
            <h4>{{ __('Students') }}</h4>
            <div class="stat-number">{{ $studentsCount }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-teacher">👨‍🏫</div>
        <div class="stat-info">
            <h4>{{ __('Teachers') }}</h4>
            <div class="stat-number">{{ $teachersCount }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-parent">👪</div>
        <div class="stat-info">
            <h4>{{ __('Parents') }}</h4>
            <div class="stat-number">{{ $parentsCount }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon icon-subject">📚</div>
        <div class="stat-info">
            <h4>{{ __('Subjects') }}</h4>
            <div class="stat-number">{{ $subjectsCount }}</div>
        </div>
    </div>

    <!-- Academic Results Card -->
    <div class="stat-card">
        <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">📊</div>
        <div class="stat-info" style="flex: 1;">
            <h4>{{ __('Matokeo ya Wanafunzi') }}</h4>
            <div class="stat-number">{{ number_format($marksCount) }}</div>
            <a href="{{ route('leader.dashboard') }}" style="font-size: 11.5px; color: #0284c7; font-weight: 700; text-decoration: none;">{{ __('Fungua Broadsheet') }} &rarr;</a>
        </div>
    </div>

    <!-- SMS Broadcast Card -->
    <div class="stat-card">
        <div class="stat-icon" style="background: #ecfdf5; color: #059669;">📱</div>
        <div class="stat-info" style="flex: 1;">
            <h4>{{ __('Ujumbe wa SMS') }}</h4>
            <div class="stat-number">{{ number_format($smsLogsCount) }}</div>
            <a href="javascript:void(0)" onclick="openAdminBulkSmsModal()" style="font-size: 11.5px; color: #059669; font-weight: 700; text-decoration: none;">{{ __('Tuma SMS Sasa') }} &rarr;</a>
        </div>
    </div>
</div>

<div class="admin-sections">
    <!-- Left Column: Schools & Recent Users -->
    <div>
        <!-- Schools Management Box -->
        <div class="panel-box">
            <h3>
                <span>🏫 {{ __('Registered Institutions') }}</span>
                <button type="button" class="btn btn-outline" style="font-size: 12px; padding: 4px 10px;" onclick="document.getElementById('addSchoolForm').style.display = document.getElementById('addSchoolForm').style.display === 'none' ? 'block' : 'none'">{{ __('+ Add School') }}</button>
            </h3>

            <!-- Add School Form -->
            <div id="addSchoolForm" style="display: none; background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px dashed #cbd5e1;">
                <form method="POST" action="{{ route('admin.schools.store') }}">
                    @csrf
                    <div style="display: grid; grid-template-columns: 2fr 2fr 1.5fr 1fr; gap: 10px;">
                        <input type="text" name="school_name" placeholder="{{ __('School Name') }}" required>
                        <input type="text" name="address" placeholder="{{ __('Location / Address') }}">
                        <input type="text" name="phone" placeholder="{{ __('Phone Number') }}">
                        <button type="submit" class="btn btn-primary">{{ __('Save School') }}</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('School Name') }}</th>
                            <th>{{ __('Address') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th style="text-align: right; width: 90px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                            <tr>
                                <td><b>{{ $school->school_name }}</b></td>
                                <td>{{ $school->address ?? '-' }}</td>
                                <td>{{ $school->phone ?? '-' }}</td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        <button type="button" class="btn btn-outline" style="padding: 2px 7px; font-size: 11px;" onclick="openEditSchoolModal({{ json_encode($school) }})">
                                            ✏️ {{ __('Edit') }}
                                        </button>
                                        <form method="POST" action="{{ route('admin.schools.delete', $school->id) }}" onsubmit="return confirm('Delete school {{ $school->school_name }}?')" style="display: inline; margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; cursor: pointer; color: #dc2626; font-size: 13px; padding: 2px 4px;" title="{{ __('Delete') }}">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">{{ __('No schools registered yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Accounts -->
        <div class="panel-box">
            <h3>👥 {{ __('Recently Created Accounts') }}</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Username') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('School') }}</th>
                            <th>{{ __('Created At') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $u)
                            <tr>
                                <td><b>{{ $u->username }}</b></td>
                                <td><span style="background: #e2e8f0; padding: 2px 8px; border-radius: 6px; font-size: 12px;">{{ $u->role }}</span></td>
                                <td>{{ $u->school_name ?? 'Universal' }}</td>
                                <td>{{ $u->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Subjects Management -->
    <div>
        <div class="panel-box">
            <h3>📚 {{ __('Academic Subjects') }}</h3>
            <form method="POST" action="{{ route('admin.subjects.store') }}" style="margin-bottom: 16px;">
                @csrf
                <div style="display: flex; gap: 8px;">
                    <input type="text" name="subject_name" placeholder="{{ __('New Subject Name') }}" required>
                    <button type="submit" class="btn btn-primary" style="white-space: nowrap;">{{ __('Add') }}</button>
                </div>
            </form>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Subject Name') }}</th>
                            <th style="width: 50px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr>
                                <td><b>{{ $subject->subject_name }}</b></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.subjects.delete', $subject->id) }}" onsubmit="return confirm('Delete subject {{ $subject->subject_name }}?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: none; cursor: pointer; color: #dc2626; font-size: 14px;" title="{{ __('Delete') }}">
                                            🗑️
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: var(--text-muted); padding: 20px;">{{ __('No subjects added yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SMS Broadcast & Academic Results Section -->
<div class="panel-box" style="margin-top: 24px; border: 1.5px solid #cbd5e1;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
        <div>
            <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <span>📱</span> <span>{{ __('Usimamizi wa SMS & Matokeo ya Wanafunzi') }}</span>
            </h3>
            <p style="margin: 4px 0 0 0; color: var(--text-muted); font-size: 13px;">
                {{ __('Tuma matokeo ya wanafunzi kwa wazazi kwa njia ya ujumbe mfupi (SMS) na fuatilia hali ya utumaji (Delivery Status).') }}
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('leader.dashboard') }}" class="btn" style="background: #0284c7; color: white; text-decoration: none; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                📊 {{ __('Fungua NECTA Broadsheet') }}
            </a>
            <button type="button" onclick="openAdminBulkSmsModal()" class="btn" style="background: #059669; color: white; font-size: 13px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                📱 {{ __('Tuma SMS kwa Wazazi') }}
            </button>
        </div>
    </div>

    <!-- Recent SMS Logs Table -->
    <h4 style="font-size: 13px; font-weight: 700; color: #475569; margin: 18px 0 10px 0; text-transform: uppercase; letter-spacing: 0.5px;">
        📋 {{ __('Rekodi za Hivi Karibuni za SMS (Recent SMS Logs)') }}
    </h4>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tarehe / Saa</th>
                    <th>Mwanafunzi</th>
                    <th>Namba ya Mzazi</th>
                    <th>Ujumbe Mfupi (Preview)</th>
                    <th>Hali (Status)</th>
                    <th>Mtumaji</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSmsLogs as $log)
                    <tr>
                        <td style="font-size: 12.5px; color: #64748b; white-space: nowrap;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td style="font-weight: 700; color: #0f172a;">{{ $log->recipient_name ?: ($log->student?->student_name ?? '—') }}</td>
                        <td style="font-weight: 700; color: #0284c7;">📞 {{ $log->recipient_phone }}</td>
                        <td style="font-size: 12px; color: #334155; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->message }}">
                            {{ Str::limit($log->message, 60) }}
                        </td>
                        <td>
                            @if($log->status === 'sent')
                                <span style="background: #ecfdf5; color: #059669; padding: 3px 8px; border-radius: 9999px; font-weight: 700; font-size: 11.5px; border: 1px solid #6ee7b7;">✔️ Sent</span>
                            @elseif($log->status === 'simulated')
                                <span style="background: #fef9c3; color: #854d0e; padding: 3px 8px; border-radius: 9999px; font-weight: 700; font-size: 11.5px; border: 1px solid #fde047;">🟡 Simulated</span>
                            @else
                                <span style="background: #fef2f2; color: #dc2626; padding: 3px 8px; border-radius: 9999px; font-weight: 700; font-size: 11.5px; border: 1px solid #fca5a5;">❌ Failed</span>
                            @endif
                        </td>
                        <td style="font-size: 12px; color: #64748b;">{{ $log->sender?->username ?? 'System' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 24px;">
                            Hakuna rekodi za SMS zilizotumwa bado. Bonyeza <strong>"Tuma SMS kwa Wazazi"</strong> kuanza kutuma ripoti za matokeo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Edit School Modal -->
<div id="editSchoolModal" style="display:none; position:fixed; z-index:9999; inset:0; background:rgba(0,0,0,0.55); align-items:center; justify-content:center; padding:15px;">
    <div style="background:white; border-radius:12px; width:100%; max-width:500px; padding:25px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:18px;">
            <h3 style="margin:0; font-size:18px; font-weight:bold; color:#0f172a;" id="editSchoolModalTitle">✏️ {{ __('Edit School') }}</h3>
            <button type="button" onclick="closeEditSchoolModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#64748b; line-height:1;">&times;</button>
        </div>

        <form id="editSchoolForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 14px;">
                <label for="edit_school_name" style="display:block; font-weight:bold; font-size:13px; margin-bottom:4px; color:#334155;">{{ __('School Name') }}</label>
                <input type="text" name="school_name" id="edit_school_name" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box;">
            </div>

            <div style="margin-bottom: 14px;">
                <label for="edit_school_address" style="display:block; font-weight:bold; font-size:13px; margin-bottom:4px; color:#334155;">{{ __('Location / Address') }}</label>
                <input type="text" name="address" id="edit_school_address" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="edit_school_phone" style="display:block; font-weight:bold; font-size:13px; margin-bottom:4px; color:#334155;">{{ __('Phone Number') }}</label>
                <input type="text" name="phone" id="edit_school_phone" placeholder="e.g. +255 700 000 000" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box;">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; padding-top:14px;">
                <button type="button" class="btn btn-outline" onclick="closeEditSchoolModal()">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Admin Bulk SMS Modal -->
<div id="adminBulkSmsModal" style="display:none; position:fixed; z-index:9999; inset:0; background:rgba(15, 23, 42, 0.65); align-items:center; justify-content:center; padding:15px;">
    <div style="background:white; border-radius:12px; width:100%; max-width:520px; padding:24px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="font-size:24px;">📱</span>
                <div>
                    <h3 style="margin:0; font-size:17px; font-weight:800; color:#0f172a;">{{ __('Tuma Ripoti kwa Wazazi (Bulk SMS)') }}</h3>
                    <p style="margin:2px 0 0 0; font-size:12px; color:#64748b;">Tuma matokeo kwa wazazi wa darasa zima</p>
                </div>
            </div>
            <button type="button" onclick="closeAdminBulkSmsModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#94a3b8; line-height:1;">&times;</button>
        </div>

        <form method="POST" action="{{ route('sms.send_bulk') }}" id="adminBulkSmsForm" onsubmit="handleAdminBulkSubmit()">
            @csrf
            <div style="margin-bottom: 14px;">
                <label style="display:block; font-weight:bold; font-size:13px; margin-bottom:5px; color:#334155;">{{ __('Chagua Darasa:') }} <span style="color:#dc2626;">*</span></label>
                <select name="class_name" id="admin_class_name" required style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;">
                    <option value="">-- Chagua Darasa --</option>
                    @foreach($allClasses as $cls)
                        <option value="{{ $cls }}">{{ $cls }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display:block; font-weight:bold; font-size:13px; margin-bottom:5px; color:#334155;">{{ __('Aina ya Mtihani / Muhula:') }}</label>
                <select name="term" id="admin_term" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;">
                    @foreach($allTerms as $t)
                        <option value="{{ $t }}">{{ __($t) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 14px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px; padding:12px;">
                <div style="font-weight:bold; font-size:12px; color:#475569; text-transform:uppercase; margin-bottom:4px;">Muundo wa SMS (Preview):</div>
                <div style="font-family:monospace; font-size:12px; color:#0f172a; line-height:1.4;">
MZAZI WA [MWANAFUNZI] (Form 1)
Ripoti: Annual Examination - Kome Secondary School
Matokeo: Kiswahili: 82(A), Maths: 68(B), English: 75(B)
Wastani: 75.0% (Daraja: B)
Mahudhurio: 98% | Ada Inayodaiwa: Imekamilika
Kazi nzuri na hongera.
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; padding-top:16px;">
                <button type="button" class="btn btn-outline" onclick="closeAdminBulkSmsModal()">{{ __('Ghairi') }}</button>
                <button type="submit" id="btnAdminBulkSubmit" class="btn" style="background:#059669; color:white; font-weight:bold; display:inline-flex; align-items:center; gap:8px;">
                    <span>📱</span>
                    <span>{{ __('Thibitisha & Tuma SMS Sasa') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditSchoolModal(school) {
        document.getElementById('editSchoolModalTitle').textContent = '✏️ Edit School: ' + school.school_name;
        document.getElementById('editSchoolForm').action = '/admin/schools/' + school.id;
        document.getElementById('edit_school_name').value = school.school_name || '';
        document.getElementById('edit_school_address').value = school.address || '';
        document.getElementById('edit_school_phone').value = school.phone || '';
        document.getElementById('editSchoolModal').style.display = 'flex';
    }

    function closeEditSchoolModal() {
        document.getElementById('editSchoolModal').style.display = 'none';
    }

    function openAdminBulkSmsModal() {
        const modal = document.getElementById('adminBulkSmsModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeAdminBulkSmsModal() {
        const modal = document.getElementById('adminBulkSmsModal');
        if (modal) modal.style.display = 'none';
    }

    function handleAdminBulkSubmit() {
        const btn = document.getElementById('btnAdminBulkSubmit');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span>⏳</span> <span>Inatuma SMS... Tafadhali subiri</span>';
        }
    }

    window.addEventListener('click', function(event) {
        const editModal = document.getElementById('editSchoolModal');
        const smsModal = document.getElementById('adminBulkSmsModal');
        if (event.target === editModal) {
            closeEditSchoolModal();
        }
        if (event.target === smsModal) {
            closeAdminBulkSmsModal();
        }
    });
</script>
@endsection
