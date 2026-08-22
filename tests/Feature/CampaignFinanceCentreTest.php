<?php

namespace Tests\Feature;

use App\Models\CampaignBudget;
use App\Models\CampaignExpense;
use App\Models\Constituency;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignFinanceCentreTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_tracks_approved_spend_and_available_amount(): void
    {
        $constituency = Constituency::create(['name' => 'Vansda']);
        $approver = User::factory()->create();
        $budget = CampaignBudget::create([
            'constituency_id' => $constituency->id,
            'title' => 'Assembly Campaign Budget',
            'category' => 'Events & Rallies',
            'allocated_amount' => 100000,
        ]);

        $approved = CampaignExpense::create([
            'constituency_id' => $constituency->id,
            'campaign_budget_id' => $budget->id,
            'expense_date' => today(),
            'category' => 'Events & Rallies',
            'description' => 'Public rally sound system',
            'amount' => 25000,
        ]);
        $approved->approve($approver);

        CampaignExpense::create([
            'constituency_id' => $constituency->id,
            'campaign_budget_id' => $budget->id,
            'expense_date' => today(),
            'category' => 'Events & Rallies',
            'description' => 'Pending stage expense',
            'amount' => 10000,
        ]);

        $this->assertSame(25000.0, $budget->fresh()->approved_spent);
        $this->assertSame(75000.0, $budget->fresh()->available_amount);
        $this->assertSame(25, $budget->fresh()->utilization_percent);
        $this->assertNotEmpty($approved->expense_code);
    }

    public function test_authorized_user_can_open_finance_pages(): void
    {
        foreach (['campaign_finance.view', 'campaign_finance.create'] as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo(['campaign_finance.view', 'campaign_finance.create']);

        $this->actingAs($user)->get('/admin/campaign-finance-centre')->assertOk();
        $this->get('/admin/campaign-budgets')->assertOk();
        $this->get('/admin/campaign-expenses')->assertOk();
        $this->get('/admin/campaign-budgets/create')->assertOk();
        $this->get('/admin/campaign-expenses/create')->assertOk();
    }

    public function test_assembly_admin_only_sees_assigned_constituency_finance(): void
    {
        $first = Constituency::create(['name' => 'Vansda']);
        $second = Constituency::create(['name' => 'Dang']);

        foreach ([$first, $second] as $index => $constituency) {
            CampaignBudget::create([
                'constituency_id' => $constituency->id,
                'title' => 'Budget '.$index,
                'category' => 'Other',
                'allocated_amount' => 50000,
            ]);
            CampaignExpense::create([
                'constituency_id' => $constituency->id,
                'expense_date' => today(),
                'category' => 'Other',
                'description' => 'Expense '.$index,
                'amount' => 5000,
            ]);
        }

        $role = Role::create(['name' => 'Assembly Admin', 'guard_name' => 'web']);
        $user = User::factory()->create(['constituency_id' => $first->id]);
        $user->assignRole($role);
        $this->actingAs($user);

        $this->assertSame(1, CampaignBudget::query()->count());
        $this->assertSame(1, CampaignExpense::query()->count());
        $this->assertSame($first->id, CampaignBudget::query()->first()->constituency_id);
    }
}
