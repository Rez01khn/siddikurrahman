<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Throwable;

class LegacyRestoreCheck extends Command
{
    protected $signature = 'legacy:restore-check {backup : Directory below private legacy/backups} {--mysql= : Absolute local MySQL client path}';

    protected $description = 'Restore a verified local backup into a new isolated database, compare counts and retain evidence; never replace/drop a database';

    public function handle(): int
    {
        $root = realpath(storage_path('app/private/legacy/backups'));
        $dir = realpath($this->argument('backup'));
        $binary = $this->option('mysql');
        $c = DB::connection()->getConfig();
        if (app()->isProduction() || DB::connection()->getDriverName() !== 'mysql' || ! in_array($c['host'], ['127.0.0.1', 'localhost', '::1'], true) || ! $root || ! $dir || ! str_starts_with($dir, $root.DIRECTORY_SEPARATOR) || ! is_string($binary) || ! is_file($binary) || ! preg_match('/\Amysql(?:\.exe)?\z/i', basename($binary))) {
            $this->error('Use a private local backup and local MySQL client; production/remote servers are refused.');

            return self::FAILURE;
        }
        $created = null;
        try {
            $m = json_decode(File::get($dir.'/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
            $sql = File::get($dir.'/database.sql');
            if (empty($m['integrity_verified']) || ! hash_equals($m['sql_sha256'], hash('sha256', $sql)) || preg_match('/^\s*(?:USE\s|CREATE\s+DATABASE|CONNECT\s|SOURCE\s|\\!)/mi', $sql)) {
                throw new \RuntimeException('Backup validation failed');
            }
            $clone = 'sid_restore_'.now()->format('YmdHis').'_'.strtolower(Str::random(8));
            DB::statement('CREATE DATABASE `'.$clone.'` CHARACTER SET utf8mb4');
            $created = $clone;
            // The destination is newly created and empty. Remove mysqldump table-drop lines as an extra safeguard.
            $restoreSql = preg_replace('/^DROP TABLE IF EXISTS[^\r\n]*;\r?\n/m', '', $sql);
            $p = new Process([$binary, '--host='.(string) $c['host'], '--port='.(string) $c['port'], '--user='.(string) $c['username'], '--database='.$clone], base_path(), ['MYSQL_PWD' => (string) $c['password']], $restoreSql, 180);
            $p->run();
            if (! $p->isSuccessful()) {
                throw new \RuntimeException('Restore failed');
            }
            foreach ($m['tables'] as $table => $count) {
                if (! preg_match('/\A[a-zA-Z0-9_]+\z/', $table) || DB::selectOne('SELECT COUNT(*) AS n FROM `'.$clone.'`.`'.$table.'`')->n != $count) {
                    throw new \RuntimeException('Restored count mismatch');
                }
            }
            foreach ($m['media'] as $media) {
                $path = realpath($dir.'/media/'.$media['path']);
                if (! $path || ! str_starts_with($path, $dir.DIRECTORY_SEPARATOR) || ! hash_equals($media['sha256'], hash_file('sha256', $path))) {
                    throw new \RuntimeException('Media mismatch');
                }
            }
            $m['restore_tested'] = true;
            $m['restore_database'] = $clone;
            $m['restore_checked_at'] = now()->toIso8601String();
            File::put($dir.'/manifest.json', json_encode($m, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            $this->info('Restore/count/media checks passed in isolated database '.$clone.'. Retained; current database untouched.');

            return self::SUCCESS;
        } catch (Throwable) {
            $this->error('Restore verification failed; no existing database was replaced or dropped. Isolated evidence retained: '.($created ?? 'none'));

            return self::FAILURE;
        }
    }
}
