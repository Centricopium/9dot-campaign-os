<?php

namespace Tests\Feature;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\UnauthorizedException;
use Tests\TestCase;

class AssemblyConstituencyScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_assembly_admin_only_sees_records_from_assigned_constituency(): void
    {
        [$first, $second] = $this->constituenciesWithBooths();
        $admin = User::factory()->create(['constituency_id' => $first->id]);
        Role::create(['name' => 'Assembly Admin', 'guard_name' => 'web']);
        $admin->assignRole('Assembly Admin');

        $this->actingAs($admin);

        $this->assertSame([$first->id], Constituency::query()->pluck('id')->all());
        $this->assertCount(1, Village::all());
        $this->assertCount(1, Booth::all());
        $this->assertFalse(Constituency::query()->whereKey($second->id)->exists());
    }

    public function test_assembly_admin_cannot_save_record_for_another_constituency(): void
    {
        [$first, $second] = $this->constituenciesWithBooths();
        $admin = User::factory()->create(['constituency_id' => $first->id]);
        Role::create(['name' => 'Assembly Admin', 'guard_name' => 'web']);
        $admin->assignRole('Assembly Admin');
        $this->actingAs($admin);

        $this->expectException(UnauthorizedException::class);

        Village::create([
            'constituency_id' => $second->id,
            'name' => 'Blocked village',
            'taluka' => 'Taluka',
            'district' => 'District',
        ]);
    }

    public function test_super_admin_is_not_scoped_to_one_constituency(): void
    {
        $this->constituenciesWithBooths();
        $admin = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($admin);

        $this->assertCount(2, Constituency::all());
        $this->assertCount(2, Village::all());
        $this->assertCount(2, Booth::all());
    }

    private function constituenciesWithBooths(): array
    {
        $constituencies = collect(['First', 'Second'])->map(function (string $name): Constituency {
            $constituency = Constituency::create(['name' => $name]);
            $village = Village::create([
                'constituency_id' => $constituency->id,
                'name' => $name.' village',
                'taluka' => 'Taluka',
                'district' => 'District',
            ]);
            Booth::create([
                'village_id' => $village->id,
                'booth_no' => $name,
                'booth_name' => $name.' booth',
            ]);

            return $constituency;
        });

        return [$constituencies[0], $constituencies[1]];
    }
}
