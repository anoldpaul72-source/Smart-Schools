<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\School;
use App\Models\Subject;
use App\Models\Student;
use App\Models\TeacherAssignment;
use App\Models\FeeStructure;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::where('username', 'admin')->exists()) {
            return;
        }

        // 1. Demo School
        $school = School::create([
            'school_name' => 'Smart Academy',
            'address'     => 'Dar es Salaam, Tanzania',
            'phone'       => '+255 700 000 000',
        ]);

        // 2. Users
        $admin = User::create([
            'username'    => 'admin',
            'name'        => 'System Administrator',
            'email'       => 'admin@smartresults.com',
            'role'        => 'Admin',
            'school_name' => 'Smart Academy',
            'password'    => Hash::make('password123'),
        ]);

        $teacher = User::create([
            'username'    => 'teacher1',
            'name'        => 'Teacher Michael',
            'email'       => 'teacher@smartresults.com',
            'role'        => 'Teacher',
            'school_name' => 'Smart Academy',
            'password'    => Hash::make('password123'),
        ]);

        $parent = User::create([
            'username'    => 'parent1',
            'name'        => 'Mama Kelvin',
            'email'       => 'parent@smartresults.com',
            'role'        => 'Parent',
            'school_name' => 'Smart Academy',
            'password'    => Hash::make('password123'),
        ]);

        $leader = User::create([
            'username'    => 'headmaster1',
            'name'        => 'Headmaster Juma',
            'email'       => 'headmaster@smartresults.com',
            'role'        => 'Headmaster',
            'school_name' => 'Smart Academy',
            'password'    => Hash::make('password123'),
        ]);

        $accountant = User::create([
            'username'    => 'accountant1',
            'name'        => 'Accountant Sarah',
            'email'       => 'accountant@smartresults.com',
            'role'        => 'Accountant',
            'school_name' => 'Smart Academy',
            'password'    => Hash::make('password123'),
        ]);

        $librarian = User::create([
            'username'    => 'librarian1',
            'name'        => 'Librarian Baraka',
            'email'       => 'librarian@smartresults.com',
            'role'        => 'Librarian',
            'school_name' => 'Smart Academy',
            'password'    => Hash::make('password123'),
        ]);

        // 3. Subjects
        $math = Subject::create(['subject_name' => 'Mathematics']);
        $eng  = Subject::create(['subject_name' => 'English']);
        $kisw = Subject::create(['subject_name' => 'Kiswahili']);
        $sci  = Subject::create(['subject_name' => 'Science']);
        $geo  = Subject::create(['subject_name' => 'Geography']);

        // 4. Teacher Assignment
        TeacherAssignment::create([
            'teacher_id'  => $teacher->id,
            'subject_id'  => $math->id,
            'class_name'  => 'Form 1',
            'school_name' => 'Smart Academy',
        ]);

        // 5. Student
        $student = Student::create([
            'reg_number'   => 'STD-2026-001',
            'student_name' => 'Kelvin Michael',
            'class_name'   => 'Form 1',
            'sex'          => 'M',
            'school_name'  => 'Smart Academy',
            'parent_id'    => $parent->id,
        ]);

        // 6. Fee Structure
        FeeStructure::create([
            'school_name'   => 'Smart Academy',
            'class_name'    => 'Form 1',
            'academic_year' => '2026',
            'total_amount'  => 850000.00,
        ]);

        // 7. Parent Abel Sesemkwa & Student Emmanuel Abel Sesemkwa
        $parentAbel = User::firstOrCreate(
            ['username' => 'abel'],
            [
                'name'        => 'ABEL SESEMKWA',
                'email'       => 'abel@smartresults.com',
                'role'        => 'Parent',
                'school_name' => 'Kome Secondary School',
                'password'    => Hash::make('password123'),
            ]
        );

        Student::firstOrCreate(
            ['reg_number' => 'STD-2026-002'],
            [
                'student_name' => 'EMMANUEL ABEL SESEMKWA',
                'class_name'   => 'Form 1',
                'sex'          => 'M',
                'school_name'  => 'Kome Secondary School',
                'parent_id'    => $parentAbel->id,
            ]
        );

        FeeStructure::firstOrCreate(
            [
                'school_name'   => 'Kome Secondary School',
                'class_name'    => 'Form 1',
                'academic_year' => '2026',
            ],
            [
                'total_amount'  => 20000.00,
            ]
        );

        // 8. Timetable Demo Data
        $this->call(TimetableDemoSeeder::class);
    }
}
