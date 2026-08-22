<?php

namespace App\Services\Security;

use App\Models\SystemBackup;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class SystemBackupService
{
    public function create(?int $userId = null): SystemBackup
    {
        $backup = SystemBackup::create(['initiated_by' => $userId, 'status' => 'Running', 'database_driver' => config('database.default'), 'started_at' => now()]);
        try {
            $disk = Storage::disk('local');
            $disk->makeDirectory('backups');
            $name = 'campaign-os-'.now()->format('Ymd-His').'-'.$backup->id.'.sql.gz';
            $path = 'backups/'.$name;
            $absolute = $disk->path($path);
            $driver = config('database.default');
            if ($driver === 'mysql') {
                $this->dumpMysql($absolute);
            } elseif ($driver === 'sqlite') {
                $this->dumpSqlite($absolute);
            } else {
                throw new RuntimeException("Backup driver {$driver} is not supported.");
            }$backup->update(['status' => 'Completed', 'disk' => 'local', 'file_path' => $path, 'file_name' => $name, 'file_size' => filesize($absolute) ?: 0, 'checksum' => hash_file('sha256', $absolute), 'completed_at' => now(), 'verified_at' => now()]);
            $this->expireOld();

            return $backup->fresh();
        } catch (Throwable $e) {
            $backup->update(['status' => 'Failed', 'failure_message' => $e->getMessage(), 'completed_at' => now()]);
            throw $e;
        }
    }

    public function verify(SystemBackup $backup): bool
    {
        if (! $backup->file_path || ! Storage::disk($backup->disk)->exists($backup->file_path)) {
            return false;
        }$valid = hash_file('sha256', Storage::disk($backup->disk)->path($backup->file_path)) === $backup->checksum;
        if ($valid) {
            $backup->update(['verified_at' => now()]);
        }

return $valid;
    }

    public function restore(SystemBackup $backup): void
    {
        if (! $this->verify($backup)) {
            throw new RuntimeException('Backup checksum verification failed.');
        }$driver = config('database.default');
        if ($driver !== 'mysql') {
            throw new RuntimeException('Automated restore currently supports MySQL only.');
        }$absolute = Storage::disk($backup->disk)->path($backup->file_path);
        $plain = $absolute.'.restore.sql';
        $this->gunzip($absolute, $plain);
        $c = config('database.connections.mysql');
        $process = new Process([$this->binary('mysql'), '--host='.$c['host'], '--port='.$c['port'], '--user='.$c['username'], $c['database']], null, ['MYSQL_PWD' => (string) $c['password']], fopen($plain, 'rb'), 7200);
        try {
            $process->mustRun();
        } finally {
            @unlink($plain);
        }
    }

    private function dumpMysql(string $target): void
    {
        $c = config('database.connections.mysql');
        $plain = $target.'.tmp.sql';
        $process = new Process([$this->binary('mysqldump'), '--host='.$c['host'], '--port='.$c['port'], '--user='.$c['username'], '--single-transaction', '--quick', '--routines', '--triggers', '--default-character-set=utf8mb4', '--result-file='.$plain, $c['database']], null, ['MYSQL_PWD' => (string) $c['password']], null, 3600);
        $process->mustRun();
        $this->gzip($plain, $target);
        @unlink($plain);
    }

    private function dumpSqlite(string $target): void
    {
        $source = config('database.connections.sqlite.database');
        if (! is_file($source)) {
            throw new RuntimeException('SQLite database file was not found.');
        }$this->gzip($source, $target);
    }

    private function gzip(string $source, string $target): void
    {
        $in = fopen($source, 'rb');
        $out = gzopen($target, 'wb9');
        if (! $in || ! $out) {
            throw new RuntimeException('Unable to create compressed backup.');
        }while (! feof($in)) {
            gzwrite($out, fread($in, 1024 * 1024));
        }fclose($in);
        gzclose($out);
    }

    private function gunzip(string $source, string $target): void
    {
        $in = gzopen($source, 'rb');
        $out = fopen($target, 'wb');
        if (! $in || ! $out) {
            throw new RuntimeException('Unable to read compressed backup.');
        }while (! gzeof($in)) {
            fwrite($out, gzread($in, 1024 * 1024));
        }gzclose($in);
        fclose($out);
    }

    private function binary(string $name): string
    {
        foreach ([env('BACKUP_'.strtoupper($name).'_PATH'), '/usr/local/bin/'.$name, '/usr/bin/'.$name] as $path) {
            if ($path && is_executable($path)) {
                return $path;
            }
        }throw new RuntimeException("{$name} binary was not found on the server.");
    }

    private function expireOld(): void
    {
        $days = max(7, (int) config('backup.retention_days', 14));
        SystemBackup::query()->where('status', 'Completed')->where('created_at', '<', now()->subDays($days))->each(function (SystemBackup $old): void {
            if ($old->file_path) {
                Storage::disk($old->disk)->delete($old->file_path);
            }$old->update(['status' => 'Expired']);
        });
    }
}
