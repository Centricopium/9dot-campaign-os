<?php

namespace Tests\Feature;

use App\Models\CampaignCommunication;
use App\Models\Constituency;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaCommunicationCentreTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_moves_through_approval_and_publishing_workflow(): void
    {
        $constituency = Constituency::create(['name' => 'Vansda']);
        $manager = User::factory()->create();
        $content = CampaignCommunication::create([
            'constituency_id' => $constituency->id,
            'title' => 'Development Vision Video',
            'channel' => 'Instagram',
            'content_type' => 'Reel / Short',
            'status' => 'Pending Approval',
            'scheduled_at' => now()->addDay(),
            'planned_reach' => 10000,
        ]);

        $content->approve($manager, 'Message and creative approved.');
        $this->assertSame('Scheduled', $content->fresh()->status);
        $this->assertSame($manager->id, $content->fresh()->approved_by);

        $content->markPublished('https://example.com/post', 8000, 1200);
        $published = $content->fresh();

        $this->assertSame('Published', $published->status);
        $this->assertSame(15.0, $published->engagement_rate);
        $this->assertNotNull($published->published_at);
        $this->assertNotEmpty($published->communication_code);
    }

    public function test_authorized_user_can_open_communication_pages(): void
    {
        foreach (['campaign_communication.view', 'campaign_communication.create'] as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo(['campaign_communication.view', 'campaign_communication.create']);

        $this->actingAs($user)->get('/admin/media-communication-centre')->assertOk();
        $this->get('/admin/campaign-communications')->assertOk();
        $this->get('/admin/campaign-communications/create')->assertOk();
    }

    public function test_assembly_admin_only_sees_assigned_constituency_communication(): void
    {
        $first = Constituency::create(['name' => 'Vansda']);
        $second = Constituency::create(['name' => 'Dang']);

        foreach ([$first, $second] as $index => $constituency) {
            CampaignCommunication::create([
                'constituency_id' => $constituency->id,
                'title' => 'Content '.$index,
                'channel' => 'WhatsApp',
            ]);
        }

        $role = Role::create(['name' => 'Assembly Admin', 'guard_name' => 'web']);
        $user = User::factory()->create(['constituency_id' => $first->id]);
        $user->assignRole($role);
        $this->actingAs($user);

        $this->assertSame(1, CampaignCommunication::query()->count());
        $this->assertSame($first->id, CampaignCommunication::query()->first()->constituency_id);
    }
}
