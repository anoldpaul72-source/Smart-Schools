<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'address',
        'phone',
        'periods_per_day',
        'breakfast_time',
        'breakfast_after_period',
        'lunch_time',
        'lunch_after_period',
        'class_start_time',
        'period_duration',
    ];

    /**
     * Get timetable configuration for a specific school, including dynamically computed period time slots.
     */
    public static function getTimetableConfig(string $schoolName): array
    {
        $schoolName = trim($schoolName) ?: 'Kome Secondary School';

        $school = null;
        if (Schema::hasTable('schools')) {
            $school = self::where('school_name', $schoolName)->first();
            if (!$school) {
                $school = self::firstOrCreate(['school_name' => $schoolName]);
            }
        }

        $periodsPerDay        = max(4, min(14, (int) ($school->periods_per_day ?? 10)));
        $breakfastTime        = trim($school->breakfast_time ?? '') ?: '11:20 - 11:40';
        $breakfastAfterPeriod = (int) ($school->breakfast_after_period ?? 5);
        $lunchTime            = trim($school->lunch_time ?? '') ?: '14:20 - 15:00';
        $lunchAfterPeriod     = (int) ($school->lunch_after_period ?? 9);
        $classStartTime       = trim($school->class_start_time ?? '') ?: '08:00';
        $periodDuration       = max(20, min(120, (int) ($school->period_duration ?? 40)));

        // Ensure break positions make sense relative to periods_per_day
        if ($breakfastAfterPeriod >= $periodsPerDay) {
            $breakfastAfterPeriod = max(1, (int) floor($periodsPerDay / 2));
        }
        if ($lunchAfterPeriod > $periodsPerDay) {
            $lunchAfterPeriod = max($breakfastAfterPeriod + 1, $periodsPerDay - 1);
        }

        $periodSlots = self::buildPeriodSlots(
            $periodsPerDay,
            $classStartTime,
            $periodDuration,
            $breakfastTime,
            $breakfastAfterPeriod,
            $lunchTime,
            $lunchAfterPeriod
        );

        return [
            'school'                 => $school,
            'school_name'            => $schoolName,
            'periods_per_day'        => $periodsPerDay,
            'breakfast_time'         => $breakfastTime,
            'breakfast_after_period' => $breakfastAfterPeriod,
            'lunch_time'             => $lunchTime,
            'lunch_after_period'     => $lunchAfterPeriod,
            'class_start_time'       => $classStartTime,
            'period_duration'        => $periodDuration,
            'period_slots'           => $periodSlots,
        ];
    }

    /**
     * Build human-readable time range labels for each period 1..$periodsPerDay.
     */
    public static function buildPeriodSlots(
        int $periodsPerDay,
        string $classStartTime,
        int $periodDuration,
        string $breakfastTime,
        int $breakfastAfterPeriod,
        string $lunchTime,
        int $lunchAfterPeriod
    ): array {
        $currentMin = self::parseTimeToMinutes($classStartTime) ?? (8 * 60); // default 08:00
        $bfRange    = self::parseRangeToMinutes($breakfastTime);
        $lnRange    = self::parseRangeToMinutes($lunchTime);

        $slots = [];
        for ($p = 1; $p <= $periodsPerDay; $p++) {
            $startMin = $currentMin;
            $endMin   = $startMin + $periodDuration;

            $slots[$p] = self::formatMinutesToAmPm($startMin) . ' - ' . self::formatMinutesToAmPm($endMin);
            $currentMin = $endMin;

            // Advance clock for Breakfast Break after $breakfastAfterPeriod
            if ($breakfastAfterPeriod > 0 && $p === $breakfastAfterPeriod) {
                if ($bfRange) {
                    $bfDuration = max(0, $bfRange[1] - $bfRange[0]);
                    if ($bfRange[1] > $currentMin) {
                        $currentMin = $bfRange[1];
                    } else {
                        $currentMin += ($bfDuration ?: 20);
                    }
                } else {
                    $currentMin += 20;
                }
            }

            // Advance clock for Lunch Break after $lunchAfterPeriod
            if ($lunchAfterPeriod > 0 && $p === $lunchAfterPeriod) {
                if ($lnRange) {
                    $lnDuration = max(0, $lnRange[1] - $lnRange[0]);
                    if ($lnRange[1] > $currentMin) {
                        $currentMin = $lnRange[1];
                    } else {
                        $currentMin += ($lnDuration ?: 40);
                    }
                } else {
                    $currentMin += 40;
                }
            }
        }

        return $slots;
    }

    public static function parseTimeToMinutes(string $timeStr): ?int
    {
        $timeStr = trim($timeStr);
        if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)?$/i', $timeStr, $m)) {
            $h = (int) $m[1];
            $min = (int) $m[2];
            $ampm = isset($m[3]) ? strtoupper($m[3]) : null;
            if ($ampm === 'PM' && $h < 12) {
                $h += 12;
            } elseif ($ampm === 'AM' && $h === 12) {
                $h = 0;
            }
            return ($h * 60) + $min;
        }
        return null;
    }

    public static function parseRangeToMinutes(string $rangeStr): ?array
    {
        $parts = preg_split('/\s*[-–to]+\s*/i', trim($rangeStr));
        if (count($parts) >= 2) {
            $s = self::parseTimeToMinutes($parts[0]);
            $e = self::parseTimeToMinutes($parts[1]);
            if ($s !== null && $e !== null) {
                if ($e < $s) {
                    $e += 12 * 60;
                }
                return [$s, $e];
            }
        }
        return null;
    }

    public static function formatMinutesToAmPm(int $totalMinutes): string
    {
        $totalMinutes = ($totalMinutes % (24 * 60) + (24 * 60)) % (24 * 60);
        $h24 = (int) floor($totalMinutes / 60);
        $min = $totalMinutes % 60;
        $ampm = $h24 >= 12 ? 'PM' : 'AM';
        $h12 = $h24 % 12;
        if ($h12 === 0) {
            $h12 = 12;
        }
        return sprintf('%02d:%02d %s', $h12, $min, $ampm);
    }
}
