<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamSubjectsConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_academic_master_can_configure_stream_subjects_and_generate_separate_timetables()
    {
        $schoolName = 'Azania Secondary';
        $school = School::create([
            'school_name' => $schoolName,
            'periods_per_day' => 8,
            'class_streams' => ['Form 1' => 2],
        ]);

        $admin = User::factory()->create([
            'username' => 'admin_azania',
            'role' => 'Admin',
            'school_name' => $schoolName,
        ]);

        $teacher = User::factory()->create([
            'username' => 'teacher_azania',
            'role' => 'Teacher',
            'school_name' => $schoolName,
        ]);

        $cs = Subject::create(['subject_name' => 'Computer Science']);
        $phy = Subject::create(['subject_name' => 'Physics']);
        $comm = Subject::create(['subject_name' => 'Commerce']);
        $kisw = Subject::create(['subject_name' => 'Kiswahili']);

        // Assign teacher to all 4 subjects for Form 1
        foreach ([$cs, $phy, $comm, $kisw] as $sub) {
            TeacherAssignment::create([
                'teacher_id' => $teacher->id,
                'subject_id' => $sub->id,
                'class_name' => 'Form 1',
                'school_name' => $schoolName,
            ]);
        }

        // Configure Form 1 A with Computer Science & Physics
        $responseA = $this->actingAs($admin)->post(route('timetable.save_stream_subjects'), [
            'school_name' => $schoolName,
            'stream_class' => 'Form 1 A',
            'subject_ids' => [$cs->id, $phy->id],
        ]);
        $responseA->assertRedirect();

        // Configure Form 1 B with Commerce & Kiswahili
        $responseB = $this->actingAs($admin)->post(route('timetable.save_stream_subjects'), [
            'school_name' => $schoolName,
            'stream_class' => 'Form 1 B',
            'subject_ids' => [$comm->id, $kisw->id],
        ]);
        $responseB->assertRedirect();

        $school->refresh();
        $this->assertEquals([$cs->id, $phy->id], School::getSubjectsForStream('Form 1 A', $school));
        $this->assertEquals([$comm->id, $kisw->id], School::getSubjectsForStream('Form 1 B', $school));

        // Academic subjects for Form 1 A
        $academicA = Subject::getRegisteredAcademicSubjectsForClass('Form 1 A', null, $schoolName)->pluck('subject_name')->toArray();
        $this->assertContains('Computer Science', $academicA);
        $this->assertContains('Physics', $academicA);
        $this->assertNotContains('Commerce', $academicA);

        // Academic subjects for Form 1 B
        $academicB = Subject::getRegisteredAcademicSubjectsForClass('Form 1 B', null, $schoolName)->pluck('subject_name')->toArray();
        $this->assertContains('Commerce', $academicB);
        $this->assertContains('Kiswahili', $academicB);
        $this->assertNotContains('Computer Science', $academicB);
        $this->assertNotContains('Physics', $academicB);

        // Check timetable slots generated for each stream
        $f1aSlots = Timetable::where('school_name', $schoolName)->where('class_name', 'Form 1 A')->with('subject')->get();
        $f1bSlots = Timetable::where('school_name', $schoolName)->where('class_name', 'Form 1 B')->with('subject')->get();

        $f1aSubs = $f1aSlots->pluck('subject.subject_name')->filter()->unique()->toArray();
        $f1bSubs = $f1bSlots->pluck('subject.subject_name')->filter()->unique()->toArray();

        $this->assertNotEmpty($f1aSubs);
        $this->assertContains('Computer Science', $f1aSubs);
        $this->assertNotContains('Commerce', $f1aSubs);

        $this->assertNotEmpty($f1bSubs);
        $this->assertContains('Commerce', $f1bSubs);
        $this->assertNotContains('Computer Science', $f1bSubs);
        $this->assertNotContains('Physics', $f1bSubs);
    }
}
