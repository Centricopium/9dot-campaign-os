<?php

namespace App\Filament\Pages;

use App\Models\House;
use App\Models\Survey;
use App\Models\Voter;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use App\Models\SurveyResponse;
use App\Models\SurveyAnswer;

class SurveyRunner extends Page
{
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
            $this->voters = Voter::where('house_id', $this->house->id)->get();
        } else {
            $this->voters = [];
        }

        // Reset previous survey state
        $this->selectedVoter = null;
        $this->questions = [];
        $this->answers = [];
    }

    public function startSurvey(int $voterId): void
    {
        if (! $this->surveyId) {
            return;
        }

        $this->selectedVoter = Voter::find($voterId);

        $survey = Survey::with('questions')->find($this->surveyId);

        $this->questions = $survey?->questions ?? [];

        $this->answers = [];
    }

    public function getSurveysProperty()
    {
        return Survey::orderBy('name')->get();
    }
    public function saveSurvey()
    {
        if (!$this->selectedVoter || !$this->surveyId) {
            return;
    }

    $response = SurveyResponse::create([
        'survey_id' => $this->surveyId,
        'voter_id' => $this->selectedVoter->id,
        'house_id' => $this->selectedVoter->house_id,
        'submitted_by' => auth()->id(),
    ]);

    foreach ($this->answers as $questionId => $answer) {

        SurveyAnswer::create([
            'survey_response_id' => $response->id,
            'question_id' => $questionId,
            'answer' => $answer,
        ]);

    }

    $this->dispatch('notify', 'Survey Saved Successfully.');

    $this->selectedVoter = null;
    $this->questions = [];
    $this->answers = [];
}
}