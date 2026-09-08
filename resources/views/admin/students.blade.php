@extends('layouts.app')

@section('title', 'Manage Students | Smart-Results')

@section('content')
<div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800;">Student Directory & Enrollment</h1>
        <p style="color: var(--text-muted); font-size: 14px;">Register students, link parents, and import bulk CSV admission rosters</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.students.template') }}" class="btn btn-outline">📥 Download CSV Template</a>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('enrollSection').scrollIntoView({ behavior: 'smooth' })">+ Enroll Student</button>
    </div>
</div>

<!-- Search & Filter Bar -->
<div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
    <form method="GET" action="{{ route('admin.students') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
        <div style="flex: 1; min-width: 200px;">
            <label style="margin-bottom: 4px;">Search Student / Reg #:</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or reg #...">
        </div>

        <div style="min-width: 160px;">
            <label style="margin-bottom: 4px;">Class:</label>
            <select name="class" onchange="this.form.submit()">
                <option value="">-- All Classes --</option>
                @foreach(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6', 'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'] as $cls)
                    <option value="{{ $cls }}" {{ request('class') === $cls ? 'selected' : '' }}>{{ $cls }}</option>
                @endforeach
            </select>
        </div>

        <div style="min-width: 200px;">
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
                <th>Sex</th>
                <th>School</th>
                <th>Parent Guardian</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $stud)
                <tr>
                    <td><code>{{ $stud->reg_number }}</code></td>
                    <td><b>{{ $stud->student_name }}</b></td>
                    <td>{{ $stud->class_name }}</td>
                    <td><span style="font-weight: 700; color: {{ $stud->sex === 'M' ? '#2563eb' : '#ec4899' }};">{{ $stud->sex }}</span></td>
                    <td>{{ $stud->school_name }}</td>
                    <td>{{ $stud->parent ? $stud->parent->username : '— Not Linked —' }}</td>
                    <td style="text-align: right;">
                        <form method="POST" action="{{ route('admin.students.delete', $stud->id) }}" onsubmit="return confirm('Delete student record for {{ $stud->student_name }}?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 4px 10px; font-size: 12px;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 25px; color: var(--text-muted);">No student records found.</td>
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
                    <input type="text" name="reg_number" id="reg_number" placeholder="e.g. STD-2026-009" required>
                </div>

                <div class="form-group">
                    <label for="student_name">Full Student Name</label>
                    <input type="text" name="student_name" id="student_name" placeholder="e.g. Kelvin Michael" required>
                </div>

                <div class="form-group">
                    <label for="class_name">Class / Grade</label>
                    <select name="class_name" id="class_name" required>
                        @foreach(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6', 'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'] as $cls)
                            <option value="{{ $cls }}">{{ $cls }}</option>
                        @endforeach
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
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Enroll Student</button>
        </form>
    </div>

    <!-- Bulk CSV Ingestion -->
    <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 25px;">
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 15px;">📁 Bulk CSV Ingestion</h3>
        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 15px;">
            Upload entire classroom cohorts at once via CSV spreadsheet format.
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

        <div style="margin-top: 20px; font-size: 12px; color: var(--text-muted); background: #f8fafc; padding: 12px; border-radius: 8px;">
            Format required: <code>reg_number, student_name, class_name, sex, parent_username</code>
        </div>
    </div>
</div>
@endsection
