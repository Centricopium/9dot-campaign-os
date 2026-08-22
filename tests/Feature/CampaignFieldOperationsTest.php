<?php

namespace Tests\Feature;

use App\Models\CampaignTask;
use App\Models\Constituency;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CampaignFieldOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_moves_through_field_completion_and_supervisor_review_with_timeline(): void
    {
        $constituency = Constituency::create(['name' => 'Test Assembly']);
        $worker = User::factory()->create(['is_active' => true, 'constituency_id' => $constituency->id]);
        $reviewer = User::factory()->create(['is_active' => true, 'constituency_id' => $constituency->id]);

        $this->actingAs($worker);
        $task = CampaignTask::create([
            'constituency_id' => $constituency->id,
            'assigned_to' => $worker->id,
            'title' => 'Verify booth team',
            'priority' => 'High',
            'requires_proof' => true,
        ]);

        $this->assertSame('Assigned', $task->status);
        $this->assertCount(1, $task->updates);

        $task->start($worker);
        $this->assertSame('In Progress', $task->fresh()->status);
        $this->assertSame(10, $task->fresh()->completion_percent);

        try {
            $task->fresh()->complete($worker, 'Work completed');
            $this->fail('Proof validation did not run.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('proof_files', $exception->errors());
        }

        $task->fresh()->complete($worker, 'Verified with photo', ['campaign-tasks/proof.jpg']);
        $this->assertSame('Completed', $task->fresh()->status);
        $this->assertSame(100, $task->fresh()->completion_percent);

        $this->actingAs($reviewer);
        $task->fresh()->review($reviewer, true, 'Proof approved');

        $approved = $task->fresh();
        $this->assertSame('Approved', $approved->status);
        $this->assertSame($reviewer->id, $approved->reviewed_by);
        $this->assertNotNull($approved->reviewed_at);
        $this->assertGreaterThanOrEqual(4, $approved->updates()->count());
    }

    public function test_assembly_admin_only_sees_campaign_tasks_from_assigned_constituency(): void
    {
        $assigned = Constituency::create(['name' => 'Assigned Assembly']);
        $other = Constituency::create(['name' => 'Other Assembly']);

        CampaignTask::create(['constituency_id' => $assigned->id, 'title' => 'Visible Task']);
        CampaignTask::create(['constituency_id' => $other->id, 'title' => 'Hidden Task']);

        $role = Role::create(['name' => 'Assembly Admin', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'campaign_task.view', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $admin = User::factory()->create([
            'is_active' => true,
            'constituency_id' => $assigned->id,
        ]);
        $admin->assignRole($role);

        $this->actingAs($admin);

        $this->assertSame(['Visible Task'], CampaignTask::query()->pluck('title')->all());
        $this->get('/admin/field-operations')->assertOk()->assertSee('Visible Task')->assertDontSee('Hidden Task');
    }

    public function test_task_rejects_village_from_another_constituency(): void
    {
        $assigned = Constituency::create(['name' => 'Assigned Assembly']);
        $other = Constituency::create(['name' => 'Other Assembly']);
        $wrongVillage = Village::create([
            'constituency_id' => $other->id,
            'name' => 'Wrong Village',
            'taluka' => 'Other Taluka',
            'district' => 'Other District',
        ]);

        $this->expectException(ValidationException::class);

        CampaignTask::create([
            'constituency_id' => $assigned->id,
            'village_id' => $wrongVillage->id,
            'title' => 'Invalid Area Task',
        ]);
    }

    public function test_authorized_user_can_open_field_operations_and_task_resource_pages(): void
    {
        foreach (['campaign_task.view', 'campaign_task.create'] as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo(['campaign_task.view', 'campaign_task.create']);

        $this->actingAs($user)
            ->get('/admin/field-operations')
            ->assertOk();

        $this->get('/admin/campaign-tasks')->assertOk();
        $this->get('/admin/campaign-tasks/create')->assertOk();
    }
}
