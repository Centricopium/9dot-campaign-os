<?php

namespace App\Filament\Pages;

use App\Models\House;
use App\Services\HouseService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class House360 extends Page
{
    public ?House $house = null;

    public ?string $search = '';

    public array $politicalSummary = [];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign';

    protected static ?string $navigationLabel = 'House 360';

    protected static ?string $title = 'House 360';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.house360';

    public function searchHouse(HouseService $service): void
    {
        $record = House::query()
            ->where('house_no', $this->search)
            ->orWhere('head_of_family', 'like', '%' . $this->search . '%')
            ->orWhere('mobile', $this->search)
            ->first();

        if (! $record) {

            $this->house = null;
            $this->politicalSummary = [];

            Notification::make()
                ->title('House not found')
                ->body('Please check House Number, Head of Family or Mobile Number.')
                ->warning()
                ->send();

            return;
        }

        $this->house = $service->getHouse($record->id);

        $this->politicalSummary = $service->getPoliticalSummary($this->house);
    }

    public function startSurvey()
    {
        return redirect()->route('filament.admin.pages.survey-runner');
    }
}