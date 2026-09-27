@extends('layouts.app')

@section('title', 'Manage Students | Smart-Results')

@section('content')
<div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800;">Student Directory & Enrollment</h1>
        <p style="color: var(--text-muted); font-size: 14px;">Register students, link parents, and import bulk CSV admission rosters</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.students.template') }}" download="students_template.csv" class="btn btn-outline">📥 Download CSV Template</a>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('enrollSection').scrollIntoView({ behavior: 'smooth' })">+ Enroll Student</button>
    </div>
</div>

<!-- Search & Filter Bar -->
<div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
    <form method="GET" action="{{ route('admin.students') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
        <div style="flex: 1; min-width: 200px;">
            <label style="margin-bottom: 4px;">Search Student / Reg #:</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, reg #, or combination...">
        </div>

        <div style="min-width: 150px;">
            <label style="margin-bottom: 4px;">Class:</label>
            <select name="class" onchange="this.form.submit()">
                <option value="">-- All Classes --</option>
                @foreach(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6', 'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'] as $cls)
                    <option value="{{ $cls }}" {{ request('class') === $cls ? 'selected' : '' }}>{{ $cls }}</option>
                @endforeach
            </select>
        </div>

        <div style="min-width: 170px;">
            <label style="margin-bottom: 4px;">Mchepuo (Combination):</label>
            <select name="combination" onchange="this.form.submit()">
                <option value="">-- All Combinations --</option>
                @foreach($combinations as $cKey => $cDesc)
                    <option value="{{ $cKey }}" {{ request('combination') === $cKey ? 'selected' : '' }}>
                        {{ $cKey }} ({{ explode(',', $cDesc)[0] }}...)
                    </option>
                @endforeach
            </select>
        </div>

        <div style="min-width: 180px;">
            <label style="margin-bottom: 4px;">School:</label>
            <select name="school" onchange="this.form.submit()">
                <option value="">-- All Schools --</option>
                @foreach($schools as $sch)
                    <option value="{{ $sch->school_name }}" {{ request('school') === $sch->school_name ? 'selected' : '' }}>{{ $sch->school_name }}</option>
                @endforeach
            </select>
        </div>

        <div style="align-self: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 16px;">Search</button>
            <a href="{{ route('admin.students') }}" class="btn btn-outline" style="padding: 10px 14px;">Reset</a>
        </div>
    </form>
</div>

<!-- Students Table -->
<div class="table-responsive" style="margin-bottom: 30px;">
    <table>
        <thead>
            <tr>
                <th>Reg Number</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Mchepuo (Combination)</th>
                <th>Sex</th>
                <th>School</th>
                <th>Parent Guardian</th>
                <th>Parent Phone (SMS)</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $stud)
                <tr>
                    <td><code>{{ $stud->reg_number }}</code></td>
                    <td><b>{{ $stud->student_name }}</b></td>
                    <td>
                        <span style="font-weight: 600;">{{ $stud->class_name }}</span>
                    </td>
                    <td>
                        @if($stud->effective_combination)
                            <span style="display: inline-block; background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 4px; font-weight: 800; font-size: 11px; border: 1px solid #c7d2fe; letter-spacing: 0.5px;" title="{{ $combinations[$stud->effective_combination] ?? $stud->effective_combination }}">
                                🎓 {{ $stud->effective_combination }}
                            </span>
                        @elseif($stud->isALevel())
                            <span style="color: #ea580c; font-size: 11px; font-weight: bold; background: #fff7ed; padding: 1px 6px; border-radius: 4px; border: 1px dashed #fdba74;">
                                ⚠️ Bila Mchepuo
                            </span>
                        @else
                            <span style="color: var(--text-muted); font-size: 12px;">—</span>
                        @endif
                    </td>
                    <td><span style="font-weight: 700; color: {{ $stud->sex === 'M' ? '#2563eb' : '#ec4899' }};">{{ $stud->sex }}</span></td>
                    <td>{{ $stud->school_name }}</td>
                    <td>
                        @if($stud->parent)
                            <span style="font-weight: 600; color: #1e293b;">{{ $stud->parent->name ?: $stud->parent->username }}</span>
                            @if($stud->parent->name && $stud->parent->username && $stud->parent->name !== $stud->parent->username)
                                <small style="color: var(--text-muted); display: block; font-size: 11px;">User: {{ $stud->parent->username }}</small>
                            @endif
                        @else
                            <span style="color: var(--text-muted); font-size: 12px;">— Not Linked —</span>
                        @endif
                    </td>
                    <td>
                        @if($stud->effective_parent_phone)
                            <span style="font-weight: 700; color: #059669; font-size: 13px;">📞 {{ $stud->effective_parent_phone }}</span>
                        @else
                            <span style="color: var(--text-muted); font-size: 12px;">— None —</span>
                        @endif
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <button type="button" class="btn btn-outline" style="padding: 4px 8px; font-size: 12px; margin-right: 4px;" onclick='openEditStudentModal(@json($stud))'>✏️ Edit</button>
                        <form method="POST" action="{{ route('admin.students.delete', $stud->id) }}" onsubmit="return confirm('Delete student record for {{ $stud->student_name }}?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 4px 10px; font-size: 12px;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 25px; color: var(--text-muted);">No student records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-bottom: 35px;">
    {{ $students->withQueryString()->links() }}
</div>

<div id="enrollSection" style="display: grid; grid-template-columns: 3fr 2fr; gap: 24px;">
    <!-- Single Student Registration -->
    <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 25px;">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 15px;">➕ Single Student Enrollment</h3>
        <form method="POST" action="{{ route('admin.students.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label for="reg_number">Registration Number</label>
                    <input type="text" name="reg_number" id="reg_number" placeholder="e.g. S0101/0101" required>
                </div>

                <div class="form-group">
                    <label for="student_name">Full Student Name</label>
                    <input type="text" name="student_name" id="student_name" placeholder="e.g. Kelvin Michael" required>
                </div>

                <div class="form-group">
                    <label for="class_name">Class / Grade</label>
                    <select name="class_name" id="class_name" required onchange="handleClassChange(this.value, 'enroll_combination', 'enroll_comb_badge')">
                        @foreach(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6', 'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'] as $cls)
                            <option value="{{ $cls }}">{{ $cls }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="enroll_combination_group">
                    <label for="enroll_combination">
                        Mchepuo (Combination)
                        <span id="enroll_comb_badge" style="display: none; font-size: 10px; background: #e0e7ff; color: #4338ca; padding: 2px 6px; border-radius: 4px; font-weight: 700; margin-left: 4px;">Advance</span>
                    </label>
                    <select name="combination" id="enroll_combination">
                        <option value="">-- Chagua Mchepuo (Advance Tu) --</option>
                        <optgroup label="Sanaa / Jamii (Arts & Social Sciences)">
                            <option value="HKL">HKL — History, Kiswahili, English Language</option>
                            <option value="HGK">HGK — History, Geography, Kiswahili</option>
                            <option value="HGL">HGL — History, Geography, English Language</option>
                            <option value="HGE">HGE — History, Geography, Economics</option>
                            <option value="KLF">KLF — Kiswahili, English Language, French</option>
                            <option value="KEC">KEC — Kiswahili, Economics, Commerce</option>
                        </optgroup>
                        <optgroup label="Sayansi (Science & Mathematics)">
                            <option value="PCB">PCB — Physics, Chemistry, Biology</option>
                            <option value="PCM">PCM — Physics, Chemistry, Advanced Mathematics</option>
                            <option value="PGM">PGM — Physics, Geography, Advanced Mathematics</option>
                            <option value="CBG">CBG — Chemistry, Biology, Geography</option>
                            <option value="CBA">CBA — Chemistry, Biology, Agriculture</option>
                            <option value="CBN">CBN — Chemistry, Biology, Nutrition</option>
                            <option value="PMC">PMC — Physics, Mathematics, Computer Science</option>
                        </optgroup>
                        <optgroup label="Biashara (Commercial)">
                            <option value="EGM">EGM — Economics, Geography, Advanced Mathematics</option>
                            <option value="ECA">ECA — Economics, Commerce, Accountancy</option>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sex">Gender</label>
                    <select name="sex" id="sex" required>
                        <option value="M">Male (M)</option>
                        <option value="F">Female (F)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="school_name">Institution / School</label>
                    <select name="school_name" id="school_name" required>
                        @foreach($schools as $sch)
                            <option value="{{ $sch->school_name }}">{{ $sch->school_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="parent_id">Link Parent Account</label>
                    <select name="parent_id" id="parent_id">
                        <option value="">-- No Parent Linked Yet --</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}">{{ $p->username }} ({{ $p->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="parent_phone">Parent Phone Number (SMS)</label>
                    <input type="text" name="parent_phone" id="parent_phone" placeholder="e.g. 0712345678 or +255...">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Enroll Student</button>
        </form>
    </div>

    <!-- Bulk CSV Ingestion -->
    <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 25px;">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 15px;">📁 Bulk CSV Ingestion</h3>
        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 15px;">
            Upload entire classroom cohorts at once via CSV spreadsheet format. Inawezesha kuweka michepuo (combinations) ya Advance moja kwa moja!
        </p>

        <form method="POST" action="{{ route('admin.students.upload_csv') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="csv_school_name">Select Target School</label>
                <select name="school_name" id="csv_school_name" required>
                    @foreach($schools as $sch)
                        <option value="{{ $sch->school_name }}">{{ $sch->school_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="csv_file">Select CSV File</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv, .txt" required style="padding: 8px;">
            </div>

            <button type="submit" class="btn btn-outline" style="width: 100%; border-color: var(--primary); color: var(--primary); font-weight: 700;">
                ⚡ Parse & Upload CSV
            </button>
        </form>

        <div style="margin-top: 20px; font-size: 12px; color: var(--text-muted); background: #f8fafc; padding: 12px; border-radius: 8px; line-height: 1.6;">
            <b>Mpangilio unaokubalika wa CSV:</b><br>
            <code>reg_number, student_name, class_name, combination, sex, parent_username, parent_phone</code><br>
            <span style="color: #4338ca; font-weight: 600;">Mfano Advance:</span> <code>S0101/0101, Bakari Ali, Form 5, HKL, M, parent1, 0755123456</code><br>
            <span style="color: #64748b;">Mfano O-Level:</span> <code>S0101/0001, Kelvin Michael, Form 1,, M, parent2, 0712345678</code>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 580px; padding: 25px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0; color: #1e293b;">✏️ Sasisha Taarifa za Mwanafunzi</h3>
            <button type="button" onclick="closeEditStudentModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
        </div>

        <form id="editStudentForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label for="edit_reg_number">Registration Number</label>
                    <input type="text" name="reg_number" id="edit_reg_number" required>
                </div>

                <div class="form-group">
                    <label for="edit_student_name">Full Student Name</label>
                    <input type="text" name="student_name" id="edit_student_name" required>
                </div>

                <div class="form-group">
                    <label for="edit_class_name">Class / Grade</label>
                    <select name="class_name" id="edit_class_name" required onchange="handleClassChange(this.value, 'edit_combination', 'edit_comb_badge')">
                        @foreach(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6', 'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'] as $cls)
                            <option value="{{ $cls }}">{{ $cls }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_combination">
                        Mchepuo (Combination)
                        <span id="edit_comb_badge" style="display: none; font-size: 10px; background: #e0e7ff; color: #4338ca; padding: 2px 6px; border-radius: 4px; font-weight: 700; margin-left: 4px;">Advance</span>
                    </label>
                    <select name="combination" id="edit_combination">
                        <option value="">-- Hakuna / Chagua Mchepuo --</option>
                        <optgroup label="Sanaa / Jamii (Arts & Social Sciences)">
                            <option value="HKL">HKL — History, Kiswahili, English Language</option>
                            <option value="HGK">HGK — History, Geography, Kiswahili</option>
                            <option value="HGL">HGL — History, Geography, English Language</option>
                            <option value="HGE">HGE — History, Geography, Economics</option>
                            <option value="KLF">KLF — Kiswahili, English Language, French</option>
                            <option value="KEC">KEC — Kiswahili, Economics, Commerce</option>
                        </optgroup>
                        <optgroup label="Sayansi (Science & Mathematics)">
                            <option value="PCB">PCB — Physics, Chemistry, Biology</option>
                            <option value="PCM">PCM — Physics, Chemistry, Advanced Mathematics</option>
                            <option value="PGM">PGM — Physics, Geography, Advanced Mathematics</option>
                            <option value="CBG">CBG — Chemistry, Biology, Geography</option>
                            <option value="CBA">CBA — Chemistry, Biology, Agriculture</option>
                            <option value="CBN">CBN — Chemistry, Biology, Nutrition</option>
                            <option value="PMC">PMC — Physics, Mathematics, Computer Science</option>
                        </optgroup>
                        <optgroup label="Biashara (Commercial)">
                            <option value="EGM">EGM — Economics, Geography, Advanced Mathematics</option>
                            <option value="ECA">ECA — Economics, Commerce, Accountancy</option>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_sex">Gender</label>
                    <select name="sex" id="edit_sex" required>
                        <option value="M">Male (M)</option>
                        <option value="F">Female (F)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_school_name">School</label>
                    <select name="school_name" id="edit_school_name" required>
                        @foreach($schools as $sch)
                            <option value="{{ $sch->school_name }}">{{ $sch->school_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_parent_id">Link Parent</label>
                    <select name="parent_id" id="edit_parent_id">
                        <option value="">-- No Parent Linked --</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}">{{ $p->username }} ({{ $p->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_parent_phone">Parent Phone (SMS)</label>
                    <input type="text" name="parent_phone" id="edit_parent_phone" placeholder="07XXXXXXXX">
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeEditStudentModal()" class="btn btn-outline">Sitisha</button>
                <button type="submit" class="btn btn-primary">Hifadhi Mabadiliko</button>
            </div>
        </form>
    </div>
</div>

<script>
function handleClassChange(className, combinationSelectId, badgeId) {
    const isAdvance = /form\s*5|form\s*6|f5|f6|advance|kidato\s*cha\s*5|kidato\s*cha\s*6/i.test(className);
    const combSelect = document.getElementById(combinationSelectId);
    const badge = document.getElementById(badgeId);

    if (badge) {
        badge.style.display = isAdvance ? 'inline-block' : 'none';
    }

    if (combSelect) {
        if (isAdvance) {
            combSelect.style.borderColor = '#6366f1';
            combSelect.style.backgroundColor = '#f5f3ff';
        } else {
            combSelect.style.borderColor = '';
            combSelect.style.backgroundColor = '';
        }
    }
}

function openEditStudentModal(student) {
    const form = document.getElementById('editStudentForm');
    form.action = "{{ url('admin/students') }}/" + student.id;

    document.getElementById('edit_reg_number').value = student.reg_number || '';
    document.getElementById('edit_student_name').value = student.student_name || '';
    document.getElementById('edit_class_name').value = student.class_name || '';
    document.getElementById('edit_sex').value = student.sex || 'M';
    document.getElementById('edit_school_name').value = student.school_name || '';
    document.getElementById('edit_parent_id').value = student.parent_id || '';
    document.getElementById('edit_parent_phone').value = student.parent_phone || (student.parent ? student.parent.phone : '') || '';

    // Effective combination
    const comb = student.combination || student.effective_combination || '';
    document.getElementById('edit_combination').value = comb;

    handleClassChange(student.class_name, 'edit_combination', 'edit_comb_badge');

    document.getElementById('editStudentModal').style.display = 'flex';
}

function closeEditStudentModal() {
    document.getElementById('editStudentModal').style.display = 'none';
}

// Initial check on page load for enroll class
document.addEventListener('DOMContentLoaded', function() {
    const enrollClassSelect = document.getElementById('class_name');
    if (enrollClassSelect) {
        handleClassChange(enrollClassSelect.value, 'enroll_combination', 'enroll_comb_badge');
    }
});
</script>
@endsection
