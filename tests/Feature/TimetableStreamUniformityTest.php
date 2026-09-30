<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\Timetable;
use App\Http\Controllers\TimetableController;

class TimetableStreamUniformityTest extends TestCase
{
    use RefreshDatabase;

    public function test_streams_inherit_sibling_subjects_and_maintain_uniformity(): void
    {
        $teacher1 = User::factory()->create([
            'username' => 'teacher_cs',
            'name' => 'CS Teacher',
            'role' => 'Teacher',
            'school_name' => 'Kome Secondary School',
            'email' => 'cs@example.com',
        ]);

        $teacher2 = User::factory()->create([
            'username' => 'teacher_phy',
            'name' => 'Physics Teacher',
            'role' => 'Teacher',
            'school_name' => 'Kome Secondary School',
            'email' => 'phy@example.com',
        ]);

        $csSubject = Subject::create([
            'subject_name' => 'Computer Science',
            'subject_code' => 'CS',
            'category' => 'Core',
        ]);

        $phySubject = Subject::create([
            'subject_name' => 'Physics',
            'subject_code' => 'PHY',
            'category' => 'Core',
        ]);

        // Assign Computer Science to Form 1 A ONLY
        TeacherAssignment::create([
            'teacher_id' => $teacher1->id,
            'subject_id' => $csSubject->id,
            'class_name' => 'Form 1 A',
        ]);

        // Assign Physics to Form 4 A ONLY
        TeacherAssignment::create([
            'teacher_id' => $teacher2->id,
            'subject_id' => $phySubject->id,
            'class_name' => 'Form 4 A',
        ]);

        $controller = app(TimetableController::class);
        $allAssignments = TeacherAssignment::with(['teacher', 'subject'])->get();

        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('getDirectClassPairs');
        $method->setAccessible(true);

        // Form 1 A, Form 1 B, Form 1 C must all have Computer Science
        $p1a = $method->invoke($controller, 'Form 1 A', $allAssignments);
        $p1b = $method->invoke($controller, 'Form 1 B', $allAssignments);
        $p1c = $method->invoke($controller, 'Form 1 C', $allAssignments);

        $this->assertTrue(collect($p1a)->contains('subject_id', $csSubject->id));
        $this->assertTrue(collect($p1b)->contains('subject_id', $csSubject->id));
        $this->assertTrue(collect($p1c)->contains('subject_id', $csSubject->id));

        // Form 4 A and Form 4 B must both have Physics
        $p4a = $method->invoke($controller, 'Form 4 A', $allAssignments);
        $p4b = $method->invoke($controller, 'Form 4 B', $allAssignments);

        $this->assertTrue(collect($p4a)->contains('subject_id', $phySubject->id));
        $this->assertTrue(collect($p4b)->contains('subject_id', $phySubject->id));
    }
}
