<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\House;
use App\Models\Survey;
use App\Models\Voter;
use App\Services\SurveyService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SurveyRunner extends Page
{
    use AuthorizesPagePermission;
    protected static string $requiredPermission = 'survey.create';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Survey Runner';

    protected static string|UnitEnum|null $navigationGroup = 'Campaign';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Survey Runner';

    protected string $view = 'filament.pages.survey-runner';


    public string $search = '';

    public ?int $surveyId = null;

    public ?House $house = null;

    public $voters = [];

    public ?Voter $selectedVoter = null;

    public $questions = [];

    public $answers = [];


    public function searchHouse(): void
    {
        $this->house = House::query()
            ->where('house_no', $this->search)
            ->orWhere('head_of_family', 'like', "%{$this->search}%")
            ->orWhere('mobile', $this->search)
            ->first();


        if ($this->house) {

            $this->voters = Voter::where('house_id', $this->house->id)
                ->get();

        } else {

            $this->voters = [];

        }


        $this->selectedVoter = null;

        $this->questions = [];

        $this->answers = [];
    }



    public function startSurvey(int $voterId): void
    {

        if (! $this->surveyId) {

            Notification::make()
                ->title('Please select a survey first.')
                ->warning()
                ->send();

            return;

        }


        $this->selectedVoter = Voter::find($voterId);


        $survey = Survey::with('questions')
            ->find($this->surveyId);


        $this->questions = $survey?->questions ?? [];


        /*
         |--------------------------------------------------------------------------
         | Initialize Answers
         |--------------------------------------------------------------------------
         |
         | Checkbox questions require array values.
         | Other question types require single value.
         |
         */

        $this->answers = [];


        foreach ($this->questions as $question) {


            if ($question->type === 'checkbox') {


                $this->answers[$question->id] = [];


            } else {


                $this->answers[$question->id] = null;


            }

        }

    }




    public function saveSurvey(SurveyService $service): void
    {

        if (! $this->selectedVoter || ! $this->surveyId) {


            Notification::make()
                ->title('Please select Survey and Voter.')
                ->danger()
                ->send();


            return;

        }



        $service->save(

            surveyId: $this->surveyId,

            voterId: $this->selectedVoter->id,

            houseId: $this->selectedVoter->house_id,

            userId: auth()->id(),

            answers: $this->answers,

        );



        Notification::make()

            ->title('Survey Saved Successfully')

            ->success()

            ->send();



        $this->selectedVoter = null;

        $this->questions = [];

        $this->answers = [];

    }





    public function getSurveysProperty()
    {
        return Survey::orderBy('name')->get();
    }
}
