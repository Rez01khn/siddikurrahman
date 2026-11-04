<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Throwable;

class LegacyBackup extends Command
{
    protected $signature = 'legacy:backup {--mysqldump= : Absolute path to the local mysqldump executable}';

    protected $description = 'Create local private SQL/media backups and verify checksums/schema; never restore or import';

    public function handle(): int
    {
        if (app()->isProduction() || DB::connection()->getDriverName() !== 'mysql') {
            $this->error('This preparation command requires a local MySQL environment.');

            return self::FAILURE;
        }
        $binary = $this->option('mysqldump');
        if (! is_string($binary) || ! is_file($binary) || ! preg_match('/\Amysqldump(?:\.exe)?\z/i', basename($binary))) {
            $this->error('Supply an absolute mysqldump executable path.');

            return self::FAILURE;
        }
        $dir = storage_path('app/private/legacy/backups/'.now()->format('Ymd-His').'-'.Str::random(8));
        File::ensureDirectoryExists($dir, 0700);
        $sql = $dir.'/database.sql';
        $config = DB::connection()->getConfig();
        $stage = 'table inventory';
        try {
            $tables = collect(Schema::getTables(DB::connection()->getDatabaseName()))->pluck('name')->all();
            if (! $tables) {
                throw new \RuntimeException('No tables found.');
            }
            $counts = fn () => collect($tables)->mapWithKeys(fn ($t) => [$t => DB::table($t)->count()])->all();
            $before = $counts();
            $stage = 'mysqldump execution';
            $process = new Process([$binary, '--single-transaction', '--skip-lock-tables', '--routines', '--events', '--triggers', '--hex-blob', '--no-tablespaces', '--set-gtid-purged=OFF', '--column-statistics=0', '--host='.(string) $config['host'], '--port='.(string) $config['port'], '--user='.(string) $config['username'], '--result-file='.$sql, (string) $config['database']], base_path(), ['MYSQL_PWD' => (string) $config['password']], null, 180);
            $process->run();
            if (! $process->isSuccessful() || ! is_file($sql) || filesize($sql) === 0) {
                preg_match('/(?:error|errno)[^0-9]*([0-9]+)/i', $process->getErrorOutput(), $code);
                $stage .= ' (exit '.$process->getExitCode().', error code '.($code[1] ?? 'unavailable').')';
                throw new \RuntimeException('Dump failed.');
            }
            $stage = 'SQL coverage verification';
            $dump = File::get($sql);
            if (! str_contains($dump, '-- Dump completed on')) {
                throw new \RuntimeException('Dump is incomplete.');
            }foreach ($tables as $table) {
                if (! str_contains($dump, 'CREATE TABLE `'.$table.'`')) {
                    throw new \RuntimeException('A table definition is missing.');
                }
            }
            $stage = 'media checksum verification';
            $media = [];
            $root = realpath(storage_path('app/public'));
            foreach (File::allFiles($root) as $file) {
                if ($file->isLink() || ! str_starts_with($file->getRealPath(), $root.DIRECTORY_SEPARATOR)) {
                    throw new \RuntimeException('Media symlink requires manual review.');
                }$relative = $file->getRelativePathname();
                $target = $dir.'/media/'.$relative;
                File::ensureDirectoryExists(dirname($target), 0700);
                if (! File::copy($file->getPathname(), $target) || hash_file('sha256', $target) !== hash_file('sha256', $file->getPathname())) {
                    throw new \RuntimeException('Media verification failed.');
                }$media[] = ['path' => $relative, 'bytes' => $file->getSize(), 'sha256' => hash_file('sha256', $target)];
            }
            $after = $counts();
            if ($before !== $after) {
                throw new \RuntimeException('Record counts changed during backup; retry while local writes are paused.');
            }
            $manifest = ['created_at' => now()->toIso8601String(), 'scope' => 'configured local MySQL and public storage, not legacy production', 'sql_sha256' => hash_file('sha256', $sql), 'sql_bytes' => filesize($sql), 'tables' => $before, 'media' => $media, 'integrity_verified' => true, 'restore_tested' => false, 'source_archive_available' => false];
            File::put($dir.'/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            $this->info('Backup: '.str_replace(base_path().DIRECTORY_SEPARATOR, '', $dir));
            $this->line('SQL completed; table coverage, stable counts and copied-media SHA-256 verified. Restore NOT tested.');

            return self::SUCCESS;
        } catch (Throwable) {
            File::put($dir.'/FAILED.txt', 'Backup incomplete. Do not use for an import or restore. No import was executed.');
            $this->error('Backup failed at '.$stage.'. Private files retained. Credentials and dump output suppressed.');

            return self::FAILURE;
        }
    }
}
