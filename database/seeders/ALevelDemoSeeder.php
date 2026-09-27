<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Mark;
use App\Models\Attendance;

class ALevelDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Subjects exist
        $gs   = Subject::firstOrCreate(['subject_name' => 'General Studies']);
        $bam  = Subject::firstOrCreate(['subject_name' => 'Basic Applied Mathematics']);
        $phy  = Subject::firstOrCreate(['subject_name' => 'Physics']);
        $chem = Subject::firstOrCreate(['subject_name' => 'Chemistry']);
        $bio  = Subject::firstOrCreate(['subject_name' => 'Biology']);
        $hist = Subject::firstOrCreate(['subject_name' => 'History']);
        $geo  = Subject::firstOrCreate(['subject_name' => 'Geography']);
        $eng  = Subject::firstOrCreate(['subject_name' => 'English']);

        // 2. Form 5 PCB Student
        $st5 = Student::updateOrCreate(
            ['reg_number' => 'S.0123/0501'],
            [
                'student_name' => 'Kelvin Michael Senior',
                'class_name'   => 'Form 5 PCB',
                'sex'          => 'M',
                'school_name'  => 'Smart Academy',
                'parent_id'    => 3,
                'parent_phone' => '0712345678',
            ]
        );

        $examDate = '2026-09-20';

        // Marks for Kelvin (Form 5 PCB)
        $marks5 = [
            [$gs->id,   68.0, 'C', 'Vizuri, aongeze bidii katika masuala ya kimataifa'],
            [$bam->id,  74.0, 'B', 'Amefanya vizuri sana katika mahesabu ya vitendo'],
            [$phy->id,  85.0, 'A', 'Bora sana, uelewa wa hali ya juu katika fizikia'],
            [$chem->id, 82.0, 'A', 'Bora sana, anafanya majaribio ya maabara kwa ufasaha'],
            [$bio->id,  89.0, 'A', 'Ufaulu wa kiwango cha juu sana wa biolojia'],
        ];

        foreach ($marks5 as [$subId, $score, $grd, $rmk]) {
            Mark::updateOrCreate(
                ['student_id' => $st5->id, 'subject_id' => $subId, 'term' => 'Annual Examination'],
                [
                    'marks'     => $score,
                    'grade'     => $grd,
                    'remarks'   => $rmk,
                    'exam_date' => $examDate,
                ]
            );
        }

        // Attendance for Kelvin
        for ($day = 1; $day <= 10; $day++) {
            Attendance::updateOrCreate(
                ['student_id' => $st5->id, 'date' => "2026-09-" . sprintf('%02d', $day), 'period_number' => 1],
                ['status' => 'Present', 'subject_id' => $phy->id]
            );
        }

        // 3. Form 6 HGL Student
        $st6 = Student::updateOrCreate(
            ['reg_number' => 'S.0123/0602'],
            [
                'student_name' => 'Amina Juma Nassoro',
                'class_name'   => 'Form 6 HGL',
                'sex'          => 'F',
                'school_name'  => 'Smart Academy',
                'parent_id'    => 13,
                'parent_phone' => '0754123456',
            ]
        );

        $marks6 = [
            [$gs->id,   72.0, 'B', 'Vizuri sana, anashiriki vizuri mijadala'],
            [$bam->id,  60.0, 'C', 'Wastani mzuri, aendelee kufanya mazoezi'],
            [$hist->id, 78.0, 'B', 'Uchambuzi mzuri sana wa historia ya Afrika'],
            [$geo->id,  84.0, 'A', 'Kazi nzuri sana katika ramani na jiografia'],
            [$eng->id,  75.0, 'B', 'Uwezo mkubwa wa lugha na insha za kiingereza'],
        ];

        foreach ($marks6 as [$subId, $score, $grd, $rmk]) {
            Mark::updateOrCreate(
                ['student_id' => $st6->id, 'subject_id' => $subId, 'term' => 'Annual Examination'],
                [
                    'marks'     => $score,
                    'grade'     => $grd,
                    'remarks'   => $rmk,
                    'exam_date' => $examDate,
                ]
            );
        }
    }
}
