<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

            RolesSeeder::class,

            PermissionsSeeder::class,

            RolePermissionSeeder::class,

            CampaignTaskPermissionsSeeder::class,
            CampaignIssuePermissionsSeeder::class,
            WarRoomPermissionsSeeder::class,
            SecurityAuditPermissionsSeeder::class,
            BackupPermissionsSeeder::class,
            CampaignEventPermissionsSeeder::class,
            CampaignFinancePermissionsSeeder::class,
            CampaignCommunicationPermissionsSeeder::class,
            InternalMessagePermissionsSeeder::class,

            SuperAdminSeeder::class,

        ]);
    }
}
