<?php

namespace Tests\Feature;

use App\Models\CampaignIssue;
use App\Models\Constituency;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignIssueTrackerTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_has_sla_and_moves_through_resolution_and_closure(): void
    {
        $assembly = Constituency::create(['name' => 'Test Assembly']);
        $worker = User::factory()->create(['constituency_id' => $assembly->id]);
        $manager = User::factory()->create(['constituency_id' => $assembly->id]);
        $this->actingAs($worker);

        $issue = CampaignIssue::create(['constituency_id' => $assembly->id, 'assigned_to' => $worker->id, 'title' => 'Drinking water problem', 'description' => 'Water supply is unavailable.', 'priority' => 'Critical']);
        $this->assertSame('Assigned', $issue->status);
        $this->assertTrue($issue->due_at->lessThanOrEqualTo(now()->addHours(24)));
        $this->assertCount(1, $issue->updates);

        $issue->start($worker);
        $issue->fresh()->resolve($worker, 'Water line repaired.', ['private/proof.jpg']);
        $this->actingAs($manager);
        $issue->fresh()->close($manager, 'Resolution verified.');

        $closed = $issue->fresh();
        $this->assertSame('Closed', $closed->status);
        $this->assertNotNull($closed->resolved_at);
        $this->assertNotNull($closed->closed_at);
        $this->assertGreaterThanOrEqual(4, $closed->updates()->count());
    }

    public function test_assembly_admin_only_sees_issues_in_assigned_constituency(): void
    {
        $assigned = Constituency::create(['name' => 'Assigned']);
        $other = Constituency::create(['name' => 'Other']);
        CampaignIssue::create(['constituency_id' => $assigned->id, 'title' => 'Visible Issue', 'description' => 'Visible']);
        CampaignIssue::create(['constituency_id' => $other->id, 'title' => 'Hidden Issue', 'description' => 'Hidden']);
        $role = Role::create(['name' => 'Assembly Admin', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'campaign_issue.view', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        $admin = User::factory()->create(['is_active' => true, 'constituency_id' => $assigned->id]);
        $admin->assignRole($role);
        $this->actingAs($admin);

        $this->assertSame(['Visible Issue'], CampaignIssue::query()->pluck('title')->all());
        $this->get('/admin/issue-tracker')->assertOk()->assertSee('Visible Issue')->assertDontSee('Hidden Issue');
    }

    public function test_authorized_user_can_open_issue_pages(): void
    {
        foreach (['campaign_issue.view', 'campaign_issue.create'] as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }
        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo(['campaign_issue.view', 'campaign_issue.create']);
        $this->actingAs($user)->get('/admin/issue-tracker')->assertOk();
        $this->get('/admin/campaign-issues')->assertOk();
        $this->get('/admin/campaign-issues/create')->assertOk();
    }
}
