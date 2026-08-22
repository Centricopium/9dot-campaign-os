<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\SystemBackup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackupRestoreCentreTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_open_backup_centre(): void
    {
        Permission::create(['name' => 'backup.view', 'guard_name' => 'web']);
        $u = User::factory()->create(['is_active' => true]);
        $u->givePermissionTo('backup.view');
        $this->actingAs($u)->get('/admin/backup-restore-centre')->assertOk();
    }

    public function test_backup_metadata_formats_size(): void
    {
        $b = SystemBackup::create(['status' => 'Completed', 'file_size' => 1048576, 'file_name' => 'test.sql.gz']);
        $this->assertSame('1.00 MB', $b->human_size);
    }
}
