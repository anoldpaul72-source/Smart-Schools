<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg_number',
        'student_name',
        'class_name',
        'sex',
        'school_name',
        'parent_id',
        'parent_phone',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    /**
     * Determine if a given class name belongs to Advanced Level (A-Level).
     */
    public static function isClassALevel(?string $className): bool
    {
        if (!$className) {
            return false;
        }

        $cls = strtoupper(trim($className));
        return str_contains($cls, 'FORM 5')
            || str_contains($cls, 'FORM 6')
            || str_contains($cls, 'FORM V')
            || str_contains($cls, 'FORM VI')
            || str_contains($cls, 'F5')
            || str_contains($cls, 'F6')
            || str_contains($cls, 'A-LEVEL')
            || str_contains($cls, 'A LEVEL')
            || str_contains($cls, 'ADVANCED')
            || str_contains($cls, 'KIDATO CHA 5')
            || str_contains($cls, 'KIDATO CHA 6');
    }

    /**
     * Determine if this student is in Advanced Level (A-Level).
     */
    public function isALevel(): bool
    {
        return self::isClassALevel($this->class_name);
    }

    /**
     * Get education level short name ('A-Level' or 'O-Level').
     */
    public function getLevelNameAttribute(): string
    {
        return $this->isALevel() ? 'A-Level' : 'O-Level';
    }

    /**
     * Get education level badge description.
     */
    public function getLevelBadgeAttribute(): string
    {
        return $this->isALevel() ? 'Advanced Level (A-Level)' : 'Ordinary Level (O-Level)';
    }
}
