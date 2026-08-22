<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemBackup extends Model
{
    protected $fillable = ['initiated_by', 'type', 'status', 'disk', 'file_path', 'file_name', 'file_size', 'checksum', 'database_driver', 'failure_message', 'started_at', 'completed_at', 'verified_at'];

    protected $casts = ['file_size' => 'integer', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'verified_at' => 'datetime'];

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        }if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

return number_format($bytes / 1024, 2).' KB';
    }
}
