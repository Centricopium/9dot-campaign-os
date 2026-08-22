<?php

namespace App\Console\Commands;

use App\Services\House\HouseHeadService;
use Illuminate\Console\Command;

class BackfillHouseHeads extends Command
{
    protected $signature = 'houses:backfill-heads {--constituency= : Only update one constituency}';

    protected $description = 'Fill missing household heads from the oldest voter in each house';

    public function handle(HouseHeadService $service): int
    {
        $constituencyId = $this->option('constituency');
        $updated = $service->backfill(filled($constituencyId) ? (int) $constituencyId : null);

        $this->info("Updated {$updated} houses.");

        return self::SUCCESS;
    }
}
