<?php

namespace Tests\Feature;

use App\Filament\Pages\EventTourPlanner;
use App\Models\CampaignEvent;
use App\Models\Constituency;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CampaignEventPlannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_tracks_schedule_attendance_and_budget(): void
    {
        $c = Constituency::create(['name' => 'Test']);
        $e = CampaignEvent::create(['constituency_id' => $c->id, 'title' => 'Candidate Tour', 'event_type' => 'Candidate Tour', 'venue' => 'Main Road', 'starts_at' => now()->addDay(), 'expected_attendance' => 1000, 'actual_attendance' => 750, 'estimated_budget' => 50000]);
        $this->assertSame(75, $e->attendance_percent);
        $this->assertNotEmpty($e->event_code);
    }

    public function test_authorized_user_can_open_event_pages(): void
    {
        foreach (['campaign_event.view', 'campaign_event.create'] as $p) {
            Permission::create(['name' => $p, 'guard_name' => 'web']);
        }$u = User::factory()->create(['is_active' => true]);
        $u->givePermissionTo(['campaign_event.view', 'campaign_event.create']);
        $this->actingAs($u)->get('/admin/event-tour-planner')->assertOk();
        $this->get('/admin/campaign-events')->assertOk();
        $this->get('/admin/campaign-events/create')->assertOk();
    }

    public function test_authorized_user_can_export_filtered_event_planner_pdf(): void
    {
        foreach (['campaign_event.view', 'campaign_event.export'] as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo(['campaign_event.view', 'campaign_event.export']);

        $constituency = Constituency::create(['name' => 'Vansda']);
        CampaignEvent::create([
            'constituency_id' => $constituency->id,
            'title' => 'Public Rally',
            'event_type' => 'Rally',
            'venue' => 'Central Ground',
            'starts_at' => now()->addDay(),
            'expected_attendance' => 1500,
            'estimated_budget' => 75000,
            'permission_status' => 'Approved',
        ]);

        $this->actingAs($user);

        Livewire::test(EventTourPlanner::class)
            ->set('constituencyId', (string) $constituency->id)
            ->set('type', 'Rally')
            ->call('exportPdf')
            ->assertFileDownloaded();
    }
}
