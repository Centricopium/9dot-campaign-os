<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\Booth;
use App\Models\Voter;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class AssemblyBoothOrganisation extends Page
{
    use AuthorizesPagePermission;
    protected static string $requiredPermission = 'report.view';
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel =
        'Assembly Booth Organisation';

    protected static string|UnitEnum|null $navigationGroup =
        'Campaign';

    protected static ?int $navigationSort =
        6;

    protected static ?string $title =
        'Assembly Booth Organisation';

    protected string $view =
        'filament.pages.assembly-booth-organisation';


    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    public string $search = '';

    public string $villageFilter = '';

    public string $roleFilter = '';

    public string $statusFilter = 'all';


    /*
    |--------------------------------------------------------------------------
    | Organisation Roles
    |--------------------------------------------------------------------------
    */

    public const ROLES = [

        'Booth President' =>
            'Booth President',

        'Mahila Booth President' =>
            'Mahila Booth President',

        'Youth Booth President' =>
            'Youth Booth President',

        'Booth Vice President' =>
            'Booth Vice President',

        'Booth General Secretary' =>
            'Booth General Secretary',

        'Booth Secretary' =>
            'Booth Secretary',

        'Booth Treasurer' =>
            'Booth Treasurer',

        'Panna Pramukh' =>
            'Panna Pramukh',

        'Polling Agent' =>
            'Polling Agent',

        'Booth Volunteer' =>
            'Booth Volunteer',

        'Mahila Booth Volunteer' =>
            'Mahila Booth Volunteer',

        'Youth Booth Volunteer' =>
            'Youth Booth Volunteer',

        'Other' =>
            'Other',
    ];


    /*
    |--------------------------------------------------------------------------
    | Booth Organisation Data
    |--------------------------------------------------------------------------
    */

    public function getBoothOrganisationProperty(): Collection
    {
        $booths = Booth::query()
            ->where('is_active', true)
            ->with('village')
            ->orderByRaw(
                'CAST(booth_no AS UNSIGNED)'
            )
            ->orderBy('booth_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Get voters having organisation roles
        |--------------------------------------------------------------------------
        */

        $voters = Voter::query()
            ->with([
                'house',
            ])
            ->whereNotNull('booth_committee_role')
            ->where('booth_committee_role', '!=', '')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Group voters by booth
        |--------------------------------------------------------------------------
        */

        $votersByBooth = $voters
            ->filter(
                fn (Voter $voter): bool =>
                    ! empty($voter->house?->booth_id)
            )
            ->groupBy(
                fn (Voter $voter) =>
                    $voter->house->booth_id
            );


        /*
        |--------------------------------------------------------------------------
        | Build Booth Rows
        |--------------------------------------------------------------------------
        */

        $rows = $booths
            ->map(function (Booth $booth) use ($votersByBooth) {

                $boothVoters =
                    $votersByBooth->get(
                        $booth->id,
                        collect()
                    );


                return [

                    'booth_id' =>
                        $booth->id,

                    'booth_no' =>
                        $booth->booth_no,

                    'booth_name' =>
                        $booth->booth_name
                        ?: 'Booth ' . $booth->booth_no,

                    'village' =>
                        $booth->village?->name
                        ?: '-',

                    'president' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Booth President'
                        ),

                    'mahila_president' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Mahila Booth President'
                        ),

                    'youth_president' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Youth Booth President'
                        ),

                    'vice_president' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Booth Vice President'
                        ),

                    'general_secretary' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Booth General Secretary'
                        ),

                    'secretary' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Booth Secretary'
                        ),

                    'treasurer' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Booth Treasurer'
                        ),

                    'panna_pramukh' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Panna Pramukh'
                        ),

                    'polling_agent' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Polling Agent'
                        ),

                    'volunteers' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Booth Volunteer'
                        ),

                    'mahila_volunteers' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Mahila Booth Volunteer'
                        ),

                    'youth_volunteers' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Youth Booth Volunteer'
                        ),

                    'other_roles' =>
                        $this->getRoleMembers(
                            $boothVoters,
                            'Other'
                        ),
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Apply Search
        |--------------------------------------------------------------------------
        */

        $search = trim(
            mb_strtolower($this->search)
        );

        if ($search !== '') {

            $rows = $rows->filter(
                function (array $row) use ($search): bool {

                    $basicText = mb_strtolower(
                        implode(' ', [
                            $row['booth_no'] ?? '',
                            $row['booth_name'] ?? '',
                            $row['village'] ?? '',
                        ])
                    );

                    if (
                        str_contains(
                            $basicText,
                            $search
                        )
                    ) {
                        return true;
                    }


                    foreach ([
                        'president',
                        'mahila_president',
                        'youth_president',
                        'vice_president',
                        'general_secretary',
                        'secretary',
                        'treasurer',
                        'panna_pramukh',
                        'polling_agent',
                        'volunteers',
                        'mahila_volunteers',
                        'youth_volunteers',
                        'other_roles',
                    ] as $role) {

                        foreach ($row[$role] ?? [] as $person) {

                            $personText = mb_strtolower(
                                implode(' ', [
                                    $person['name'] ?? '',
                                    $person['mobile'] ?? '',
                                    $person['house_no'] ?? '',
                                ])
                            );

                            if (
                                str_contains(
                                    $personText,
                                    $search
                                )
                            ) {
                                return true;
                            }
                        }
                    }


                    return false;
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Village Filter
        |--------------------------------------------------------------------------
        */

        if ($this->villageFilter !== '') {

            $rows = $rows->filter(
                fn (array $row): bool =>
                    $row['village'] ===
                    $this->villageFilter
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Role Filter
        |--------------------------------------------------------------------------
        */

        if ($this->roleFilter !== '') {

            $roleMap = [

                'Booth President' =>
                    'president',

                'Mahila Booth President' =>
                    'mahila_president',

                'Youth Booth President' =>
                    'youth_president',

                'Booth Vice President' =>
                    'vice_president',

                'Booth General Secretary' =>
                    'general_secretary',

                'Booth Secretary' =>
                    'secretary',

                'Booth Treasurer' =>
                    'treasurer',

                'Panna Pramukh' =>
                    'panna_pramukh',

                'Polling Agent' =>
                    'polling_agent',

                'Booth Volunteer' =>
                    'volunteers',

                'Mahila Booth Volunteer' =>
                    'mahila_volunteers',

                'Youth Booth Volunteer' =>
                    'youth_volunteers',

                'Other' =>
                    'other_roles',
            ];


            $roleKey =
                $roleMap[$this->roleFilter]
                ?? null;


            if ($roleKey) {

                $rows = $rows->filter(
                    fn (array $row): bool =>
                        ($row[$roleKey] ?? collect())
                            ->isNotEmpty()
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($this->statusFilter === 'assigned') {

            $rows = $rows->filter(
                fn (array $row): bool =>
                    $this->isCoreTeamComplete($row)
            );
        }


        if ($this->statusFilter === 'pending') {

            $rows = $rows->filter(
                fn (array $row): bool =>
                    ! $this->isCoreTeamComplete($row)
            );
        }


        return $rows->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Villages
    |--------------------------------------------------------------------------
    */

    public function getVillagesProperty(): Collection
    {
        return Booth::query()
            ->where('is_active', true)
            ->with('village')
            ->get()
            ->map(
                fn (Booth $booth) =>
                    $booth->village?->name
            )
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Get Role Members
    |--------------------------------------------------------------------------
    */

    private function getRoleMembers(
        Collection $voters,
        string $role
    ): Collection {

        return $voters
            ->filter(
                fn (Voter $voter): bool =>
                    $voter->booth_committee_role === $role
            )
            ->map(function (Voter $voter) {

                return [

                    'id' =>
                        $voter->id,

                    'name' =>
                        $voter->name,

                    'mobile' =>
                        $voter->mobile,

                    'gender' =>
                        $voter->gender,

                    'house_no' =>
                        $voter->house?->house_no,
                ];
            })
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Core Team Status
    |--------------------------------------------------------------------------
    */

    private function isCoreTeamComplete(
        array $row
    ): bool {

        return
            ($row['president'] ?? collect())
                ->isNotEmpty()
            &&
            ($row['mahila_president'] ?? collect())
                ->isNotEmpty()
            &&
            ($row['youth_president'] ?? collect())
                ->isNotEmpty();
    }


    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    public function getSummaryProperty(): array
    {
        $rows =
            $this->boothOrganisation;


        return [

            'total_booths' =>
                $rows->count(),

            'presidents' =>
                $rows->filter(
                    fn ($row) =>
                        $row['president']->isNotEmpty()
                )->count(),

            'mahila_presidents' =>
                $rows->filter(
                    fn ($row) =>
                        $row['mahila_president']->isNotEmpty()
                )->count(),

            'youth_presidents' =>
                $rows->filter(
                    fn ($row) =>
                        $row['youth_president']->isNotEmpty()
                )->count(),

            'fully_empty' =>
                $rows->filter(
                    fn ($row) =>
                        $row['president']->isEmpty()
                        &&
                        $row['mahila_president']->isEmpty()
                        &&
                        $row['youth_president']->isEmpty()
                )->count(),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Filters
    |--------------------------------------------------------------------------
    */

    public function resetFilters(): void
    {
        $this->search = '';

        $this->villageFilter = '';

        $this->roleFilter = '';

        $this->statusFilter = 'all';
    }


    /*
    |--------------------------------------------------------------------------
    | CSV Export
    |--------------------------------------------------------------------------
    */

    public function exportCsv(): StreamedResponse
    {
        $rows =
            $this->boothOrganisation;


        return response()->streamDownload(
            function () use ($rows) {

                $handle =
                    fopen('php://output', 'w');


                /*
                |--------------------------------------------------------------------------
                | UTF-8 BOM
                |--------------------------------------------------------------------------
                */

                fwrite(
                    $handle,
                    "\xEF\xBB\xBF"
                );


                fputcsv(
                    $handle,
                    [
                        'Booth No',
                        'Booth Name',
                        'Village',
                        'Booth President',
                        'President Mobile',
                        'Mahila President',
                        'Mahila President Mobile',
                        'Youth President',
                        'Youth President Mobile',
                        'Vice President',
                        'General Secretary',
                        'Secretary',
                        'Treasurer',
                        'Panna Pramukh',
                        'Polling Agent',
                        'Volunteers',
                        'Status',
                    ]
                );


                foreach ($rows as $row) {

                    fputcsv(
                        $handle,
                        [
                            $row['booth_no'] ?? '-',

                            $row['booth_name'] ?? '-',

                            $row['village'] ?? '-',

                            $this->names(
                                $row['president']
                            ),

                            $this->mobiles(
                                $row['president']
                            ),

                            $this->names(
                                $row['mahila_president']
                            ),

                            $this->mobiles(
                                $row['mahila_president']
                            ),

                            $this->names(
                                $row['youth_president']
                            ),

                            $this->mobiles(
                                $row['youth_president']
                            ),

                            $this->names(
                                $row['vice_president']
                            ),

                            $this->names(
                                $row['general_secretary']
                            ),

                            $this->names(
                                $row['secretary']
                            ),

                            $this->names(
                                $row['treasurer']
                            ),

                            $this->names(
                                $row['panna_pramukh']
                            ),

                            $this->names(
                                $row['polling_agent']
                            ),

                            $this->names(
                                collect()
                                    ->merge(
                                        $row['volunteers']
                                            ?? collect()
                                    )
                                    ->merge(
                                        $row['mahila_volunteers']
                                            ?? collect()
                                    )
                                    ->merge(
                                        $row['youth_volunteers']
                                            ?? collect()
                                    )
                            ),

                            $this->isCoreTeamComplete($row)
                                ? 'Core Team Complete'
                                : 'Core Team Pending',
                        ]
                    );
                }


                fclose($handle);
            },
            'assembly-booth-organisation.csv',
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PDF Export
    |--------------------------------------------------------------------------
    */

    public function exportPdf()
{
    return redirect()->route(
        'assembly-booth-organisation.pdf',
        [
            'search' => $this->search,
            'village' => $this->villageFilter,
            'role' => $this->roleFilter,
            'status' => $this->statusFilter,
        ]
    );
}


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function names(
        ?Collection $people
    ): string {

        if (! $people || $people->isEmpty()) {
            return '-';
        }


        return $people
            ->pluck('name')
            ->filter()
            ->implode(', ');
    }


    private function mobiles(
        ?Collection $people
    ): string {

        if (! $people || $people->isEmpty()) {
            return '-';
        }


        return $people
            ->pluck('mobile')
            ->filter()
            ->implode(', ');
    }
}
