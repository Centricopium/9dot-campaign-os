<?php

namespace App\Filament\Pages;

use App\Models\Survey;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use UnitEnum;

class SurveyEngine extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Survey Engine';

    protected static ?string $title = 'Survey Engine';
    protected static bool $shouldRegisterNavigation = false;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign';
    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.survey-engine';

    public ?array $data = [];
    public ?\App\Models\Voter $voter = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([

                Forms\Components\Section::make('Survey')
                    ->schema([

                        Forms\Components\Select::make('survey_id')
                            ->label('Select Survey')
                            ->options(
                                Survey::orderBy('name')->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                    ]),

                Forms\Components\Section::make('Search Voter')
                    ->schema([

                        Forms\Components\TextInput::make('epic_no')
                            ->label('EPIC Number'),

                        Forms\Components\TextInput::make('mobile')
                            ->label('Mobile Number'),

                    ])->columns(2),

            ]);
    }

    public function searchVoter(): void
{
    $data = $this->form->getState();

    $this->voter = \App\Models\Voter::query()
        ->when(
            filled($data['epic_no'] ?? null),
            fn ($query) => $query->where('epic_no', $data['epic_no'])
        )
        ->when(
            filled($data['mobile'] ?? null),
            fn ($query) => $query->orWhere('mobile', $data['mobile'])
        )
        ->with(['house', 'village', 'booth'])
        ->first();

    if (! $this->voter) {
        $this->notify('danger', 'Voter not found.');
    }
}

    protected function getHeaderActions(): array
    {
        return [

            Action::make('Search Voter')
                ->action('searchVoter')
                ->color('primary'),

        ];
    }
}