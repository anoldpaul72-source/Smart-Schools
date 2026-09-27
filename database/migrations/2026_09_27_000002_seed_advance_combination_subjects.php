<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $advanceSubjects = [
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

        foreach ($advanceSubjects as $subjectName) {
            $exists = DB::table('subjects')
                ->whereRaw('LOWER(TRIM(subject_name)) = ?', [strtolower(trim($subjectName))])
                ->exists();

            if (!$exists) {
                DB::table('subjects')->insert([
                    'subject_name' => $subjectName,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe to leave subjects in place
    }
};
