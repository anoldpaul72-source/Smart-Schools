<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'term',
        'exam_date',
        'marks',
        'grade',
        'remarks',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Helper to detect if a given context represents A-Level.
     */
    public static function isALevel(mixed $level = null): bool
    {
        if ($level instanceof Student) {
            return $level->isALevel();
        }
        if (is_bool($level)) {
            return $level;
        }
        if (is_string($level)) {
            return Student::isClassALevel($level);
        }
        return false;
    }

    /**
     * Calculate Grade & Remarks based on score and education level.
     * Default is O-Level if not specified.
     */
    public static function calculateGrade(float $score, mixed $level = null): array
    {
        if (self::isALevel($level)) {
            return self::calculateALevelGrade($score);
        }

        return self::calculateOLevelGrade($score);
    }

    /**
     * O-Level Grading Scale (NECTA CSEE):
     * 75 - 100 = A (Excellent)
     * 60 - 74  = B (Very Good)
     * 45 - 59  = C (Good)
     * 30 - 44  = D (Pass)
     *  0 - 29  = F (Fail)
     */
    public static function calculateOLevelGrade(float $score): array
    {
        if ($score >= 75) {
            return ['A', 'Excellent'];
        } elseif ($score >= 60) {
            return ['B', 'Very Good'];
        } elseif ($score >= 45) {
            return ['C', 'Good'];
        } elseif ($score >= 30) {
            return ['D', 'Pass'];
        } else {
            return ['F', 'Fail'];
        }
    }

    /**
     * A-Level Grading Scale (NECTA ACSEE):
     * 80 - 100 = A (Excellent)
     * 70 - 79  = B (Very Good)
     * 60 - 69  = C (Good)
     * 50 - 59  = D (Satisfactory)
     * 40 - 49  = E (Pass)
     * 36 - 40  = S (Subsidiary)
     *  0 - 35  = F (Fail)
     */
    public static function calculateALevelGrade(float $score): array
    {
        if ($score >= 80) {
            return ['A', 'Excellent'];
        } elseif ($score >= 70) {
            return ['B', 'Very Good'];
        } elseif ($score >= 60) {
            return ['C', 'Good'];
        } elseif ($score >= 50) {
            return ['D', 'Satisfactory'];
        } elseif ($score >= 40) {
            return ['E', 'Pass'];
        } elseif ($score >= 36) {
            return ['S', 'Subsidiary'];
        } else {
            return ['F', 'Fail'];
        }
    }

    /**
     * Calculate NECTA Points for a score.
     */
    public static function calculatePoints(float $score, mixed $level = null): int
    {
        if (self::isALevel($level)) {
            if ($score >= 80) return 1;
            if ($score >= 70) return 2;
            if ($score >= 60) return 3;
            if ($score >= 50) return 4;
            if ($score >= 40) return 5;
            if ($score >= 36) return 6;
            return 7;
        }

        if ($score >= 75) return 1;
        if ($score >= 60) return 2;
        if ($score >= 45) return 3;
        if ($score >= 30) return 4;
        return 5;
    }
}
