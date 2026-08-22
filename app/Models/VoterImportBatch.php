<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoterImportBatch extends Model
{
    use ScopedToAssemblyConstituency;

    protected $fillable = ['constituency_id', 'user_id', 'file_name', 'file_path', 'status', 'total_rows', 'processed_rows', 'imported_rows', 'skipped_rows', 'failed_rows', 'errors', 'failure_message', 'started_at', 'completed_at'];

    protected $casts = ['total_rows' => 'integer', 'processed_rows' => 'integer', 'imported_rows' => 'integer', 'skipped_rows' => 'integer', 'failed_rows' => 'integer', 'errors' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->status === 'Completed') {
            return 100;
        }if (! $this->total_rows) {
            return 0;
        }

return min(99, (int) round(($this->processed_rows / $this->total_rows) * 100));
    }
}
