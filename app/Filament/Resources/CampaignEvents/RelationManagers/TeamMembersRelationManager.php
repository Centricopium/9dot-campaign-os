<?php

namespace App\Filament\Resources\CampaignEvents\RelationManagers;

use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'teamMembers';

    protected static ?string $title = 'Event Team & Attendance';

    public function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('user_id')->options(fn () => User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())->searchable()->required(), Select::make('role')->options(['Coordinator' => 'Coordinator', 'Security' => 'Security', 'Media' => 'Media', 'Transport' => 'Transport', 'Stage' => 'Stage & Sound', 'Volunteer' => 'Volunteer', 'Other' => 'Other'])->required(), Select::make('attendance_status')->options(['Assigned' => 'Assigned', 'Present' => 'Present', 'Absent' => 'Absent', 'Excused' => 'Excused'])->default('Assigned')->required(), DateTimePicker::make('checked_in_at'), Textarea::make('notes')]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('user.name')->searchable(), TextColumn::make('role')->badge(), TextColumn::make('attendance_status')->badge(), TextColumn::make('checked_in_at')->dateTime('d M, h:i A')])->headerActions([CreateAction::make()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
