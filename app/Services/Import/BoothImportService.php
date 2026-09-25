<?php

namespace App\Services\Import;

use App\Models\Booth;
use App\Models\Village;
use App\Models\VillageAlias;
use Exception;

class BoothImportService
{
    public function import(array $row, int $constituencyId): Booth
    {
        $data = BoothRowMapper::map($row);

        $villageName = trim(
            preg_replace('/\s+/u', ' ', $data['village'])
        );

        // 1. Exact Village Match
        $village = Village::where('constituency_id', $constituencyId)
            ->where('name', $villageName)
            ->first();

        // 2. Alias Match
        if (! $village) {

            $alias = VillageAlias::query()
                ->where('alias', $villageName)
                ->whereHas(
                    'village',
                    fn ($query) => $query->where('constituency_id', $constituencyId),
                )
                ->first();

            if ($alias) {
                $village = $alias->village;
            }
        }

        // Resolve a short village name using the full village name written in
        // the booth's Area column. We only accept one unambiguous match.
        if (! $village) {
            $village = $this->resolveVillageFromArea(
                $data['area'],
                $villageName,
                $constituencyId,
            );
        }

        // Still not found.
        if (! $village) {
            throw new Exception("Village not found : {$villageName}");
        }

        return Booth::updateOrCreate(
            [
                'village_id' => $village->id,
                'booth_no'   => $data['booth_no'],
            ],
            [
                'part_no'    => $data['booth_no'],
                'booth_name' => $data['booth_name'],
                'is_active'  => true,
            ]
        );
    }

    private function resolveVillageFromArea(
        string $area,
        string $shortVillageName,
        int $constituencyId,
    ): ?Village {
        if (blank($area)) {
            return null;
        }

        $normalizedArea = $this->normalizeForComparison($area);
        $normalizedShortName = $this->normalizeForComparison($shortVillageName);

        $matches = Village::query()
            ->where('constituency_id', $constituencyId)
            ->get()
            ->filter(function (Village $candidate) use ($normalizedArea, $normalizedShortName): bool {
                $normalizedCandidateName = $this->normalizeForComparison($candidate->name);

                return str_starts_with($normalizedCandidateName, "{$normalizedShortName}(")
                    && str_contains($normalizedArea, $normalizedCandidateName);
            })
            ->values();

        return $matches->count() === 1 ? $matches->first() : null;
    }

    private function normalizeForComparison(string $value): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value));

        return mb_strtolower((string) preg_replace('/\s*([()])\s*/u', '$1', $value));
    }
}
