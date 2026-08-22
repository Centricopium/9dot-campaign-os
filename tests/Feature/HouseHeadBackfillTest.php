<?php

namespace Tests\Feature;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\House;
use App\Models\Village;
use App\Models\Voter;
use App\Services\House\HouseHeadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HouseHeadBackfillTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_house_head_is_filled_from_oldest_voter_without_overwriting_manual_data(): void
    {
        $constituency = Constituency::create(['name' => 'Assembly']);
        $village = Village::create(['constituency_id' => $constituency->id, 'name' => 'Village', 'taluka' => 'Taluka', 'district' => 'District']);
        $booth = Booth::create(['village_id' => $village->id, 'booth_no' => '1', 'booth_name' => 'Booth']);
        $house = House::create(['booth_id' => $booth->id, 'house_no' => '10']);
        $manualHouse = House::create(['booth_id' => $booth->id, 'house_no' => '11', 'head_of_family' => 'Manual Head', 'mobile' => '9999999999']);

        Voter::create(['house_id' => $house->id, 'name' => 'Young Voter', 'age' => 25, 'mobile' => '1111111111']);
        Voter::create(['house_id' => $house->id, 'name' => 'Oldest Voter', 'age' => 67, 'mobile' => '2222222222']);
        Voter::create(['house_id' => $manualHouse->id, 'name' => 'Older Person', 'age' => 80, 'mobile' => '3333333333']);

        $this->assertSame(1, app(HouseHeadService::class)->backfill($constituency->id));

        $this->assertSame('Oldest Voter', $house->fresh()->head_of_family);
        $this->assertSame('2222222222', $house->fresh()->mobile);
        $this->assertSame('Manual Head', $manualHouse->fresh()->head_of_family);
        $this->assertSame('9999999999', $manualHouse->fresh()->mobile);
    }
}
