<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Mark;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Services\BeemSmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportSubjectsAndLanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_sms_builder_supports_english_and_swahili_with_all_registered_class_subjects()
    {
        $student = Student::create([
            'student_name' => 'Kelvin Michael',
            'reg_number'   => 'S0123/0001',
            'school_name'  => 'Kome Secondary School',
            'class_name'   => 'Form 1',
            'parent_phone' => '0712345678',
        ]);

        $subMath = Subject::create(['subject_name' => 'Basic Mathematics']);
        $subEng  = Subject::create(['subject_name' => 'English Language']);
        $subKisw = Subject::create(['subject_name' => 'Kiswahili']);
        $subBio  = Subject::create(['subject_name' => 'Biology']);

        $teacher = User::create([
            'name'     => 'Teacher One',
            'username' => 'teacher_' . uniqid(),
            'email'    => 'teacher_test_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role'     => 'Teacher',
        ]);
        TeacherAssignment::create(['teacher_id' => $teacher->id, 'class_name' => 'Form 1', 'subject_id' => $subMath->id]);
        TeacherAssignment::create(['teacher_id' => $teacher->id, 'class_name' => 'Form 1', 'subject_id' => $subEng->id]);
        TeacherAssignment::create(['teacher_id' => $teacher->id, 'class_name' => 'Form 1', 'subject_id' => $subKisw->id]);
        TeacherAssignment::create(['teacher_id' => $teacher->id, 'class_name' => 'Form 1', 'subject_id' => $subBio->id]);

        // Student only has marks for Mathematics
        Mark::create([
            'student_id' => $student->id,
            'subject_id' => $subMath->id,
            'term'       => 'Terminal Examination',
            'marks'      => 75,
            'exam_date'  => '2026-06-15',
        ]);

        $service = new BeemSmsService();

        // 1. Swahili Report
        $swText = $service->buildStudentReportText($student, 'Terminal Examination', 'sw');
        $this->assertStringContainsString('MZAZI WA KELVIN MICHAEL', $swText);
        $this->assertStringContainsString('Ripoti:', $swText);
        $this->assertStringContainsString('Matokeo:', $swText);
        $this->assertStringContainsString('Math: 75', $swText);
        $this->assertStringContainsString('Eng: -', $swText);
        $this->assertStringContainsString('Kisw: -', $swText);
        $this->assertStringContainsString('Bio: -', $swText);
        $this->assertStringContainsString('Wastani:', $swText);
        $this->assertStringContainsString('Mahudhurio:', $swText);
        $this->assertStringContainsString('Kazi nzuri na hongera.', $swText);

        // 2. English Report
        $enText = $service->buildStudentReportText($student, 'Terminal Examination', 'en');
        $this->assertStringContainsString('PARENT OF KELVIN MICHAEL', $enText);
        $this->assertStringContainsString('Report:', $enText);
        $this->assertStringContainsString('Results:', $enText);
        $this->assertStringContainsString('Math: 75', $enText);
        $this->assertStringContainsString('Eng: -', $enText);
        $this->assertStringContainsString('Kisw: -', $enText);
        $this->assertStringContainsString('Bio: -', $enText);
        $this->assertStringContainsString('Average:', $enText);
        $this->assertStringContainsString('Attendance:', $enText);
        $this->assertStringContainsString('Good job and congratulations.', $enText);
    }

    public function test_parent_report_displays_all_registered_class_subjects_including_pending()
    {
        $parent = User::create([
            'name'     => 'Parent User',
            'username' => 'parent_' . uniqid(),
            'email'    => 'parent_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role'     => 'Parent',
        ]);

        $student = Student::create([
            'student_name' => 'Neema Juma',
            'reg_number'   => 'S0123/0002',
            'school_name'  => 'Kome Secondary School',
            'class_name'   => 'Form 1',
            'parent_phone' => '0788111222',
            'user_id'      => $parent->id,
        ]);

        $subMath = Subject::create(['subject_name' => 'Basic Mathematics']);
        $subCiv  = Subject::create(['subject_name' => 'Civics']);
        $subChem = Subject::create(['subject_name' => 'Chemistry']);

        $teacher = User::create([
            'name'     => 'Teacher Two',
            'username' => 'teacher_' . uniqid(),
            'email'    => 'teacher_two_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role'     => 'Teacher',
        ]);
        TeacherAssignment::create(['teacher_id' => $teacher->id, 'class_name' => 'Form 1', 'subject_id' => $subMath->id]);
        TeacherAssignment::create(['teacher_id' => $teacher->id, 'class_name' => 'Form 1', 'subject_id' => $subCiv->id]);
        TeacherAssignment::create(['teacher_id' => $teacher->id, 'class_name' => 'Form 1', 'subject_id' => $subChem->id]);

        // Student only has marks for Civics
        Mark::create([
            'student_id' => $student->id,
            'subject_id' => $subCiv->id,
            'term'       => 'Annual Examination',
            'marks'      => 80,
            'exam_date'  => '2026-11-20',
        ]);

        $response = $this->actingAs($parent)->get(route('parent.reports', [
            'student_id'  => $student->id,
            'report_type' => 'Annual Examination',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Civics');
        $response->assertSee('Basic Mathematics');
        $response->assertSee('Chemistry');
    }
}
