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
use App\Models\School;

class TimetableController extends Controller
{
    /**
     * Gather all registered school names across the system.
     */
    protected function getAllSchools(): array
    {
        $fromSchools = School::orderBy('school_name')->pluck('school_name');
        $fromUsers = User::whereNotNull('school_name')->where('school_name', '!=', '')->pluck('school_name');
        $fromAssignments = TeacherAssignment::whereNotNull('school_name')->where('school_name', '!=', '')->pluck('school_name');
        $fromStudents = Student::whereNotNull('school_name')->where('school_name', '!=', '')->pluck('school_name');
        $fromTimetables = Timetable::whereNotNull('school_name')->where('school_name', '!=', '')->pluck('school_name');

        $all = $fromSchools
            ->merge($fromUsers)
            ->merge($fromAssignments)
            ->merge($fromStudents)
            ->merge($fromTimetables)
            ->map(fn($s) => trim($s))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($all)) {
            $all = ['Kome Secondary School'];
        }

        return $all;
    }

    /**
     * Resolve school name scope for queries.
     * - School-bound users (Teacher, Academic Master, Head of School, Parent, etc.) with a school_name use their own school,
     *   unless an Admin or Guest selects a specific school via request.
     */
    protected function resolveSchoolName($user, Request $request = null): string
    {
        $canSwitchSchool = !$user || $user->role === 'Admin' || empty($user->school_name);

        if ($canSwitchSchool && $request && $request->filled('school_name')) {
            return trim($request->input('school_name'));
        }
        if ($user && !empty($user->school_name)) {
            return trim($user->school_name);
        }
        if ($request && $request->filled('school_name')) {
            return trim($request->input('school_name'));
        }

        $firstSchool = School::orderBy('id')->value('school_name');
        if ($firstSchool) {
            return trim($firstSchool);
        }

        $asgSchool = TeacherAssignment::whereNotNull('school_name')->where('school_name', '!=', '')->value('school_name');
        if ($asgSchool) {
            return trim($asgSchool);
        }

        $userSchool = User::whereIn('role', ['Teacher', 'Academic Master', 'Head of School', 'Headmaster', 'Headmistress'])
            ->whereNotNull('school_name')
            ->where('school_name', '!=', '')
            ->value('school_name');
        if ($userSchool) {
            return trim($userSchool);
        }

        return 'Kome Secondary School';
    }

    /**
     * Scope query strictly to a specific school.
     * Legacy null/empty school_name rows are only included if there is a single school in the system.
     */
    protected function applySchoolScope($query, string $schoolName, bool $includeNullForSingleSchool = false)
    {
        return $query->where(function ($q) use ($schoolName, $includeNullForSingleSchool) {
            $q->where('school_name', $schoolName)
              ->orWhereRaw('LOWER(TRIM(school_name)) = ?', [strtolower(trim($schoolName))]);
            if ($includeNullForSingleSchool) {
                $q->orWhereNull('school_name')->orWhere('school_name', '');
            }
        });
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $allSchools = $this->getAllSchools();
        $schoolName = $this->resolveSchoolName($user, $request);
        $isSingleSchoolSystem = count($allSchools) <= 1;

        // Ensure school record exists in `schools` table so settings can be stored per school
        School::firstOrCreate(['school_name' => $schoolName]);

        $timetableConfig = School::getTimetableConfig($schoolName);
        $periodsPerDay = $timetableConfig['periods_per_day'];
        $breakfastTime = $timetableConfig['breakfast_time'];
        $breakfastAfterPeriod = $timetableConfig['breakfast_after_period'];
        $lunchTime = $timetableConfig['lunch_time'];
        $lunchAfterPeriod = $timetableConfig['lunch_after_period'];
        $classStartTime = $timetableConfig['class_start_time'];
        $periodDuration = $timetableConfig['period_duration'];
        $periodSlots = $timetableConfig['period_slots'];

        $userRole = $user ? $user->role : 'Guest';
        $isAcademic = in_array($userRole, ['Academic Master', 'Head of School', 'Head Of School', 'Headmaster', 'Headmistress', 'Admin']);
        $canSwitchSchool = !$user || $userRole === 'Admin' || empty($user->school_name);

        // Gather all classes for this specific school
        $standardClasses = collect(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6']);
        $assignedClasses = $this->applySchoolScope(TeacherAssignment::query(), $schoolName, $isSingleSchoolSystem)->pluck('class_name');
        $studentClasses = $this->applySchoolScope(Student::query(), $schoolName, $isSingleSchoolSystem)->pluck('class_name');
        $timetableClasses = $this->applySchoolScope(Timetable::query(), $schoolName, $isSingleSchoolSystem)->pluck('class_name');

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

        // Fetch registered teachers strictly in this school
        $allTeachers = $this->applySchoolScope(
                User::whereIn('role', ['Teacher', 'Academic Master'])->with(['teacherAssignments.subject']),
                $schoolName,
                $isSingleSchoolSystem
            )
            ->orderBy('name')
            ->get();

        // Fetch all valid teacher assignments strictly for this school (only academic subjects assigned to teachers of this school)
        $schoolAssignments = $this->applySchoolScope(
                TeacherAssignment::with(['teacher', 'subject'])
                    ->whereHas('teacher', function ($q) use ($schoolName, $isSingleSchoolSystem) {
                        $q->whereIn('role', ['Teacher', 'Academic Master']);
                        $this->applySchoolScope($q, $schoolName, $isSingleSchoolSystem);
                    })
                    ->whereHas('subject'),
                $schoolName,
                $isSingleSchoolSystem
            )
            ->get()
            ->filter(function ($asg) {
                return $asg->teacher && $asg->subject && Subject::isAcademicSubject($asg->subject->subject_name);
            })
            ->values();

        // Auto-sync check: If timetable has stale/invalid teacher-subject assignments, unassigned subjects,
        // or period count mismatch for this school, regenerate automatically!
        if ($schoolAssignments->isNotEmpty() && $this->isTimetableOutOfSync($schoolName, $schoolAssignments, $periodsPerDay, $isSingleSchoolSystem)) {
            $this->regenerateTimetableForSchool($schoolName, $allTeachers, $schoolAssignments, $periodsPerDay, $breakfastAfterPeriod, $lunchAfterPeriod, $isSingleSchoolSystem);
        }

        // Assignments specifically for the selected class in this school
        $classAssignments = $schoolAssignments->filter(function ($asg) use ($selectedClass) {
            return strcasecmp(trim($asg->class_name), trim($selectedClass)) === 0;
        })->values();

        $assignedTeacherIds = $classAssignments->pluck('teacher_id')->unique()->toArray();

        // Filter subjects for modal dropdown: academic subjects for this level
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

        // Fetch timetable slots for selected class and school (up to $periodsPerDay)
        $rows = $this->applySchoolScope(
                Timetable::where(function ($q) use ($selectedClass) {
                    $q->where('class_name', $selectedClass)
                      ->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($selectedClass))]);
                })->where('period_number', '<=', $periodsPerDay),
                $schoolName,
                $isSingleSchoolSystem
            )
            ->with(['subject', 'teacher'])
            ->get();

        // Valid assignment lookup for this class: [subject_id_teacher_id => true]
        $validClassPairs = [];
        foreach ($classAssignments as $asg) {
            $validClassPairs[$asg->subject_id . '_' . $asg->teacher_id] = true;
        }

        $timetableMatrix = [];
        foreach ($rows as $row) {
            if (!empty($row->event_name)) {
                $timetableMatrix[$row->day_of_week][$row->period_number] = [
                    'is_event'   => true,
                    'event_name' => $row->event_name,
                    'subject'    => $row->event_name,
                    'subject_id' => null,
                    'teacher'    => $row->teacher ? ($row->teacher->name ?: $row->teacher->username) : null,
                    'teacher_id' => $row->teacher_id,
                ];
                continue;
            }

            // Do not display any slot whose subject is non-academic
            if (!$row->subject || !$row->teacher || !Subject::isAcademicSubject($row->subject->subject_name)) {
                continue;
            }

            $timetableMatrix[$row->day_of_week][$row->period_number] = [
                'is_event'   => false,
                'event_name' => null,
                'subject'    => $row->subject->subject_name,
                'subject_id' => $row->subject_id,
                'teacher'    => $row->teacher->name ?: $row->teacher->username,
                'teacher_id' => $row->teacher_id,
            ];
        }

        return view('timetable.index', compact(
            'schoolName',
            'allSchools',
            'canSwitchSchool',
            'userRole',
            'isAcademic',
            'classes',
            'selectedClass',
            'isALevel',
            'days',
            'periodsPerDay',
            'breakfastTime',
            'breakfastAfterPeriod',
            'lunchTime',
            'lunchAfterPeriod',
            'classStartTime',
            'periodDuration',
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
     * Save school-specific timetable settings (School Name, Periods per Day, Breakfast Time, Lunch Time, etc.)
     */
    public function saveSettings(Request $request)
    {
        $user = Auth::user();
        $userRole = $user ? $user->role : 'Guest';
        $isAcademic = in_array($userRole, ['Academic Master', 'Head of School', 'Head Of School', 'Headmaster', 'Headmistress', 'Admin']);

        if (!$isAcademic) {
            return back()->with('error', '❌ ' . __('Please login as Academic Master, Head of School, or Admin to edit timetable settings.'));
        }

        $request->validate([
            'school_name'            => 'required|string|max:150',
            'periods_per_day'        => 'required|integer|min:4|max:14',
            'breakfast_time'         => 'required|string|max:50',
            'breakfast_after_period' => 'required|integer|min:0|max:14',
            'lunch_time'             => 'required|string|max:50',
            'lunch_after_period'     => 'required|integer|min:0|max:14',
            'class_start_time'       => 'nullable|string|max:20',
            'period_duration'        => 'nullable|integer|min:20|max:120',
        ]);

        $schoolName = $this->resolveSchoolName($user, $request);
        $periodsPerDay = (int) $request->input('periods_per_day', 10);
        $breakfastTime = trim($request->input('breakfast_time', '11:20 - 11:40'));
        $breakfastAfterPeriod = min($periodsPerDay, max(0, (int) $request->input('breakfast_after_period', 5)));
        $lunchTime = trim($request->input('lunch_time', '14:20 - 15:00'));
        $lunchAfterPeriod = min($periodsPerDay, max(0, (int) $request->input('lunch_after_period', 9)));
        $classStartTime = trim($request->input('class_start_time', '08:00')) ?: '08:00';
        $periodDuration = (int) ($request->input('period_duration', 40) ?: 40);

        $school = School::where('school_name', $schoolName)
            ->orWhereRaw('LOWER(TRIM(school_name)) = ?', [strtolower(trim($schoolName))])
            ->first();

        if (!$school) {
            $school = new School(['school_name' => $schoolName]);
        }

        // Allow renaming the school if new_school_name is provided and different
        if ($request->filled('new_school_name')) {
            $newSchoolName = trim($request->input('new_school_name'));
            if ($newSchoolName !== '' && strcasecmp($newSchoolName, $schoolName) !== 0) {
                $exists = School::whereRaw('LOWER(TRIM(school_name)) = ?', [strtolower($newSchoolName)])
                    ->where('id', '!=', $school->id ?? 0)
                    ->exists();
                if (!$exists) {
                    // Update school_name references for this school
                    User::where('school_name', $schoolName)->update(['school_name' => $newSchoolName]);
                    TeacherAssignment::where('school_name', $schoolName)->update(['school_name' => $newSchoolName]);
                    Student::where('school_name', $schoolName)->update(['school_name' => $newSchoolName]);
                    Timetable::where('school_name', $schoolName)->update(['school_name' => $newSchoolName]);
                    $school->school_name = $newSchoolName;
                    $schoolName = $newSchoolName;
                }
            }
        }

        $school->periods_per_day = $periodsPerDay;
        $school->breakfast_time = $breakfastTime;
        $school->breakfast_after_period = $breakfastAfterPeriod;
        $school->lunch_time = $lunchTime;
        $school->lunch_after_period = $lunchAfterPeriod;
        $school->class_start_time = $classStartTime;
        $school->period_duration = $periodDuration;
        $school->save();

        // Remove any timetable slots exceeding the new periods_per_day for this school
        $isSingleSchoolSystem = count($this->getAllSchools()) <= 1;
        $this->applySchoolScope(Timetable::query(), $schoolName, $isSingleSchoolSystem)
            ->where('period_number', '>', $periodsPerDay)
            ->delete();

        // Regenerate timetable for this school so all periods 1..$periodsPerDay are properly scheduled
        $this->syncTimetable($schoolName);

        return redirect()->route('timetable.index', [
            'school_name' => $schoolName,
            'class_name'  => $request->input('class_name', 'Form 1'),
        ])->with('success', '✔️ ' . __('Timetable settings for :school updated successfully! (:periods periods per day, Breakfast: :breakfast, Lunch: :lunch)', [
            'school'    => $schoolName,
            'periods'   => $periodsPerDay,
            'breakfast' => $breakfastTime,
            'lunch'     => $lunchTime,
        ]));
    }

    /**
     * Check if the current Timetable table has any rows that do not match TeacherAssignment
     * or if the configured number of periods per day changed.
     */
    protected function isTimetableOutOfSync(string $schoolName, $schoolAssignments, int $periodsPerDay = 10, bool $isSingleSchoolSystem = false): bool
    {
        $existingRows = $this->applySchoolScope(Timetable::query(), $schoolName, $isSingleSchoolSystem)
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
        $maxPeriodFound = 0;
        foreach ($existingRows as $row) {
            if ($row->period_number > $periodsPerDay) {
                return true;
            }
            if ($row->period_number > $maxPeriodFound) {
                $maxPeriodFound = $row->period_number;
            }
            $cKey = strtolower(trim($row->class_name));
            // Custom Event slots (e.g. Sports and Games) are always valid
            if (!empty($row->event_name)) {
                $scheduledClasses[$cKey] = true;
                continue;
            }
            if (!$row->subject || !Subject::isAcademicSubject($row->subject->subject_name)) {
                return true;
            }
            $scheduledClasses[$cKey] = true;
        }

        if ($maxPeriodFound < $periodsPerDay) {
            return true;
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
        $isSingleSchoolSystem = count($this->getAllSchools()) <= 1;
        $config = School::getTimetableConfig($resolvedSchool);

        $schoolTeachers = $this->applySchoolScope(
                User::whereIn('role', ['Teacher', 'Academic Master'])->with(['teacherAssignments.subject']),
                $resolvedSchool,
                $isSingleSchoolSystem
            )->get();

        $allAssignments = $this->applySchoolScope(
                TeacherAssignment::with(['teacher', 'subject'])
                    ->whereHas('teacher', function ($q) use ($resolvedSchool, $isSingleSchoolSystem) {
                        $q->whereIn('role', ['Teacher', 'Academic Master']);
                        $this->applySchoolScope($q, $resolvedSchool, $isSingleSchoolSystem);
                    })
                    ->whereHas('subject'),
                $resolvedSchool,
                $isSingleSchoolSystem
            )
            ->get()
            ->filter(function ($asg) {
                return $asg->teacher && $asg->subject && Subject::isAcademicSubject($asg->subject->subject_name);
            })
            ->values();

        if ($schoolTeachers->isNotEmpty() && $allAssignments->isNotEmpty()) {
            $this->regenerateTimetableForSchool(
                $resolvedSchool,
                $schoolTeachers,
                $allAssignments,
                $config['periods_per_day'],
                $config['breakfast_after_period'],
                $config['lunch_after_period'],
                $isSingleSchoolSystem
            );
        }
    }

    public function autoGenerate(Request $request)
    {
        $user = Auth::user();
        $schoolName = $this->resolveSchoolName($user, $request);
        $isSingleSchoolSystem = count($this->getAllSchools()) <= 1;
        $config = School::getTimetableConfig($schoolName);

        try {
            $schoolTeachers = $this->applySchoolScope(
                    User::whereIn('role', ['Teacher', 'Academic Master'])->with(['teacherAssignments.subject']),
                    $schoolName,
                    $isSingleSchoolSystem
                )->get();

            if ($schoolTeachers->isEmpty()) {
                return back()->with('error', '❌ ' . __('No teachers are registered for :school. Please register teachers and their subjects first.', ['school' => $schoolName]));
            }

            $allAssignments = $this->applySchoolScope(
                    TeacherAssignment::with(['teacher', 'subject'])
                        ->whereIn('teacher_id', $schoolTeachers->pluck('id'))
                        ->whereHas('subject'),
                    $schoolName,
                    $isSingleSchoolSystem
                )
                ->get()
                ->filter(function ($asg) {
                    return $asg->teacher && $asg->subject && Subject::isAcademicSubject($asg->subject->subject_name);
                })
                ->values();

            if ($allAssignments->isEmpty()) {
                return back()->with('error', '❌ ' . __('Teachers are registered but have not been assigned any subjects! Please go to User Management to assign subjects and classes to teachers.'));
            }

            $scheduledClassesCount = $this->regenerateTimetableForSchool(
                $schoolName,
                $schoolTeachers,
                $allAssignments,
                $config['periods_per_day'],
                $config['breakfast_after_period'],
                $config['lunch_after_period'],
                $isSingleSchoolSystem
            );

            $uniqueTeachersUsed = $allAssignments->pluck('teacher_id')->unique()->count();
            $uniqueSubjectsUsed = $allAssignments->pluck('subject_id')->unique()->count();

            return back()->with('success', '✔️ ' . __('School timetable generated successfully using :teachers registered teachers and :subjects assigned subjects across :classes classes!', [
                'teachers' => $uniqueTeachersUsed,
                'subjects' => $uniqueSubjectsUsed,
                'classes'  => $scheduledClassesCount,
            ]));
        } catch (\Exception $e) {
            return back()->with('error', __('Error generating timetable: ') . $e->getMessage());
        }
    }

    /**
     * Core timetable generator:
     * - Strictly schedules ONLY classes that have TeacherAssignments in $schoolName.
     * - Preserves any custom Event slots (where event_name is set) within 1..$periodsPerDay.
     * - Schedules periods 1..$periodsPerDay for each school.
     * - Prevents teacher clashes across classes at the same day & period.
     */
    protected function regenerateTimetableForSchool(
        string $schoolName,
        $schoolTeachers,
        $allAssignments,
        int $periodsPerDay = 10,
        int $breakfastAfterPeriod = 5,
        int $lunchAfterPeriod = 9,
        bool $isSingleSchoolSystem = false
    ): int {
        return DB::transaction(function () use ($schoolName, $allAssignments, $periodsPerDay, $breakfastAfterPeriod, $lunchAfterPeriod, $isSingleSchoolSystem) {
            // 1. Delete existing subject slots for this school while keeping custom Event slots within 1..$periodsPerDay
            $this->applySchoolScope(Timetable::query(), $schoolName, $isSingleSchoolSystem)
                ->where(function ($q) use ($periodsPerDay) {
                    $q->whereNull('event_name')
                      ->orWhere('event_name', '')
                      ->orWhere('period_number', '>', $periodsPerDay);
                })
                ->delete();

            // Preload preserved custom event slots so we don't overwrite them
            $existingEvents = $this->applySchoolScope(Timetable::query(), $schoolName, $isSingleSchoolSystem)
                ->whereNotNull('event_name')
                ->where('event_name', '!=', '')
                ->get();

            $preservedEvents = [];
            $teacherSchedule = [];
            foreach ($existingEvents as $ev) {
                $cKey = strtolower(trim($ev->class_name));
                $preservedEvents[$cKey][$ev->day_of_week][$ev->period_number] = true;
                if ($ev->teacher_id) {
                    $teacherSchedule[$ev->day_of_week][$ev->period_number][$ev->teacher_id] = $ev->class_name;
                }
            }

            // 2. Identify classes that actually have assigned teachers & subjects in this school
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

            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $scheduledClassesCount = 0;

            foreach ($classes as $classIndex => $cls) {
                $pairs = $this->getDirectClassPairs($cls, $allAssignments);
                if (empty($pairs)) {
                    continue;
                }

                $isALevel = Student::isClassALevel($cls);
                $classEvents = $preservedEvents[strtolower(trim($cls))] ?? [];

                if ($isALevel) {
                    $this->generateAdvanceTimetable(
                        $cls,
                        $schoolName,
                        $pairs,
                        $teacherSchedule,
                        $days,
                        $classIndex,
                        $periodsPerDay,
                        $breakfastAfterPeriod,
                        $lunchAfterPeriod,
                        $classEvents
                    );
                } else {
                    $this->generateOLevelTimetable(
                        $cls,
                        $schoolName,
                        $pairs,
                        $teacherSchedule,
                        $days,
                        $classIndex,
                        $periodsPerDay,
                        $classEvents
                    );
                }
                $scheduledClassesCount++;
            }

            return $scheduledClassesCount;
        });
    }

    /**
     * Gather valid (teacher_id, subject_id, subject_name) pairs strictly assigned to $cls in TeacherAssignment.
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
     * Generate O-Level (Form 1 - 4) Timetable for periods 1..$periodsPerDay.
     */
    protected function generateOLevelTimetable(
        string $cls,
        string $schoolName,
        array $pairs,
        array &$teacherSchedule,
        array $days,
        int $classIndex,
        int $periodsPerDay = 10,
        array $classEvents = []
    ) {
        $pairCount = count($pairs);
        if ($pairCount === 0) {
            return;
        }

        $weeklyCount = array_fill(0, $pairCount, 0);

        foreach ($days as $dayIndex => $day) {
            $dailySubjectCount = [];
            $lastSubjectId = null;

            for ($p = 1; $p <= $periodsPerDay; $p++) {
                // Skip if a custom Event is already placed in this slot
                if (!empty($classEvents[$day][$p])) {
                    continue;
                }

                $bestIdx = null;
                $bestScore = PHP_INT_MAX;

                for ($offset = 0; $offset < $pairCount; $offset++) {
                    $idx = ($classIndex * 2 + $dayIndex * 3 + $p + $offset) % $pairCount;
                    $cand = $pairs[$idx];
                    $tId = $cand['teacher_id'];
                    $sId = $cand['subject_id'];

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

                if ($bestIdx !== null) {
                    $chosen = $pairs[$bestIdx];
                    $teacherSchedule[$day][$p][$chosen['teacher_id']] = $cls;
                    Timetable::create([
                        'school_name'   => $schoolName,
                        'class_name'    => $cls,
                        'day_of_week'   => $day,
                        'period_number' => $p,
                        'event_name'    => null,
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
     * Generate Advance (A-Level: Form 5 & 6) Timetable dynamically for 1..$periodsPerDay
     * respecting break boundaries ($breakfastAfterPeriod and $lunchAfterPeriod).
     */
    protected function generateAdvanceTimetable(
        string $cls,
        string $schoolName,
        array $allPairs,
        array &$teacherSchedule,
        array $days,
        int $classIndex,
        int $periodsPerDay = 10,
        int $breakfastAfterPeriod = 5,
        int $lunchAfterPeriod = 9,
        array $classEvents = []
    ) {
        if (empty($allPairs)) {
            return;
        }

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
        $subPool = !empty($subsidiaryPairs) ? $subsidiaryPairs : $principalPairs;

        foreach ($days as $day) {
            $p = 1;
            while ($p <= $periodsPerDay) {
                if (!empty($classEvents[$day][$p])) {
                    $p++;
                    continue;
                }

                $canPairWithNext = ($p + 1 <= $periodsPerDay)
                    && empty($classEvents[$day][$p + 1])
                    && ($p !== $breakfastAfterPeriod)
                    && ($p !== $lunchAfterPeriod);

                if ($canPairWithNext) {
                    $this->assignAdvanceDoubleBlock($day, $p, $p + 1, $cls, $schoolName, $principalPairs, $allPairs, $princIndex, $teacherSchedule);
                    $p += 2;
                } else {
                    $poolToUse = ($p === $breakfastAfterPeriod) ? $subPool : $principalPairs;
                    $cursorRef = ($p === $breakfastAfterPeriod) ? $subIndex : $princIndex;
                    $this->assignSingleFromPool($day, $p, $cls, $schoolName, $poolToUse, $allPairs, $cursorRef, $teacherSchedule);
                    if ($p === $breakfastAfterPeriod) {
                        $subIndex = $cursorRef;
                    } else {
                        $princIndex = $cursorRef;
                    }
                    $p++;
                }
            }
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
                        'event_name'    => null,
                        'subject_id'    => $sId,
                        'teacher_id'    => $tId,
                    ]);
                    Timetable::create([
                        'school_name'   => $schoolName,
                        'class_name'    => $cls,
                        'day_of_week'   => $day,
                        'period_number' => $p2,
                        'event_name'    => null,
                        'subject_id'    => $sId,
                        'teacher_id'    => $tId,
                    ]);

                    $princIndex = ($princIndex + $attempt + 1) % $count;
                    return;
                }
            }
        }

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
                        'event_name'    => null,
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
        $slotType = $request->input('slot_type', 'subject');

        $request->validate([
            'class_name'    => 'required|string',
            'day_of_week'   => 'required|string',
            'period_number' => 'required|integer|min:1|max:14',
            'slot_type'     => 'nullable|in:subject,event',
        ]);

        $user = Auth::user();
        $schoolName = $this->resolveSchoolName($user, $request);
        $isSingleSchoolSystem = count($this->getAllSchools()) <= 1;

        $day       = $request->day_of_week;
        $period    = (int) $request->period_number;
        $className = $request->class_name;

        if ($slotType === 'event') {
            $request->validate([
                'event_name' => 'required|string|max:120',
                'teacher_id' => 'nullable|exists:users,id',
            ]);

            $eventName = trim($request->event_name);
            $teacherId = $request->filled('teacher_id') ? $request->teacher_id : null;

            if ($request->boolean('apply_all_classes')) {
                $standardClasses = collect(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6']);
                $assignedClasses = $this->applySchoolScope(TeacherAssignment::query(), $schoolName, $isSingleSchoolSystem)->pluck('class_name');
                $studentClasses = $this->applySchoolScope(Student::query(), $schoolName, $isSingleSchoolSystem)->pluck('class_name');
                $targetClasses = $standardClasses->merge($assignedClasses)->merge($studentClasses)
                    ->map(fn($c) => trim($c))->unique()->filter()->values()->all();

                foreach ($targetClasses as $cls) {
                    Timetable::updateOrCreate(
                        [
                            'school_name'   => $schoolName,
                            'class_name'    => $cls,
                            'day_of_week'   => $day,
                            'period_number' => $period,
                        ],
                        [
                            'event_name' => $eventName,
                            'subject_id' => null,
                            'teacher_id' => $teacherId,
                        ]
                    );
                }

                return back()->with('success', '✔️ ' . __('Event ":event" saved for all classes on :day (Period :period)!', [
                    'event'  => $eventName,
                    'day'    => __($day),
                    'period' => $period,
                ]));
            }

            Timetable::updateOrCreate(
                [
                    'school_name'   => $schoolName,
                    'class_name'    => $className,
                    'day_of_week'   => $day,
                    'period_number' => $period,
                ],
                [
                    'event_name' => $eventName,
                    'subject_id' => null,
                    'teacher_id' => $teacherId,
                ]
            );

            return back()->with('success', '✔️ ' . __('Event ":event" saved for :class (:day, Period :period)!', [
                'event'  => $eventName,
                'class'  => $className,
                'day'    => __($day),
                'period' => $period,
            ]));
        }

        // Standard academic Subject slot
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $teacherId = $request->teacher_id;
        $subjectId = $request->subject_id;

        // Check if teacher is busy with another class in the same slot in this school
        $clash = $this->applySchoolScope(
                Timetable::where('teacher_id', $teacherId)
                    ->where('day_of_week', $day)
                    ->where('period_number', $period)
                    ->where('class_name', '!=', $className),
                $schoolName,
                $isSingleSchoolSystem
            )->first();

        if ($clash) {
            return back()->with('error', '❌ ' . __('Clash detected! Teacher is already teaching :class during period :period on :day.', [
                'class'  => $clash->class_name,
                'period' => $period,
                'day'    => __($day),
            ]));
        }

        Timetable::updateOrCreate(
            [
                'school_name'   => $schoolName,
                'class_name'    => $className,
                'day_of_week'   => $day,
                'period_number' => $period,
            ],
            [
                'event_name' => null,
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
            ]
        );

        return back()->with('success', '✔️ ' . __('Timetable slot updated for :class (:day, Period :period)!', [
            'class'  => $className,
            'day'    => __($day),
            'period' => $period,
        ]));
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
        $isSingleSchoolSystem = count($this->getAllSchools()) <= 1;

        $this->applySchoolScope(Timetable::query(), $schoolName, $isSingleSchoolSystem)
            ->where('class_name', $request->class_name)
            ->where('day_of_week', $request->day_of_week)
            ->where('period_number', $request->period_number)
            ->delete();

        return back()->with('success', '✔️ ' . __('Timetable slot cleared!'));
    }
}
