<?php

use App\Http\Controllers\AssemblyBoothOrganisationExportController;
use App\Http\Controllers\InternalMessageAttachmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Default
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/admin/login');

/*
|--------------------------------------------------------------------------
| Assembly Booth Organisation PDF Export
|--------------------------------------------------------------------------
*/

Route::get(
    '/assembly-booth-organisation/export-pdf',
    [
        AssemblyBoothOrganisationExportController::class,
        'pdf',
    ]
)
    ->name(
        'assembly-booth-organisation.pdf'
    );

Route::get('/internal-messages/{message}/attachment', InternalMessageAttachmentController::class)
    ->middleware('auth')
    ->name('internal-messages.attachment');
