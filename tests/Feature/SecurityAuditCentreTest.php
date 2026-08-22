<?php

namespace Tests\Feature;

use App\Models\CampaignIssue;
use App\Models\Constituency;
use App\Models\Permission;
use App\Models\SecurityAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAuditCentreTest extends TestCase
{
    use RefreshDatabase;

    public function test_sensitive_values_are_redacted_and_changes_are_logged(): void
    {
        $c = Constituency::create(['name' => 'Test']);
        $u = User::factory()->create(['constituency_id' => $c->id]);
        $this->actingAs($u);
        $issue = CampaignIssue::create(['constituency_id' => $c->id, 'title' => 'Private issue', 'description' => 'Test', 'contact_mobile' => '9999999999']);
        $log = SecurityAuditLog::query()->where('auditable_type', CampaignIssue::class)->where('auditable_id', $issue->id)->where('event', 'created')->firstOrFail();
        $this->assertSame('[REDACTED]', $log->new_values['contact_mobile']);
        $issue->update(['status' => 'In Progress']);
        $this->assertDatabaseHas('security_audit_logs', ['auditable_type' => CampaignIssue::class, 'auditable_id' => $issue->id, 'event' => 'updated']);
    }

    public function test_authorized_user_can_open_audit_centre(): void
    {
        Permission::create(['name' => 'audit.view', 'guard_name' => 'web']);
        $u = User::factory()->create(['is_active' => true]);
        $u->givePermissionTo('audit.view');
        $this->actingAs($u)->get('/admin/security-audit-centre')->assertOk();
    }
}
