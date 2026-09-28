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
    public function index(Request $request)
    {
        $user = Auth::user();
        $schoolName = $user ? ($user->school_name ?: 'Kome Secondary School') : 'Kome Secondary School';
        $userRole = $user ? $user->role : 'Guest';

        $isAcademic = in_array($userRole, ['Academic Master', 'Head of School', 'Head Of School', 'Headmaster', 'Headmistress', 'Admin']);

        // Gather all classes from system (standard, assigned, and students)
        $standardClasses = collect(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6']);
        $assignedClasses = TeacherAssignment::where('school_name', $schoolName)->pluck('class_name');
        $studentClasses  = Student::where('school_name', $schoolName)->pluck('class_name');

        $classes = $standardClasses->merge($assignedClasses)->merge($studentClasses)
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

        // Fetch teachers in this school
        $allTeachers = User::whereIn('role', ['Teacher', 'Academic Master'])
            ->where(function ($q) use ($schoolName) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
            })
            ->orderBy('name')
            ->get();

        if ($allTeachers->isEmpty()) {
            $allTeachers = User::whereIn('role', ['Teacher', 'Academic Master'])->orderBy('name')->get();
        }

        // Identify which teachers are assigned to this class
        $assignedTeacherIds = TeacherAssignment::where('class_name', $selectedClass)
            ->where(function ($q) use ($schoolName) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
            })
            ->pluck('teacher_id')
            ->unique()
            ->toArray();

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
            'isALevel',
            'days',
            'periodSlots',
            'allSubjects',
            'allTeachers',
            'assignedTeacherIds',
            'timetableMatrix'
        ));
    }

    public function autoGenerate(Request $request)
    {
        $user = Auth::user();
        $schoolName = $user ? ($user->school_name ?: 'Kome Secondary School') : 'Kome Secondary School';

        DB::beginTransaction();
        try {
            // 1. Clear existing timetable for this school
            Timetable::where('school_name', $schoolName)->delete();

            // 2. Identify all classes to generate for
            $baseClasses = collect(['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6']);
            $assignedClasses = TeacherAssignment::where('school_name', $schoolName)->pluck('class_name');
            $studentClasses  = Student::where('school_name', $schoolName)->pluck('class_name');
            $classes = $baseClasses->merge($assignedClasses)->merge($studentClasses)->unique()->filter()->values()->all();

            // Sort: O-Level first, then Advance
            usort($classes, function ($a, $b) {
                $advA = Student::isClassALevel($a);
                $advB = Student::isClassALevel($b);
                if ($advA !== $advB) return $advA ? 1 : -1;
                return strnatcasecmp($a, $b);
            });

            // 3. Fetch all registered teachers in this school
            $schoolTeachers = User::whereIn('role', ['Teacher', 'Academic Master'])
                ->where(function ($q) use ($schoolName) {
                    if ($schoolName) $q->where('school_name', $schoolName);
                })
                ->get();

            if ($schoolTeachers->isEmpty()) {
                $schoolTeachers = User::whereIn('role', ['Teacher', 'Academic Master'])->get();
            }

            if ($schoolTeachers->isEmpty()) {
                DB::rollBack();
                return back()->with('error', 'No registered teachers found. Please register teachers first.');
            }

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

            foreach ($classes as $classIndex => $cls) {
                $isALevel = Student::isClassALevel($cls);

                // Assign a distinct class teacher supervisor for this class to prevent clashes on shared periods
                $classSupervisor = $schoolTeachers[$classIndex % count($schoolTeachers)];

                if ($isALevel) {
                    $this->generateAdvanceTimetable(
                        $cls,
                        $schoolName,
                        $schoolTeachers,
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
            }

            DB::commit();
            return back()->with('success', '✔️ Timetable generated automatically! O-Level and Advance have distinct tailored schedules based on registered teachers and assigned classes.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Generation Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate O-Level (Ordinary Level: Form 1 - 4) Timetable.
     * Uses only O-Level curriculum subjects (No A-Level subjects like BAM, Advanced Math, GS, Economics).
     * Follows teacher assignments specifically for this class.
     */
    protected function generateOLevelTimetable(
        string $cls,
        string $schoolName,
        $schoolTeachers,
        $classSupervisor,
        array &$teacherSchedule,
        array $days,
        int $classIndex,
        $subDiscussion,
        $subReligion,
        $subDebate,
        $subSports
    ) {
        // 1. Fetch teacher assignments specifically for this class
        $directAssignments = TeacherAssignment::where('school_name', $schoolName)
            ->where(function ($q) use ($cls) {
                $q->where('class_name', $cls)
                  ->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($cls))]);
            })
            ->with(['teacher', 'subject'])
            ->get();

        $validSubjectTeacherMap = [];
        foreach ($directAssignments as $asg) {
            if ($asg->subject && !Subject::isAdvanceOnlySubject($asg->subject->subject_name) && Subject::isAcademicSubject($asg->subject->subject_name)) {
                $sName = $asg->subject->subject_name;
                $validSubjectTeacherMap[strtolower(trim($sName))] = [
                    'subject_id'   => $asg->subject_id,
                    'teacher_id'   => $asg->teacher_id,
                    'subject_name' => $sName,
                ];
            }
        }

        // Standard O-Level curriculum subjects
        $olevelCoreSubjects = [
            'Mathematics', 'English', 'Kiswahili', 'Biology',
            'Chemistry', 'Physics', 'History', 'Geography',
            'Civics', 'Business', 'Computer Science'
        ];

        // For any O-Level subject not directly assigned to this class,
        // match with a teacher from the school who teaches this subject in other classes
        foreach ($olevelCoreSubjects as $sName) {
            $key = strtolower($sName);
            if (!isset($validSubjectTeacherMap[$key])) {
                $sub = Subject::firstOrCreate(['subject_name' => $sName]);
                $existingAsg = TeacherAssignment::where('subject_id', $sub->id)
                    ->where(function ($q) use ($schoolName) {
                        if ($schoolName) $q->where('school_name', $schoolName);
                    })
                    ->first();

                $teacherId = $existingAsg ? $existingAsg->teacher_id : $schoolTeachers->random()->id;

                $validSubjectTeacherMap[$key] = [
                    'subject_id'   => $sub->id,
                    'teacher_id'   => $teacherId,
                    'subject_name' => $sName,
                ];
            }
        }

        // Standard, pedagogically sound daily subjects plan for O-Level (37 academic slots across the week):
        // Each day has balanced, diverse subjects (never 4 of the same subject on one day)
        $dailySubjectPlans = [
            'Monday' => [
                'Mathematics', 'English', 'Kiswahili', 'Biology', 'Chemistry',
                'Physics', 'Geography', 'Civics', 'Business'
            ], // 9 periods
            'Tuesday' => [
                'English', 'Mathematics', 'Biology', 'Chemistry', 'Physics',
                'History', 'Civics', 'Kiswahili', 'Computer Science'
            ], // 9 periods
            'Wednesday' => [
                'Kiswahili', 'Mathematics', 'English', 'Biology', 'Chemistry',
                'Physics', 'Geography'
            ], // 7 periods (P8 & 9 are Religion)
            'Thursday' => [
                'Mathematics', 'English', 'History', 'Geography', 'Chemistry',
                'Physics', 'Civics'
            ], // 7 periods (P8 & 9 are Debate/Club)
            'Friday' => [
                'English', 'Kiswahili', 'Biology', 'History', 'Mathematics'
            ], // 5 periods (P6 to 9 are Sports & Games)
        ];

        // 2. Schedule each day:
        foreach ($days as $day) {
            $daySubjects = $dailySubjectPlans[$day] ?? [];

            // Apply rotation offset per class index so Form 1, Form 2, Form 3, Form 4
            // don't start with the exact same subject at Period 1
            $shift = ($classIndex * 2) % max(1, count($daySubjects));
            $rotatedDaySubjects = array_merge(array_slice($daySubjects, $shift), array_slice($daySubjects, 0, $shift));
            $subjectCursor = 0;

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

                // Academic Slot:
                $sName = $rotatedDaySubjects[$subjectCursor % count($rotatedDaySubjects)];
                $key = strtolower($sName);
                $pair = $validSubjectTeacherMap[$key] ?? $validSubjectTeacherMap[array_key_first($validSubjectTeacherMap)];
                $tId = $pair['teacher_id'];
                $sId = $pair['subject_id'];

                // Check collision
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
                    $subjectCursor++;
                } else {
                    // Try alternative subject from the day's subjects that has a free teacher
                    $placed = false;
                    for ($step = 1; $step < count($rotatedDaySubjects); $step++) {
                        $altSName = $rotatedDaySubjects[($subjectCursor + $step) % count($rotatedDaySubjects)];
                        $altPair = $validSubjectTeacherMap[strtolower($altSName)] ?? null;
                        if ($altPair && !isset($teacherSchedule[$day][$p][$altPair['teacher_id']])) {
                            $teacherSchedule[$day][$p][$altPair['teacher_id']] = $cls;
                            Timetable::create([
                                'school_name'   => $schoolName,
                                'class_name'    => $cls,
                                'day_of_week'   => $day,
                                'period_number' => $p,
                                'subject_id'    => $altPair['subject_id'],
                                'teacher_id'    => $altPair['teacher_id'],
                            ]);
                            $placed = true;
                            break;
                        }
                    }

                    // If still busy, find any free teacher in the school for this subject
                    if (!$placed) {
                        foreach ($schoolTeachers as $altTeacher) {
                            if (!isset($teacherSchedule[$day][$p][$altTeacher->id])) {
                                $teacherSchedule[$day][$p][$altTeacher->id] = $cls;
                                Timetable::create([
                                    'school_name'   => $schoolName,
                                    'class_name'    => $cls,
                                    'day_of_week'   => $day,
                                    'period_number' => $p,
                                    'subject_id'    => $sId,
                                    'teacher_id'    => $altTeacher->id,
                                ]);
                                $placed = true;
                                break;
                            }
                        }
                    }

                    $subjectCursor++;
                }
            }
        }
    }

    /**
     * Generate Advance (A-Level: Form 5 & 6) Timetable.
     * Uses Advance curriculum: Combination subjects in Double Lecture Blocks (80 min),
     * General Studies, Basic Applied Mathematics, Lab Practicals, and GS Seminars.
     * Differs structurally and in subjects from O-Level.
     */
    protected function generateAdvanceTimetable(
        string $cls,
        string $schoolName,
        $schoolTeachers,
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
        // 1. Identify combination or subjects for this Advance class
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

        if ($combination && isset($combinationMap[$combination])) {
            $advanceSubjectNames = $combinationMap[$combination];
        } else {
            // General Form 5 / Form 6: combination subjects + GS + BAM
            $advanceSubjectNames = [
                'General Studies', 'Basic Applied Mathematics', 'Advanced Mathematics',
                'Physics', 'Chemistry', 'Biology', 'History', 'Geography', 'Economics', 'Kiswahili', 'English'
            ];
        }

        // 2. Fetch direct assignments for this Advance class
        $directAssignments = TeacherAssignment::where('school_name', $schoolName)
            ->where(function ($q) use ($cls) {
                $q->where('class_name', $cls)
                  ->orWhereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($cls))]);
            })
            ->with(['teacher', 'subject'])
            ->get();

        $advanceSubjectTeacherMap = [];
        foreach ($directAssignments as $asg) {
            if ($asg->subject && Subject::isAcademicSubject($asg->subject->subject_name) && !Subject::isOLevelOnlySubject($asg->subject->subject_name)) {
                $sName = $asg->subject->subject_name;
                $advanceSubjectTeacherMap[strtolower(trim($sName))] = [
                    'subject_id'   => $asg->subject_id,
                    'teacher_id'   => $asg->teacher_id,
                    'subject_name' => $sName,
                ];
            }
        }

        // For any Advance subject not directly assigned to this class:
        foreach ($advanceSubjectNames as $sName) {
            $key = strtolower($sName);
            if (!isset($advanceSubjectTeacherMap[$key])) {
                $sub = Subject::firstOrCreate(['subject_name' => $sName]);
                $existingAsg = TeacherAssignment::where('subject_id', $sub->id)
                    ->where(function ($q) use ($schoolName) {
                        if ($schoolName) $q->where('school_name', $schoolName);
                    })
                    ->first();

                $teacherId = $existingAsg ? $existingAsg->teacher_id : $schoolTeachers->random()->id;

                $advanceSubjectTeacherMap[$key] = [
                    'subject_id'   => $sub->id,
                    'teacher_id'   => $teacherId,
                    'subject_name' => $sName,
                ];
            }
        }

        // Separate Principal combination subjects vs Subsidiaries (GS, BAM)
        $principalPairs = [];
        $subsidiaryPairs = [];
        foreach ($advanceSubjectTeacherMap as $p) {
            $n = strtolower($p['subject_name']);
            if (str_contains($n, 'general studies') || str_contains($n, 'basic applied')) {
                $subsidiaryPairs[] = $p;
            } else {
                $principalPairs[] = $p;
            }
        }
        if (empty($principalPairs)) {
            $principalPairs = array_values($advanceSubjectTeacherMap);
        }

        $princIndex = ($classIndex * 3) % max(1, count($principalPairs));
        $subIndex = 0;

        foreach ($days as $day) {
            // Block 1: Double Period 1 & 2 (08:00 - 09:20) - Principal Combination Block
            $this->assignAdvanceDoubleBlock($day, 1, 2, $cls, $schoolName, $principalPairs, $princIndex, $teacherSchedule, $schoolTeachers);

            // Block 2: Double Period 3 & 4 (09:20 - 10:40) - Principal Combination Block
            $this->assignAdvanceDoubleBlock($day, 3, 4, $cls, $schoolName, $principalPairs, $princIndex, $teacherSchedule, $schoolTeachers);

            // Period 5: Subsidiary / Tutorial (10:40 - 11:20) - General Studies / BAM
            $subPair = !empty($subsidiaryPairs) ? $subsidiaryPairs[$subIndex++ % count($subsidiaryPairs)] : $principalPairs[$princIndex % count($principalPairs)];
            $this->assignSinglePeriod($day, 5, $cls, $schoolName, $subPair, $teacherSchedule, $schoolTeachers);

            // Block 3: Periods 6 & 7 (11:40 - 13:00)
            if ($day === 'Friday') {
                // Advance: Science / Combination Laboratory Practicals
                $this->assignSpecialSlot($schoolName, $cls, $day, 6, $subPracticals->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
                $this->assignSpecialSlot($schoolName, $cls, $day, 7, $subPracticals->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
            } else {
                // Combination Double Lecture Block
                $this->assignAdvanceDoubleBlock($day, 6, 7, $cls, $schoolName, $principalPairs, $princIndex, $teacherSchedule, $schoolTeachers);
            }

            // Block 4: Periods 8 & 9 (13:00 - 14:20)
            if ($day === 'Wednesday') {
                // General Studies Seminar & Academic Symposium
                $gsSub = !empty($subsidiaryPairs) ? $subsidiaryPairs[0] : null;
                $gsSubId = $gsSub ? $gsSub['subject_id'] : $subGsSeminar->id;
                $gsTrId = $gsSub ? $gsSub['teacher_id'] : $classSupervisor->id;

                $this->assignSpecialSlot($schoolName, $cls, $day, 8, $gsSubId, $gsTrId, $teacherSchedule, $schoolTeachers);
                $this->assignSpecialSlot($schoolName, $cls, $day, 9, $gsSubId, $gsTrId, $teacherSchedule, $schoolTeachers);
            } elseif ($day === 'Thursday') {
                // Subject Club & Academic Research
                $this->assignSpecialSlot($schoolName, $cls, $day, 8, $subDebate->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
                $this->assignSpecialSlot($schoolName, $cls, $day, 9, $subDebate->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
            } elseif ($day === 'Friday') {
                // Sports & Health Club
                $this->assignSpecialSlot($schoolName, $cls, $day, 8, $subSports->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
                $this->assignSpecialSlot($schoolName, $cls, $day, 9, $subSports->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
            } else {
                // Mon & Tue P8 & 9: Combination Double Lecture Block or BAM
                $this->assignAdvanceDoubleBlock($day, 8, 9, $cls, $schoolName, $principalPairs, $princIndex, $teacherSchedule, $schoolTeachers);
            }

            // Period 10 (15:00 - 17:00): Advance Discussion & Examination Preparation
            $this->assignSpecialSlot($schoolName, $cls, $day, 10, $subDiscussion->id, $classSupervisor->id, $teacherSchedule, $schoolTeachers);
        }
    }

    /**
     * Assign Advance Double Period Block (e.g. Periods 1 & 2) ensuring teacher is free for both slots.
     */
    protected function assignAdvanceDoubleBlock(
        string $day,
        int $p1,
        int $p2,
        string $cls,
        string $schoolName,
        array $pairs,
        int &$princIndex,
        array &$teacherSchedule,
        $schoolTeachers
    ) {
        $count = count($pairs);
        $attempts = 0;
        $placed = false;

        while ($attempts < $count) {
            $cand = $pairs[$princIndex % $count];
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

                $princIndex++;
                $placed = true;
                break;
            }

            $princIndex++;
            $attempts++;
        }

        if (!$placed) {
            foreach ($pairs as $cand) {
                $sId = $cand['subject_id'];
                foreach ($schoolTeachers as $altTeacher) {
                    if (!isset($teacherSchedule[$day][$p1][$altTeacher->id]) && !isset($teacherSchedule[$day][$p2][$altTeacher->id])) {
                        $teacherSchedule[$day][$p1][$altTeacher->id] = $cls;
                        $teacherSchedule[$day][$p2][$altTeacher->id] = $cls;

                        Timetable::create([
                            'school_name'   => $schoolName,
                            'class_name'    => $cls,
                            'day_of_week'   => $day,
                            'period_number' => $p1,
                            'subject_id'    => $sId,
                            'teacher_id'    => $altTeacher->id,
                        ]);
                        Timetable::create([
                            'school_name'   => $schoolName,
                            'class_name'    => $cls,
                            'day_of_week'   => $day,
                            'period_number' => $p2,
                            'subject_id'    => $sId,
                            'teacher_id'    => $altTeacher->id,
                        ]);
                        $placed = true;
                        break 2;
                    }
                }
            }
        }

        // If still not placed (e.g. no single teacher free for both periods concurrently),
        // fallback to placing p1 and p2 individually so slots are never left empty!
        if (!$placed) {
            $cand1 = $pairs[$princIndex % $count];
            $this->assignSinglePeriod($day, $p1, $cls, $schoolName, $cand1, $teacherSchedule, $schoolTeachers);
            $cand2 = $pairs[($princIndex + 1) % $count];
            $this->assignSinglePeriod($day, $p2, $cls, $schoolName, $cand2, $teacherSchedule, $schoolTeachers);
            $princIndex += 2;
        }
    }

    /**
     * Assign a single period avoiding teacher clash.
     */
    protected function assignSinglePeriod(
        string $day,
        int $p,
        string $cls,
        string $schoolName,
        array $pair,
        array &$teacherSchedule,
        $schoolTeachers
    ) {
        $tId = $pair['teacher_id'];
        $sId = $pair['subject_id'];

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
            return;
        }

        foreach ($schoolTeachers as $altTeacher) {
            if (!isset($teacherSchedule[$day][$p][$altTeacher->id])) {
                $teacherSchedule[$day][$p][$altTeacher->id] = $cls;
                Timetable::create([
                    'school_name'   => $schoolName,
                    'class_name'    => $cls,
                    'day_of_week'   => $day,
                    'period_number' => $p,
                    'subject_id'    => $sId,
                    'teacher_id'    => $altTeacher->id,
                ]);
                return;
            }
        }

        // Fallback if all teachers are busy at this period
        $teacherSchedule[$day][$p][$tId] = $cls;
        Timetable::create([
            'school_name'   => $schoolName,
            'class_name'    => $cls,
            'day_of_week'   => $day,
            'period_number' => $p,
            'subject_id'    => $sId,
            'teacher_id'    => $tId,
        ]);
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
        $schoolName = $user ? ($user->school_name ?: 'Kome Secondary School') : 'Kome Secondary School';

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
        $schoolName = $user ? ($user->school_name ?: 'Kome Secondary School') : 'Kome Secondary School';

        Timetable::where('school_name', $schoolName)
            ->where('class_name', $request->class_name)
            ->where('day_of_week', $request->day_of_week)
            ->where('period_number', $request->period_number)
            ->delete();

        return back()->with('success', '✔️ Timetable slot cleared!');
    }
}
