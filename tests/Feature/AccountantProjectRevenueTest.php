<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SchoolIncome;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountantProjectRevenueTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_or_store_project_revenue(): void
    {
        $response = $this->post(route('accountant.project_income.store'), [
            'category' => 'Mauzo ya Mazao',
            'source_title' => 'Mauzo ya Mahindi',
            'amount' => 50000,
            'payment_date' => '2026-09-11',
            'payment_method' => 'Cash',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_accountant_can_record_project_revenue(): void
    {
        $accountant = User::factory()->create([
            'username' => 'accountant_1',
            'role' => 'Accountant',
            'school_name' => 'Kome Secondary School',
        ]);

        $response = $this->actingAs($accountant)->post(route('accountant.project_income.store'), [
            'category' => 'Mauzo ya Mazao',
            'source_title' => 'Mauzo ya Mahindi Gunia 30',
            'payer_name' => 'Juma Athumani',
            'amount' => 350000,
            'payment_date' => '2026-09-11',
            'receipt_number' => 'REC-101',
            'payment_method' => 'Cash',
            'academic_year' => '2026',
            'notes' => 'Mauzo ya shamba la shule',
        ]);

        $response->assertRedirect(route('accountant.fees', ['tab' => 'projects', 'project_year' => '2026']));
        $this->assertDatabaseHas('school_incomes', [
            'category' => 'Mauzo ya Mazao',
            'source_title' => 'Mauzo ya Mahindi Gunia 30',
            'amount' => 350000,
            'receipt_number' => 'REC-101',
        ]);
    }

    public function test_accountant_can_view_project_revenue_and_print_report(): void
    {
        $accountant = User::factory()->create([
            'username' => 'accountant_2',
            'role' => 'Accountant',
            'school_name' => 'Kome Secondary School',
        ]);

        $income = SchoolIncome::create([
            'school_name' => 'Kome Secondary School',
            'category' => 'Ushuru wa Mama Ntilie',
            'source_title' => 'Ushuru wa Mama Ashura - Banda 3',
            'payer_name' => 'Mama Ashura',
            'amount' => 20000,
            'payment_date' => '2026-09-11',
            'payment_method' => 'Cash',
            'academic_year' => '2026',
        ]);

        // View page
        $pageResponse = $this->actingAs($accountant)->get(route('accountant.fees', ['tab' => 'projects']));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Ushuru wa Mama Ashura - Banda 3');
        $pageResponse->assertSee('20,000.00');

        // View printable statement
        $printResponse = $this->actingAs($accountant)->get(route('accountant.project_income.print', ['year' => '2026']));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Ushuru wa Mama Ashura - Banda 3');
        $printResponse->assertSee('OFFICIAL STATEMENT OF INSTITUTIONAL PROJECT REVENUES');

        // Delete income
        $deleteResponse = $this->actingAs($accountant)->delete(route('accountant.project_income.destroy', $income->id));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('school_incomes', ['id' => $income->id]);
    }
}
