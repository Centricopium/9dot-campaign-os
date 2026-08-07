<?php

namespace Database\Seeders;

use App\Models\Village;
use App\Models\VillageAlias;
use Illuminate\Database\Seeder;

class VillageAliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aliases = [

            'Kangvai' => [
                'Kangavi',
                'Knagavi',
            ],

            'Rupvel' => [
                'Rupwel',
            ],

            'MotivalzarU' => [
                'MotivaljarU',
            ],

            'Mankuniya' => [
                'Mankunyia',
            ],

            'Kalaamba' => [
                'Kalamba',
            ],

            'Bartad(U)' => [
                'Bartad U',
            ],

            'Baratad(Kha)' => [
                'Baratad Kha',
            ],

        ];

        foreach ($aliases as $villageName => $list) {

            $village = Village::where('name', $villageName)->first();

            if (! $village) {
                $this->command?->warn("Village not found: {$villageName}");
                continue;
            }

            foreach ($list as $alias) {

                VillageAlias::updateOrCreate(
                    [
                        'village_id' => $village->id,
                        'alias' => $alias,
                    ]
                );
            }
        }

        $this->command?->info('✅ Village aliases imported successfully.');
    }
}