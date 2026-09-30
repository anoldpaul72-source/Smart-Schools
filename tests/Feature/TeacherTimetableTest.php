<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\Timetable;

class TeacherTimetableTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_timetable_and_sync_schedule(): void
    {
        // Create teacher with username
        $teacher = User::factory()->create([
            'username' => 'anoldsylvester',
            'name' => 'Anold Sylvester',
            'role' => 'Teacher',
            'school_name' => 'Kome Secondary School',
            'email' => 'anold@example.com',
        ]);

        $subject = Subject::create([
            'subject_name' => 'Physics',
            'subject_code' => 'PHY',
            'category' => 'Core',
        ]);

        // Assign teacher to Form 1
        TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'class_name' => 'Form 1',
        ]);

        // Create timetable slot with stream Form 1 A
        Timetable::create([
            'school_name' => 'Kome Secondary School',
            'class_name' => 'Form 1 A',
            'day_of_week' => 'Monday',
            'period_number' => 1,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
        ]);

        $response = $this->actingAs($teacher)->get(route('teacher.timetable'));
        $response->assertStatus(200);
        $response->assertSee('Physics');
        $response->assertSee('Form 1 A');
        $response->assertSee('allocated instruction periods per week');

        // Test sync route
        $syncResponse = $this->actingAs($teacher)->post(route('teacher.timetable.sync'));
        $syncResponse->assertRedirect(route('teacher.timetable'));
        $syncResponse->assertSessionHas('success');
    }

    public function test_admin_teacher_can_view_and_sync_timetable(): void
    {
        // Admin who is also a teacher
        $admin = User::factory()->create([
            'username' => 'admin_teacher',
            'name' => 'Admin Instructor',
            'role' => 'Admin',
            'school_name' => 'Kome Secondary School',
            'email' => 'admin_teacher@example.com',
        ]);

        $subject = Subject::create([
            'subject_name' => 'Mathematics',
            'subject_code' => 'MATH',
            'category' => 'Core',
        ]);

        TeacherAssignment::create([
            'teacher_id' => $admin->id,
            'subject_id' => $subject->id,
            'class_name' => 'Form 2',
        ]);

        $response = $this->actingAs($admin)->get(route('teacher.timetable'));
        $response->assertStatus(200);
        $response->assertSee('Mathematics');
    }
}
