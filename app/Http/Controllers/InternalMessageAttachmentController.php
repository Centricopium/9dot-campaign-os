<?php

namespace App\Http\Controllers;

use App\Models\InternalMessage;
use Illuminate\Support\Facades\Storage;

class InternalMessageAttachmentController extends Controller
{
    public function __invoke(InternalMessage $message)
    {
        abort_unless(auth()->user()?->can('internal_message.view'), 403);
        abort_unless($message->conversation()->whereHas('participants', fn ($query) => $query->whereKey(auth()->id()))->exists(), 403);
        abort_unless($message->attachment_path && Storage::disk('local')->exists($message->attachment_path), 404);

        return Storage::disk('local')->download($message->attachment_path);
    }
}
