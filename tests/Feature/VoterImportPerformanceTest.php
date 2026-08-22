<?php

namespace Tests\Feature;

use App\Imports\VotersImport;
use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Village;
use App\Models\Voter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class VoterImportPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_batches_valid_rows_and_skips_duplicate_epics(): void
    {
        $constituency = Constituency::create(['name' => 'Assembly']);
        $village = Village::create([
            'constituency_id' => $constituency->id,
            'name' => 'Village',
            'taluka' => 'Taluka',
            'district' => 'District',
        ]);
        Booth::create([
            'village_id' => $village->id,
            'booth_no' => '1',
            'part_no' => '1',
            'booth_name' => 'Booth',
        ]);

        $import = new VotersImport($constituency->id);
        $import->collection(new Collection([
            $this->row('ABC001', '1'),
            $this->row('ABC001', '2'),
            $this->row('ABC002', '2'),
        ]));

        $this->assertSame(2, $import->imported);
        $this->assertSame(1, $import->skipped);
        $this->assertSame(2, Voter::count());
        $this->assertSame(2, Voter::pluck('house_id')->unique()->count());

        $import->collection(new Collection([
            $this->row('ABC001', '1'),
            $this->row('ABC002', '2'),
        ]));

        $this->assertSame(2, Voter::count());
        $this->assertSame(3, $import->skipped);
    }

    private function row(string $epic, string $house): Collection
    {
        return collect([
            'part_no' => '1',
            'house_no' => $house,
            'idcard_no' => $epic,
            'slnoinpart' => $house,
            'eng_f_name' => 'Test',
            'eng_surname' => 'Voter',
            'eng_m_name' => 'Parent',
            'sex' => 'M',
            'age' => 30,
            'contactno' => null,
            'ecast' => null,
        ]);
    }
}
