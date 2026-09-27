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
        'combination',
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

    public const COMBINATIONS = [
        'HKL' => 'History, Kiswahili, English Language (HKL)',
        'HGK' => 'History, Geography, Kiswahili (HGK)',
        'HGL' => 'History, Geography, English Language (HGL)',
        'HGE' => 'History, Geography, Economics (HGE)',
        'PCM' => 'Physics, Chemistry, Advanced Mathematics (PCM)',
        'PCB' => 'Physics, Chemistry, Biology (PCB)',
        'PGM' => 'Physics, Geography, Advanced Mathematics (PGM)',
        'CBG' => 'Chemistry, Biology, Geography (CBG)',
        'CBA' => 'Chemistry, Biology, Agriculture (CBA)',
        'CBN' => 'Chemistry, Biology, Nutrition (CBN)',
        'PMC' => 'Physics, Mathematics, Computer Science (PMC)',
        'EGM' => 'Economics, Geography, Advanced Mathematics (EGM)',
        'ECA' => 'Economics, Commerce, Accountancy (ECA)',
        'KLF' => 'Kiswahili, English Language, French (KLF)',
        'KEC' => 'Kiswahili, Economics, Commerce (KEC)',
    ];

    /**
     * Get effective combination, checking either combination column or class_name.
     */
    public function getEffectiveCombinationAttribute(): ?string
    {
        if (!empty($this->attributes['combination'])) {
            return strtoupper(trim($this->attributes['combination']));
        }

        if (!empty($this->class_name)) {
            if (preg_match('/\b(PCB|PCM|PGM|CBG|CBA|CBN|PMC|EGM|ECA|HGL|HKL|HGE|HGK|KLF|KEC)\b/i', $this->class_name, $m)) {
                return strtoupper($m[1]);
            }
        }

        return null;
    }

    /**
     * Get effective parent phone number.
     */
    public function getEffectiveParentPhoneAttribute(): ?string
    {
        return $this->parent_phone ?: $this->parent?->phone;
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
        $comb = $this->effective_combination;
        if ($this->isALevel()) {
            return $comb ? "A-Level ({$comb})" : 'Advanced Level (A-Level)';
        }
        return 'Ordinary Level (O-Level)';
    }
}
