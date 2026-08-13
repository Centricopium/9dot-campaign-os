<?php

namespace App\Filament\Resources\Voters\Pages;

use App\Filament\Resources\Voters\VoterResource;
use App\Imports\VotersImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListVoters extends ListRecords
{
    protected static string $resource = VoterResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('importVoters')
                ->label('Import Voters')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Import Voters')
                ->modalDescription(
                    'Upload the voter Excel file to import voters, houses and booth mapping.'
                )
                ->modalSubmitActionLabel('Import Voters')
                ->modalWidth('lg')

                ->schema([

                    FileUpload::make('file')
                        ->label('Voter Excel File')
                        ->helperText(
                            'Upload .xlsx, .xls or .csv file.'
                        )
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ])
                        ->required()
                        ->disk('local')
                        ->directory('imports')
                        ->preserveFilenames()
                        ->maxSize(51200),

                ])

                ->action(function (array $data): void {

                    $file = $data['file'] ?? null;

                    if (! $file) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body('Please select a voter Excel file.')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {

                        $path = Storage::disk('local')->path($file);

                        if (! file_exists($path)) {
                            throw new \RuntimeException(
                                'Uploaded file could not be found.'
                            );
                        }

                        $import = new VotersImport();

                        Excel::import($import, $path);

                        $body = "Imported: {$import->imported} voters.";

                        if ($import->skipped > 0) {
                            $body .= " Skipped: {$import->skipped} rows.";
                        }

                        Notification::make()
                            ->title('Voter Import Completed')
                            ->body($body)
                            ->success()
                            ->send();

                        if (! empty($import->errors)) {
                            logger()->warning(
                                'Voter Import Errors',
                                $import->errors
                            );
                        }

                    } catch (\Throwable $e) {

                        Notification::make()
                            ->title('Voter Import Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make(),

        ];
    }
}