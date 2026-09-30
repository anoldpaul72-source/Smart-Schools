<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Timetable;
use App\Models\Subject;
use App\Models\User;
use App\Models\TeacherAssignment;
use App\Models\Student;

class TimetableController extends Controller
{
    /**
     * Resolve school name scope for queries.
     */
    protected function resolveSchoolName($user, Request $request = null): string
    {
        if ($request && $request->filled('school_name')) {
            return trim($request->input('school_name'));
        }
        if ($user && !empty($user->school_name)) {
            return trim($user->school_name);
        }
        // Check if there is a school in TeacherAssignment or User
        $asgSchool = TeacherAssignment::whereNotNull('school_name')->where('school_name', '!=', '')->value('school_name');
        if ($asgSchool) {
            return $asgSchool;
        }
        $userSchool = User::whereIn('role', ['Teacher', 'Academic Master'])->whereNotNull('school_name')->where('school_name', '!=', '')->value('school_name');
        if ($userSchool) {
            return $userSchool;
        }
        return 'Kome Secondary School';
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $schoolName = $this->resolveSchoolName($user, $request);
        $userRole = $user ? $user->role : 'Guest';

        $isAcademic = in_array($userRole, ['Academic Master', 'Head of School', 'Head Of School', 'Headmaster', 'Headmistress', 'Admin']);

        // Gather all classes from system (standard, assigned, and students)
        $standardClasses = collect(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6']);
        $assignedClasses = TeacherAssignment::where(function ($q) use ($schoolName) {
            $q->where('school_name', $schoolName)->orWhereNull('school_name')->orWhere('school_name', '');
        })->pluck('class_name');
        $studentClasses = Student::where(function ($q) use ($schoolName) {
            $q->where('school_name', $schoolName)->orWhereNull('school_name')->orWhere('school_name', '');
        })->pluck('class_name');
        $timetableClasses = Timetable::where(function ($q) use ($schoolName) {
            $q->where('school_name', $schoolName)->orWhereNull('school_name')->orWhere('school_name', '');
        })->pluck('class_name');

        $classes = $standardClasses->merge($assignedClasses)->merge($studentClasses)->merge($timetableClasses)
            ->map(fn($c) => trim($c))
            ->unique()->filter()->values()->all();

        // Sort classes: O-Level first, then Advance, then primary/standards
        usort($classes, function ($a, $b) {
            $isAdvA = Student::isClassALevel($a);
            $isAdvB = Student::isClassALevel($b);
            if ($isAdvA !== $isAdvB) {
                return $isAdvA ? 1 : -1;
            }
            return strnatcasecmp($a, $b);
        });

        $selectedClass = $request->input('class_name', $classes[0] ?? 'Form 1');
        $isALevel = Student::isClassALevel($selectedClass);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        $periodSlots = [
            1  => '08:00 AM - 08:40 AM',
            2  => '08:40 AM - 09:20 AM',
            3  => '09:20 AM - 10:00 AM',
            4  => '10:00 AM - 10:40 AM',
            5  => '10:40 AM - 11:20 AM',
            6  => '11:40 AM - 12:20 PM',
            7  => '12:20 PM - 01:00 PM',
            8  => '01:00 PM - 01:40 PM',
            9  => '01:40 PM - 02:20 PM',
            10 => '03:00 PM - 05:00 PM',
        ];

        // Ensure special routine subjects exist
        Subject::firstOrCreate(['subject_name' => 'Religion']);
        Subject::firstOrCreate(['subject_name' => 'Debate or Subject Club']);
        Subject::firstOrCreate(['subject_name' => 'Sports and Games']);
        Subject::firstOrCreate(['subject_name' => 'Discussion and Examinations']);
        Subject::firstOrCreate(['subject_name' => 'General Studies Seminar']);
        Subject::firstOrCreate(['subject_name' => 'Laboratory Practicals and Research']);

        // Filter subjects for modal dropdown according to education level
        $rawSubjects = Subject::orderBy('subject_name')->get();
        $allSubjects = $rawSubjects->filter(function ($sub) use ($isALevel) {
            $name = $sub->subject_name;
            if ($isALevel) {
                return !Subject::isOLevelOnlySubject($name);
            } else {
                return !Subject::isAdvanceOnlySubject($name);
            }
        })->values();

        // Fetch registered teachers in this school (including universal teachers with null/empty school_name)
        $allTeachers = User::whereIn('role', ['Teacher', 'Academic Master'])
            ->with(['teacherAssignments.subject'])
            ->where(function ($q) use ($schoolName) {
                $q->where('school_name', $schoolName)
                  ->orWhereNull('school_name')
                  ->orWhere('school_name', '');
            })
            ->orderBy('name')
            ->get();

        if ($allTeachers->isEmpty()) {
            $allTeachers = User::whereIn('role', ['Teacher', 'Academic Master'])
                ->with(['teacherAssignments.subject'])
                ->orderBy('name')
                ->get();
        }

        // Fetch all valid teacher assignments for this school
        $schoolAssignments = TeacherAssignment::with(['teacher', 'subject'])
            ->whereHas('teacher', function ($q) {
                $q->whereIn('role', ['Teacher', 'Academic Master']);
            })
            ->whereHas('subject')
            ->where(function ($q) use ($schoolName) {
                $q->where('school_name', $schoolName)
                  ->orWhereNull('school_name')
                  ->orWhere('school_name', '');
            })
            ->get();

        if ($schoolAssignments->isEmpty()) {
            $schoolAssignments = TeacherAssignment::with(['teacher', 'subject'])
                ->whereHas('teacher', function ($q) {
                    $q->whereIn('role', ['Teacher', 'Academic Master']);
                })
                ->whereHas('subject')
                ->get();
        }

        // Assignments specifically for the selected class
        $classAssignments = $schoolAssignments->filter(function ($asg) use ($selectedClass) {
            return strcasecmp(trim($asg->class_name), trim($selectedClass)) === 0;
        })->values();

        $assignedTeacherIds = $classAssignments->pluck('teacher_id')->unique()->toArray();

        // Build subject -> teacher mapping for the JS modal auto-select
        // Priority 1: Teacher assigned to this subject in $selectedClass
        // Priority 2: Teacher assigned to this subject in any class in the school
        $subjectTeacherDefaultMap = [];
        foreach ($schoolAssignments as $asg) {
            if (!isset($subjectTeacherDefaultMap[$asg->subject_id])) {
                $subjectTeacherDefaultMap[$asg->subject_id] = $asg->teacher_id;
            }
        }
        foreach ($classAssignments as $asg) {
            $subjectTeacherDefaultMap[$asg->subject_id] = $asg->teacher_id;
        }

        // Fetch timetable slots for selected class and school
        $rows = Timetable::where(function ($q) use ($selectedClass) {
                $q->where('class_name', $selectedClass)
                  ->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($selectedClass))]);
            })
            ->where(function ($q) use ($schoolName) {
                $q->where('school_name', $schoolName)
                  ->orWhereNull('school_name')
                  ->orWhere('school_name', '');
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
            'isALevel',
            'days',
            'periodSlots',
            'allSubjects',
            'allTeachers',
            'schoolAssignments',
            'classAssignments',
            'subjectTeacherDefaultMap',
            'assignedTeacherIds',
            'timetableMatrix'
        ));
    }

    public function autoGenerate(Request $request)
    {
        $user = Auth::user();
        $schoolName = $this->resolveSchoolName($user, $request);

        DB::beginTransaction();
        try {
            // 1. Fetch all registered teachers in this school (including universal teachers)
            $schoolTeachers = User::whereIn('role', ['Teacher', 'Academic Master'])
                ->with(['teacherAssignments.subject'])
                ->where(function ($q) use ($schoolName) {
                    $q->where('school_name', $schoolName)
                      ->orWhereNull('school_name')
                      ->orWhere('school_name', '');
                })
                ->get();

            if ($schoolTeachers->isEmpty()) {
                $schoolTeachers = User::whereIn('role', ['Teacher', 'Academic Master'])
                    ->with(['teacherAssignments.subject'])
                    ->get();
            }

            if ($schoolTeachers->isEmpty()) {
                DB::rollBack();
                return back()->with('error', '❌ Hakuna walimu waliosajiliwa kwenye mfumo. Tafadhali sajili walimu na masomo yao kwanza.');
            }

            // 2. Fetch all registered teacher-subject assignments
            $allAssignments = TeacherAssignment::with(['teacher', 'subject'])
                ->whereIn('teacher_id', $schoolTeachers->pluck('id'))
                ->whereHas('subject')
                ->get()
                ->filter(function ($asg) {
                    return $asg->teacher && $asg->subject && Subject::isAcademicSubject($asg->subject->subject_name);
                })
                ->values();

            if ($allAssignments->isEmpty()) {
                DB::rollBack();
                return back()->with('error', '❌ Walimu wamesajiliwa lakini hawajapangiwa masomo wanayofundisha! Tafadhali nenda User Management uwawekee walimu masomo na madarasa wanayofundisha.');
            }

            // 3. Clear existing timetable for this school
            Timetable::where(function ($q) use ($schoolName) {
                $q->where('school_name', $schoolName)
                  ->orWhereNull('school_name')
                  ->orWhere('school_name', '');
            })->delete();

            // 4. Identify classes to generate for:
            // Prioritize classes that actually have teacher assignments or students, plus standard classes
            $assignedClasses = $allAssignments->pluck('class_name')->map(fn($c) => trim($c))->filter();
            $studentClasses  = Student::where(function ($q) use ($schoolName) {
                $q->where('school_name', $schoolName)->orWhereNull('school_name')->orWhere('school_name', '');
            })->pluck('class_name')->map(fn($c) => trim($c))->filter();
            $baseClasses = collect(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6']);

            $classes = $assignedClasses->merge($studentClasses)->merge($baseClasses)
                ->unique()->filter()->values()->all();

            // Sort: O-Level first, then Advance
            usort($classes, function ($a, $b) {
                $advA = Student::isClassALevel($a);
                $advB = Student::isClassALevel($b);
                if ($advA !== $advB) return $advA ? 1 : -1;
                return strnatcasecmp($a, $b);
            });

            // Special activity subjects
            $subReligion   = Subject::firstOrCreate(['subject_name' => 'Religion']);
            $subDebate     = Subject::firstOrCreate(['subject_name' => 'Debate or Subject Club']);
            $subSports     = Subject::firstOrCreate(['subject_name' => 'Sports and Games']);
            $subDiscussion = Subject::firstOrCreate(['subject_name' => 'Discussion and Examinations']);
            $subGsSeminar  = Subject::firstOrCreate(['subject_name' => 'General Studies Seminar']);
            $subPracticals = Subject::firstOrCreate(['subject_name' => 'Laboratory Practicals and Research']);

            // Teacher clash collision detector: [day][period][teacher_id] = class_name
            $teacherSchedule = [];

            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $scheduledClassesCount = 0;

            foreach ($classes as $classIndex => $cls) {
                $isALevel = Student::isClassALevel($cls);

                // Pick a class supervisor from teachers assigned to this class, or fallback to school teachers
                $classTeacherIds = $allAssignments->filter(fn($a) => strcasecmp(trim($a->class_name), trim($cls)) === 0)->pluck('teacher_id')->unique()->values();
                if ($classTeacherIds->isNotEmpty()) {
                    $supId = $classTeacherIds[$classIndex % $classTeacherIds->count()];
                    $classSupervisor = $schoolTeachers->firstWhere('id', $supId) ?? $schoolTeachers[$classIndex % $schoolTeachers->count()];
                } else {
                    $classSupervisor = $schoolTeachers[$classIndex % $schoolTeachers->count()];
                }

                if ($isALevel) {
                    $this->generateAdvanceTimetable(
                        $cls,
                        $schoolName,
                        $schoolTeachers,
                        $allAssignments,
                        $classSupervisor,
                        $teacherSchedule,
                        $days,
                        $classIndex,
                        $subDiscussion,
                        $subGsSeminar,
                        $subPracticals,
                        $subSports,
                        $subDebate
                    );
                } else {
                    $this->generateOLevelTimetable(
                        $cls,
                        $schoolName,
                        $schoolTeachers,
                        $allAssignments,
                        $classSupervisor,
                        $teacherSchedule,
                        $days,
                        $classIndex,
                        $subDiscussion,
                        $subReligion,
                        $subDebate,
                        $subSports
                    );
                }
                $scheduledClassesCount++;
            }

            $uniqueTeachersUsed = $allAssignments->pluck('teacher_id')->unique()->count();
            $uniqueSubjectsUsed = $allAssignments->pluck('subject_id')->unique()->count();

            DB::commit();
            return back()->with('success', "✔️ Ratiba ya shule imetengenezwa kikamilifu kwa kutumia walimu {$uniqueTeachersUsed} waliosajiliwa na masomo {$uniqueSubjectsUsed} wanayofundisha kwa madarasa {$scheduledClassesCount} bila mgongano wa vipindi!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hitilafu wakati wa kutengeneza ratiba: ' . $e->getMessage());
        }
    }

    /**
     * Gather valid (teacher_id, subject_id, subject_name) pairs for an O-Level class
     * strictly from registered TeacherAssignments.
     */
    protected function getOLevelClassPairs(string $cls, $allAssignments): array
    {
        $pairs = [];

        // 1. Direct assignments for this exact class
        $direct = $allAssignments->filter(function ($asg) use ($cls) {
            return strcasecmp(trim($asg->class_name), trim($cls)) === 0
                && !Subject::isAdvanceOnlySubject($asg->subject->subject_name);
        });

        foreach ($direct as $asg) {
            $key = $asg->teacher_id . '_' . $asg->subject_id;
            $pairs[$key] = [
                'subject_id'   => $asg->subject_id,
                'teacher_id'   => $asg->teacher_id,
                'subject_name' => $asg->subject->subject_name,
                'teacher_name' => $asg->teacher->name ?: $asg->teacher->username,
                'is_direct'    => true,
            ];
        }

        // 2. If this class has no direct assignments (e.g. admin only assigned teachers to Form 1 or other O-Level classes),
        // use the registered O-Level teacher-subject assignments from the school so every teacher still only teaches their registered subject.
        if (empty($pairs)) {
            $olevelAssignments = $allAssignments->filter(function ($asg) {
                return !Student::isClassALevel($asg->class_name)
                    && !Subject::isAdvanceOnlySubject($asg->subject->subject_name);
            });

            // Fallback to any non-Advance-only subject assignment if all assignments were on A-Level classes
            if ($olevelAssignments->isEmpty()) {
                $olevelAssignments = $allAssignments->filter(function ($asg) {
                    return !Subject::isAdvanceOnlySubject($asg->subject->subject_name);
                });
            }

            $seenSubjects = [];
            foreach ($olevelAssignments as $asg) {
                if (!isset($seenSubjects[$asg->subject_id])) {
                    $seenSubjects[$asg->subject_id] = true;
                    $key = $asg->teacher_id . '_' . $asg->subject_id;
                    $pairs[$key] = [
                        'subject_id'   => $asg->subject_id,
                        'teacher_id'   => $asg->teacher_id,
                        'subject_name' => $asg->subject->subject_name,
                        'teacher_name' => $asg->teacher->name ?: $asg->teacher->username,
                        'is_direct'    => false,
                    ];
                }
            }
        }

        return array_values($pairs);
    }

    /**
     * Generate O-Level (Form 1 - 4) Timetable strictly using registered teachers and their assigned subjects.
     */
    protected function generateOLevelTimetable(
        string $cls,
        string $schoolName,
        $schoolTeachers,
        $allAssignments,
        $classSupervisor,
        array &$teacherSchedule,
        array $days,
        int $classIndex,
        $subDiscussion,
        $subReligion,
        $subDebate,
        $subSports
    ) {
        $pairs = $this->getOLevelClassPairs($cls, $allAssignments);
        if (empty($pairs)) {
            return;
        }

        // Track weekly counts per pair index to distribute subjects evenly across the week
        $weeklyCount = array_fill(0, count($pairs), 0);

        // Rotate starting order per classIndex so Form 1, Form 2, Form 3, Form 4 don't all want the exact same teacher on Period 1
        $pairCount = count($pairs);

        foreach ($days as $dayIndex => $day) {
            $dailySubjectCount = [];
            $lastSubjectId = null;

            for ($p = 1; $p <= 10; $p++) {
                // Period 10: Discussion and Examinations (15:00 - 17:00)
                if ($p === 10) {
                    $this->assignSpecialSlot($schoolName, $cls, $day, 10, $subDiscussion->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
                    continue;
                }

                // Wednesday Periods 8 & 9: Religion (13:00 - 14:20)
                if ($day === 'Wednesday' && ($p === 8 || $p === 9)) {
                    $this->assignSpecialSlot($schoolName, $cls, $day, $p, $subReligion->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
                    continue;
                }

                // Thursday Periods 8 & 9: Debate or Subject Club (13:00 - 14:20)
                if ($day === 'Thursday' && ($p === 8 || $p === 9)) {
                    $this->assignSpecialSlot($schoolName, $cls, $day, $p, $subDebate->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
                    continue;
                }

                // Friday Periods 6 to 9: Sports and Games (11:40 - 14:20)
                if ($day === 'Friday' && ($p >= 6 && $p <= 9)) {
                    $this->assignSpecialSlot($schoolName, $cls, $day, $p, $subSports->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
                    continue;
                }

                // Select the best registered (teacher, subject) pair for this academic slot:
                // Criteria:
                // 1. Teacher MUST be free at [$day][$p] (not teaching another class)
                // 2. Prefer subjects with fewer periods today (max 2 periods/day when enough subjects exist)
                // 3. Balance weekly count across all registered subjects for this class
                $bestIdx = null;
                $bestScore = PHP_INT_MAX;

                for ($offset = 0; $offset < $pairCount; $offset++) {
                    $idx = ($classIndex * 2 + $dayIndex * 3 + $p + $offset) % $pairCount;
                    $cand = $pairs[$idx];
                    $tId = $cand['teacher_id'];
                    $sId = $cand['subject_id'];

                    // Check teacher availability at this day & period
                    if (isset($teacherSchedule[$day][$p][$tId])) {
                        continue;
                    }

                    $todayCnt = $dailySubjectCount[$sId] ?? 0;

                    // Penalize having too many periods of the same subject on the same day
                    // If todayCnt == 0 -> +0 penalty
                    // If todayCnt == 1 and it's consecutive ($lastSubjectId === $sId) -> +20 (nice double period)
                    // If todayCnt == 1 and non-consecutive -> +50
                    // If todayCnt >= 2 -> +500 (avoid >2 times a day unless necessary)
                    if ($todayCnt === 0) {
                        $dayPenalty = 0;
                    } elseif ($todayCnt === 1 && $lastSubjectId === $sId) {
                        $dayPenalty = 25;
                    } elseif ($todayCnt === 1) {
                        $dayPenalty = 60;
                    } else {
                        $dayPenalty = $todayCnt * 400;
                    }

                    $score = $dayPenalty + ($weeklyCount[$idx] * 10);

                    if ($score < $bestScore) {
                        $bestScore = $score;
                        $bestIdx = $idx;
                    }
                }

                // If all teachers directly assigned to this class are busy at [$day][$p],
                // check if another registered teacher in the school teaches one of this class's subjects and is free
                if ($bestIdx === null) {
                    $classSubjectIds = array_column($pairs, 'subject_id');
                    $altAssignment = $allAssignments->first(function ($asg) use ($classSubjectIds, $teacherSchedule, $day, $p) {
                        return in_array($asg->subject_id, $classSubjectIds)
                            && !isset($teacherSchedule[$day][$p][$asg->teacher_id]);
                    });

                    if ($altAssignment) {
                        $teacherSchedule[$day][$p][$altAssignment->teacher_id] = $cls;
                        Timetable::create([
                            'school_name'   => $schoolName,
                            'class_name'    => $cls,
                            'day_of_week'   => $day,
                            'period_number' => $p,
                            'subject_id'    => $altAssignment->subject_id,
                            'teacher_id'    => $altAssignment->teacher_id,
                        ]);
                        $dailySubjectCount[$altAssignment->subject_id] = ($dailySubjectCount[$altAssignment->subject_id] ?? 0) + 1;
                        $lastSubjectId = $altAssignment->subject_id;
                        continue;
                    }

                    // Next fallback: any registered teacher in the school who is free at [$day][$p], teaching THEIR OWN registered subject (O-Level compatible)
                    $anyFreeAsg = $allAssignments->first(function ($asg) use ($teacherSchedule, $day, $p) {
                        return !Subject::isAdvanceOnlySubject($asg->subject->subject_name)
                            && !isset($teacherSchedule[$day][$p][$asg->teacher_id]);
                    });

                    if ($anyFreeAsg) {
                        $teacherSchedule[$day][$p][$anyFreeAsg->teacher_id] = $cls;
                        Timetable::create([
                            'school_name'   => $schoolName,
                            'class_name'    => $cls,
                            'day_of_week'   => $day,
                            'period_number' => $p,
                            'subject_id'    => $anyFreeAsg->subject_id,
                            'teacher_id'    => $anyFreeAsg->teacher_id,
                        ]);
                        $dailySubjectCount[$anyFreeAsg->subject_id] = ($dailySubjectCount[$anyFreeAsg->subject_id] ?? 0) + 1;
                        $lastSubjectId = $anyFreeAsg->subject_id;
                        continue;
                    }
                }

                if ($bestIdx !== null) {
                    $chosen = $pairs[$bestIdx];
                    $teacherSchedule[$day][$p][$chosen['teacher_id']] = $cls;
                    Timetable::create([
                        'school_name'   => $schoolName,
                        'class_name'    => $cls,
                        'day_of_week'   => $day,
                        'period_number' => $p,
                        'subject_id'    => $chosen['subject_id'],
                        'teacher_id'    => $chosen['teacher_id'],
                    ]);
                    $weeklyCount[$bestIdx]++;
                    $dailySubjectCount[$chosen['subject_id']] = ($dailySubjectCount[$chosen['subject_id']] ?? 0) + 1;
                    $lastSubjectId = $chosen['subject_id'];
                }
            }
        }
    }

    /**
     * Gather valid (teacher_id, subject_id, subject_name) pairs for an Advance (Form 5 & 6) class
     * strictly from registered TeacherAssignments.
     */
    protected function getAdvanceClassPairs(string $cls, $allAssignments): array
    {
        $pairs = [];

        // 1. Direct assignments for this Advance class
        $direct = $allAssignments->filter(function ($asg) use ($cls) {
            return strcasecmp(trim($asg->class_name), trim($cls)) === 0
                && !Subject::isOLevelOnlySubject($asg->subject->subject_name);
        });

        foreach ($direct as $asg) {
            $key = $asg->teacher_id . '_' . $asg->subject_id;
            $pairs[$key] = [
                'subject_id'   => $asg->subject_id,
                'teacher_id'   => $asg->teacher_id,
                'subject_name' => $asg->subject->subject_name,
                'teacher_name' => $asg->teacher->name ?: $asg->teacher->username,
            ];
        }

        // 2. If no direct assignments for this Advance class, look for Advance assignments in the school
        // (matching combination subjects if class has a combination, or all Advance-compatible registered assignments)
        if (empty($pairs)) {
            $combination = null;
            if (preg_match('/\b(PCB|PCM|PGM|CBG|CBA|CBN|PMC|EGM|ECA|HGL|HKL|HGE|HGK|KLF|KEC)\b/i', $cls, $m)) {
                $combination = strtoupper($m[1]);
            } else {
                $firstStud = Student::where('class_name', $cls)->whereNotNull('combination')->first();
                if ($firstStud && $firstStud->combination) {
                    $combination = strtoupper(trim($firstStud->combination));
                }
            }

            $combinationMap = [
                'PCB' => ['Physics', 'Chemistry', 'Biology', 'General Studies', 'Basic Applied Mathematics'],
                'PCM' => ['Physics', 'Chemistry', 'Advanced Mathematics', 'General Studies'],
                'PGM' => ['Physics', 'Geography', 'Advanced Mathematics', 'General Studies'],
                'CBG' => ['Chemistry', 'Biology', 'Geography', 'General Studies', 'Basic Applied Mathematics'],
                'CBA' => ['Chemistry', 'Biology', 'Agriculture', 'General Studies', 'Basic Applied Mathematics'],
                'CBN' => ['Chemistry', 'Biology', 'Food and Human Nutrition', 'General Studies', 'Basic Applied Mathematics'],
                'PMC' => ['Physics', 'Advanced Mathematics', 'Computer Science', 'General Studies'],
                'EGM' => ['Economics', 'Geography', 'Advanced Mathematics', 'General Studies'],
                'ECA' => ['Economics', 'Commerce', 'Accountancy', 'General Studies', 'Basic Applied Mathematics'],
                'HKL' => ['History', 'Kiswahili', 'English', 'General Studies', 'Basic Applied Mathematics'],
                'HGL' => ['History', 'Geography', 'English', 'General Studies', 'Basic Applied Mathematics'],
                'HGE' => ['History', 'Geography', 'Economics', 'General Studies', 'Basic Applied Mathematics'],
                'HGK' => ['History', 'Geography', 'Kiswahili', 'General Studies', 'Basic Applied Mathematics'],
            ];

            $targetSubjects = ($combination && isset($combinationMap[$combination]))
                ? array_map('strtolower', $combinationMap[$combination])
                : null;

            // Prefer assignments from A-Level classes first, then any assignment for Advance-compatible subjects
            $sortedAssignments = $allAssignments->sortByDesc(function ($asg) {
                return Student::isClassALevel($asg->class_name) ? 1 : 0;
            });

            $seenSubjects = [];
            foreach ($sortedAssignments as $asg) {
                $sName = $asg->subject->subject_name;
                if (Subject::isOLevelOnlySubject($sName)) {
                    continue;
                }
                if ($targetSubjects !== null && !in_array(strtolower(trim($sName)), $targetSubjects)) {
                    continue;
                }
                if (!isset($seenSubjects[$asg->subject_id])) {
                    $seenSubjects[$asg->subject_id] = true;
                    $key = $asg->teacher_id . '_' . $asg->subject_id;
                    $pairs[$key] = [
                        'subject_id'   => $asg->subject_id,
                        'teacher_id'   => $asg->teacher_id,
                        'subject_name' => $sName,
                        'teacher_name' => $asg->teacher->name ?: $asg->teacher->username,
                    ];
                }
            }

            // If combination filter left it empty, allow any Advance-compatible registered teacher+subject
            if (empty($pairs)) {
                foreach ($sortedAssignments as $asg) {
                    $sName = $asg->subject->subject_name;
                    if (Subject::isOLevelOnlySubject($sName)) {
                        continue;
                    }
                    if (!isset($seenSubjects[$asg->subject_id])) {
                        $seenSubjects[$asg->subject_id] = true;
                        $key = $asg->teacher_id . '_' . $asg->subject_id;
                        $pairs[$key] = [
                            'subject_id'   => $asg->subject_id,
                            'teacher_id'   => $asg->teacher_id,
                            'subject_name' => $sName,
                            'teacher_name' => $asg->teacher->name ?: $asg->teacher->username,
                        ];
                    }
                }
            }
        }

        return array_values($pairs);
    }

    /**
     * Generate Advance (A-Level: Form 5 & 6) Timetable strictly using registered teachers and their subjects.
     */
    protected function generateAdvanceTimetable(
        string $cls,
        string $schoolName,
        $schoolTeachers,
        $allAssignments,
        $classSupervisor,
        array &$teacherSchedule,
        array $days,
        int $classIndex,
        $subDiscussion,
        $subGsSeminar,
        $subPracticals,
        $subSports,
        $subDebate
    ) {
        $allPairs = $this->getAdvanceClassPairs($cls, $allAssignments);
        if (empty($allPairs)) {
            return;
        }

        // Separate Principal combination subjects vs Subsidiaries (GS, BAM) if available
        $principalPairs = [];
        $subsidiaryPairs = [];
        foreach ($allPairs as $p) {
            $n = strtolower($p['subject_name']);
            if (str_contains($n, 'general studies') || str_contains($n, 'basic applied')) {
                $subsidiaryPairs[] = $p;
            } else {
                $principalPairs[] = $p;
            }
        }
        if (empty($principalPairs)) {
            $principalPairs = $allPairs;
        }

        $princIndex = ($classIndex * 2) % max(1, count($principalPairs));
        $subIndex = 0;

        foreach ($days as $day) {
            // Block 1: Double Period 1 & 2 (08:00 - 09:20)
            $this->assignAdvanceDoubleBlock($day, 1, 2, $cls, $schoolName, $principalPairs, $allPairs, $princIndex, $teacherSchedule, $allAssignments);

            // Block 2: Double Period 3 & 4 (09:20 - 10:40)
            $this->assignAdvanceDoubleBlock($day, 3, 4, $cls, $schoolName, $principalPairs, $allPairs, $princIndex, $teacherSchedule, $allAssignments);

            // Period 5: Subsidiary / Single Period (10:40 - 11:20)
            $subPool = !empty($subsidiaryPairs) ? $subsidiaryPairs : $principalPairs;
            $this->assignSingleFromPool($day, 5, $cls, $schoolName, $subPool, $allPairs, $subIndex, $teacherSchedule, $allAssignments);

            // Block 3: Periods 6 & 7 (11:40 - 13:00)
            if ($day === 'Friday') {
                // Advance: Science / Combination Laboratory Practicals
                $this->assignSpecialSlot($schoolName, $cls, $day, 6, $subPracticals->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
                $this->assignSpecialSlot($schoolName, $cls, $day, 7, $subPracticals->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
            } else {
                $this->assignAdvanceDoubleBlock($day, 6, 7, $cls, $schoolName, $principalPairs, $allPairs, $princIndex, $teacherSchedule, $allAssignments);
            }

            // Block 4: Periods 8 & 9 (13:00 - 14:20)
            if ($day === 'Wednesday') {
                $gsSub = !empty($subsidiaryPairs) ? $subsidiaryPairs[0] : null;
                $gsSubId = $gsSub ? $gsSub['subject_id'] : $subGsSeminar->id;
                $gsTrId = $gsSub ? $gsSub['teacher_id'] : $classSupervisor->id;

                $this->assignSpecialSlot($schoolName, $cls, $day, 8, $gsSubId, $gsTrId, $teacherSchedule, $schoolTeachers);
                $this->assignSpecialSlot($schoolName, $cls, $day, 9, $gsSubId, $gsTrId, $teacherSchedule, $schoolTeachers);
            } elseif ($day === 'Thursday') {
                $this->assignSpecialSlot($schoolName, $cls, $day, 8, $subDebate->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
                $this->assignSpecialSlot($schoolName, $cls, $day, 9, $subDebate->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
            } elseif ($day === 'Friday') {
                $this->assignSpecialSlot($schoolName, $cls, $day, 8, $subSports->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
                $this->assignSpecialSlot($schoolName, $cls, $day, 9, $subSports->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
            } else {
                $this->assignAdvanceDoubleBlock($day, 8, 9, $cls, $schoolName, $principalPairs, $allPairs, $princIndex, $teacherSchedule, $allAssignments);
            }

            // Period 10 (15:00 - 17:00): Advance Discussion & Examination Preparation
            $this->assignSpecialSlot($schoolName, $cls, $day, 10, $subDiscussion->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
        }
    }

    /**
     * Assign Advance Double Period Block ensuring the teacher teaches that subject and is free for both slots.
     */
    protected function assignAdvanceDoubleBlock(
        string $day,
        int $p1,
        int $p2,
        string $cls,
        string $schoolName,
        array $primaryPool,
        array $fallbackPool,
        int &$princIndex,
        array &$teacherSchedule,
        $allAssignments
    ) {
        $pools = [$primaryPool, $fallbackPool];

        foreach ($pools as $pool) {
            $count = count($pool);
            if ($count === 0) continue;

            for ($attempt = 0; $attempt < $count; $attempt++) {
                $cand = $pool[($princIndex + $attempt) % $count];
                $tId = $cand['teacher_id'];
                $sId = $cand['subject_id'];

                if (!isset($teacherSchedule[$day][$p1][$tId]) && !isset($teacherSchedule[$day][$p2][$tId])) {
                    $teacherSchedule[$day][$p1][$tId] = $cls;
                    $teacherSchedule[$day][$p2][$tId] = $cls;

                    Timetable::create([
                        'school_name'   => $schoolName,
                        'class_name'    => $cls,
                        'day_of_week'   => $day,
                        'period_number' => $p1,
                        'subject_id'    => $sId,
                        'teacher_id'    => $tId,
                    ]);
                    Timetable::create([
                        'school_name'   => $schoolName,
                        'class_name'    => $cls,
                        'day_of_week'   => $day,
                        'period_number' => $p2,
                        'subject_id'    => $sId,
                        'teacher_id'    => $tId,
                    ]);

                    $princIndex = ($princIndex + $attempt + 1) % $count;
                    return;
                }
            }
        }

        // If no single teacher is free for both periods simultaneously, schedule p1 and p2 individually
        // using only registered teacher-subject pairs
        $this->assignSingleFromPool($day, $p1, $cls, $schoolName, $primaryPool, $fallbackPool, $princIndex, $teacherSchedule, $allAssignments);
        $this->assignSingleFromPool($day, $p2, $cls, $schoolName, $primaryPool, $fallbackPool, $princIndex, $teacherSchedule, $allAssignments);
    }

    /**
     * Assign a single period strictly from registered teacher-subject pairs.
     */
    protected function assignSingleFromPool(
        string $day,
        int $p,
        string $cls,
        string $schoolName,
        array $primaryPool,
        array $fallbackPool,
        int &$cursor,
        array &$teacherSchedule,
        $allAssignments
    ) {
        $pools = [$primaryPool, $fallbackPool];

        foreach ($pools as $pool) {
            $count = count($pool);
            if ($count === 0) continue;

            for ($attempt = 0; $attempt < $count; $attempt++) {
                $cand = $pool[($cursor + $attempt) % $count];
                $tId = $cand['teacher_id'];
                $sId = $cand['subject_id'];

                if (!isset($teacherSchedule[$day][$p][$tId])) {
                    $teacherSchedule[$day][$p][$tId] = $cls;
                    Timetable::create([
                        'school_name'   => $schoolName,
                        'class_name'    => $cls,
                        'day_of_week'   => $day,
                        'period_number' => $p,
                        'subject_id'    => $sId,
                        'teacher_id'    => $tId,
                    ]);
                    $cursor = ($cursor + $attempt + 1) % $count;
                    return;
                }
            }
        }

        // Fallback: any free registered teacher in the school teaching their own Advance-compatible subject
        $anyFreeAsg = $allAssignments->first(function ($asg) use ($teacherSchedule, $day, $p) {
            return !Subject::isOLevelOnlySubject($asg->subject->subject_name)
                && !isset($teacherSchedule[$day][$p][$asg->teacher_id]);
        });

        if ($anyFreeAsg) {
            $teacherSchedule[$day][$p][$anyFreeAsg->teacher_id] = $cls;
            Timetable::create([
                'school_name'   => $schoolName,
                'class_name'    => $cls,
                'day_of_week'   => $day,
                'period_number' => $p,
                'subject_id'    => $anyFreeAsg->subject_id,
                'teacher_id'    => $anyFreeAsg->teacher_id,
            ]);
        }
    }

    /**
     * Assign a special activity period avoiding teacher collision across classes.
     */
    protected function assignSpecialSlot(
        string $schoolName,
        string $cls,
        string $day,
        int $p,
        int $subjectId,
        int $preferredTeacherId,
        array &$teacherSchedule,
        $schoolTeachers
    ) {
        $chosenTeacherId = $preferredTeacherId;

        // If preferred teacher is already busy with another class at this period, pick a free teacher
        if (isset($teacherSchedule[$day][$p][$chosenTeacherId])) {
            foreach ($schoolTeachers as $altTeacher) {
                if (!isset($teacherSchedule[$day][$p][$altTeacher->id])) {
                    $chosenTeacherId = $altTeacher->id;
                    break;
                }
            }
        }

        $teacherSchedule[$day][$p][$chosenTeacherId] = $cls;

        Timetable::create([
            'school_name'   => $schoolName,
            'class_name'    => $cls,
            'day_of_week'   => $day,
            'period_number' => $p,
            'subject_id'    => $subjectId,
            'teacher_id'    => $chosenTeacherId,
        ]);
    }

    public function saveSlot(Request $request)
    {
        $request->validate([
            'class_name'    => 'required|string',
            'day_of_week'   => 'required|string',
            'period_number' => 'required|integer|min:1|max:10',
            'subject_id'    => 'required|exists:subjects,id',
            'teacher_id'    => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $schoolName = $this->resolveSchoolName($user, $request);

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
            ->where(function ($q) use ($schoolName) {
                $q->where('school_name', $schoolName)
                  ->orWhereNull('school_name')
                  ->orWhere('school_name', '');
            })
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
        $schoolName = $this->resolveSchoolName($user, $request);

        Timetable::where(function ($q) use ($schoolName) {
                $q->where('school_name', $schoolName)
                  ->orWhereNull('school_name')
                  ->orWhere('school_name', '');
            })
            ->where('class_name', $request->class_name)
            ->where('day_of_week', $request->day_of_week)
            ->where('period_number', $request->period_number)
            ->delete();

        return back()->with('success', '✔️ Timetable slot cleared!');
    }
}
