<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_name',
    ];

    public const NON_ACADEMIC_ACTIVITIES = [
        'Religion',
        'Debate or Subject Club',
        'Debate or Subject club',
        'Sports and Games',
        'Discussion and Examination',
        'Discussion and Examinations',
        'General Studies Seminar',
        'Laboratory Practicals and Research',
    ];

    public const ADVANCE_SUBJECTS = [
        'General Studies',
        'Basic Applied Mathematics',
        'Advanced Mathematics',
        'Physics',
        'Chemistry',
        'Biology',
        'History',
        'Geography',
        'Kiswahili',
        'English',
        'Economics',
        'Commerce',
        'Accountancy',
        'Agriculture',
        'Food and Human Nutrition',
        'Computer Science',
        'French',
    ];

    public const ADVANCE_ONLY_SUBJECTS = [
        'General Studies',
        'Basic Applied Mathematics',
        'Advanced Mathematics',
        'Economics',
        'Accountancy',
        'Food and Human Nutrition',
        'General Studies Seminar',
        'Laboratory Practicals and Research',
    ];

    public const OLEVEL_ONLY_SUBJECTS = [
        'Civics',
    ];

    public static function isAdvanceOnlySubject(?string $name): bool
    {
        if (!$name) return false;
        $name = strtolower(trim($name));
        foreach (self::ADVANCE_ONLY_SUBJECTS as $as) {
            if (strcasecmp($name, strtolower(trim($as))) === 0 || str_contains($name, strtolower(trim($as)))) {
                return true;
            }
        }
        return false;
    }

    public static function isOLevelOnlySubject(?string $name): bool
    {
        if (!$name) return false;
        $name = strtolower(trim($name));
        foreach (self::OLEVEL_ONLY_SUBJECTS as $os) {
            if (strcasecmp($name, strtolower(trim($os))) === 0) {
                return true;
            }
        }
        return false;
    }

    public static function isAdvanceSubject(?string $name): bool
    {
        if (!$name) return false;
        $name = strtolower(trim($name));
        foreach (self::ADVANCE_SUBJECTS as $as) {
            if (strcasecmp($name, strtolower(trim($as))) === 0) {
                return true;
            }
        }
        return false;
    }

    public function scopeAcademic($query)
    {
        return $query->whereNotIn('subject_name', self::NON_ACADEMIC_ACTIVITIES);
    }

    public static function isAcademicSubject(?string $name): bool
    {
        if (!$name) {
            return false;
        }

        foreach (self::NON_ACADEMIC_ACTIVITIES as $activity) {
            if (strcasecmp(trim($name), trim($activity)) === 0) {
                return false;
            }
        }

        return true;
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    /**
     * Get all registered academic curriculum subjects for a given class and optional combination.
     * Guaranteed to return only the subjects registered for that class in standard NECTA order.
     */
    public static function getRegisteredAcademicSubjectsForClass(string $className, ?string $combination = null): \Illuminate\Support\Collection
    {
        $upperClass = strtoupper($className);
        $isALevel = str_contains($upperClass, 'FORM 5') 
            || str_contains($upperClass, 'FORM 6') 
            || str_contains($upperClass, 'FORM V') 
            || str_contains($upperClass, 'FORM VI')
            || str_contains($upperClass, 'F5')
            || str_contains($upperClass, 'F6');

        $combinationSubjectsMap = [
            'HKL' => ['History', 'Kiswahili', 'English', 'General Studies', 'Basic Applied Mathematics'],
            'HGK' => ['History', 'Geography', 'Kiswahili', 'General Studies', 'Basic Applied Mathematics'],
            'HGL' => ['History', 'Geography', 'English', 'General Studies', 'Basic Applied Mathematics'],
            'HGE' => ['History', 'Geography', 'Economics', 'General Studies', 'Basic Applied Mathematics'],
            'PCM' => ['Physics', 'Chemistry', 'Advanced Mathematics', 'General Studies'],
            'PCB' => ['Physics', 'Chemistry', 'Biology', 'General Studies', 'Basic Applied Mathematics'],
            'PGM' => ['Physics', 'Geography', 'Advanced Mathematics', 'General Studies'],
            'CBG' => ['Chemistry', 'Biology', 'Geography', 'General Studies', 'Basic Applied Mathematics'],
            'CBA' => ['Chemistry', 'Biology', 'Agriculture', 'General Studies', 'Basic Applied Mathematics'],
            'CBN' => ['Chemistry', 'Biology', 'Food and Human Nutrition', 'General Studies', 'Basic Applied Mathematics'],
            'PMC' => ['Physics', 'Advanced Mathematics', 'Computer Science', 'General Studies'],
            'EGM' => ['Economics', 'Geography', 'Advanced Mathematics', 'General Studies'],
            'ECA' => ['Economics', 'Commerce', 'Accountancy', 'General Studies', 'Basic Applied Mathematics'],
            'KLF' => ['Kiswahili', 'English', 'French', 'General Studies', 'Basic Applied Mathematics'],
            'KEC' => ['Kiswahili', 'Economics', 'Commerce', 'General Studies', 'Basic Applied Mathematics'],
        ];

        $targetSubNames = [];
        if ($isALevel && !empty($combination)) {
            $combUpper = strtoupper(trim($combination));
            $targetSubNames = $combinationSubjectsMap[$combUpper] ?? [];
        }

        $baseClass = \App\Models\School::extractBaseClass($className);

        $assignedSubjectIds = TeacherAssignment::where(function ($q) use ($className, $baseClass) {
                $q->where('class_name', $className)
                  ->orWhere('class_name', $baseClass)
                  ->orWhere('class_name', 'like', "{$baseClass}%")
                  ->orWhere('class_name', 'like', "{$className}%");
            })
            ->pluck('subject_id');

        $timetableSubjectIds = Timetable::where(function ($q) use ($className, $baseClass) {
                $q->where('class_name', $className)
                  ->orWhere('class_name', $baseClass)
                  ->orWhere('class_name', 'like', "{$baseClass}%")
                  ->orWhere('class_name', 'like', "{$className}%");
            })
            ->whereNotNull('subject_id')
            ->pluck('subject_id');

        $marksSubjectIds = Mark::whereHas('student', fn($q) => $q->where('class_name', $className)->orWhere('class_name', 'like', "{$baseClass}%"))
            ->pluck('subject_id');

        $registeredSubjectIds = $assignedSubjectIds->merge($timetableSubjectIds)->merge($marksSubjectIds)->unique()->filter()->values()->all();

        if ($isALevel && !empty($targetSubNames)) {
            $subjects = self::academic()
                ->where(function ($q) use ($targetSubNames) {
                    foreach ($targetSubNames as $tsn) {
                        $q->orWhere('subject_name', 'like', "%{$tsn}%");
                    }
                })
                ->get();
        } elseif (!empty($registeredSubjectIds)) {
            $subjects = self::academic()
                ->whereIn('id', $registeredSubjectIds)
                ->get();

            if (!$isALevel) {
                $subjects = $subjects->filter(fn($sub) => !self::isAdvanceOnlySubject($sub->subject_name))->values();
            } else {
                $subjects = $subjects->filter(fn($sub) => !self::isOLevelOnlySubject($sub->subject_name))->values();
            }
        } else {
            if ($isALevel) {
                $subjects = self::academic()
                    ->get()
                    ->filter(fn($sub) => self::isAdvanceSubject($sub->subject_name))
                    ->values();
            } else {
                $subjects = self::academic()
                    ->get()
                    ->filter(fn($sub) => !self::isAdvanceOnlySubject($sub->subject_name))
                    ->values();
            }
        }

        $standardOLevelOrder = [
            'civics', 'history', 'geography', 'kiswahili', 'english',
            'physics', 'chemistry', 'biology', 'mathematic', 'basic math',
            'book keeping', 'commerce', 'business', 'computer', 'agriculture', 'religion'
        ];

        return $subjects->sortBy(function ($sub) use ($standardOLevelOrder, $isALevel, $targetSubNames) {
            $name = strtolower(trim($sub->subject_name));
            if ($isALevel && !empty($targetSubNames)) {
                if (str_contains($name, 'general studies')) return 80;
                if (str_contains($name, 'basic applied')) return 85;
                foreach ($targetSubNames as $idx => $tsn) {
                    if (str_contains($name, strtolower($tsn))) {
                        return $idx;
                    }
                }
                return 90;
            }

            foreach ($standardOLevelOrder as $idx => $ord) {
                if (str_contains($name, $ord)) {
                    return $idx;
                }
            }
            return 50;
        })->values();
    }
}
