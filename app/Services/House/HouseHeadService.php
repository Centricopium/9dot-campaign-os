<?php

namespace App\Services\House;

use App\Models\House;
use App\Models\Voter;

class HouseHeadService
{
    public function backfill(?int $constituencyId = null): int
    {
        $updated = 0;

        House::query()
            ->where(fn ($query) => $query->whereNull('head_of_family')->orWhere('head_of_family', ''))
            ->when($constituencyId, fn ($query, $id) => $query->whereHas(
                'booth.village',
                fn ($village) => $village->where('constituency_id', $id),
            ))
            ->select('id', 'booth_id', 'house_no', 'is_verified', 'is_active')
            ->chunkById(500, function ($houses) use (&$updated): void {
                $houseIds = $houses->pluck('id');

                $oldestByHouse = Voter::query()
                    ->whereIn('house_id', $houseIds)
                    ->whereNotNull('name')
                    ->where('name', '!=', '')
                    ->orderByDesc('age')
                    ->orderBy('id')
                    ->get(['id', 'house_id', 'name', 'age', 'mobile'])
                    ->groupBy('house_id')
                    ->map->first();

                $now = now();
                $updates = [];

                foreach ($houses as $house) {
                    $voter = $oldestByHouse->get($house->id);

                    if (! $voter) {
                        continue;
                    }

                    $updates[] = [
                        'id' => $house->id,
                        'booth_id' => $house->booth_id,
                        'house_no' => $house->house_no,
                        'is_verified' => $house->is_verified,
                        'is_active' => $house->is_active,
                        'head_of_family' => trim($voter->name),
                        'mobile' => filled($voter->mobile) ? $voter->mobile : null,
                        'updated_at' => $now,
                    ];
                }

                if ($updates !== []) {
                    House::query()->upsert(
                        $updates,
                        ['id'],
                        ['head_of_family', 'mobile', 'updated_at'],
                    );
                    $updated += count($updates);
                }
            });

        return $updated;
    }
}
