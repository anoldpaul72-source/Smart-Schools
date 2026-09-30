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

        // Fetch all valid teacher assignments for this school (only academic subjects assigned to teachers)
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
            ->get()
            ->filter(function ($asg) {
                return $asg->teacher && $asg->subject && Subject::isAcademicSubject($asg->subject->subject_name);
            })
            ->values();

        if ($schoolAssignments->isEmpty()) {
            $schoolAssignments = TeacherAssignment::with(['teacher', 'subject'])
                ->whereHas('teacher', function ($q) {
                    $q->whereIn('role', ['Teacher', 'Academic Master']);
                })
                ->whereHas('subject')
                ->get()
                ->filter(function ($asg) {
                    return $asg->teacher && $asg->subject && Subject::isAcademicSubject($asg->subject->subject_name);
                })
                ->values();
        }

        // Auto-sync check: If timetable has stale/invalid teacher-subject assignments or unassigned subjects
        // (e.g. Sports and Games, Debate, Religion, or wrong teacher for a subject in a class), regenerate automatically!
        if ($schoolAssignments->isNotEmpty() && $this->isTimetableOutOfSync($schoolName, $schoolAssignments)) {
            $this->regenerateTimetableForSchool($schoolName, $allTeachers, $schoolAssignments);
        }

        // Assignments specifically for the selected class
        $classAssignments = $schoolAssignments->filter(function ($asg) use ($selectedClass) {
            return strcasecmp(trim($asg->class_name), trim($selectedClass)) === 0;
        })->values();

        $assignedTeacherIds = $classAssignments->pluck('teacher_id')->unique()->toArray();

        // Filter subjects for modal dropdown: show subjects assigned to this class first, or academic subjects for this level
        $rawSubjects = Subject::academic()->orderBy('subject_name')->get();
        $allSubjects = $rawSubjects->filter(function ($sub) use ($isALevel) {
            $name = $sub->subject_name;
            if ($isALevel) {
                return !Subject::isOLevelOnlySubject($name);
            } else {
                return !Subject::isAdvanceOnlySubject($name);
            }
        })->values();

        // Build subject -> teacher mapping for the JS modal auto-select
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

        // Valid assignment lookup for this class: [subject_id => teacher_id]
        $validClassPairs = [];
        foreach ($classAssignments as $asg) {
            $validClassPairs[$asg->subject_id . '_' . $asg->teacher_id] = true;
        }

        $timetableMatrix = [];
        foreach ($rows as $row) {
            // Do not display any slot whose subject is non-academic or doesn't belong to this class's assigned teachers
            if (!$row->subject || !$row->teacher || !Subject::isAcademicSubject($row->subject->subject_name)) {
                continue;
            }
            if (!empty($validClassPairs) && !isset($validClassPairs[$row->subject_id . '_' . $row->teacher_id])) {
                continue;
            }
            if (empty($validClassPairs)) {
                continue;
            }

            $timetableMatrix[$row->day_of_week][$row->period_number] = [
                'subject'    => $row->subject->subject_name,
                'subject_id' => $row->subject_id,
                'teacher'    => $row->teacher->name ?: $row->teacher->username,
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

    /**
     * Check if the current Timetable table has any rows that do not match TeacherAssignment
     * (e.g., wrong teacher for a subject, unassigned subjects like Sports/Debate/Religion, or missing classes).
     */
    protected function isTimetableOutOfSync(string $schoolName, $schoolAssignments): bool
    {
        $existingRows = Timetable::where(function ($q) use ($schoolName) {
                $q->where('school_name', $schoolName)
                  ->orWhereNull('school_name')
                  ->orWhere('school_name', '');
            })
            ->with('subject')
            ->get();

        if ($existingRows->isEmpty()) {
            return true;
        }

        // Build map of valid (class_lower | subject_id | teacher_id) from TeacherAssignment
        $validTriples = [];
        $classesWithAssignments = [];
        foreach ($schoolAssignments as $asg) {
            $cKey = strtolower(trim($asg->class_name));
            $validTriples["{$cKey}|{$asg->subject_id}|{$asg->teacher_id}"] = true;
            $classesWithAssignments[$cKey] = true;
        }

        $scheduledClasses = [];
        foreach ($existingRows as $row) {
            if (!$row->subject || !Subject::isAcademicSubject($row->subject->subject_name)) {
                // Found non-academic/unassigned subject like Religion, Sports, Debate, Discussion
                return true;
            }
            $cKey = strtolower(trim($row->class_name));
            $triple = "{$cKey}|{$row->subject_id}|{$row->teacher_id}";
            if (!isset($validTriples[$triple])) {
                // Found a slot where the teacher or subject is NOT assigned to this class in TeacherAssignment!
                return true;
            }
            $scheduledClasses[$cKey] = true;
        }

        // Ensure every class that has teacher assignments has slots in the timetable
        foreach (array_keys($classesWithAssignments) as $cKey) {
            if (!isset($scheduledClasses[$cKey])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Public helper so AdminController can trigger timetable regeneration whenever teachers/assignments change.
     */
    public function syncTimetable(?string $schoolName = null): void
    {
        $resolvedSchool = $schoolName ?: $this->resolveSchoolName(Auth::user());

        $schoolTeachers = User::whereIn('role', ['Teacher', 'Academic Master'])
            ->with(['teacherAssignments.subject'])
            ->get();

        $allAssignments = TeacherAssignment::with(['teacher', 'subject'])
            ->whereHas('teacher', function ($q) {
                $q->whereIn('role', ['Teacher', 'Academic Master']);
            })
            ->whereHas('subject')
            ->get()
            ->filter(function ($asg) {
                return $asg->teacher && $asg->subject && Subject::isAcademicSubject($asg->subject->subject_name);
            })
            ->values();

        if ($schoolTeachers->isNotEmpty() && $allAssignments->isNotEmpty()) {
            $this->regenerateTimetableForSchool($resolvedSchool, $schoolTeachers, $allAssignments);
        }
    }

    public function autoGenerate(Request $request)
    {
        $user = Auth::user();
        $schoolName = $this->resolveSchoolName($user, $request);

        try {
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
                return back()->with('error', '❌ Hakuna walimu waliosajiliwa kwenye mfumo. Tafadhali sajili walimu na masomo yao kwanza.');
            }

            $allAssignments = TeacherAssignment::with(['teacher', 'subject'])
                ->whereIn('teacher_id', $schoolTeachers->pluck('id'))
                ->whereHas('subject')
                ->get()
                ->filter(function ($asg) {
                    return $asg->teacher && $asg->subject && Subject::isAcademicSubject($asg->subject->subject_name);
                })
                ->values();

            if ($allAssignments->isEmpty()) {
                return back()->with('error', '❌ Walimu wamesajiliwa lakini hawajapangiwa masomo wanayofundisha! Tafadhali nenda User Management uwawekee walimu masomo na madarasa wanayofundisha.');
            }

            $scheduledClassesCount = $this->regenerateTimetableForSchool($schoolName, $schoolTeachers, $allAssignments);

            $uniqueTeachersUsed = $allAssignments->pluck('teacher_id')->unique()->count();
            $uniqueSubjectsUsed = $allAssignments->pluck('subject_id')->unique()->count();

            return back()->with('success', "✔️ Ratiba ya shule imetengenezwa kikamilifu kwa kutumia walimu {$uniqueTeachersUsed} waliosajiliwa na masomo {$uniqueSubjectsUsed} wanayofundisha kwa madarasa {$scheduledClassesCount} (masomo yasiyo na mwalimu hayajawekwa)!");
        } catch (\Exception $e) {
            return back()->with('error', 'Hitilafu wakati wa kutengeneza ratiba: ' . $e->getMessage());
        }
    }

    /**
     * Core timetable generator:
     * - Strictly schedules ONLY classes that have TeacherAssignments.
     * - For each class, ONLY schedules the exact (subject, teacher) pairs assigned to that class.
     * - Never schedules unassigned subjects (no Religion, Sports and Games, Debate, Discussion, etc.).
     * - Prevents teacher clashes across classes at the same day & period.
     */
    protected function regenerateTimetableForSchool(string $schoolName, $schoolTeachers, $allAssignments): int
    {
        return DB::transaction(function () use ($schoolName, $allAssignments) {
            // 1. Clear existing timetable for this school (and any null school_name rows)
            Timetable::where(function ($q) use ($schoolName) {
                $q->where('school_name', $schoolName)
                  ->orWhereNull('school_name')
                  ->orWhere('school_name', '');
            })->delete();

            // 2. Identify classes that actually have assigned teachers & subjects
            $classes = $allAssignments->pluck('class_name')
                ->map(fn($c) => trim($c))
                ->unique()
                ->filter()
                ->values()
                ->all();

            // Sort: O-Level first, then Advance
            usort($classes, function ($a, $b) {
                $advA = Student::isClassALevel($a);
                $advB = Student::isClassALevel($b);
                if ($advA !== $advB) return $advA ? 1 : -1;
                return strnatcasecmp($a, $b);
            });

            // Teacher clash collision detector: [day][period][teacher_id] = class_name
            $teacherSchedule = [];
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $scheduledClassesCount = 0;

            foreach ($classes as $classIndex => $cls) {
                // Strictly get ONLY direct assignments for this specific class
                $pairs = $this->getDirectClassPairs($cls, $allAssignments);
                if (empty($pairs)) {
                    continue;
                }

                $isALevel = Student::isClassALevel($cls);

                if ($isALevel) {
                    $this->generateAdvanceTimetable(
                        $cls,
                        $schoolName,
                        $pairs,
                        $teacherSchedule,
                        $days,
                        $classIndex
                    );
                } else {
                    $this->generateOLevelTimetable(
                        $cls,
                        $schoolName,
                        $pairs,
                        $teacherSchedule,
                        $days,
                        $classIndex
                    );
                }
                $scheduledClassesCount++;
            }

            return $scheduledClassesCount;
        });
    }

    /**
     * Gather valid (teacher_id, subject_id, subject_name) pairs strictly assigned to $cls in TeacherAssignment.
     * No unassigned subjects and no teachers from other classes!
     */
    protected function getDirectClassPairs(string $cls, $allAssignments): array
    {
        $pairs = [];

        $direct = $allAssignments->filter(function ($asg) use ($cls) {
            return strcasecmp(trim($asg->class_name), trim($cls)) === 0
                && $asg->subject
                && $asg->teacher
                && Subject::isAcademicSubject($asg->subject->subject_name);
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

        return array_values($pairs);
    }

    /**
     * Generate O-Level (Form 1 - 4) Timetable strictly using ONLY the teachers and subjects assigned to $cls.
     * Unassigned subjects (such as Sports and Games, Debate, Religion, Discussion) are NEVER added.
     */
    protected function generateOLevelTimetable(
        string $cls,
        string $schoolName,
        array $pairs,
        array &$teacherSchedule,
        array $days,
        int $classIndex
    ) {
        $pairCount = count($pairs);
        if ($pairCount === 0) {
            return;
        }

        // Track weekly counts per pair index to distribute assigned subjects evenly across the week
        $weeklyCount = array_fill(0, $pairCount, 0);

        foreach ($days as $dayIndex => $day) {
            $dailySubjectCount = [];
            $lastSubjectId = null;

            // Schedule Periods 1 to 10 strictly with this class's assigned (subject, teacher) pairs
            for ($p = 1; $p <= 10; $p++) {
                $bestIdx = null;
                $bestScore = PHP_INT_MAX;

                for ($offset = 0; $offset < $pairCount; $offset++) {
                    $idx = ($classIndex * 2 + $dayIndex * 3 + $p + $offset) % $pairCount;
                    $cand = $pairs[$idx];
                    $tId = $cand['teacher_id'];
                    $sId = $cand['subject_id'];

                    // Teacher MUST be free at this day & period (no clash with another class)
                    if (isset($teacherSchedule[$day][$p][$tId])) {
                        continue;
                    }

                    $todayCnt = $dailySubjectCount[$sId] ?? 0;

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

                // Only schedule if a teacher actually assigned to this class & subject is free
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
     * Generate Advance (A-Level: Form 5 & 6) Timetable strictly using ONLY the teachers and subjects assigned to $cls.
     * Unassigned subjects are NEVER added.
     */
    protected function generateAdvanceTimetable(
        string $cls,
        string $schoolName,
        array $allPairs,
        array &$teacherSchedule,
        array $days,
        int $classIndex
    ) {
        if (empty($allPairs)) {
            return;
        }

        // Separate Principal combination subjects vs Subsidiaries (GS, BAM) if assigned to this class
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
            $this->assignAdvanceDoubleBlock($day, 1, 2, $cls, $schoolName, $principalPairs, $allPairs, $princIndex, $teacherSchedule);

            // Block 2: Double Period 3 & 4 (09:20 - 10:40)
            $this->assignAdvanceDoubleBlock($day, 3, 4, $cls, $schoolName, $principalPairs, $allPairs, $princIndex, $teacherSchedule);

            // Period 5: Single Period (10:40 - 11:20)
            $subPool = !empty($subsidiaryPairs) ? $subsidiaryPairs : $principalPairs;
            $this->assignSingleFromPool($day, 5, $cls, $schoolName, $subPool, $allPairs, $subIndex, $teacherSchedule);

            // Block 3: Double Period 6 & 7 (11:40 - 13:00)
            $this->assignAdvanceDoubleBlock($day, 6, 7, $cls, $schoolName, $principalPairs, $allPairs, $princIndex, $teacherSchedule);

            // Block 4: Double Period 8 & 9 (13:00 - 14:20)
            $this->assignAdvanceDoubleBlock($day, 8, 9, $cls, $schoolName, $principalPairs, $allPairs, $princIndex, $teacherSchedule);

            // Period 10: Single Period (15:00 - 17:00)
            $this->assignSingleFromPool($day, 10, $cls, $schoolName, $principalPairs, $allPairs, $princIndex, $teacherSchedule);
        }
    }

    /**
     * Assign Advance Double Period Block ensuring the teacher is assigned to that subject in $cls and is free for both slots.
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
        array &$teacherSchedule
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
        // strictly from this class's assigned (teacher, subject) pairs
        $this->assignSingleFromPool($day, $p1, $cls, $schoolName, $primaryPool, $fallbackPool, $princIndex, $teacherSchedule);
        $this->assignSingleFromPool($day, $p2, $cls, $schoolName, $primaryPool, $fallbackPool, $princIndex, $teacherSchedule);
    }

    /**
     * Assign a single period strictly from this class's registered teacher-subject pairs.
     */
    protected function assignSingleFromPool(
        string $day,
        int $p,
        string $cls,
        string $schoolName,
        array $primaryPool,
        array $fallbackPool,
        int &$cursor,
        array &$teacherSchedule
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
