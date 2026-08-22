<?php

namespace App\Filament\Resources\Constituencies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ConstituenciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->select('constituencies.*')
                ->selectSub(
                    DB::table('voters')
                        ->selectRaw('COUNT(*)')
                        ->join('houses', 'voters.house_id', '=', 'houses.id')
                        ->join('booths', 'houses.booth_id', '=', 'booths.id')
                        ->join('villages', 'booths.village_id', '=', 'villages.id')
                        ->whereColumn('villages.constituency_id', 'constituencies.id'),
                    'actual_voters_count'
                )
                ->selectSub(
                    DB::table('booths')
                        ->selectRaw('COUNT(*)')
                        ->join('villages', 'booths.village_id', '=', 'villages.id')
                        ->whereColumn('villages.constituency_id', 'constituencies.id'),
                    'actual_booths_count'
                ))
            ->columns([
                TextColumn::make('name')
                    ->label('Constituency Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('state')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('district')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('actual_voters_count')
                    ->label('Total Voters')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('actual_booths_count')
                    ->label('Total Booths')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
