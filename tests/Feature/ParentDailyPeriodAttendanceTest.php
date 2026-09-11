<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Attendance;
use App\Models\Timetable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentDailyPeriodAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_view_daily_period_attendance_for_child(): void
    {
        $parent = User::factory()->create([
            'name' => 'Siphaely Misango',
            'username' => 'parent_siphaely',
            'role' => 'Parent',
        ]);

        $student = Student::create([
            'student_name' => 'Aimidiwe Siphaely Misango',
            'parent_id' => $parent->id,
            'class_name' => 'Form 1',
            'school_name' => 'Kome Secondary School',
            'reg_number' => 'SS/2026/001',
        ]);

        $subject = Subject::firstOrCreate(['subject_name' => 'Biology']);
        $teacher = User::factory()->create([
            'role' => 'Teacher',
            'name' => 'Nzaria Waziri',
            'username' => 'nzaria_teacher',
        ]);

        // Timetable period 1 on Friday
        Timetable::create([
            'school_name' => 'Kome Secondary School',
            'class_name' => 'Form 1',
            'day_of_week' => 'Friday',
            'period_number' => 1,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
        ]);

        // Record attendance for period 1 on 2026-09-11
        Attendance::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'period_number' => 1,
            'date' => '2026-09-11',
            'status' => 'Present',
            'recorded_by' => $teacher->id,
        ]);

        $responseSw = $this->actingAs($parent)
            ->withSession(['locale' => 'sw'])
            ->get(route('parent.reports', [
                'student_id' => $student->id,
                'attendance_date' => '2026-09-11',
            ]));

        $responseSw->assertStatus(200);
        $responseSw->assertSee('Mahudhurio ya Kila Kipindi Kila Siku');
        $responseSw->assertSee('Biology');
        $responseSw->assertSee('Alihudhuria');

        $responseEn = $this->actingAs($parent)
            ->withSession(['locale' => 'en'])
            ->get(route('parent.reports', [
                'student_id' => $student->id,
                'attendance_date' => '2026-09-11',
            ]));

        $responseEn->assertStatus(200);
        $responseEn->assertSee('Daily Period-by-Period Attendance');
        $responseEn->assertSee('Biology');
        $responseEn->assertSee('Present');
    }

    public function test_teacher_can_record_attendance_for_specific_period(): void
    {
        $teacher = User::factory()->create([
            'name' => 'Nzaria Waziri',
            'username' => 'nzaria_waziri',
            'role' => 'Teacher',
            'school_name' => 'Kome Secondary School',
        ]);

        $student = Student::create([
            'student_name' => 'Aimidiwe Siphaely Misango',
            'class_name' => 'Form 1',
            'school_name' => 'Kome Secondary School',
            'reg_number' => 'SS/2026/002',
        ]);

        $response = $this->actingAs($teacher)->post(route('teacher.attendance.store'), [
            'class_name' => 'Form 1',
            'period_number' => 2,
            'date' => '2026-09-11',
            'status' => [
                $student->id => 'Present',
            ],
        ]);

        $response->assertRedirect(route('teacher.attendance', [
            'class_name' => 'Form 1',
            'period_number' => 2,
            'date' => '2026-09-11',
        ]));

        $this->assertDatabaseHas('attendance', [
            'student_id' => $student->id,
            'period_number' => 2,
            'date' => '2026-09-11',
            'status' => 'Present',
            'recorded_by' => $teacher->id,
        ]);
    }
}
