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
    ];

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
}
