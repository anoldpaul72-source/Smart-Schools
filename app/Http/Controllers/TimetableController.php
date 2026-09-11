<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Timetable;
use App\Models\Subject;
use App\Models\User;
use App\Models\TeacherAssignment;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $schoolName = $user ? ($user->school_name ?: 'Kome Secondary School') : 'Kome Secondary School';
        $userRole = $user ? $user->role : 'Guest';

        $isAcademic = in_array($userRole, ['Academic Master', 'Head of School', 'Head Of School', 'Headmaster', 'Headmistress', 'Admin']);

        $classes = [
            'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
        ];

        $selectedClass = $request->input('class_name', 'Form 1');
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

        // Fetch subjects & teachers for modal dropdowns
        $allSubjects = Subject::orderBy('subject_name')->get();
        $allTeachers = User::where('role', 'Teacher')
            ->where(function ($q) use ($schoolName) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
            })
            ->orderBy('username')
            ->get();

        if ($allTeachers->isEmpty()) {
            $allTeachers = User::where('role', 'Teacher')->orderBy('username')->get();
        }

        // Fetch timetable slots for selected class and school
        $rows = Timetable::where('class_name', $selectedClass)
            ->where(function ($q) use ($schoolName) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
            })
            ->with(['subject', 'teacher'])
            ->get();

        $timetableMatrix = [];
        foreach ($rows as $row) {
            $timetableMatrix[$row->day_of_week][$row->period_number] = [
                'subject'    => $row->subject ? $row->subject->subject_name : 'Subject',
                'subject_id' => $row->subject_id,
                'teacher'    => $row->teacher ? ($row->teacher->name ?: $row->teacher->username) : 'Teacher',
                'teacher_id' => $row->teacher_id,
            ];
        }

        return view('timetable.index', compact(
            'schoolName',
            'userRole',
            'isAcademic',
            'classes',
            'selectedClass',
            'days',
            'periodSlots',
            'allSubjects',
            'allTeachers',
            'timetableMatrix'
        ));
    }

    public function autoGenerate(Request $request)
    {
        $user = Auth::user();
        $schoolName = $user->school_name ?: 'Kome Secondary School';

        $classes = [
            'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
        ];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        DB::beginTransaction();
        try {
            // Delete existing timetable for this school
            Timetable::where('school_name', $schoolName)->delete();

            // Fetch subjects and teachers
            $subjects = Subject::all();
            $teachers = User::where('role', 'Teacher')->get();

            if ($subjects->isEmpty() || $teachers->isEmpty()) {
                DB::rollBack();
                return back()->with('error', 'No registered subjects or teachers found. Please add subjects and teachers first.');
            }

            // Build assigned subject pairs
            $assignedSubjects = [];
            foreach ($subjects as $index => $sub) {
                $teacher = $teachers[$index % count($teachers)];
                $assignedSubjects[] = [
                    'subject_id' => $sub->id,
                    'teacher_id' => $teacher->id,
                ];
            }

            $teacherSchedule = []; // [day][period][teacher_id] = true
            $totalAssigned = count($assignedSubjects);

            foreach ($classes as $cls) {
                $subIndex = 0;
                foreach ($days as $day) {
                    for ($p = 1; $p <= 9; $p++) {
                        $attempts = 0;
                        while ($attempts < $totalAssigned) {
                            $pair = $assignedSubjects[$subIndex % $totalAssigned];
                            $tId = $pair['teacher_id'];
                            $sId = $pair['subject_id'];

                            // Check collision
                            if (!isset($teacherSchedule[$day][$p][$tId])) {
                                $teacherSchedule[$day][$p][$tId] = true;

                                Timetable::create([
                                    'school_name'   => $schoolName,
                                    'class_name'    => $cls,
                                    'day_of_week'   => $day,
                                    'period_number' => $p,
                                    'subject_id'    => $sId,
                                    'teacher_id'    => $tId,
                                ]);

                                $subIndex++;
                                break;
                            }

                            $subIndex++;
                            $attempts++;
                        }
                    }
                }
            }

            DB::commit();
            return back()->with('success', '✔️ Timetable generated automatically without teacher clashes!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Generation Error: ' . $e->getMessage());
        }
    }

    public function saveSlot(Request $request)
    {
        $request->validate([
            'class_name'    => 'required|string',
            'day_of_week'   => 'required|string',
            'period_number' => 'required|integer|min:1|max:9',
            'subject_id'    => 'required|exists:subjects,id',
            'teacher_id'    => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $schoolName = $user->school_name ?: 'Kome Secondary School';

        $day       = $request->day_of_week;
        $period    = $request->period_number;
        $className = $request->class_name;
        $teacherId = $request->teacher_id;
        $subjectId = $request->subject_id;

        // Check if teacher is busy with another class in the same slot
        $clash = Timetable::where('teacher_id', $teacherId)
            ->where('day_of_week', $day)
            ->where('period_number', $period)
            ->where('class_name', '!=', $className)
            ->where('school_name', $schoolName)
            ->first();

        if ($clash) {
            return back()->with('error', "❌ Clash detected! Teacher is already teaching {$clash->class_name} during period $period on $day.");
        }

        Timetable::updateOrCreate(
            [
                'school_name'   => $schoolName,
                'class_name'    => $className,
                'day_of_week'   => $day,
                'period_number' => $period,
            ],
            [
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
            ]
        );

        return back()->with('success', "✔️ Timetable slot updated for $className ($day, Period $period)!");
    }

    public function deleteSlot(Request $request)
    {
        $request->validate([
            'class_name'    => 'required|string',
            'day_of_week'   => 'required|string',
            'period_number' => 'required|integer',
        ]);

        $user = Auth::user();
        $schoolName = $user->school_name ?: 'Kome Secondary School';

        Timetable::where('school_name', $schoolName)
            ->where('class_name', $request->class_name)
            ->where('day_of_week', $request->day_of_week)
            ->where('period_number', $request->period_number)
            ->delete();

        return back()->with('success', '✔️ Timetable slot cleared!');
    }
}
