<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\User;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Mark;
use App\Models\TeacherAssignment;
use App\Models\Timetable;

class AdminController extends Controller
{
    public function dashboard()
    {
        $schoolsCount  = School::count();
        $studentsCount = Student::count();
        $teachersCount = User::where('role', 'Teacher')->count();
        $parentsCount  = User::where('role', 'Parent')->count();
        $subjectsCount = Subject::count();

        $recentUsers = User::latest()->take(8)->get();
        $schools     = School::orderBy('school_name')->get();
        $subjects    = Subject::orderBy('subject_name')->get();

        return view('admin.dashboard', compact(
            'schoolsCount',
            'studentsCount',
            'teachersCount',
            'parentsCount',
            'subjectsCount',
            'recentUsers',
            'schools',
            'subjects'
        ));
    }

    // --- Schools ---
    public function addSchool(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|unique:schools,school_name',
            'address'     => 'nullable|string',
            'phone'       => 'nullable|string',
        ]);

        School::create([
            'school_name' => trim($request->school_name),
            'address'     => trim($request->address),
            'phone'       => trim($request->phone),
        ]);

        return back()->with('success', '✔️ School added successfully!');
    }

    public function updateSchool(Request $request, $id)
    {
        $school = School::findOrFail($id);

        $request->validate([
            'school_name' => 'required|string|unique:schools,school_name,' . $school->id,
            'address'     => 'nullable|string',
            'phone'       => 'nullable|string',
        ]);

        $oldName = $school->school_name;
        $newName = trim($request->school_name);

        $school->update([
            'school_name' => $newName,
            'address'     => trim($request->address),
            'phone'       => trim($request->phone),
        ]);

        // If school name was renamed, cascade update to users, students, etc.
        if ($oldName !== $newName) {
            User::where('school_name', $oldName)->update(['school_name' => $newName]);
            Student::where('school_name', $oldName)->update(['school_name' => $newName]);
            TeacherAssignment::where('school_name', $oldName)->update(['school_name' => $newName]);
            Timetable::where('school_name', $oldName)->update(['school_name' => $newName]);
        }

        return back()->with('success', "✔️ Taarifa za shule '{$newName}' zimesasishwa kikamilifu!");
    }

    public function deleteSchool($id)
    {
        $school = School::findOrFail($id);
        $name = $school->school_name;
        $school->delete();

        return back()->with('success', "✔️ Shule '{$name}' imefutwa kikamilifu!");
    }

    // --- Subjects ---
    public function addSubject(Request $request)
    {
        $request->validate([
            'subject_name' => 'required|string',
        ]);

        Subject::firstOrCreate(['subject_name' => trim($request->subject_name)]);

        return back()->with('success', '✔️ Subject added successfully!');
    }

    public function deleteSubject($id)
    {
        Subject::findOrFail($id)->delete();
        return back()->with('success', '✔️ Subject deleted successfully!');
    }

    // --- Users ---
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('school')) {
            $query->where('school_name', $request->school);
        }

        $users   = $query->with(['teacherAssignments.subject'])->latest()->paginate(15);
        $schools = School::orderBy('school_name')->get();
        $roles   = ['Admin', 'Teacher', 'Parent', 'Accountant', 'Headmaster', 'Academic Master'];
        $allSubjects = Subject::orderBy('subject_name')->get();
        $allClasses = [
            'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
        ];

        return view('admin.users', compact('users', 'schools', 'roles', 'allSubjects', 'allClasses'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'username'    => 'required|string|unique:users,username',
            'name'        => 'nullable|string',
            'role'        => 'required|string',
            'password'    => 'required|min:6',
            'school_name' => 'nullable|string',
            'assignments' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'username'    => trim($request->username),
                'name'        => $request->name ?: $request->username,
                'role'        => $request->role,
                'school_name' => $request->school_name,
                'password'    => Hash::make($request->password),
            ]);

            if ($user->role === 'Teacher' && $request->has('assignments') && is_array($request->assignments)) {
                $added = [];
                foreach ($request->assignments as $item) {
                    $cls   = trim($item['class_name'] ?? '');
                    $subId = intval($item['subject_id'] ?? 0);
                    if ($cls && $subId > 0) {
                        $key = "{$cls}_{$subId}";
                        if (!isset($added[$key])) {
                            $added[$key] = true;
                            TeacherAssignment::create([
                                'teacher_id'  => $user->id,
                                'subject_id'  => $subId,
                                'class_name'  => $cls,
                                'school_name' => $user->school_name,
                            ]);
                        }
                    }
                }
            }
        });

        return back()->with('success', '✔️ User account registered successfully!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username'    => 'required|string|unique:users,username,' . $user->id,
            'name'        => 'nullable|string',
            'role'        => 'required|string',
            'school_name' => 'nullable|string',
            'password'    => 'nullable|min:6',
            'assignments' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->username    = trim($request->username);
            $user->name        = $request->name ?: $request->username;
            $user->role        = $request->role;
            $user->school_name = $request->school_name;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            if ($user->role === 'Teacher') {
                TeacherAssignment::where('teacher_id', $user->id)->delete();

                if ($request->has('assignments') && is_array($request->assignments)) {
                    $added = [];
                    foreach ($request->assignments as $item) {
                        $cls   = trim($item['class_name'] ?? '');
                        $subId = intval($item['subject_id'] ?? 0);
                        if ($cls && $subId > 0) {
                            $key = "{$cls}_{$subId}";
                            if (!isset($added[$key])) {
                                $added[$key] = true;
                                TeacherAssignment::create([
                                    'teacher_id'  => $user->id,
                                    'subject_id'  => $subId,
                                    'class_name'  => $cls,
                                    'school_name' => $user->school_name,
                                ]);
                            }
                        }
                    }
                }
            } else {
                TeacherAssignment::where('teacher_id', $user->id)->delete();
            }
        });

        return back()->with('success', "✔️ Taarifa za mtumiaji {$user->username} zimesasishwa kikamilifu!");
    }

    public function deleteUser($id)
    {
        if (auth()->id() == $id) {
            return back()->with('error', '❌ You cannot delete your own account while logged in!');
        }

        DB::transaction(function () use ($id) {
            TeacherAssignment::where('teacher_id', $id)->delete();
            Student::where('parent_id', $id)->update(['parent_id' => null]);
            User::findOrFail($id)->delete();
        });

        return back()->with('success', '✔️ User deleted successfully!');
    }

    // --- Students ---
    public function students(Request $request)
    {
        $query = Student::with('parent');

        if ($request->filled('class')) {
            $query->where('class_name', $request->class);
        }

        if ($request->filled('school')) {
            $query->where('school_name', $request->school);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('student_name', 'like', "%$s%")
                  ->orWhere('reg_number', 'like', "%$s%");
            });
        }

        $sortBy = $request->get('sort', 'id');
        if ($sortBy === 'name') {
            $query->orderBy('student_name', 'asc');
        } elseif ($sortBy === 'reg') {
            $query->orderBy('reg_number', 'asc');
        } else {
            // Default: exact Excel insertion order
            $query->orderBy('id', 'asc');
        }

        $students = $query->paginate(30);
        $schools  = School::orderBy('school_name')->get();
        $parents  = User::where('role', 'Parent')->orderBy('username')->get();

        return view('admin.students', compact('students', 'schools', 'parents'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'reg_number'   => 'required|string|unique:students,reg_number',
            'student_name' => 'required|string',
            'class_name'   => 'required|string',
            'sex'          => 'required|in:M,F',
            'school_name'  => 'required|string',
            'parent_id'    => 'nullable|exists:users,id',
        ]);

        Student::create($request->all());

        return back()->with('success', '✔️ Student enrolled successfully!');
    }

    public function deleteStudent($id)
    {
        Student::findOrFail($id)->delete();
        return back()->with('success', '✔️ Student record deleted successfully!');
    }

    // Bulk upload CSV
    public function uploadStudentsCsv(Request $request)
    {
        @set_time_limit(300);
        @ini_set('max_execution_time', '300');

        $request->validate([
            'school_name' => 'required|string',
            'csv_file'    => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // skip header row

        // Pre-compute password hash once to avoid repeated slow bcrypt hashing
        $defaultPasswordHash = Hash::make('password123');

        // Pre-cache existing parents in memory to avoid hundreds of database queries
        $parentCache = [];
        $existingParents = User::where('role', 'Parent')->get(['id', 'username', 'name']);
        foreach ($existingParents as $p) {
            if (!empty($p->name)) {
                $parentCache[strtolower(trim($p->name))] = $p->id;
            }
            if (!empty($p->username)) {
                $parentCache[strtolower(trim($p->username))] = $p->id;
            }
        }

        // Pre-load existing usernames for fast unique check
        $existingUsernames = User::pluck('username')->map(fn($u) => strtolower($u))->flip()->toArray();

        $count = 0;
        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) >= 3 && !empty($row[0])) {
                    $regNumber   = trim($row[0]);
                    $studentName = trim($row[1]);
                    $className   = trim($row[2]);
                    $sex         = isset($row[3]) && in_array(strtoupper(trim($row[3])), ['M', 'F']) ? strtoupper(trim($row[3])) : 'M';
                    $parentRaw   = isset($row[4]) ? trim($row[4]) : null;

                    $parentId = null;
                    if (!empty($parentRaw)) {
                        $parentKey = strtolower($parentRaw);
                        if (isset($parentCache[$parentKey])) {
                            $parentId = $parentCache[$parentKey];
                        } else {
                            // Generate unique username
                            $cleanUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $parentRaw));
                            if (empty($cleanUsername)) {
                                $cleanUsername = 'parent_' . substr(uniqid(), -6);
                            }
                            $baseUsername = $cleanUsername;
                            $idx = 1;
                            while (isset($existingUsernames[$cleanUsername])) {
                                $cleanUsername = $baseUsername . $idx;
                                $idx++;
                            }
                            $existingUsernames[$cleanUsername] = true;

                            // Fast direct DB insert without redundant Eloquent model overhead
                            $parentId = DB::table('users')->insertGetId([
                                'username'    => $cleanUsername,
                                'name'        => $parentRaw,
                                'role'        => 'Parent',
                                'school_name' => $request->school_name,
                                'password'    => $defaultPasswordHash,
                                'created_at'  => now(),
                                'updated_at'  => now(),
                            ]);

                            $parentCache[$parentKey] = $parentId;
                            $parentCache[$cleanUsername] = $parentId;
                        }
                    }

                    Student::updateOrCreate(
                        ['reg_number' => $regNumber],
                        [
                            'student_name' => $studentName,
                            'class_name'   => $className,
                            'sex'          => $sex,
                            'school_name'  => $request->school_name,
                            'parent_id'    => $parentId,
                        ]
                    );
                    $count++;
                }
            }
            DB::commit();
            fclose($handle);
            return back()->with('success', "✔️ Wanafunzi $count na wazazi wao wameingizwa na kuunganishwa kikamilifu!");
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Error reading CSV: ' . $e->getMessage());
        }
    }

    public function downloadStudentTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="students_template.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $csv = "reg_number,student_name,class_name,sex,parent_username\r\n"
             . "STD001,Kelvin Michael,Form 1,M,parent1\r\n"
             . "STD002,Amina Juma,Form 1,F,parent1\r\n";

        return response($csv, 200, $headers);
    }
}
