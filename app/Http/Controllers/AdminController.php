<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        $marksCount    = Mark::count();

        $smsLogsCount  = 0;
        $recentSmsLogs = collect();
        if (Schema::hasTable('sms_logs')) {
            $smsLogsCount  = \App\Models\SmsLog::count();
            $recentSmsLogs = \App\Models\SmsLog::with(['student', 'sender'])->latest()->take(8)->get();
        }

        $recentUsers   = User::latest()->take(8)->get();
        $schools       = School::orderBy('school_name')->get();
        $subjects      = Subject::orderBy('subject_name')->get();

        $dbClasses = Student::distinct()->whereNotNull('class_name')->pluck('class_name')->toArray();
        $defaultClasses = [
            'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
        ];
        $allClasses = array_values(array_unique(array_filter(array_merge($defaultClasses, $dbClasses))));

        $dbTerms = Mark::distinct()->whereNotNull('term')->pluck('term')->toArray();
        $defaultTerms = ['Weekly Test', 'Monthly Test', 'Midterm', 'Terminal Examination', 'Annual Examination'];
        $allTerms = array_values(array_unique(array_filter(array_merge($defaultTerms, $dbTerms))));

        $paymentsCount   = Schema::hasTable('student_payments') ? DB::table('student_payments')->count() : 0;
        $attendanceCount = Schema::hasTable('attendance') ? DB::table('attendance')->count() : 0;
        $totalUsersCount = User::count();
        $totalRecordsCount = $schoolsCount + $studentsCount + $totalUsersCount + $subjectsCount + $marksCount + $smsLogsCount + $paymentsCount + $attendanceCount;

        return view('admin.dashboard', compact(
            'schoolsCount',
            'studentsCount',
            'teachersCount',
            'parentsCount',
            'subjectsCount',
            'marksCount',
            'smsLogsCount',
            'paymentsCount',
            'attendanceCount',
            'totalRecordsCount',
            'recentUsers',
            'schools',
            'subjects',
            'recentSmsLogs',
            'allClasses',
            'allTerms'
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
            if (in_array($request->role, ['Headmaster', 'Head of School'])) {
                $query->whereIn('role', ['Headmaster', 'Head of School', 'Head Of School']);
            } else {
                $query->where('role', $request->role);
            }
        }

        if ($request->filled('school')) {
            $query->where('school_name', $request->school);
        }

        $users   = $query->with(['teacherAssignments.subject'])->latest()->paginate(15);
        $schools = School::orderBy('school_name')->get();
        $roles   = ['Admin', 'Teacher', 'Parent', 'Accountant', 'Librarian', 'Headmaster', 'Headmistress', 'Academic Master'];
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

            if (in_array($user->role, ['Teacher', 'Academic Master']) && $request->has('assignments') && is_array($request->assignments)) {
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

            if (in_array($user->role, ['Teacher', 'Academic Master'])) {
                if ($user->teacherAssignments()->exists()) {
                    TeacherAssignment::where('teacher_id', $user->id)->delete();
                }

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
                if ($user->teacherAssignments()->exists()) {
                    TeacherAssignment::where('teacher_id', $user->id)->delete();
                }
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
            if (TeacherAssignment::where('teacher_id', $id)->exists()) {
                TeacherAssignment::where('teacher_id', $id)->delete();
            }
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

        $sortBy = $request->get('sort', 'reg');
        if ($sortBy === 'name') {
            $query->orderBy('student_name', 'asc');
        } elseif ($sortBy === 'id') {
            $query->orderBy('id', 'asc');
        } else {
            // Default: reg_number (S0762/0001, S0762/0002...)
            $query->orderBy('reg_number', 'asc')->orderBy('id', 'asc');
        }

        $students = $query->paginate(30);
        $schools  = School::orderBy('school_name')->get();
        $parents  = User::where('role', 'Parent')->orderBy('username')->get();

        return view('admin.students', compact('students', 'schools', 'parents'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'reg_number'   => [
                'required',
                'string',
                \Illuminate\Validation\Rule::unique('students')->where(function ($query) use ($request) {
                    return $query->where('class_name', $request->class_name)
                                 ->where('school_name', $request->school_name);
                }),
            ],
            'student_name' => 'required|string',
            'class_name'   => 'required|string',
            'sex'          => 'required|in:M,F',
            'school_name'  => 'required|string',
            'parent_id'    => 'nullable|exists:users,id',
            'parent_phone' => 'nullable|string',
        ]);

        Student::create([
            'reg_number'   => $request->reg_number,
            'student_name' => $request->student_name,
            'class_name'   => $request->class_name,
            'sex'          => $request->sex,
            'school_name'  => $request->school_name,
            'parent_id'    => $request->parent_id,
            'parent_phone' => $request->parent_phone,
        ]);

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
        $errors = [];

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) >= 3 && !empty($row[0])) {
                $regNumber   = trim($row[0]);
                $studentName = trim($row[1]);
                $className   = trim($row[2]);
                $sex         = isset($row[3]) && in_array(strtoupper(trim($row[3])), ['M', 'F']) ? strtoupper(trim($row[3])) : 'M';
                $parentRaw   = isset($row[4]) ? trim($row[4]) : null;
                $parentPhone = isset($row[5]) ? trim($row[5]) : null;

                try {
                    $parentId = null;
                    if (!empty($parentRaw)) {
                        $parentKey = strtolower(trim($parentRaw));
                        if (isset($parentCache[$parentKey])) {
                            $parentId = $parentCache[$parentKey];
                        } else {
                            $existingDbParent = DB::table('users')
                                ->where('role', 'Parent')
                                ->where(function($q) use ($parentRaw) {
                                    $q->whereRaw('LOWER(name) = ?', [strtolower($parentRaw)])
                                      ->orWhereRaw('LOWER(username) = ?', [strtolower($parentRaw)]);
                                })
                                ->first();

                            if ($existingDbParent) {
                                $parentId = $existingDbParent->id;
                            } else {
                                $cleanUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $parentRaw));
                                if (empty($cleanUsername)) {
                                    $cleanUsername = 'parent_' . substr(uniqid(), -6);
                                }
                                $baseUsername = $cleanUsername;
                                $idx = 1;
                                while (isset($existingUsernames[$cleanUsername]) || DB::table('users')->where('username', $cleanUsername)->exists()) {
                                    $cleanUsername = $baseUsername . $idx;
                                    $idx++;
                                }
                                $existingUsernames[$cleanUsername] = true;

                                $parentId = DB::table('users')->insertGetId([
                                    'username'    => $cleanUsername,
                                    'name'        => $parentRaw,
                                    'phone'       => $parentPhone,
                                    'role'        => 'Parent',
                                    'school_name' => $request->school_name,
                                    'password'    => $defaultPasswordHash,
                                    'created_at'  => now(),
                                    'updated_at'  => now(),
                                ]);
                            }

                            $parentCache[$parentKey] = $parentId;
                        }
                    }

                    $existingStud = DB::table('students')
                        ->where('reg_number', $regNumber)
                        ->where('class_name', $className)
                        ->where('school_name', $request->school_name)
                        ->first();

                    if ($existingStud) {
                        $updateData = [
                            'student_name' => $studentName,
                            'sex'          => $sex,
                            'parent_id'    => $parentId,
                            'updated_at'   => now(),
                        ];
                        if (!empty($parentPhone)) {
                            $updateData['parent_phone'] = $parentPhone;
                        }
                        DB::table('students')->where('id', $existingStud->id)->update($updateData);
                    } else {
                        DB::table('students')->insert([
                            'reg_number'   => $regNumber,
                            'student_name' => $studentName,
                            'class_name'   => $className,
                            'sex'          => $sex,
                            'school_name'  => $request->school_name,
                            'parent_id'    => $parentId,
                            'parent_phone' => !empty($parentPhone) ? $parentPhone : null,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    }
                    $count++;
                } catch (\Exception $e) {
                    $errors[] = "$regNumber ($studentName): " . $e->getMessage();
                }
            }
        }
        fclose($handle);

        if (!empty($errors)) {
            return back()->with('warning', "Wanafunzi $count wameingizwa, lakini kuna makosa kwenye baadhi: " . implode('; ', array_slice($errors, 0, 3)));
        }

        return back()->with('success', "✔️ Wanafunzi $count na wazazi wao wameingizwa na kuunganishwa kikamilifu!");
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

    /**
     * Export all system data for backup purposes.
     * Supports formats:
     * - 'json': Complete hierarchical JSON archive for disaster recovery / system restore
     * - 'csv_zip': ZIP package of separate CSV spreadsheets for Excel analysis
     * - 'sql': Complete SQL INSERT dump statements
     */
    public function exportBackup(Request $request)
    {
        $format = $request->query('format', 'json');
        $timestamp = date('Y-m-d_His');

        $tables = [
            'schools',
            'users',
            'subjects',
            'students',
            'teacher_assignments',
            'marks',
            'attendance',
            'timetables',
            'fee_structures',
            'student_payments',
            'sms_logs',
        ];

        // Gather all table data securely
        $backupData = [];
        $statistics = [];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $rows = DB::table($table)->orderBy('id')->get()->map(function ($row) {
                    return (array) $row;
                })->toArray();
                $backupData[$table] = $rows;
                $statistics[$table] = count($rows);
            } else {
                $statistics[$table] = 0;
            }
        }

        // Format 0: ALL-IN-ONE COMPLETE BUNDLE (Excel CSVs + JSON Restore + SQL Dump)
        if (in_array($format, ['bundle', 'all']) && class_exists('\ZipArchive')) {
            $zip = new \ZipArchive();
            $tempZipFile = tempnam(sys_get_temp_dir(), 'smart_schools_all_') . '.zip';

            if ($zip->open($tempZipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                // 1. Add all CSV spreadsheets (Excel)
                foreach ($backupData as $table => $rows) {
                    $handle = fopen('php://temp', 'r+');
                    if (!empty($rows)) {
                        fputcsv($handle, array_keys($rows[0]));
                        foreach ($rows as $row) {
                            fputcsv($handle, array_values($row));
                        }
                    } else {
                        fputcsv($handle, ['Info']);
                        fputcsv($handle, ['Hakuna kumbukumbu kwenye meza ya: ' . $table]);
                    }
                    rewind($handle);
                    $csvContent = stream_get_contents($handle);
                    fclose($handle);

                    $zip->addFromString('01_Excel_Majedwali/' . $table . '.csv', "\xEF\xBB\xBF" . $csvContent);
                }

                // 2. Add complete JSON System Restore archive
                $jsonPayload = [
                    'meta' => [
                        'system'        => 'Smart-Schools Management System',
                        'description'   => 'Full institutional data backup archive',
                        'exported_at'   => date('Y-m-d H:i:s'),
                        'timestamp'     => time(),
                        'exported_by'   => auth()->user()?->username ?? 'Admin',
                        'total_records' => array_sum($statistics),
                        'statistics'    => $statistics,
                    ],
                    'database' => $backupData,
                ];
                $zip->addFromString('02_System_Restore/smart_schools_backup_' . $timestamp . '.json', json_encode($jsonPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

                // 3. Add SQL database script
                $sql = "-- ==========================================================\n"
                     . "-- Smart-Schools Database Backup (SQL Export)\n"
                     . "-- Tarehe: " . date('Y-m-d H:i:s') . "\n"
                     . "-- Mtumiaji: " . (auth()->user()->username ?? 'Admin') . "\n"
                     . "-- Jumla ya kumbukumbu: " . number_format(array_sum($statistics)) . "\n"
                     . "-- ==========================================================\n\n";

                foreach ($backupData as $table => $rows) {
                    if (empty($rows)) continue;
                    $sql .= "-- ----------------------------------------------------------\n";
                    $sql .= "-- Meza: $table (" . count($rows) . " records)\n";
                    $sql .= "-- ----------------------------------------------------------\n";
                    $columns = array_keys($rows[0]);
                    $colList = implode(', ', array_map(function ($c) {
                        return '"' . str_replace('"', '""', $c) . '"';
                    }, $columns));

                    foreach ($rows as $row) {
                        $values = array_map(function ($val) {
                            if (is_null($val)) return 'NULL';
                            $clean = str_replace(["\\", "'"], ["\\\\", "''"], (string)$val);
                            return "'" . $clean . "'";
                        }, array_values($row));
                        $sql .= "INSERT INTO \"$table\" ($colList) VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
                $zip->addFromString('03_Database_SQL/smart_schools_backup_' . $timestamp . '.sql', $sql);

                // 4. Add Summary README
                $readme = "=========================================================\r\n"
                    . "SMART-SCHOOLS - HIFADHI NA BACKUP KAMILI (ALL-IN-ONE BUNDLE)\r\n"
                    . "=========================================================\r\n"
                    . "Tarehe ya Backup   : " . date('d/m/Y H:i:s') . "\r\n"
                    . "Iliyopakuliwa na   : " . (auth()->user()->username ?? 'Admin') . "\r\n"
                    . "Jumla ya Kumbukumbu: " . number_format(array_sum($statistics)) . " records\r\n\r\n"
                    . "YALIYOMO NDANI YA FOLDA HII:\r\n"
                    . "1. Folda ya '01_Excel_Majedwali/':\r\n"
                    . "   Inajumuisha mafaili yote ya CSV (Excel) kwa kila meza: wanafunzi, alama, watumiaji, malipo ya ada, na mahudhurio.\r\n\r\n"
                    . "2. Folda ya '02_System_Restore/':\r\n"
                    . "   Faili kamili la JSON lenye muundo mzima wa mfumo kwa ajili ya kurudisha mfumo (System Restore).\r\n\r\n"
                    . "3. Folda ya '03_Database_SQL/':\r\n"
                    . "   Faili la SQL script lenye amri za kuingiza taarifa kwenye PostgreSQL au MySQL.\r\n\r\n"
                    . "Mchanganuo wa Kumbukumbu zilizohifadhiwa:\r\n";
                foreach ($statistics as $tbl => $cnt) {
                    $readme .= "  - " . str_pad($tbl, 24) . ": " . number_format($cnt) . " records\r\n";
                }
                $zip->addFromString('SOMA_KWANZA_MAELEZO.txt', $readme);
                $zip->close();

                $filename = 'smart_schools_COMPLETE_BUNDLE_' . $timestamp . '.zip';
                return response()->download($tempZipFile, $filename, [
                    'Content-Type' => 'application/zip',
                ])->deleteFileAfterSend(true);
            }
        }

        // Format 1: ZIP of CSV spreadsheets
        if ($format === 'csv_zip' && class_exists('\ZipArchive')) {
            $zip = new \ZipArchive();
            $tempZipFile = tempnam(sys_get_temp_dir(), 'smart_schools_') . '.zip';

            if ($zip->open($tempZipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                foreach ($backupData as $table => $rows) {
                    $handle = fopen('php://temp', 'r+');
                    if (!empty($rows)) {
                        // Headers
                        fputcsv($handle, array_keys($rows[0]));
                        // Rows
                        foreach ($rows as $row) {
                            fputcsv($handle, array_values($row));
                        }
                    } else {
                        fputcsv($handle, ['Info']);
                        fputcsv($handle, ['Hakuna kumbukumbu kwenye meza ya: ' . $table]);
                    }
                    rewind($handle);
                    $csvContent = stream_get_contents($handle);
                    fclose($handle);

                    // Prepend UTF-8 BOM so Excel displays Swahili and special characters properly
                    $zip->addFromString($table . '.csv', "\xEF\xBB\xBF" . $csvContent);
                }

                // Summary info inside ZIP
                $readme = "=========================================================\r\n"
                    . "Smart-Schools - Ripoti Kamili ya Backup ya Mfumo\r\n"
                    . "=========================================================\r\n"
                    . "Tarehe ya Backup : " . date('d/m/Y H:i:s') . "\r\n"
                    . "Iliyoombwa na    : " . (auth()->user()->username ?? 'Admin') . "\r\n"
                    . "Jumla ya Data    : " . number_format(array_sum($statistics)) . " records\r\n\r\n"
                    . "Mchanganuo wa Kumbukumbu kwa Kila Jedwali:\r\n";
                foreach ($statistics as $tbl => $cnt) {
                    $readme .= "  * " . str_pad($tbl, 22) . ": " . number_format($cnt) . " records\r\n";
                }
                $zip->addFromString('TAARIFA_ZA_BACKUP.txt', $readme);
                $zip->close();

                $filename = 'smart_schools_backup_csv_' . $timestamp . '.zip';
                return response()->download($tempZipFile, $filename, [
                    'Content-Type' => 'application/zip',
                ])->deleteFileAfterSend(true);
            }
        }

        // Format 2: SQL dump
        if ($format === 'sql') {
            $sql = "-- ==========================================================\n"
                 . "-- Smart-Schools Database Backup (SQL Export)\n"
                 . "-- Tarehe: " . date('Y-m-d H:i:s') . "\n"
                 . "-- Mtumiaji: " . (auth()->user()->username ?? 'Admin') . "\n"
                 . "-- Jumla ya kumbukumbu: " . number_format(array_sum($statistics)) . "\n"
                 . "-- ==========================================================\n\n";

            foreach ($backupData as $table => $rows) {
                if (empty($rows)) continue;
                $sql .= "-- ----------------------------------------------------------\n";
                $sql .= "-- Meza: $table (" . count($rows) . " records)\n";
                $sql .= "-- ----------------------------------------------------------\n";
                $columns = array_keys($rows[0]);
                $colList = implode(', ', array_map(function ($c) {
                    return '"' . str_replace('"', '""', $c) . '"';
                }, $columns));

                foreach ($rows as $row) {
                    $values = array_map(function ($val) {
                        if (is_null($val)) return 'NULL';
                        $clean = str_replace(["\\", "'"], ["\\\\", "''"], (string)$val);
                        return "'" . $clean . "'";
                    }, array_values($row));
                    $sql .= "INSERT INTO \"$table\" ($colList) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }

            $filename = 'smart_schools_backup_' . $timestamp . '.sql';
            return response($sql, 200, [
                'Content-Type'        => 'application/sql; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma'              => 'no-cache',
                'Expires'             => '0',
            ]);
        }

        // Format 3 (Default): Full JSON Structured System Backup
        $payload = [
            'meta' => [
                'system'        => 'Smart-Schools Management System',
                'description'   => 'Full institutional data backup archive',
                'exported_at'   => date('Y-m-d H:i:s'),
                'timestamp'     => time(),
                'exported_by'   => auth()->user()?->username ?? 'Admin',
                'total_records' => array_sum($statistics),
                'statistics'    => $statistics,
            ],
            'database' => $backupData,
        ];

        $filename = 'smart_schools_backup_' . $timestamp . '.json';
        return response(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 200, [
            'Content-Type'        => 'application/json; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    /**
     * Preview count of marks before bulk deletion
     */
    public function countMarksForDeletion(Request $request)
    {
        $className = trim($request->query('class_name', ''));
        $term      = trim($request->query('term', ''));
        $subjectId = $request->query('subject_id', '');

        if (!$className || !$term) {
            return response()->json(['count' => 0]);
        }

        $studentIds = Student::where('class_name', $className)->pluck('id');
        if ($studentIds->isEmpty()) {
            return response()->json(['count' => 0]);
        }

        $query = Mark::whereIn('student_id', $studentIds)
            ->where(function ($q) use ($term) {
                $q->where('term', $term)
                  ->orWhere('term', 'like', $term . '%');
            });

        if (!empty($subjectId) && $subjectId !== 'all') {
            $query->where('subject_id', $subjectId);
        }

        return response()->json(['count' => $query->count()]);
    }

    /**
     * Bulk delete marks by class and assessment type (term), with optional subject filter
     */
    public function deleteMarksByClassAndTerm(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string',
            'term'       => 'required|string',
            'subject_id' => 'nullable',
        ]);

        $className = trim($request->class_name);
        $term      = trim($request->term);
        $subjectId = $request->subject_id;

        $studentIds = Student::where('class_name', $className)->pluck('id');

        if ($studentIds->isEmpty()) {
            return back()->with('error', '⚠️ ' . __('No students found for class :class.', ['class' => $className]));
        }

        $query = Mark::whereIn('student_id', $studentIds)
            ->where(function ($q) use ($term) {
                $q->where('term', $term)
                  ->orWhere('term', 'like', $term . '%');
            });

        $subjectName = null;
        if (!empty($subjectId) && $subjectId !== 'all') {
            $query->where('subject_id', $subjectId);
            $subject = Subject::find($subjectId);
            $subjectName = $subject ? $subject->subject_name : null;
        }

        $count = $query->count();

        if ($count === 0) {
            $msg = '⚠️ ' . __('No marks found to delete for :class (:term).', [
                'class' => $className,
                'term'  => $term . ($subjectName ? " - {$subjectName}" : '')
            ]);
            return back()->with('error', $msg);
        }

        $deleted = $query->delete();

        $msg = '✔️ ' . __('Successfully deleted :count marks for :class (:term).', [
            'count' => number_format($deleted),
            'class' => $className,
            'term'  => $term . ($subjectName ? " - {$subjectName}" : '')
        ]);
        return back()->with('success', $msg);
    }
}
