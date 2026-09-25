<?php

namespace App\Jobs;

use App\Imports\VotersImport;
use App\Models\VoterImportBatch;
use App\Services\House\HouseHeadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CsvVoterImportChunk implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const CHUNK_SIZE = 500;

    public int $timeout = 900;

    public int $tries = 2;

    public function __construct(
        public readonly string $path,
        public readonly int $constituencyId,
        public readonly ?int $batchId = null,
        public readonly int $byteOffset = 0,
    ) {}

    public function handle(): void
    {
        $disk = Storage::disk('local');
        $file = $disk->path($this->path);

        if (! $disk->exists($this->path) || ! $handle = fopen($file, 'rb')) {
            throw new \RuntimeException('The CSV voter import file is no longer available.');
        }

        try {
            [$headers, $startOffset] = $this->readHeaders($handle);
            $offset = $this->byteOffset ?: $startOffset;
            fseek($handle, $offset);

            $rows = collect();
            while ($rows->count() < self::CHUNK_SIZE && ($values = fgetcsv($handle)) !== false) {
                if ($values === [null] || $values === []) {
                    continue;
                }

                $rows->push(new Collection(array_combine($headers, array_pad($values, count($headers), null))));
            }

            $nextOffset = ftell($handle);
            $finished = feof($handle);
        } finally {
            fclose($handle);
        }

        if ($rows->isNotEmpty()) {
            (new VotersImport($this->constituencyId, $this->path, $this->batchId))->collection($rows);
        }

        if (! $finished) {
            self::dispatch($this->path, $this->constituencyId, $this->batchId, $nextOffset);

            return;
        }

        app(HouseHeadService::class)->backfill($this->constituencyId);

        if ($this->batchId) {
            VoterImportBatch::query()->whereKey($this->batchId)->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);
        }

        $disk->delete($this->path);
    }

    /** @return array{array<int, string>, int} */
    private function readHeaders($handle): array
    {
        rewind($handle);
        $headers = fgetcsv($handle);

        if (! is_array($headers) || $headers === []) {
            throw new \RuntimeException('The CSV voter import file has no headings.');
        }

        $headers[0] = preg_replace('/^\\xEF\\xBB\\xBF/', '', (string) $headers[0]);

        return [$headers, ftell($handle)];
    }
}
