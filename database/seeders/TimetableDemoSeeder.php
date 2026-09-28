<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\User;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\Timetable;
use Illuminate\Support\Facades\Hash;

class TimetableDemoSeeder extends Seeder
{
    public function run()
    {
        $school = School::firstOrCreate(
            ['school_name' => 'Kome Secondary School'],
            ['address' => 'Geita, Tanzania', 'phone' => '+255 700 000 001']
        );

        User::where('username', 'admin')->update(['school_name' => 'Kome Secondary School']);
        User::where('username', 'headmaster1')->update(['school_name' => 'Kome Secondary School']);

        // Full faculty of registered teachers matching real school operations
        $teachersData = [
            'Anold Sylvester'  => ['email' => 'anold@kome.ac.tz',     'role' => 'Teacher'],
            'Bakari Mkali'     => ['email' => 'bakarim@kome.ac.tz',   'role' => 'Teacher'],
            'Emily Lusuva'     => ['email' => 'emilyl@kome.ac.tz',    'role' => 'Teacher'],
            'Hamis Kimosa'     => ['email' => 'hamis@kome.ac.tz',     'role' => 'Teacher'],
            'Waziri Nzalia'    => ['email' => 'waziri@kome.ac.tz',    'role' => 'Teacher'],
            'Hassan Hussein'   => ['email' => 'hassan@kome.ac.tz',    'role' => 'Teacher'],
            'Jordan Tabisho'   => ['email' => 'jordan@kome.ac.tz',    'role' => 'Teacher'],
            'Philimon Mataba'  => ['email' => 'philimon@kome.ac.tz',  'role' => 'Teacher'],
            'Domina Chipi'     => ['email' => 'domina@kome.ac.tz',    'role' => 'Teacher'],
            'Michael Masombo'  => ['email' => 'michaelm@kome.ac.tz',  'role' => 'Teacher'],
            'Akida Kidiko'     => ['email' => 'akida@kome.ac.tz',     'role' => 'Teacher'],
            'Tumaini Pandelini'=> ['email' => 'tumaini@kome.ac.tz',   'role' => 'Teacher'],
            'Ayubu Neubuni'    => ['email' => 'ayubu@kome.ac.tz',     'role' => 'Teacher'],
            'Seni Salumu'      => ['email' => 'seni@kome.ac.tz',      'role' => 'Teacher'],
            'Yohana Petro'     => ['email' => 'yohana@kome.ac.tz',    'role' => 'Academic Master'],
            'Monica Makulo'    => ['email' => 'monica@kome.ac.tz',    'role' => 'Teacher'],
            'Mwidini Barnabas' => ['email' => 'mwidini@kome.ac.tz',   'role' => 'Teacher'],
            'Justine Priscus'  => ['email' => 'justine@kome.ac.tz',   'role' => 'Teacher'],
        ];

        $teachers = [];
        foreach ($teachersData as $tName => $tMeta) {
            $username = strtolower(str_replace(' ', '', $tName));
            $user = User::where('username', $username)
                ->orWhere('name', $tName)
                ->first();

            if (!$user) {
                $user = User::create([
                    'username'    => $username,
                    'name'        => $tName,
                    'email'       => $tMeta['email'],
                    'password'    => Hash::make('password123'),
                    'role'        => $tMeta['role'],
                    'school_name' => 'Kome Secondary School',
                ]);
            } else {
                $user->update([
                    'name'        => $tName,
                    'role'        => $tMeta['role'],
                    'school_name' => 'Kome Secondary School',
                ]);
            }
            $teachers[$tName] = $user;
        }

        // Keep legacy teachers mapped if any
        if (isset($teachers['Waziri Nzalia'])) {
            User::where('username', 'nzariawaziri')->update(['name' => 'Waziri Nzalia', 'school_name' => 'Kome Secondary School']);
        }
        if (isset($teachers['Emily Lusuva'])) {
            User::where('username', 'emily')->update(['name' => 'Emily Lusuva', 'school_name' => 'Kome Secondary School']);
        }
        if (isset($teachers['Bakari Mkali'])) {
            User::where('username', 'bakari')->update(['name' => 'Bakari Mkali', 'school_name' => 'Kome Secondary School']);
        }

        // Subjects
        $allSubjectNames = [
            // O-Level & Core
            'Mathematics', 'English', 'Kiswahili', 'Biology',
            'Chemistry', 'Physics', 'History', 'Geography',
            'Civics', 'Business', 'Computer Science',
            // Advance (A-Level)
            'Basic Applied Mathematics', 'Advanced Mathematics',
            'General Studies', 'Economics', 'Commerce', 'Accountancy',
            // Special Activities
            'Religion', 'Debate or Subject Club', 'Sports and Games',
            'Discussion and Examinations', 'General Studies Seminar',
            'Laboratory Practicals and Research'
        ];

        $subjects = [];
        foreach ($allSubjectNames as $sName) {
            $subjects[$sName] = Subject::firstOrCreate(['subject_name' => $sName]);
        }

        // Teacher Assignments: Assign teachers to classes and subjects
        // O-Level classes assignments (Form 1 - 4)
        $olevelClasses = ['Form 1', 'Form 2', 'Form 3', 'Form 4'];
        $olevelMapping = [
            'Mathematics'      => 'Anold Sylvester',
            'English'          => 'Bakari Mkali',
            'Kiswahili'        => 'Emily Lusuva',
            'Geography'        => 'Hamis Kimosa',
            'Biology'          => 'Waziri Nzalia',
            'Business'         => 'Hassan Hussein',
            'Chemistry'        => 'Jordan Tabisho',
            'Civics'           => 'Philimon Mataba',
            'Computer Science' => 'Domina Chipi',
            'History'          => 'Michael Masombo',
            'Physics'          => 'Akida Kidiko',
        ];

        foreach ($olevelClasses as $cls) {
            foreach ($olevelMapping as $subName => $teachName) {
                TeacherAssignment::firstOrCreate([
                    'school_name' => 'Kome Secondary School',
                    'class_name'  => $cls,
                    'subject_id'  => $subjects[$subName]->id,
                ], [
                    'teacher_id'  => $teachers[$teachName]->id,
                ]);
            }
        }

        // Advance classes assignments (Form 5, Form 6, Form 5 PCB, Form 6 HGL)
        $advanceClasses = ['Form 5', 'Form 6', 'Form 5 PCB', 'Form 6 HGL'];
        $advanceMapping = [
            'Basic Applied Mathematics' => 'Monica Makulo',
            'General Studies'           => 'Monica Makulo',
            'Advanced Mathematics'      => 'Mwidini Barnabas',
            'Economics'                 => 'Justine Priscus',
            'Physics'                   => 'Mwidini Barnabas',
            'Chemistry'                 => 'Jordan Tabisho',
            'Biology'                   => 'Waziri Nzalia',
            'Geography'                 => 'Hamis Kimosa',
            'History'                   => 'Michael Masombo',
            'English'                   => 'Bakari Mkali',
            'Kiswahili'                 => 'Emily Lusuva',
            'Computer Science'          => 'Domina Chipi',
        ];

        foreach ($advanceClasses as $cls) {
            foreach ($advanceMapping as $subName => $teachName) {
                TeacherAssignment::firstOrCreate([
                    'school_name' => 'Kome Secondary School',
                    'class_name'  => $cls,
                    'subject_id'  => $subjects[$subName]->id,
                ], [
                    'teacher_id'  => $teachers[$teachName]->id,
                ]);
            }
        }
    }
}
