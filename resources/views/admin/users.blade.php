@extends('layouts.app')

@section('title', 'Manage Users | Smart-Results')

@section('content')
<div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800;">{{ __('User Management') }}</h1>
        <p style="color: var(--text-muted); font-size: 14px;">{{ __('Create, allocate roles, and manage institutional staff & parents') }}</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('newUserCard').scrollIntoView({ behavior: 'smooth' })">
        + {{ __('Create New Account') }}
    </button>
</div>

<!-- Filters -->
<div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
    <form method="GET" action="{{ route('admin.users') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
        <div style="min-width: 180px;">
            <label style="margin-bottom: 4px;">{{ __('Role') }}:</label>
            <select name="role" onchange="this.form.submit()">
                <option value="">-- {{ __('All Roles') }} --</option>
                @foreach($roles as $r)
                    <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ __($r) }}</option>
                @endforeach
            </select>
        </div>

        <div style="min-width: 220px;">
            <label style="margin-bottom: 4px;">{{ __('School') }}:</label>
            <select name="school" onchange="this.form.submit()">
                <option value="">-- {{ __('All Schools') }} --</option>
                @foreach($schools as $sch)
                    <option value="{{ $sch->school_name }}" {{ request('school') === $sch->school_name ? 'selected' : '' }}>{{ $sch->school_name }}</option>
                @endforeach
            </select>
        </div>

        <div style="align-self: flex-end;">
            <a href="{{ route('admin.users') }}" class="btn btn-outline" style="padding: 10px 14px;">{{ __('Filter') }}</a>
        </div>
    </form>
</div>

@if(session('success'))
    <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
        {{ session('error') }}
    </div>
@endif

<!-- Users Table -->
<div class="table-responsive" style="margin-bottom: 35px;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Role</th>
                <th>Assigned Subjects & Classes</th>
                <th>Associated School</th>
                <th>Created</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>#{{ $user->id }}</td>
                    <td><b>{{ $user->username }}</b></td>
                    <td>{{ $user->name ?? '-' }}</td>
                    <td>
                        <span style="background: #e0e7ff; color: #3730a3; padding: 3px 10px; border-radius: 9999px; font-weight: 600; font-size: 12px;">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td>
                        @if($user->role === 'Teacher')
                            @if($user->teacherAssignments && $user->teacherAssignments->isNotEmpty())
                                <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 280px;">
                                    @foreach($user->teacherAssignments as $asg)
                                        <span style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                            {{ $asg->subject ? $asg->subject->subject_name : 'Subject' }} ({{ $asg->class_name }})
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span style="color: #ef4444; font-size: 11px; font-weight: bold; background: #fef2f2; padding: 2px 6px; border-radius: 4px; border: 1px solid #fecaca;">
                                    ⚠️ No subject/class assigned
                                </span>
                            @endif
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td>{{ $user->school_name ?: 'Global / Any' }}</td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                            <button type="button" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px;" onclick="openEditUserModal({{ json_encode([
                                'id' => $user->id,
                                'username' => $user->username,
                                'name' => $user->name,
                                'role' => $user->role,
                                'school_name' => $user->school_name,
                                'assignments' => $user->teacherAssignments->map(fn($a) => ['class_name' => $a->class_name, 'subject_id' => $a->subject_id])
                            ]) }})">
                                ✏️ Edit
                            </button>

                            @if(auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" onsubmit="return confirm('Delete user {{ $user->username }}? This cannot be undone.')" style="display:inline; margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 4px 10px; font-size: 12px;">Delete</button>
                                </form>
                            @else
                                <span style="font-size: 11px; color: var(--text-muted); font-style: italic;">(Current)</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 25px; color: var(--text-muted);">No users found matching current filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-bottom: 30px;">
    {{ $users->withQueryString()->links() }}
</div>

<!-- Edit User Modal -->
<div id="editUserModal" style="display:none; position:fixed; z-index:9999; inset:0; background:rgba(0,0,0,0.55); align-items:center; justify-content:center; padding:15px;">
    <div style="background:white; border-radius:12px; width:100%; max-width:680px; max-height:90vh; overflow-y:auto; padding:25px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:18px;">
            <h3 style="margin:0; font-size:18px; font-weight:bold; color:#0f172a;" id="editModalTitle">✏️ Edit User Account</h3>
            <button type="button" onclick="closeEditUserModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#64748b; line-height:1;">&times;</button>
        </div>

        <form id="editUserForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group">
                    <label for="edit_username">Username (Login ID)</label>
                    <input type="text" name="username" id="edit_username" required>
                </div>

                <div class="form-group">
                    <label for="edit_name">Full Name</label>
                    <input type="text" name="name" id="edit_name">
                </div>

                <div class="form-group">
                    <label for="edit_role">User Role</label>
                    <select name="role" id="edit_role" required onchange="toggleEditTeacherSection()">
                        @foreach($roles as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_school_name">Assigned School</label>
                    <select name="school_name" id="edit_school_name">
                        <option value="">-- Universal / No Specific School --</option>
                        @foreach($schools as $sch)
                            <option value="{{ $sch->school_name }}">{{ $sch->school_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label for="edit_password">New Password <span style="font-weight:normal; color:#64748b;">(acha wazi kubakiza iliyopo)</span></label>
                    <input type="password" name="password" id="edit_password" placeholder="Weka nenosiri jipya (au acha wazi)">
                </div>
            </div>

            <!-- Teacher Teaching Allocations Section (Dynamic Rows) -->
            <div id="editTeacherSection" style="margin-top:16px; border-top:1px solid #e2e8f0; padding-top:16px; display:none;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <div>
                        <label style="margin:0; font-size:14px; font-weight:bold; color:#0f172a;">📚 Masomo & Madarasa Anayofundisha (Teaching Allocation)</label>
                        <p style="margin:2px 0 0 0; font-size:12px; color:#64748b;">Mwalimu huyu ataweza tu kuingiza na kuona matokeo ya masomo na madarasa yaliyopangwa hapa chini.</p>
                    </div>
                    <button type="button" class="btn btn-outline" style="padding:4px 10px; font-size:12px;" onclick="addEditAssignmentRow()">
                        + Ongeza Somo & Darasa
                    </button>
                </div>

                <div id="editAssignmentsContainer" style="display:flex; flex-direction:column; gap:8px;">
                    <!-- dynamic rows inserted by JS -->
                </div>
            </div>

            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; padding-top:14px;">
                <button type="button" class="btn btn-outline" onclick="closeEditUserModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- New User Form Card -->
<div id="newUserCard" style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 25px; max-width: 750px;">
    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 15px;">➕ Register New User Account</h3>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="username">Username (Login ID)</label>
                <input type="text" name="username" id="username" placeholder="e.g. teacher_juma" required>
            </div>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" placeholder="e.g. Juma Ali">
            </div>

            <div class="form-group">
                <label for="role">User Role</label>
                <select name="role" id="role" required onchange="toggleCreateTeacherSection()">
                    <option value="Teacher">Teacher (Academic Desk)</option>
                    <option value="Parent">Parent (Report Viewer)</option>
                    <option value="Headmaster">Headmaster (School Leadership)</option>
                    <option value="Headmistress">Headmistress (School Leadership)</option>
                    <option value="Academic Master">Academic Master</option>
                    <option value="Accountant">Accountant (Fee Desk)</option>
                    <option value="Admin">System Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="school_name">Assigned School</label>
                <select name="school_name" id="school_name">
                    <option value="">-- Universal / No Specific School --</option>
                    @foreach($schools as $sch)
                        <option value="{{ $sch->school_name }}">{{ $sch->school_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label for="password">Password (minimum 6 chars)</label>
                <input type="password" name="password" id="password" placeholder="Enter secure password" required>
            </div>
        </div>

        <!-- Teacher Assignment Section for New User -->
        <div id="createTeacherSection" style="margin-top: 16px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <div>
                    <label style="margin:0; font-size:14px; font-weight:bold; color:#0f172a;">📚 Masomo & Madarasa Anayofundisha (Teaching Allocation)</label>
                    <p style="margin:2px 0 0 0; font-size:12px; color:#64748b;">Mwalimu ataweza tu kuingiza na kuona matokeo ya masomo na madarasa yaliyopangwa hapa.</p>
                </div>
                <button type="button" class="btn btn-outline" style="padding:4px 10px; font-size:12px;" onclick="addCreateAssignmentRow()">
                    + Ongeza Somo & Darasa
                </button>
            </div>

            <div id="createAssignmentsContainer" style="display:flex; flex-direction:column; gap:8px;">
                <!-- dynamic rows inserted by JS -->
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Create User Account</button>
    </form>
</div>

<script>
    const availableClasses = @json($allClasses);
    const availableSubjects = @json($allSubjects);

    let editRowCount = 0;
    let createRowCount = 0;

    function buildAssignmentRowHtml(prefix, index, selectedClass = '', selectedSubjectId = '') {
        let classOptions = '<option value="">-- Select Class --</option>';
        availableClasses.forEach(cls => {
            const sel = cls === selectedClass ? 'selected' : '';
            classOptions += `<option value="${cls}" ${sel}>${cls}</option>`;
        });

        let subjectOptions = '<option value="">-- Select Subject --</option>';
        availableSubjects.forEach(sub => {
            const sel = String(sub.id) === String(selectedSubjectId) ? 'selected' : '';
            subjectOptions += `<option value="${sub.id}" ${sel}>${sub.subject_name}</option>`;
        });

        return `
            <div style="display:flex; gap:10px; align-items:center; background:#f8fafc; padding:8px 10px; border-radius:6px; border:1px solid #e2e8f0;">
                <div style="flex:1;">
                    <select name="assignments[${index}][class_name]" required style="margin:0; font-size:13px;">
                        ${classOptions}
                    </select>
                </div>
                <div style="flex:1;">
                    <select name="assignments[${index}][subject_id]" required style="margin:0; font-size:13px;">
                        ${subjectOptions}
                    </select>
                </div>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:#ef4444; font-size:16px; cursor:pointer; padding:4px;" title="Remove">
                    🗑️
                </button>
            </div>
        `;
    }

    // Modal Edit functions
    function openEditUserModal(user) {
        document.getElementById('editModalTitle').textContent = '✏️ Edit User: ' + user.username;
        document.getElementById('editUserForm').action = '/admin/users/' + user.id;

        document.getElementById('edit_username').value = user.username || '';
        document.getElementById('edit_name').value = user.name || '';
        document.getElementById('edit_role').value = user.role || 'Teacher';
        document.getElementById('edit_school_name').value = user.school_name || '';
        document.getElementById('edit_password').value = '';

        const container = document.getElementById('editAssignmentsContainer');
        container.innerHTML = '';
        editRowCount = 0;

        if (user.role === 'Teacher') {
            document.getElementById('editTeacherSection').style.display = 'block';
            if (user.assignments && user.assignments.length > 0) {
                user.assignments.forEach(asg => {
                    container.insertAdjacentHTML('beforeend', buildAssignmentRowHtml('edit', editRowCount++, asg.class_name, asg.subject_id));
                });
            } else {
                addEditAssignmentRow();
            }
        } else {
            document.getElementById('editTeacherSection').style.display = 'none';
        }

        document.getElementById('editUserModal').style.display = 'flex';
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').style.display = 'none';
    }

    function toggleEditTeacherSection() {
        const role = document.getElementById('edit_role').value;
        const section = document.getElementById('editTeacherSection');
        if (role === 'Teacher') {
            section.style.display = 'block';
            if (document.getElementById('editAssignmentsContainer').children.length === 0) {
                addEditAssignmentRow();
            }
        } else {
            section.style.display = 'none';
        }
    }

    function addEditAssignmentRow() {
        const container = document.getElementById('editAssignmentsContainer');
        container.insertAdjacentHTML('beforeend', buildAssignmentRowHtml('edit', editRowCount++));
    }

    // Create user functions
    function toggleCreateTeacherSection() {
        const role = document.getElementById('role').value;
        const section = document.getElementById('createTeacherSection');
        if (role === 'Teacher') {
            section.style.display = 'block';
            if (document.getElementById('createAssignmentsContainer').children.length === 0) {
                addCreateAssignmentRow();
            }
        } else {
            section.style.display = 'none';
        }
    }

    function addCreateAssignmentRow() {
        const container = document.getElementById('createAssignmentsContainer');
        container.insertAdjacentHTML('beforeend', buildAssignmentRowHtml('create', createRowCount++));
    }

    // Init create form teacher row if role is Teacher
    document.addEventListener('DOMContentLoaded', function() {
        toggleCreateTeacherSection();
    });

    window.onclick = function(event) {
        const modal = document.getElementById('editUserModal');
        if (event.target === modal) {
            closeEditUserModal();
        }
    };
</script>
@endsection
