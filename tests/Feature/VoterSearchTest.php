<?php

namespace Tests\Feature;

use App\Filament\Resources\Voters\Pages\ListVoters;
use App\Models\Booth;
use App\Models\Constituency;
use App\Models\House;
use App\Models\User;
use App\Models\Village;
use App\Models\Voter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VoterSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_voters_can_be_searched_by_computed_house_display_fields(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
            'is_active' => true,
        ]);
        $constituency = Constituency::create(['name' => 'Assembly']);
        $village = Village::create([
            'constituency_id' => $constituency->id,
            'name' => 'Village',
            'taluka' => 'Taluka',
            'district' => 'District',
        ]);
        $booth = Booth::create([
            'village_id' => $village->id,
            'booth_no' => '101',
            'booth_name' => 'Test Booth',
        ]);
        $house = House::create([
            'booth_id' => $booth->id,
            'house_no' => 'H-42',
            'head_of_family' => 'Family Head',
            'mobile' => '9876543210',
        ]);
        $voter = Voter::create([
            'house_id' => $house->id,
            'serial_no' => '1',
            'epic_no' => 'TEST001',
            'name' => 'Searchable Voter',
        ]);

        $this->actingAs($admin);

        Livewire::test(ListVoters::class)
            ->searchTable('Family Head')
            ->assertCanSeeTableRecords([$voter]);
    }
}
