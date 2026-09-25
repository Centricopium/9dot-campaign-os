<?php

namespace Tests\Feature;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Village;
use App\Services\Import\BoothImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoothImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resolves_a_short_village_name_from_the_area_column(): void
    {
        $constituency = Constituency::create(['name' => 'Dang']);
        $sajaVillage = Village::create([
            'constituency_id' => $constituency->id,
            'name' => 'Bardipada (Saja )',
            'taluka' => 'Shubir',
            'district' => 'Dang',
        ]);
        Village::create([
            'constituency_id' => $constituency->id,
            'name' => 'Bardipada(Nakatyahanvant)',
            'taluka' => 'Shubir',
            'district' => 'Dang',
        ]);

        $service = app(BoothImportService::class);
        $service->import([
            17,
            'Bardipada',
            'Bardipada',
            'Primary School, Bardipada, Bardipada (Saja)',
        ], $constituency->id);

        $this->assertDatabaseHas(Booth::class, [
            'village_id' => $sajaVillage->id,
            'booth_no' => 17,
            'booth_name' => 'Bardipada',
        ]);
    }

    public function test_it_does_not_guess_when_the_area_does_not_identify_one_village(): void
    {
        $constituency = Constituency::create(['name' => 'Dang']);
        Village::create([
            'constituency_id' => $constituency->id,
            'name' => 'Bardipada (Saja)',
            'taluka' => 'Shubir',
            'district' => 'Dang',
        ]);
        Village::create([
            'constituency_id' => $constituency->id,
            'name' => 'Bardipada (Nakatyahanvant)',
            'taluka' => 'Shubir',
            'district' => 'Dang',
        ]);

        $this->expectExceptionMessage('Village not found : Bardipada');

        app(BoothImportService::class)->import([
            17,
            'Bardipada',
            'Bardipada',
            'Primary School, Bardipada',
        ], $constituency->id);
    }
}
