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

    public static function calculateGrade(float $score): array
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
}
