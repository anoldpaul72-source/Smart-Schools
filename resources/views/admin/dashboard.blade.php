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
            📊 {{ __('View Student Results') }}
        </a>
        <button type="button" onclick="openAdminBulkSmsModal()" class="btn" style="background: #059669; color: white; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; cursor: pointer; border: none;">
            📱 {{ __('Send SMS to Parents') }}
        </button>
        <button type="button" onclick="openExportBackupModal()" class="btn" style="background: #4f46e5; color: white; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; cursor: pointer; border: none; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);">
            💾 {{ __('Export Backup Data') }}
        </button>
        <button type="button" onclick="openAdminDeleteMarksModal()" class="btn" style="background: #dc2626; color: white; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; cursor: pointer; border: none; box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);">
            🗑️ {{ __('Delete Results') }}
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
            <h4>{{ __('Student Results') }}</h4>
            <div class="stat-number">{{ number_format($marksCount) }}</div>
            <div style="display: flex; gap: 10px; align-items: center; margin-top: 4px;">
                <a href="{{ route('leader.dashboard') }}" style="font-size: 11.5px; color: #0284c7; font-weight: 700; text-decoration: none;">{{ __('Open Broadsheet') }} &rarr;</a>
                <span style="color: #cbd5e1;">&bull;</span>
                <a href="javascript:void(0)" onclick="openAdminDeleteMarksModal()" style="font-size: 11.5px; color: #dc2626; font-weight: 700; text-decoration: none;">🗑️ {{ __('Delete Marks') }}</a>
            </div>
        </div>
    </div>

    <!-- SMS Broadcast Card -->
    <div class="stat-card">
        <div class="stat-icon" style="background: #ecfdf5; color: #059669;">📱</div>
        <div class="stat-info" style="flex: 1;">
            <h4>{{ __('SMS Messages') }}</h4>
            <div class="stat-number">{{ number_format($smsLogsCount) }}</div>
            <a href="javascript:void(0)" onclick="openAdminBulkSmsModal()" style="font-size: 11.5px; color: #059669; font-weight: 700; text-decoration: none;">{{ __('Send SMS Now') }} &rarr;</a>
        </div>
    </div>

    <!-- System Backup Card -->
    <div class="stat-card" style="border-left: 4px solid #4f46e5;">
        <div class="stat-icon" style="background: #eef2ff; color: #4f46e5;">💾</div>
        <div class="stat-info" style="flex: 1;">
            <h4>{{ __('System Backup') }}</h4>
            <div class="stat-number" style="color: #4f46e5;">{{ number_format($totalRecordsCount) }}</div>
            <a href="javascript:void(0)" onclick="openExportBackupModal()" style="font-size: 11.5px; color: #4f46e5; font-weight: 700; text-decoration: none;">{{ __('Download Backup Now') }} &rarr;</a>
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

<!-- System Backup & Data Export Section -->
<div class="panel-box" style="margin-top: 24px; border: 1.5px solid #c7d2fe; background: #ffffff; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 26px; border: 1px solid #e0e7ff;">
                💾
            </div>
            <div>
                <h3 style="margin: 0; font-size: 19px; font-weight: 800; color: #1e1b4b;">
                    {{ __('System Backup & Data Export') }}
                </h3>
                <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13.5px;">
                    {{ __('Download a complete copy of all institutional data (students, marks, users, fees, attendance, and settings) for disaster recovery and offline archives.') }}
                </p>
            </div>
        </div>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <span style="display: inline-flex; align-items: center; gap: 6px; background: #e0e7ff; color: #3730a3; padding: 6px 14px; border-radius: 9999px; font-weight: 800; font-size: 13px;">
                📊 {{ number_format($totalRecordsCount) }} {{ __('All Records Ready') }}
            </span>
            <button type="button" onclick="openExportBackupModal()" class="btn" style="background: #4f46e5; color: white; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; border: none; cursor: pointer; padding: 8px 16px;">
                <span>💾</span> <span>{{ __('Select Backup Type') }}</span>
            </button>
        </div>
    </div>

    <!-- Featured: All-in-One Complete Bundle Banner -->
    <div style="background: linear-gradient(135deg, #eef2ff 0%, #ede9fe 100%); border: 2px solid #818cf8; border-radius: 12px; padding: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="font-size: 34px;">🌟</div>
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <h4 style="margin: 0; font-size: 16.5px; font-weight: 800; color: #1e1b4b;">{{ __('Download All in One (Complete Bundle)') }}</h4>
                    <span style="background: #4f46e5; color: white; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 9999px;">{{ __('BEST CHOICE ⭐') }}</span>
                </div>
                <p style="margin: 4px 0 0 0; font-size: 13px; color: #3730a3;">
                    {{ __('Download everything in 1 click: ZIP archive containing All Excel CSVs + JSON System Restore + SQL Database Script.') }}
                </p>
            </div>
        </div>
        <a href="{{ route('admin.backup.export', ['format' => 'bundle']) }}" class="btn" style="background: #4f46e5; color: white; font-weight: 800; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);">
            <span>🚀</span> <span>{{ __('Download All (.ZIP)') }}</span>
        </a>
    </div>

    <!-- Export Format Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        <!-- Card 1: JSON Backup -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 24px;">📦</span>
                        <h4 style="margin: 0; font-size: 15.5px; font-weight: 800; color: #0f172a;">{{ __('Full System Backup (JSON)') }}</h4>
                    </div>
                    <span style="background: #e0e7ff; color: #4338ca; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 9999px;">{{ __('Recommended') }}</span>
                </div>
                <p style="font-size: 12.5px; color: #64748b; line-height: 1.5; margin: 0 0 16px 0;">
                    {{ __('Complete institutional database archive including all marks, students, users, and configurations. Best for disaster recovery and system restore.') }}
                </p>
            </div>
            <a href="{{ route('admin.backup.export', ['format' => 'json']) }}" class="btn" style="background: #4f46e5; color: white; text-decoration: none; font-weight: 700; font-size: 13px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border-radius: 8px;">
                <span>📥</span> <span>{{ __('Download Full Backup (JSON)') }}</span>
            </a>
        </div>

        <!-- Card 2: Excel / CSV Spreadsheets ZIP -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 24px;">📑</span>
                        <h4 style="margin: 0; font-size: 15.5px; font-weight: 800; color: #0f172a;">{{ __('Excel / CSV Spreadsheets (.ZIP)') }}</h4>
                    </div>
                    <span style="background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 9999px;">Excel</span>
                </div>
                <p style="font-size: 12.5px; color: #64748b; line-height: 1.5; margin: 0 0 16px 0;">
                    {{ __('ZIP package containing individual CSV spreadsheets for each table: students, marks, users, fee payments, attendance, and more.') }}
                </p>
            </div>
            <a href="{{ route('admin.backup.export', ['format' => 'csv_zip']) }}" class="btn" style="background: #059669; color: white; text-decoration: none; font-weight: 700; font-size: 13px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border-radius: 8px;">
                <span>📊</span> <span>{{ __('Download Spreadsheets (Excel ZIP)') }}</span>
            </a>
        </div>

        <!-- Card 3: SQL Dump Script -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 24px;">🗄️</span>
                        <h4 style="margin: 0; font-size: 15.5px; font-weight: 800; color: #0f172a;">{{ __('SQL Database Dump (.sql)') }}</h4>
                    </div>
                    <span style="background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 9999px;">Database</span>
                </div>
                <p style="font-size: 12.5px; color: #64748b; line-height: 1.5; margin: 0 0 16px 0;">
                    {{ __('Standard SQL script with INSERT queries ready to be imported directly into any PostgreSQL or MySQL database on another server.') }}
                </p>
            </div>
            <a href="{{ route('admin.backup.export', ['format' => 'sql']) }}" class="btn" style="background: #0284c7; color: white; text-decoration: none; font-weight: 700; font-size: 13px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; border-radius: 8px;">
                <span>💾</span> <span>{{ __('Download SQL Script (.sql)') }}</span>
            </a>
        </div>
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

<!-- Admin Bulk Delete Marks Modal -->
<div id="adminDeleteMarksModal" style="display:none; position:fixed; z-index:9999; inset:0; background:rgba(15, 23, 42, 0.65); align-items:center; justify-content:center; padding:15px;">
    <div style="background:white; border-radius:14px; width:100%; max-width:540px; padding:26px; box-shadow:0 25px 30px -5px rgba(0,0,0,0.25);">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:14px; margin-bottom:18px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <span style="font-size:26px;">🗑️</span>
                <div>
                    <h3 style="margin:0; font-size:18px; font-weight:800; color:#0f172a;">{{ __('Delete Student Results') }}</h3>
                    <p style="margin:2px 0 0 0; font-size:12.5px; color:#64748b;">{{ __('Futa alama za wanafunzi kwa kuchagua darasa na aina ya mtihani') }}</p>
                </div>
            </div>
            <button type="button" onclick="closeAdminDeleteMarksModal()" style="background:none; border:none; font-size:26px; cursor:pointer; color:#94a3b8; line-height:1;">&times;</button>
        </div>

        <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px; padding: 12px 14px; margin-bottom: 18px; display: flex; gap: 10px; align-items: flex-start;">
            <span style="font-size: 18px; line-height: 1;">⚠️</span>
            <div style="font-size: 12px; color: #991b1b; line-height: 1.45;">
                <strong style="display: block; margin-bottom: 2px;">{{ __('Tahadhari Muhimu:') }}</strong>
                {{ __('Kitendo hiki kitafuta alama za mtihani huu moja kwa moja kwenye kanzidata. Hakikisha umechagua darasa na aina ya mtihani kwa usahihi kabla ya kuthibitisha.') }}
            </div>
        </div>

        <form method="POST" action="{{ route('admin.marks.bulk_delete') }}" id="adminDeleteMarksForm" onsubmit="return confirmAdminDeleteMarks()">
            @csrf
            @method('DELETE')

            <div style="margin-bottom: 14px;">
                <label style="display:block; font-weight:bold; font-size:13px; margin-bottom:5px; color:#334155;">
                    {{ __('Class:') }} <span style="color:#dc2626;">*</span>
                </label>
                <select name="class_name" id="del_class_name" required onchange="checkMarksCountForDeletion()" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;">
                    <option value="">-- {{ __('Select Class') }} --</option>
                    @foreach($allClasses as $cls)
                        <option value="{{ $cls }}">{{ $cls }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display:block; font-weight:bold; font-size:13px; margin-bottom:5px; color:#334155;">
                    {{ __('Exam Assessment Type:') }} <span style="color:#dc2626;">*</span>
                </label>
                <select name="term" id="del_term" required onchange="checkMarksCountForDeletion()" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;">
                    <option value="">-- {{ __('Select Exam Type') }} --</option>
                    @foreach($allTerms as $t)
                        <option value="{{ $t }}">{{ __($t) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display:block; font-weight:bold; font-size:13px; margin-bottom:5px; color:#334155;">
                    {{ __('Subject:') }} <span style="color:#64748b; font-weight:normal; font-size:12px;">({{ __('Hiari / Optional') }})</span>
                </label>
                <select name="subject_id" id="del_subject_id" onchange="checkMarksCountForDeletion()" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;">
                    <option value="all">-- {{ __('All Subjects') }} --</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Live Status/Count Box -->
            <div id="delMarksCountBox" style="display:none; padding:10px 14px; border-radius:6px; font-size:12.5px; font-weight:600; margin-bottom:16px;"></div>

            <!-- Confirmation Checkbox -->
            <div style="margin-bottom: 20px; background:#fff1f2; border:1px solid #fecdd3; border-radius:8px; padding:10px 14px;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin:0;">
                    <input type="checkbox" id="delConfirmCheck" required style="width:17px; height:17px; accent-color:#dc2626; cursor:pointer;">
                    <span style="font-size:12.5px; font-weight:700; color:#be123c;">
                        {{ __('I confirm that I want to permanently delete these results.') }}
                    </span>
                </label>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; padding-top:16px;">
                <button type="button" class="btn btn-outline" onclick="closeAdminDeleteMarksModal()">{{ __('Cancel') }}</button>
                <button type="submit" id="btnAdminDeleteSubmit" class="btn" style="background:#dc2626; color:white; font-weight:bold; display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                    <span>🗑️</span>
                    <span>{{ __('Delete Marks Now') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Export Backup Modal -->
<div id="exportBackupModal" style="display:none; position:fixed; z-index:9999; inset:0; background:rgba(15, 23, 42, 0.65); align-items:center; justify-content:center; padding:15px;">
    <div style="background:white; border-radius:14px; width:100%; max-width:560px; padding:26px; box-shadow:0 25px 30px -5px rgba(0,0,0,0.25);">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:14px; margin-bottom:18px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <span style="font-size:28px;">💾</span>
                <div>
                    <h3 style="margin:0; font-size:18px; font-weight:800; color:#0f172a;">{{ __('Download System Data Backup') }}</h3>
                    <p style="margin:2px 0 0 0; font-size:12.5px; color:#64748b;">{{ __('Choose the format of the file you want to download') }}</p>
                </div>
            </div>
            <button type="button" onclick="closeExportBackupModal()" style="background:none; border:none; font-size:26px; cursor:pointer; color:#94a3b8; line-height:1;">&times;</button>
        </div>

        <div style="margin-bottom: 20px;">
            <div style="background: #eef2ff; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 13px; color: #3730a3; font-weight: 700;">{{ __('Total System Records:') }}</span>
                <span style="background: #4f46e5; color: white; font-weight: 800; font-size: 12.5px; padding: 3px 10px; border-radius: 9999px;">{{ number_format($totalRecordsCount) }}</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                <!-- ALL-IN-ONE BUNDLE (BEST CHOICE) -->
                <a href="{{ route('admin.backup.export', ['format' => 'bundle']) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 16px; border: 2px solid #6366f1; border-radius: 10px; text-decoration: none; background: #eef2ff; transition: background 0.15s, transform 0.15s; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.15);" onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 28px;">🌟</span>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="font-weight: 800; color: #1e1b4b; font-size: 14.5px;">{{ __('Download All in One (Complete Bundle)') }}</div>
                                <span style="background: #4f46e5; color: white; font-size: 10.5px; font-weight: 800; padding: 2px 7px; border-radius: 9999px;">{{ __('BEST CHOICE ⭐') }}</span>
                            </div>
                            <div style="font-size: 12px; color: #4338ca; margin-top: 2px;">{{ __('Includes: All Excel CSVs + JSON Backup + SQL Script in 1 ZIP file') }}</div>
                        </div>
                    </div>
                    <span style="background: #4f46e5; color: white; font-size: 12.5px; font-weight: 800; padding: 7px 14px; border-radius: 6px; white-space: nowrap; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);">{{ __('Download All (.zip)') }}</span>
                </a>

                <!-- JSON Option -->
                <a href="{{ route('admin.backup.export', ['format' => 'json']) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border: 1.5px solid #c7d2fe; border-radius: 10px; text-decoration: none; background: #ffffff; transition: background 0.15s, border-color 0.15s;" onmouseover="this.style.background='#f5f7ff'" onmouseout="this.style.background='#ffffff'">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 24px;">📦</span>
                        <div>
                            <div style="font-weight: 800; color: #1e1b4b; font-size: 14px;">{{ __('Full System Backup (JSON)') }}</div>
                            <div style="font-size: 12px; color: #64748b;">{{ __('Complete database backup of all tables (Restorable)') }}</div>
                        </div>
                    </div>
                    <span style="background: #4f46e5; color: white; font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 6px; white-space: nowrap;">{{ __('Download .json') }}</span>
                </a>

                <!-- CSV ZIP Option -->
                <a href="{{ route('admin.backup.export', ['format' => 'csv_zip']) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border: 1.5px solid #bbf7d0; border-radius: 10px; text-decoration: none; background: #ffffff; transition: background 0.15s, border-color 0.15s;" onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='#ffffff'">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 24px;">📑</span>
                        <div>
                            <div style="font-weight: 800; color: #064e3b; font-size: 14px;">{{ __('Excel / CSV Spreadsheets (.ZIP)') }}</div>
                            <div style="font-size: 12px; color: #64748b;">{{ __('Student lists, marks, users, and fee tables for Excel') }}</div>
                        </div>
                    </div>
                    <span style="background: #059669; color: white; font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 6px; white-space: nowrap;">{{ __('Download .zip') }}</span>
                </a>

                <!-- SQL Option -->
                <a href="{{ route('admin.backup.export', ['format' => 'sql']) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border: 1.5px solid #bae6fd; border-radius: 10px; text-decoration: none; background: #ffffff; transition: background 0.15s, border-color 0.15s;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='#ffffff'">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 24px;">🗄️</span>
                        <div>
                            <div style="font-weight: 800; color: #0c4a6e; font-size: 14px;">{{ __('SQL Database Script (.sql)') }}</div>
                            <div style="font-size: 12px; color: #64748b;">{{ __('SQL INSERT statements for PostgreSQL or MySQL database') }}</div>
                        </div>
                    </div>
                    <span style="background: #0284c7; color: white; font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 6px; white-space: nowrap;">{{ __('Download .sql') }}</span>
                </a>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; border-top:1px solid #e2e8f0; padding-top:14px;">
            <button type="button" class="btn btn-outline" onclick="closeExportBackupModal()">{{ __('Close') }}</button>
        </div>
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

    function openExportBackupModal() {
        const modal = document.getElementById('exportBackupModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeExportBackupModal() {
        const modal = document.getElementById('exportBackupModal');
        if (modal) modal.style.display = 'none';
    }

    function openAdminDeleteMarksModal() {
        const modal = document.getElementById('adminDeleteMarksModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeAdminDeleteMarksModal() {
        const modal = document.getElementById('adminDeleteMarksModal');
        if (modal) modal.style.display = 'none';
    }

    function checkMarksCountForDeletion() {
        const className = document.getElementById('del_class_name').value;
        const term = document.getElementById('del_term').value;
        const subjectId = document.getElementById('del_subject_id').value;
        const box = document.getElementById('delMarksCountBox');

        if (!className || !term) {
            box.style.display = 'none';
            return;
        }

        box.style.display = 'block';
        box.style.background = '#f1f5f9';
        box.style.border = '1px solid #cbd5e1';
        box.style.color = '#475569';
        box.innerHTML = '<span>⏳ Inatafuta idadi ya alama...</span>';

        const url = `{{ route('admin.marks.count_delete') }}?class_name=${encodeURIComponent(className)}&term=${encodeURIComponent(term)}&subject_id=${encodeURIComponent(subjectId)}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    box.style.background = '#fef2f2';
                    box.style.border = '1px solid #fca5a5';
                    box.style.color = '#991b1b';
                    box.innerHTML = `⚠️ Zimepatikana <strong>${data.count}</strong> rekodi za alama zitakazofutwa.`;
                } else {
                    box.style.background = '#f8fafc';
                    box.style.border = '1px solid #e2e8f0';
                    box.style.color = '#64748b';
                    box.innerHTML = `ℹ️ Hakuna alama zilizopatikana kwa vigezo hivi.`;
                }
            })
            .catch(() => {
                box.style.display = 'none';
            });
    }

    function confirmAdminDeleteMarks() {
        const className = document.getElementById('del_class_name').value;
        const term = document.getElementById('del_term').value;
        const check = document.getElementById('delConfirmCheck');

        if (!check || !check.checked) {
            alert("{{ __('Tafadhali weka tiki kwenye kisanduku cha uthibitisho kabla ya kuendelea.') }}");
            return false;
        }

        const msg = `Je, una uhakika unataka kufuta kabisa matokeo yote ya ${className} (${term})?\n\nTahadhari: Kitendo hiki hakiwezi kurudishwa!`;
        return confirm(msg);
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
        const exportModal = document.getElementById('exportBackupModal');
        const deleteMarksModal = document.getElementById('adminDeleteMarksModal');
        if (event.target === editModal) {
            closeEditSchoolModal();
        }
        if (event.target === smsModal) {
            closeAdminBulkSmsModal();
        }
        if (event.target === exportModal) {
            closeExportBackupModal();
        }
        if (event.target === deleteMarksModal) {
            closeAdminDeleteMarksModal();
        }
    });
</script>
@endsection
