<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\Voter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AssemblyBoothOrganisationExportController extends Controller
{
    /**
     * Export Assembly Booth Organisation as PDF.
     */
    public function pdf(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $villageFilter = trim(
            (string) $request->get('village', '')
        );

        $roleFilter = trim(
            (string) $request->get('role', '')
        );

        $statusFilter = trim(
            (string) $request->get('status', '')
        );


        /*
        |--------------------------------------------------------------------------
        | Active Booths
        |--------------------------------------------------------------------------
        */

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
        | Voters With Organisation Roles
        |--------------------------------------------------------------------------
        */

        $voters = Voter::query()
            ->with('house')
            ->whereNotNull('booth_committee_role')
            ->where(
                'booth_committee_role',
                '!=',
                ''
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Group Voters By Booth
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
        | Build Booth Organisation Rows
        |--------------------------------------------------------------------------
        */

        $rows = $booths
            ->map(function (Booth $booth) use ($votersByBooth) {

                $boothVoters = $votersByBooth->get(
                    $booth->id,
                    collect()
                );


                return [

                    'booth_id' =>
                        (int) $booth->id,

                    'booth_no' =>
                        $this->safeString(
                            $booth->booth_no
                        ),

                    'booth_name' =>
                        $this->safeString(
                            $booth->booth_name
                            ?: 'Booth ' . $booth->booth_no
                        ),

                    'village' =>
                        $this->safeString(
                            $booth->village?->name ?: '-'
                        ),


                    /*
                    |--------------------------------------------------------------------------
                    | Core Organisation
                    |--------------------------------------------------------------------------
                    */

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


                    /*
                    |--------------------------------------------------------------------------
                    | Volunteers
                    |--------------------------------------------------------------------------
                    */

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
        | Search Filter
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $searchLower = mb_strtolower(
                $search,
                'UTF-8'
            );

            $rows = $rows
                ->filter(function (array $row) use ($searchLower) {

                    /*
                    |--------------------------------------------------------------------------
                    | Booth No
                    |--------------------------------------------------------------------------
                    */

                    if (
                        str_contains(
                            mb_strtolower(
                                $row['booth_no'],
                                'UTF-8'
                            ),
                            $searchLower
                        )
                    ) {
                        return true;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Booth Name
                    |--------------------------------------------------------------------------
                    */

                    if (
                        str_contains(
                            mb_strtolower(
                                $row['booth_name'],
                                'UTF-8'
                            ),
                            $searchLower
                        )
                    ) {
                        return true;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Village
                    |--------------------------------------------------------------------------
                    */

                    if (
                        str_contains(
                            mb_strtolower(
                                $row['village'],
                                'UTF-8'
                            ),
                            $searchLower
                        )
                    ) {
                        return true;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Role Members
                    |--------------------------------------------------------------------------
                    */

                    $roleKeys = [

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
                    ];


                    foreach ($roleKeys as $roleKey) {

                        foreach (
                            ($row[$roleKey] ?? [])
                            as $person
                        ) {

                            $name = mb_strtolower(
                                (string) ($person['name'] ?? ''),
                                'UTF-8'
                            );

                            $mobile = mb_strtolower(
                                (string) ($person['mobile'] ?? ''),
                                'UTF-8'
                            );


                            if (
                                str_contains(
                                    $name,
                                    $searchLower
                                )
                            ) {
                                return true;
                            }


                            if (
                                str_contains(
                                    $mobile,
                                    $searchLower
                                )
                            ) {
                                return true;
                            }
                        }
                    }


                    return false;
                })
                ->values();
        }


        /*
        |--------------------------------------------------------------------------
        | Village Filter
        |--------------------------------------------------------------------------
        */

        if ($villageFilter !== '') {

            $rows = $rows
                ->filter(
                    fn (array $row): bool =>
                        $row['village'] === $villageFilter
                )
                ->values();
        }


        /*
        |--------------------------------------------------------------------------
        | Role Filter
        |--------------------------------------------------------------------------
        */

        if ($roleFilter !== '') {

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


            if (isset($roleMap[$roleFilter])) {

                $roleKey =
                    $roleMap[$roleFilter];


                $rows = $rows
                    ->filter(
                        fn (array $row): bool =>
                            ! empty(
                                $row[$roleKey]
                            )
                    )
                    ->values();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if (
            $statusFilter !== ''
            &&
            $statusFilter !== 'all'
        ) {

            $rows = $rows
                ->filter(function (array $row) use ($statusFilter) {

                    $complete =
                        ! empty($row['president'])
                        &&
                        ! empty($row['mahila_president'])
                        &&
                        ! empty($row['youth_president']);


                    return match ($statusFilter) {

                        'complete' =>
                            $complete,

                        'pending' =>
                            ! $complete,

                        default =>
                            true,
                    };
                })
                ->values();
        }


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $summary = [

            'total_booths' =>
                $rows->count(),

            'presidents' =>
                $rows->filter(
                    fn (array $row): bool =>
                        ! empty($row['president'])
                )->count(),

            'mahila_presidents' =>
                $rows->filter(
                    fn (array $row): bool =>
                        ! empty(
                            $row['mahila_president']
                        )
                )->count(),

            'youth_presidents' =>
                $rows->filter(
                    fn (array $row): bool =>
                        ! empty(
                            $row['youth_president']
                        )
                )->count(),

            'fully_empty' =>
                $rows->filter(function (array $row): bool {

                    return
                        empty($row['president'])
                        &&
                        empty($row['mahila_president'])
                        &&
                        empty($row['youth_president']);
                })->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | Convert Everything To Plain Arrays
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | DomPDF should not receive Eloquent Collections/models here.
        |
        */

        $rows = $rows
            ->map(function (array $row): array {

                $roleKeys = [

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
                ];


                foreach ($roleKeys as $roleKey) {

                    $row[$roleKey] =
                        array_values(
                            array_map(
                                function (array $person): array {

                                    return [

                                        'id' =>
                                            (int) (
                                                $person['id']
                                                ?? 0
                                            ),

                                        'name' =>
                                            $this->safeString(
                                                $person['name']
                                                ?? '-'
                                            ),

                                        'mobile' =>
                                            $this->safeString(
                                                $person['mobile']
                                                ?? '-'
                                            ),

                                        'gender' =>
                                            $this->safeString(
                                                $person['gender']
                                                ?? '-'
                                            ),

                                        'house_no' =>
                                            $this->safeString(
                                                $person['house_no']
                                                ?? '-'
                                            ),
                                    ];
                                },
                                $row[$roleKey] ?? []
                            )
                        );
                }


                return [

                    'booth_id' =>
                        (int) $row['booth_id'],

                    'booth_no' =>
                        $this->safeString(
                            $row['booth_no']
                        ),

                    'booth_name' =>
                        $this->safeString(
                            $row['booth_name']
                        ),

                    'village' =>
                        $this->safeString(
                            $row['village']
                        ),

                    'president' =>
                        $row['president'],

                    'mahila_president' =>
                        $row['mahila_president'],

                    'youth_president' =>
                        $row['youth_president'],

                    'vice_president' =>
                        $row['vice_president'],

                    'general_secretary' =>
                        $row['general_secretary'],

                    'secretary' =>
                        $row['secretary'],

                    'treasurer' =>
                        $row['treasurer'],

                    'panna_pramukh' =>
                        $row['panna_pramukh'],

                    'polling_agent' =>
                        $row['polling_agent'],

                    'volunteers' =>
                        $row['volunteers'],

                    'mahila_volunteers' =>
                        $row['mahila_volunteers'],

                    'youth_volunteers' =>
                        $row['youth_volunteers'],

                    'other_roles' =>
                        $row['other_roles'],
                ];
            })
            ->values()
            ->all();


        /*
        |--------------------------------------------------------------------------
        | Generate PDF
        |--------------------------------------------------------------------------
        */

        /*
|--------------------------------------------------------------------------
| PDF Memory / Rendering Configuration
|--------------------------------------------------------------------------
*/

@ini_set('memory_limit', '512M');
@set_time_limit(120);


/*
|--------------------------------------------------------------------------
| Generate PDF
|--------------------------------------------------------------------------
*/

$pdf = Pdf::loadView(
    'filament.pages.assembly-booth-organisation-pdf',
    [
        'rows' => $rows,

        'summary' => $summary,

        'search' => $search,

        'villageFilter' => $villageFilter,

        'roleFilter' => $roleFilter,

        'statusFilter' => $statusFilter,
    ]
)
    ->setPaper(
        'a4',
        'landscape'
    )
    ->setOption(
        'isHtml5ParserEnabled',
        true
    )
    ->setOption(
        'isRemoteEnabled',
        false
    )
    ->setOption(
        'isPhpEnabled',
        false
    );

        /*
        |--------------------------------------------------------------------------
        | Download
        |--------------------------------------------------------------------------
        */

        return $pdf->download(
    'assembly-booth-organisation.pdf'
);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Role Members
    |--------------------------------------------------------------------------
    */

    private function getRoleMembers(
        Collection $voters,
        string $role
    ): array {

        return $voters
            ->filter(
                fn (Voter $voter): bool =>
                    $voter->booth_committee_role === $role
            )
            ->map(function (Voter $voter): array {

                return [

                    'id' =>
                        (int) $voter->id,

                    'name' =>
                        $this->safeString(
                            $voter->name
                        ),

                    'mobile' =>
                        $this->safeString(
                            $voter->mobile
                        ),

                    'gender' =>
                        $this->safeString(
                            $voter->gender
                        ),

                    'house_no' =>
                        $this->safeString(
                            $voter->house?->house_no
                        ),
                ];
            })
            ->values()
            ->all();
    }


    /*
    |--------------------------------------------------------------------------
    | UTF-8 Safe String
    |--------------------------------------------------------------------------
    */

   private function safeString(mixed $value): string
{
    if ($value === null) {
        return '';
    }

    if (! is_string($value)) {
        $value = (string) $value;
    }

    if ($value === '') {
        return '';
    }

    /*
    |--------------------------------------------------------------------------
    | Already valid UTF-8
    |--------------------------------------------------------------------------
    */

    if (mb_check_encoding($value, 'UTF-8')) {
        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Try to repair invalid UTF-8
    |--------------------------------------------------------------------------
    */

    $converted = @iconv(
        'UTF-8',
        'UTF-8//IGNORE',
        $value
    );

    if ($converted !== false) {
        return $converted;
    }

    /*
    |--------------------------------------------------------------------------
    | Last fallback
    |--------------------------------------------------------------------------
    */

    return '';
}
}