<?php

namespace App\Services\Import;

class ImportResult
{
    public int $totalRows = 0;

    public int $imported = 0;

    /**
     * Existing voter records updated from latest EC data.
     */
    public int $updated = 0;

    public int $skipped = 0;

    public int $duplicateEpic = 0;

    public int $newHouses = 0;

    public int $errors = 0;

    /**
     * @var array<int, string>
     */
    public array $messages = [];

    public function addError(string $message): void
    {
        $this->errors++;

        $this->messages[] = $message;
    }
}