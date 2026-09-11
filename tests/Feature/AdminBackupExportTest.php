<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminBackupExportTest extends TestCase
{
    public function test_guest_cannot_access_backup_export()
    {
        $response = $this->get(route('admin.backup.export'));
        $response->assertRedirect('/login');
    }

    public function test_teacher_cannot_access_backup_export()
    {
        $teacher = User::factory()->make([
            'id'       => 998,
            'role'     => 'Teacher',
            'username' => 'teacher_test',
        ]);

        $response = $this->actingAs($teacher)->get(route('admin.backup.export'));
        $response->assertStatus(403);
    }

    public function test_admin_can_export_backup_json()
    {
        $admin = User::factory()->make([
            'id'       => 999,
            'role'     => 'Admin',
            'username' => 'admin_test',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.backup.export', ['format' => 'json']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json; charset=UTF-8');
        
        $data = $response->json();
        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('database', $data);
        $this->assertArrayHasKey('statistics', $data['meta']);
        $this->assertEquals('Smart-Schools Management System', $data['meta']['system']);
    }

    public function test_admin_can_export_backup_sql()
    {
        $admin = User::factory()->make([
            'id'       => 999,
            'role'     => 'Admin',
            'username' => 'admin_test',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.backup.export', ['format' => 'sql']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/sql; charset=UTF-8');
        $this->assertStringContainsString('Smart-Schools Database Backup', $response->getContent());
    }

    public function test_admin_can_export_backup_csv_zip()
    {
        $admin = User::factory()->make([
            'id'       => 999,
            'role'     => 'Admin',
            'username' => 'admin_test',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.backup.export', ['format' => 'csv_zip']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/zip');
    }
}
