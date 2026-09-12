<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to update existing student marks to match the new grading scale:
     * 75-100 = A (Excellent)
     * 60-74  = B (Very Good)
     * 45-59  = C (Good)
     * 30-44  = D (Pass)
     * 0-29   = F (Fail)
     */
    public function up(): void
    {
        // 75 - 100 => A
        DB::table('marks')
            ->where('marks', '>=', 75)
            ->update([
                'grade'   => 'A',
                'remarks' => 'Excellent',
            ]);

        // 60 - 74 => B
        DB::table('marks')
            ->where('marks', '>=', 60)
            ->where('marks', '<', 75)
            ->update([
                'grade'   => 'B',
                'remarks' => 'Very Good',
            ]);

        // 45 - 59 => C
        DB::table('marks')
            ->where('marks', '>=', 45)
            ->where('marks', '<', 60)
            ->update([
                'grade'   => 'C',
                'remarks' => 'Good',
            ]);

        // 30 - 44 => D
        DB::table('marks')
            ->where('marks', '>=', 30)
            ->where('marks', '<', 45)
            ->update([
                'grade'   => 'D',
                'remarks' => 'Pass',
            ]);

        // 0 - 29 => F
        DB::table('marks')
            ->where('marks', '<', 30)
            ->update([
                'grade'   => 'F',
                'remarks' => 'Fail',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
