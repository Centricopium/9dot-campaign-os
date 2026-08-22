<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\Booth;
use App\Services\Survey\BoothIntelligenceService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class BoothIntelligence extends Page
{
    use AuthorizesPagePermission;
    protected static string $requiredPermission = 'report.view';
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel =
        'Booth Intelligence';

    protected static string|UnitEnum|null $navigationGroup =
        'Campaign';

    protected static ?int $navigationSort =
        5;

    protected static ?string $title =
        'Booth Intelligence';

    protected string $view =
        'filament.pages.booth-intelligence';


    /**
     * Currently selected booth ID.
     */
    public ?int $booth = null;


    /**
     * Initialize page.
     */
    public function mount(): void
    {
        /*
         * Try booth ID from URL.
         *
         * Example:
         * /admin/booth-intelligence?booth=331
         */
        $requestedBooth = request()->integer('booth');

        if ($requestedBooth > 0) {

            $exists = Booth::query()
                ->whereKey($requestedBooth)
                ->where('is_active', true)
                ->exists();

            if ($exists) {

                $this->booth = $requestedBooth;

                return;
            }
        }


        /*
         * If no valid booth was supplied,
         * automatically select the first active booth.
         */
        $this->booth = Booth::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->value('id');
    }


    /**
     * Booth selector options.
     *
     * Used by the Blade view:
     *
     * $data['boothOptions']
     */
    public function getBoothOptionsProperty(): array
    {
        return Booth::query()
            ->where('is_active', true)
            ->with('village')
            ->orderBy('booth_name')
            ->get()
            ->mapWithKeys(function (Booth $booth) {

                $label = $booth->booth_name
                    ?: 'Booth #' . $booth->id;

                $village = $booth->village?->name;

                if ($village) {

                    $label .= ' — ' . $village;

                }

                return [
                    $booth->id => $label,
                ];
            })
            ->toArray();
    }


    /**
     * Handle booth selection.
     *
     * This is useful if you later use:
     *
     * wire:change="selectBooth($event.target.value)"
     */
    public function selectBooth(
        int|string|null $boothId
    ): void {

        $boothId = (int) $boothId;

        if ($boothId <= 0) {

            return;

        }


        $exists = Booth::query()
            ->whereKey($boothId)
            ->where('is_active', true)
            ->exists();


        if (! $exists) {

            return;

        }


        $this->booth = $boothId;
    }


    /**
     * Complete page data.
     */
    public function getDataProperty(): array
    {
        $service = app(
            BoothIntelligenceService::class
        );


        /*
         * Always expose booth options.
         *
         * This is required by the Blade selector.
         */
        $boothOptions = $this->boothOptions;


        /*
         * No active booth available.
         */
        if (! $this->booth) {

            return [

                'selectedBooth' =>
                    $this->emptyBoothData(),

                'boothOptions' =>
                    $boothOptions,

            ];
        }


        /*
         * Make sure selected booth is still active.
         *
         * This protects against a booth being deleted,
         * deactivated, or changed while the page is open.
         */
        $exists = Booth::query()
            ->whereKey($this->booth)
            ->where('is_active', true)
            ->exists();


        if (! $exists) {

            $this->booth =
                Booth::query()
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->value('id');


            if (! $this->booth) {

                return [

                    'selectedBooth' =>
                        $this->emptyBoothData(),

                    'boothOptions' =>
                        $boothOptions,

                ];
            }
        }


        /*
         * Generate complete booth intelligence.
         */
        $selectedBooth =
            $service->getBoothIntelligence(
                $this->booth
            );


        return [

            'selectedBooth' =>
                $selectedBooth,

            'boothOptions' =>
                $boothOptions,

        ];
    }


    /**
     * Empty booth data structure.
     *
     * Keeps Blade safe when no booth exists.
     */
    private function emptyBoothData(): array
    {
        return [

            'booth' => null,


            'summary' => [

                'total_voters' => 0,

                'supporters' => 0,

                'support_percentage' => 0,

                'neutral' => 0,

                'neutral_percentage' => 0,

                'undecided' => 0,

                'opposition' => 0,

                'opposition_percentage' => 0,

                'houses' => 0,

                'male_voters' => 0,

                'female_voters' => 0,

                'other_voters' => 0,

            ],


            'gender_analysis' => [

                'male' => [],

                'female' => [],

                'other' => [],

            ],


            'houses' =>
                collect(),


            'voters' =>
                collect(),


            'swing_voters' =>
                collect(),


            'influencers' =>
                collect(),


            'volunteers' =>
                collect(),


            'risk_score' =>
                0,


            'priority' =>
                'LOW',


            'actions' =>
                [],

        ];
    }
}
