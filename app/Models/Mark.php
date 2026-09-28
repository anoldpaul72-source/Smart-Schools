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
     * 65 - 74  = B (Very Good)
     * 45 - 64  = C (Good)
     * 30 - 44  = D (Pass)
     *  0 - 29  = F (Fail)
     */
    public static function calculateOLevelGrade(float $score): array
    {
        if ($score >= 75) {
            return ['A', 'Excellent'];
        } elseif ($score >= 65) {
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
     * 35 - 39  = S (Subsidiary)
     *  0 - 34  = F (Fail)
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
        } elseif ($score >= 35) {
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
            if ($score >= 35) return 6;
            return 7;
        }

        if ($score >= 75) return 1;
        if ($score >= 65) return 2;
        if ($score >= 45) return 3;
        if ($score >= 30) return 4;
        return 5;
    }

    /**
     * Convert an A-Level grade letter into NECTA points.
     * A=1, B=2, C=3, D=4, E=5, S=6, F=7
     */
    public static function gradeToALevelPoints(string $grade): int
    {
        return match (strtoupper(trim($grade))) {
            'A' => 1,
            'B' => 2,
            'C' => 3,
            'D' => 4,
            'E' => 5,
            'S' => 6,
            default => 7,
        };
    }

    /**
     * Calculate A-Level NECTA ACSEE Division based on points of 3 principal subjects.
     * Div I: 3-9, Div II: 10-12, Div III: 13-17, Div IV: 18-19, Div 0: 20-21
     */
    public static function calculateALevelDivision(int $totalPoints, int $principalPassesCount = 3): string
    {
        if ($principalPassesCount >= 2 && $totalPoints >= 3 && $totalPoints <= 9) {
            return 'Division I';
        } elseif ($principalPassesCount >= 2 && $totalPoints >= 10 && $totalPoints <= 12) {
            return 'Division II';
        } elseif ($principalPassesCount >= 1 && $totalPoints >= 13 && $totalPoints <= 17) {
            return 'Division III';
        } elseif ($totalPoints >= 18 && $totalPoints <= 19) {
            return 'Division IV';
        } elseif ($principalPassesCount >= 1 && $totalPoints <= 19) {
            return 'Division IV';
        } else {
            return 'Division 0';
        }
    }
}

