<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Mark;
use App\Models\Attendance;
use App\Models\Timetable;
use App\Models\TeacherAssignment;

class TeacherController extends Controller
{
    public function marks(Request $request)
    {
        $teacher = Auth::user();
        $schoolName = $teacher->school_name ?: 'Kome Secondary School';

        $allClasses = [
            'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
        ];

        $isPrivileged = in_array($teacher->role, ['Admin', 'Headmaster', 'Academic Master']);

        if (!$isPrivileged) {
            // Strict teacher assignments
            $assignments = TeacherAssignment::where('teacher_id', $teacher->id)->with('subject')->get();
            $assignedSubjectIds = $assignments->pluck('subject_id')->unique();
            $myClasses = $assignments->pluck('class_name')->unique()->values();
            $subjects = Subject::whereIn('id', $assignedSubjectIds)->orderBy('subject_name')->get();
            $hasAssignments = $assignments->isNotEmpty();
        } else {
            // Admin, Headmaster, Academic Master have school-wide oversight
            $subjects = Subject::orderBy('subject_name')->get();
            $myClasses = collect($allClasses);
            $hasAssignments = true;
            $assignments = collect();
        }

        // Fetch recent marks entered for this teacher's assigned subjects & classes
        $recentMarksQuery = Mark::with(['student', 'subject'])
            ->whereHas('student', function ($q) use ($schoolName, $myClasses) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
                if ($myClasses->isNotEmpty()) {
                    $q->whereIn('class_name', $myClasses);
                }
            });

        if (!$isPrivileged) {
            $recentMarksQuery->whereIn('subject_id', $subjects->pluck('id'));
        }

        $recentMarks = $recentMarksQuery->latest()->take(25)->get();

        return view('teacher.marks', compact(
            'teacher',
            'schoolName',
            'subjects',
            'myClasses',
            'hasAssignments',
            'isPrivileged',
            'assignments',
            'recentMarks'
        ));
    }

    public function storeSingleMark(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'score'      => 'required|numeric|min:0|max:100',
            'term'       => 'required|string',
            'exam_date'  => 'required|date',
        ]);

        $teacher = Auth::user();
        $isPrivileged = in_array($teacher->role, ['Admin', 'Headmaster', 'Academic Master']);

        // Check teacher authorization for this subject & class
        if (!$isPrivileged) {
            $student = Student::findOrFail($request->student_id);
            $isAssigned = TeacherAssignment::where('teacher_id', $teacher->id)
                ->where('subject_id', $request->subject_id)
                ->where('class_name', $student->class_name)
                ->exists();

            if (!$isAssigned) {
                return back()->withInput()->with('error', '❌ Hauruhusiwi kuingiza alama za somo hili au darasa hili kwa kuwa hukupangiwa kulifundisha!');
            }
        }

        $scoreVal = (float)$request->score;
        [$grade, $remarks] = Mark::calculateGrade($scoreVal);

        $exists = Mark::where('student_id', $request->student_id)
            ->where('subject_id', $request->subject_id)
            ->where('term', $request->term)
            ->where('exam_date', $request->exam_date)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', '❌ Alama za mtihani huu kwenye tarehe hii zimeshaingizwa tayari kwa mwanafunzi huyu!');
        }

        Mark::create([
            'student_id' => $request->student_id,
            'subject_id' => $request->subject_id,
            'term'       => $request->term,
            'exam_date'  => $request->exam_date,
            'marks'      => $scoreVal,
            'grade'      => $grade,
            'remarks'    => $remarks,
        ]);

        return back()->with('success', '✔️ Alama zimehifadhiwa kikamilifu!');
    }

    public function getStudents(Request $request)
    {
        $teacher = Auth::user();
        $schoolName = $teacher->school_name ?: 'Kome Secondary School';
        $className = $request->input('class_name');

        if (empty($className)) {
            return response()->json([]);
        }

        $isPrivileged = in_array($teacher->role, ['Admin', 'Headmaster', 'Academic Master']);
        if (!$isPrivileged) {
            $isAssigned = TeacherAssignment::where('teacher_id', $teacher->id)
                ->where('class_name', $className)
                ->exists();

            if (!$isAssigned) {
                return response()->json([]);
            }
        }

        $students = Student::where('class_name', $className)
            ->where(function ($q) use ($schoolName) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
            })
            ->orderBy('reg_number', 'asc')
            ->get(['id as student_id', 'student_name', 'reg_number']);

        return response()->json($students);
    }

    public function downloadTemplate(Request $request)
    {
        $teacher = Auth::user();
        $schoolName = $teacher->school_name ?: 'Kome Secondary School';

        $className = trim($request->input('class_name'));
        $subjectId = intval($request->input('subject_id'));

        if (empty($className) || $subjectId === 0) {
            return back()->with('error', 'Tafadhali chagua Somo na Darasa kabla ya kupakua template.');
        }

        $isPrivileged = in_array($teacher->role, ['Admin', 'Headmaster', 'Academic Master']);
        if (!$isPrivileged) {
            $isAssigned = TeacherAssignment::where('teacher_id', $teacher->id)
                ->where('subject_id', $subjectId)
                ->where('class_name', $className)
                ->exists();

            if (!$isAssigned) {
                return back()->with('error', '❌ Hauruhusiwi kupakua template ya somo au darasa ambalo hufundishi.');
            }
        }

        $subject = Subject::find($subjectId);
        $subjectName = $subject ? $subject->subject_name : 'Subject';

        $students = Student::where('class_name', $className)
            ->where(function ($q) use ($schoolName) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
            })
            ->orderBy('id', 'asc')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', "Hakuna wanafunzi waliopatikana darasa la $className.");
        }

        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', "{$subjectName}_{$className}_Template") . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($students) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['reg_number', 'student_name', 'score', 'exam_date']);

            foreach ($students as $stud) {
                fputcsv($output, [
                    $stud->reg_number,
                    $stud->student_name,
                    '',
                    date('Y-m-d')
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function showUploadMarks()
    {
        $teacher = Auth::user();
        $isPrivileged = in_array($teacher->role, ['Admin', 'Headmaster', 'Academic Master']);

        if (!$isPrivileged) {
            $assignedSubjectIds = TeacherAssignment::where('teacher_id', $teacher->id)->pluck('subject_id')->unique();
            $subjects = Subject::whereIn('id', $assignedSubjectIds)->orderBy('subject_name')->get();
        } else {
            $subjects = Subject::orderBy('subject_name')->get();
        }

        return view('teacher.upload_marks', compact('teacher', 'subjects', 'isPrivileged'));
    }

    public function uploadMarksCsv(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'term'       => 'required|string',
            'csv_file'   => 'required|file',
        ]);

        $teacher = Auth::user();
        $isPrivileged = in_array($teacher->role, ['Admin', 'Headmaster', 'Academic Master']);

        if (!$isPrivileged) {
            $isAssignedSubject = TeacherAssignment::where('teacher_id', $teacher->id)
                ->where('subject_id', $request->subject_id)
                ->exists();

            if (!$isAssignedSubject) {
                return back()->with('error', '❌ Hauruhusiwi kupakia matokeo ya somo hili kwa kuwa hufundishi somo hili.');
            }
        }

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // Header

        $insertedCount = 0;
        $skippedCount  = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) >= 3 && !empty($row[0])) {
                    $identifier = trim($row[0]);
                    $scoreRaw   = trim($row[2]);
                    $examDate   = isset($row[3]) && !empty(trim($row[3])) ? trim($row[3]) : date('Y-m-d');

                    if ($scoreRaw === '' || empty($identifier)) {
                        $skippedCount++;
                        continue;
                    }

                    // Find student by reg_number first, or fallback to numeric ID
                    $student = Student::where('reg_number', $identifier)->first();
                    if (!$student && is_numeric($identifier)) {
                        $student = Student::find(intval($identifier));
                    }

                    if (!$student) {
                        $skippedCount++;
                        continue;
                    }

                    // Check if teacher is assigned to this student's class
                    if (!$isPrivileged) {
                        $isAssignedClass = TeacherAssignment::where('teacher_id', $teacher->id)
                            ->where('subject_id', $request->subject_id)
                            ->where('class_name', $student->class_name)
                            ->exists();

                        if (!$isAssignedClass) {
                            $skippedCount++;
                            continue;
                        }
                    }

                    $scoreVal = (float)$scoreRaw;
                    [$grade, $remarks] = Mark::calculateGrade($scoreVal);

                    Mark::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $request->subject_id,
                            'term'       => $request->term,
                            'exam_date'  => $examDate,
                        ],
                        [
                            'marks'      => $scoreVal,
                            'grade'      => $grade,
                            'remarks'    => $remarks,
                        ]
                    );
                    $insertedCount++;
                }
            }
            DB::commit();
            fclose($handle);

            return redirect()->route('teacher.marks')->with('success', "✔️ Alama $insertedCount zimepakiwa kikamilifu! ($skippedCount zimerukwa)");
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Hitilafu ya kusoma faili la CSV: ' . $e->getMessage());
        }
    }

    public function attendance(Request $request)
    {
        $teacher = Auth::user();
        $schoolName = $teacher->school_name ?: 'Kome Secondary School';
        $isPrivileged = in_array($teacher->role, ['Admin', 'Headmaster', 'Academic Master']);

        if (!$isPrivileged) {
            $assignedClasses = TeacherAssignment::where('teacher_id', $teacher->id)
                ->pluck('class_name')
                ->unique()
                ->values();
        } else {
            $assignedClasses = collect([
                'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
                'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
            ]);
        }

        $selectedClass = trim($request->input('class_name', ''));
        $students = collect();

        if (!empty($selectedClass)) {
            $students = Student::where('class_name', $selectedClass)
                ->where(function ($q) use ($schoolName) {
                    if ($schoolName) {
                        $q->where('school_name', $schoolName);
                    }
                })
                ->orderBy('reg_number', 'asc')
                ->get();
        }

        $today = date('Y-m-d');
        $existingAttendance = Attendance::where('date', $today)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return view('teacher.attendance', compact(
            'teacher',
            'schoolName',
            'assignedClasses',
            'selectedClass',
            'students',
            'existingAttendance',
            'today',
            'isPrivileged'
        ));
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string',
            'status'     => 'required|array',
        ]);

        $teacherId = Auth::id();
        $today     = date('Y-m-d');
        $className = $request->class_name;

        DB::transaction(function () use ($request, $today, $teacherId) {
            foreach ($request->status as $studentId => $status) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'date'       => $today,
                    ],
                    [
                        'status'      => $status,
                        'recorded_by' => $teacherId,
                    ]
                );
            }
        });

        return redirect()->route('teacher.attendance', ['class_name' => $className])
            ->with('success', "✔️ Attendance for {$className} has been successfully updated and recorded!");
    }

    public function attendanceHistory(Request $request)
    {
        $teacher = Auth::user();
        $schoolName = $teacher->school_name ?: 'Kome Secondary School';
        $isPrivileged = in_array($teacher->role, ['Admin', 'Headmaster', 'Academic Master']);

        if (!$isPrivileged) {
            $assignedClasses = TeacherAssignment::where('teacher_id', $teacher->id)
                ->pluck('class_name')
                ->unique()
                ->values();
        } else {
            $assignedClasses = collect([
                'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
                'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
            ]);
        }

        $selectedClass = trim($request->input('class_name', ''));
        $startDate     = $request->input('start_date', date('Y-m-01'));
        $endDate       = $request->input('end_date', date('Y-m-d'));

        $attendanceRecords = [];

        if (!empty($selectedClass)) {
            $records = Attendance::with('student')
                ->whereHas('student', function ($q) use ($selectedClass, $schoolName) {
                    $q->where('class_name', $selectedClass);
                    if ($schoolName) {
                        $q->where('school_name', $schoolName);
                    }
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->get();

            foreach ($records as $rec) {
                $attendanceRecords[$rec->date][] = [
                    'student_name' => $rec->student ? $rec->student->student_name : 'Student',
                    'status'       => $rec->status,
                ];
            }
        }

        return view('teacher.attendance_history', compact(
            'teacher',
            'schoolName',
            'assignedClasses',
            'selectedClass',
            'startDate',
            'endDate',
            'attendanceRecords',
            'isPrivileged'
        ));
    }

    public function timetable(Request $request)
    {
        $teacher = Auth::user();
        $schoolName = $teacher->school_name ?: 'Kome Secondary School';
        $isPrivileged = in_array($teacher->role, ['Admin', 'Headmaster', 'Academic Master']);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $periodSlots = [
            1 => '08:00 AM - 08:40 AM',
            2 => '08:40 AM - 09:20 AM',
            3 => '09:20 AM - 10:00 AM',
            4 => '10:00 AM - 10:40 AM',
            5 => '11:10 AM - 11:50 AM',
            6 => '11:50 AM - 12:30 PM',
            7 => '12:30 PM - 01:10 PM',
            8 => '02:00 PM - 02:40 PM',
            9 => '02:40 PM - 03:20 PM'
        ];

        // Fetch slots specifically assigned to this teacher
        $slots = Timetable::where('teacher_id', $teacher->id)
            ->where(function ($q) use ($schoolName) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
            })
            ->with('subject')
            ->get();

        $teacherMatrix = [];
        foreach ($slots as $slot) {
            $teacherMatrix[$slot->day_of_week][$slot->period_number] = [
                'class'      => $slot->class_name,
                'subject'    => $slot->subject ? $slot->subject->subject_name : 'Subject',
                'subject_id' => $slot->subject_id
            ];
        }

        return view('teacher.timetable', compact(
            'teacher',
            'schoolName',
            'days',
            'periodSlots',
            'teacherMatrix',
            'slots',
            'isPrivileged'
        ));
    }

    public function viewAllMarks(Request $request)
    {
        $teacher = Auth::user();
        $schoolName = $teacher->school_name ?: 'Kome Secondary School';
        $isPrivileged = in_array($teacher->role, ['Admin', 'Headmaster', 'Academic Master']);

        $selectedTerm  = trim($request->input('filter_term', ''));
        $selectedClass = trim($request->input('filter_class', ''));

        $query = Mark::with(['student', 'subject'])
            ->whereHas('student', function ($q) use ($schoolName) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
            });

        if (!$isPrivileged) {
            $assignments = TeacherAssignment::where('teacher_id', $teacher->id)->get();
            $assignedSubjectIds = $assignments->pluck('subject_id')->unique();
            $assignedClasses    = $assignments->pluck('class_name')->unique();

            $query->whereIn('subject_id', $assignedSubjectIds)
                  ->whereHas('student', function ($q) use ($assignedClasses) {
                      $q->whereIn('class_name', $assignedClasses);
                  });
        }

        if (!empty($selectedTerm)) {
            $query->where('term', $selectedTerm);
        }

        if (!empty($selectedClass)) {
            $query->whereHas('student', function ($q) use ($selectedClass) {
                $q->where('class_name', $selectedClass);
            });
        }

        // SAHIHISHO: Panga alama kuanzia ya juu kwenda ya chini (Score DESC)
        $allMarks = $query->orderBy('marks', 'desc')->get();

        // Grouping kwa ajili ya ripoti safi za kitaaluma
        $groupedMarks = [];
        foreach ($allMarks as $mark) {
            $className   = $mark->student ? $mark->student->class_name : 'Unknown';
            $subjectName = $mark->subject ? $mark->subject->subject_name : 'Unknown';
            $term        = $mark->term ?: 'Exam';
            $groupKey    = $className . '_' . $subjectName . '_' . $term;

            if (!isset($groupedMarks[$groupKey])) {
                $groupedMarks[$groupKey] = [
                    'info' => [
                        'class_name'   => $className,
                        'subject_name' => $subjectName,
                        'term'         => $term,
                    ],
                    'students' => []
                ];
            }
            $groupedMarks[$groupKey]['students'][] = $mark;
        }

        $allTerms = ['Weekly Test', 'Monthly Test', 'Midterm', 'Terminal', 'Annual'];
        $allClasses = [
            'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
        ];

        return view('teacher.all_marks', compact(
            'teacher',
            'schoolName',
            'isPrivileged',
            'selectedTerm',
            'selectedClass',
            'allTerms',
            'allClasses',
            'groupedMarks',
            'allMarks'
        ));
    }
}
