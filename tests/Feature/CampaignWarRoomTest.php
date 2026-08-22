<?php

namespace Tests\Feature;

use App\Models\CampaignDailyBriefing;
use App\Models\Constituency;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignWarRoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_open_war_room(): void
    {
        Permission::create(['name' => 'war_room.view', 'guard_name' => 'web']);
        $u = User::factory()->create(['is_active' => true]);
        $u->givePermissionTo('war_room.view');
        $this->actingAs($u)->get('/admin/campaign-war-room')->assertOk();
    }

    public function test_daily_briefing_is_unique_per_assembly_and_date(): void
    {
        $c = Constituency::create(['name' => 'Test']);
        CampaignDailyBriefing::create(['constituency_id' => $c->id, 'briefing_date' => '2026-08-22', 'morning_objectives' => 'Plan']);
        $this->assertDatabaseCount('campaign_daily_briefings', 1);
        $this->assertSame('Plan', CampaignDailyBriefing::first()->morning_objectives);
    }
}
