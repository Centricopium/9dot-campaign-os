<?php

namespace App\Filament\Pages;

use App\Models\Constituency;
use App\Models\Voter;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ConstituencyDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign';

    protected static ?string $navigationLabel = 'Constituency Dashboard';

    protected static ?string $title = 'Constituency Dashboard';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.constituency-dashboard';

    public ?int $constituencyId = null;


    /*
    |--------------------------------------------------------------------------
    | Constituencies
    |--------------------------------------------------------------------------
    */

    public function getConstituenciesProperty()
    {
        return Constituency::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Selected Constituency
    |--------------------------------------------------------------------------
    */

    public function getSelectedConstituencyProperty(): ?Constituency
    {
        if (! $this->constituencyId) {
            return null;
        }

        return Constituency::query()
            ->find($this->constituencyId);
    }


    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    public function getSummaryProperty(): array
    {
        if (! $this->constituencyId) {
            return $this->emptySummary();
        }

        $constituency = $this->selectedConstituency;

        if (! $constituency) {
            return $this->emptySummary();
        }

        /*
        |--------------------------------------------------------------------------
        | Main voter query
        |--------------------------------------------------------------------------
        */

        $query = Voter::query()
            ->whereHas('house.booth.village', function ($q) use ($constituency) {
                $q->where('constituency_id', $constituency->id);
            });


        /*
        |--------------------------------------------------------------------------
        | Basic Summary
        |--------------------------------------------------------------------------
        */

        $summary = [

            'villages' => $constituency->villages()->count(),

            'booths' => $constituency->villages()
                ->withCount('booths')
                ->get()
                ->sum('booths_count'),

            'houses' => $constituency->villages()
                ->with([
                    'booths' => function ($query) {
                        $query->withCount('houses');
                    },
                ])
                ->get()
                ->flatMap(fn ($village) => $village->booths)
                ->sum('houses_count'),

            'voters' => (clone $query)->count(),

            'male' => (clone $query)
                ->where('gender', 'Male')
                ->count(),

            'female' => (clone $query)
                ->where('gender', 'Female')
                ->count(),

            'other' => (clone $query)
                ->whereNotIn('gender', ['Male', 'Female'])
                ->count(),

            'volunteers' => (clone $query)
                ->where('is_volunteer', true)
                ->count(),

            'influencers' => (clone $query)
                ->where('is_influencer', true)
                ->count(),

            'active_voters' => (clone $query)
                ->where('is_active', true)
                ->count(),

            'neutral' => (clone $query)
                ->where('support_level', 'Neutral')
                ->count(),

            'undecided' => (clone $query)
                ->where('support_level', 'Undecided')
                ->count(),

            'parties' => [],

            'villages_list' => [],

        ];


        /*
        |--------------------------------------------------------------------------
        | Party Summary
        |--------------------------------------------------------------------------
        */

        $parties = \App\Models\PoliticalParty::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($parties as $party) {

            $partyQuery = (clone $query)
                ->where('political_party_id', $party->id);

            $summary['parties'][] = [

                'id' => $party->id,

                'name' => $party->name,

                'short_name' => $party->short_name,

                'symbol' => $party->symbol,

                'total' => (clone $partyQuery)->count(),

                'strong_congress' => (clone $partyQuery)
                    ->where('support_level', 'Strong Congress')
                    ->count(),

                'congress_leaning' => (clone $partyQuery)
                    ->where('support_level', 'Congress Leaning')
                    ->count(),

                'neutral' => (clone $partyQuery)
                    ->where('support_level', 'Neutral')
                    ->count(),

                'undecided' => (clone $partyQuery)
                    ->where('support_level', 'Undecided')
                    ->count(),

                'bjp_leaning' => (clone $partyQuery)
                    ->where('support_level', 'BJP Leaning')
                    ->count(),

                'strong_bjp' => (clone $partyQuery)
                    ->where('support_level', 'Strong BJP')
                    ->count(),

                'other' => (clone $partyQuery)
                    ->where('support_level', 'Other')
                    ->count(),

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Village-wise Summary
        |--------------------------------------------------------------------------
        */

        $villages = $constituency->villages()
            ->with([
                'booths' => function ($query) {
                    $query->withCount('houses');
                },
            ])
            ->orderBy('name')
            ->get();

        foreach ($villages as $village) {

            $villageVoterQuery = Voter::query()
                ->whereHas('house.booth', function ($q) use ($village) {
                    $q->where('village_id', $village->id);
                });

            $summary['villages_list'][] = [

                'id' => $village->id,

                'name' => $village->name,

                'taluka' => $village->taluka,

                'booths' => $village->booths->count(),

                'houses' => $village->booths->sum('houses_count'),

                'voters' => $villageVoterQuery->count(),

            ];
        }


        return $summary;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty Summary
    |--------------------------------------------------------------------------
    */

    protected function emptySummary(): array
    {
        return [

            'villages' => 0,

            'booths' => 0,

            'houses' => 0,

            'voters' => 0,

            'male' => 0,

            'female' => 0,

            'other' => 0,

            'volunteers' => 0,

            'influencers' => 0,

            'active_voters' => 0,

            'neutral' => 0,

            'undecided' => 0,

            'parties' => [],

            'villages_list' => [],

        ];
    }
}