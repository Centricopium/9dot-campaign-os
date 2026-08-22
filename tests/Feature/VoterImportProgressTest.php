<?php

namespace Tests\Feature;

use App\Models\Constituency;
use App\Models\Permission;
use App\Models\User;
use App\Models\VoterImportBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoterImportProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_percentage_is_calculated(): void
    {
        $c = Constituency::create(['name' => 'Test']);
        $b = VoterImportBatch::create(['constituency_id' => $c->id, 'file_name' => 'voters.xlsx', 'file_path' => 'imports/voters.xlsx', 'status' => 'Processing', 'total_rows' => 10000, 'processed_rows' => 2500, 'imported_rows' => 2400, 'skipped_rows' => 100]);
        $this->assertSame(25, $b->progress_percent);
        $b->update(['status' => 'Completed']);
        $this->assertSame(100, $b->fresh()->progress_percent);
    }

    public function test_authorized_user_can_open_progress_centre(): void
    {
        Permission::create(['name' => 'import.voter', 'guard_name' => 'web']);
        $u = User::factory()->create(['is_active' => true]);
        $u->givePermissionTo('import.voter');
        $this->actingAs($u)->get('/admin/import-progress-centre')->assertOk();
    }
}
