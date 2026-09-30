<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeaderAcademicResultClassSubjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_leader_dashboard_only_shows_registered_subjects_for_o_level_class()
    {
        $school = 'Kome Secondary School';

        $leader = User::factory()->create([
            'username' => 'hos_leader_test',
            'role' => 'Head of School',
            'school_name' => $school,
        ]);

        // Create O-Level subjects
        $civics = Subject::create(['subject_name' => 'Civics']);
        $math = Subject::create(['subject_name' => 'Mathematics']);
        $english = Subject::create(['subject_name' => 'English']);
        $bio = Subject::create(['subject_name' => 'Biology']);

        // Create Advance-only subjects that should NOT appear in Form 1
        $advMath = Subject::create(['subject_name' => 'Advanced Mathematics']);
        $bam = Subject::create(['subject_name' => 'Basic Applied Mathematics']);
        $gs = Subject::create(['subject_name' => 'General Studies']);
        $acct = Subject::create(['subject_name' => 'Accountancy']);

        // Register teachers / subjects for Form 1
        $teacher = User::factory()->create([
            'username' => 'teacher_f1_test',
            'role' => 'Teacher',
            'school_name' => $school,
        ]);

        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $civics->id,
            'class_name' => 'Form 1',
            'school_name' => $school,
        ]);
        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $math->id,
            'class_name' => 'Form 1',
            'school_name' => $school,
        ]);
        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $english->id,
            'class_name' => 'Form 1',
            'school_name' => $school,
        ]);

        // Create Form 1 student
        Student::create([
            'student_name' => 'John Doe',
            'reg_number' => 'S0762/0001',
            'class_name' => 'Form 1',
            'school_name' => $school,
            'sex' => 'M',
        ]);

        $response = $this->actingAs($leader)->get(route('leader.dashboard', [
            'class' => 'Form 1',
            'exam_type' => 'Terminal Examination',
        ]));

        $response->assertStatus(200);

        // Registered Form 1 subjects must be present in the view
        $response->assertSee('Civics');
        $response->assertSee('Mathematics');
        $response->assertSee('English');

        // Advance-only subjects must NOT be displayed for Form 1
        $response->assertDontSee('Advanced Mathematics');
        $response->assertDontSee('Basic Applied Mathematics');
        $response->assertDontSee('General Studies');
        $response->assertDontSee('Accountancy');
    }
}
