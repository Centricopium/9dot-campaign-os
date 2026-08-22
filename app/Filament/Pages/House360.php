<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\House;
use App\Services\HouseService;
use App\Services\House\HouseIntelligenceService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;


class House360 extends Page
{
    use AuthorizesPagePermission;
    protected static string $requiredPermission = 'house.view';

    public ?House $house = null;


    public ?string $search = '';


    public array $politicalSummary = [];



    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedHome;


    protected static string|UnitEnum|null $navigationGroup =
        'Campaign';


    protected static ?string $navigationLabel =
        'House 360';


    protected static ?string $title =
        'House 360';


    protected static ?int $navigationSort =
        3;


    protected string $view =
        'filament.pages.house360';



    /*
    |--------------------------------------------------------------------------
    | Search House
    |--------------------------------------------------------------------------
    */

    public function searchHouse(
        HouseService $service
    ): void {


        $record = House::query()

            ->where(
                'house_no',
                $this->search
            )

            ->orWhere(
                'head_of_family',
                'like',
                '%' . $this->search . '%'
            )

            ->orWhere(
                'mobile',
                $this->search
            )

            ->first();



        if (! $record) {


            $this->house = null;


            $this->politicalSummary = [];



            Notification::make()

                ->title('House not found')

                ->body(
                    'Please check House Number, Head of Family or Mobile Number.'
                )

                ->warning()

                ->send();


            return;

        }



        $this->house =
            $service->getHouse(
                $record->id
            );



        $this->politicalSummary =
            $service->getPoliticalSummary(
                $this->house
            );

    }



    /*
    |--------------------------------------------------------------------------
    | Survey
    |--------------------------------------------------------------------------
    */

    public function startSurvey()
    {
        return redirect()
            ->route(
                'filament.admin.pages.survey-runner'
            );
    }




    /*
    |--------------------------------------------------------------------------
    | AI House Intelligence
    |--------------------------------------------------------------------------
    */

    public function getHouseIntelligenceProperty()
    {

        if (! $this->house) {

            return null;

        }


        return app(
            HouseIntelligenceService::class
        )
        ->analyse(
            $this->house
        );

    }


}
