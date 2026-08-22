<?php

namespace App\Filament\Resources\CandidateAssessments;

use App\Filament\Resources\CandidateAssessments\Pages\CreateCandidateAssessment;
use App\Filament\Resources\CandidateAssessments\Pages\EditCandidateAssessment;
use App\Filament\Resources\CandidateAssessments\Pages\ListCandidateAssessments;
use App\Filament\Resources\CandidateAssessments\Schemas\CandidateAssessmentForm;
use App\Filament\Resources\CandidateAssessments\Tables\CandidateAssessmentsTable;
use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Models\CandidateAssessment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CandidateAssessmentResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'candidate_assessment';

    protected static ?string $model = CandidateAssessment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Candidate Strategy';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Candidate Assessments';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return CandidateAssessmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CandidateAssessmentsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where(function (Builder $query): void {
            $query->where('is_submitted', true)
                ->orWhere('assessor_id', auth()->id());
        });
    }

    public static function canEdit($record): bool
    {
        return (auth()->user()?->can('candidate_assessment.update') ?? false)
            && ! $record->is_submitted
            && (int) $record->assessor_id === (int) auth()->id();
    }

    public static function canDelete($record): bool
    {
        return (auth()->user()?->can('candidate_assessment.delete') ?? false)
            && ! $record->is_submitted
            && (int) $record->assessor_id === (int) auth()->id();
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCandidateAssessments::route('/'),
            'create' => CreateCandidateAssessment::route('/create'),
            'edit' => EditCandidateAssessment::route('/{record}/edit'),
        ];
    }
}
