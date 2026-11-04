<?php

namespace App\Console\Commands;

use App\Models\LegacyImportBatch;
use App\Services\PublicLegacyImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

class ImportPublicLegacy extends Command
{
    protected $signature = 'legacy:import-public {source} {--apply} {--backup= : Verified and restore-tested private backup directory}';

    protected $description = 'Validate or import the supplied partial extraction as drafts with a lossless review ledger';

    public function handle(PublicLegacyImporter $importer): int
    {
        $root = realpath(storage_path('app/private/legacy/sources'));
        $path = realpath($this->argument('source'));
        if (! $root || ! $path || ! str_starts_with($path, $root.DIRECTORY_SEPARATOR) || ! is_file($path)) {
            $this->error('Use the supplied JSON staged inside private legacy/sources.');

            return self::FAILURE;
        }
        try {
            $d = $importer->read($path);
            if (! $this->option('apply')) {
                $this->info('Dry run: valid partial extraction; no database writes, downloads or publication.');

                return self::SUCCESS;
            }
            if (app()->isProduction() || DB::connection()->getDriverName() !== 'mysql') {
                throw new \RuntimeException('Only local MySQL apply is allowed');
            }
            $backupRoot = realpath(storage_path('app/private/legacy/backups'));
            $dir = realpath((string) $this->option('backup'));
            if (! $dir || ! $backupRoot || ! str_starts_with($dir, $backupRoot.DIRECTORY_SEPARATOR)) {
                throw new \RuntimeException('Private backup required');
            }
            $m = json_decode(File::get($dir.'/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
            if (empty($m['integrity_verified']) || empty($m['restore_tested']) || ! hash_equals($m['sql_sha256'], hash_file('sha256', $dir.'/database.sql'))) {
                throw new \RuntimeException('Restore-tested intact backup required');
            }
            // Compare the source database before first import; repeat imports are idempotent and do not change existing records.
            $hash = hash_file('sha256', $path);
            if (! LegacyImportBatch::where('source_sha256', $hash)->exists()) {
                foreach ($m['tables'] as $table => $count) {
                    if (! preg_match('/\A[a-zA-Z0-9_]+\z/', $table) || DB::table($table)->count() != $count) {
                        throw new \RuntimeException('Local row counts changed; take a fresh backup');
                    }
                }
            }
            foreach ($m['media'] as $media) {
                $mediaPath = realpath($dir.'/media/'.$media['path']);
                if (! $mediaPath || ! str_starts_with($mediaPath, $dir.DIRECTORY_SEPARATOR) || ! hash_equals($media['sha256'], hash_file('sha256', $mediaPath))) {
                    throw new \RuntimeException('Backup media mismatch');
                }
            }
            $sourceBackup = $dir.'/source-json/'.$hash.'.json';
            File::ensureDirectoryExists(dirname($sourceBackup));
            if (! is_file($sourceBackup)) {
                File::copy($path, $sourceBackup);
            }
            if (! hash_equals($hash, hash_file('sha256', $sourceBackup))) {
                throw new \RuntimeException('Source backup mismatch');
            }
            $m['source_archive'] = ['sha256' => $hash, 'file' => 'source-json/'.$hash.'.json'];
            File::put($dir.'/manifest.json', json_encode($m, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            $alreadyImported = LegacyImportBatch::where('source_sha256', $hash)->exists();
            $b = $importer->apply($path);
            if ($alreadyImported) {
                $this->info('Source already imported: 0 additional records; existing edits preserved.');

                return self::SUCCESS;
            }$this->info('Batch '.$b->id.' archived/imported safely. Existing records preserved; new content remains draft.');
            $this->line(json_encode($b->summary, JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        } catch (Throwable $e) {
            File::ensureDirectoryExists(storage_path('app/private/legacy'));
            File::append(storage_path('app/private/legacy/import-failures.jsonl'), json_encode(['time' => now()->toIso8601String(), 'source_sha256' => is_file($path) ? hash_file('sha256', $path) : null, 'exception_type' => get_class($e), 'state' => 'apply refused or transaction rolled back'])."\n");
            $this->error('Import refused or rolled back. Check source validation and intact restore-tested backup; private failure journal contains no credentials.');

            return self::FAILURE;
        }
    }
}
