<?php

namespace App\Console\Commands;

use App\Models\LegacyImportBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

class VerifyPublicLegacy extends Command
{
    protected $signature = 'legacy:verify-public {source} {--backup= : Restore-tested private backup directory}';

    protected $description = 'Verify byte-exact archive, every source entry, target existence and preservation of previous content; not historical fact certification';

    public function handle(): int
    {
        try {
            $root = realpath(storage_path('app/private/legacy/sources'));
            $path = realpath($this->argument('source'));
            if (! $root || ! $path || ! str_starts_with($path, $root.DIRECTORY_SEPARATOR)) {
                throw new RuntimeException;
            }
            $hash = hash_file('sha256', $path);
            $source = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
            $batch = LegacyImportBatch::where('source_sha256', $hash)->firstOrFail();
            $archive = storage_path('app/private/legacy/sources/'.$hash.'.json');
            if (! is_file($archive) || ! hash_equals($hash, hash_file('sha256', $archive))) {
                throw new RuntimeException;
            }
            $entries = $batch->entries()->get();
            $expected = ['/', '/metadata', '/source_pages', '/profile_as_displayed', '/biography'];
            foreach (['asian_tour_victories', 'other_wins_listed', 'asian_development_tour_victories', 'historical_highlights'] as $group) {
                foreach (($source['biography'][$group] ?? []) as $i => $value) {
                    $expected[] = '/biography/'.$group.'/'.$i;
                }
            }
            foreach (['tournaments', 'news_index', 'photo_gallery_index', 'video_gallery_index', 'coaching_tips_homepage_titles'] as $group) {
                foreach (($source[$group] ?? []) as $i => $value) {
                    $expected[] = '/'.$group.'/'.$i;
                }
            }
            foreach (['social_works', 'sponsors', 'in_the_bag'] as $group) {
                if (array_key_exists($group, $source)) {
                    $expected[] = '/'.$group;
                }
            }
            $missingEntries = array_values(array_diff($expected, $entries->pluck('source_key')->all()));
            $entryMismatch = [];
            $missingTargets = [];
            foreach ($entries as $entry) {
                $original = $source;
                if ($entry->source_key !== '/') {
                    foreach (explode('/', ltrim($entry->source_key, '/')) as $key) {
                        if (! is_array($original) || ! array_key_exists($key, $original)) {
                            throw new RuntimeException;
                        }$original = $original[$key];
                    }
                }if ($this->fingerprint($entry->raw_data['original']) !== $this->fingerprint($original)) {
                    $entryMismatch[] = $entry->source_key;
                }
                if ($entry->target_id && (! in_array($entry->target_table, config('legacy.tables'), true) || ! DB::table($entry->target_table)->where('id', $entry->target_id)->exists())) {
                    $missingTargets[] = $entry->source_key;
                }
            }
            if ($this->fingerprint($entries->firstWhere('source_key', '/')?->raw_data['original']) !== $this->fingerprint($source)) {
                throw new RuntimeException;
            }
            $compared = null;
            $changed = [];
            $dir = $this->option('backup') ? realpath((string) $this->option('backup')) : false;
            $backupRoot = realpath(storage_path('app/private/legacy/backups'));
            if ($dir) {
                if (! $backupRoot || ! str_starts_with($dir, $backupRoot.DIRECTORY_SEPARATOR)) {
                    throw new RuntimeException;
                }$m = json_decode(File::get($dir.'/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
                $clone = $m['restore_database'] ?? '';
                if (empty($m['restore_tested']) || ! preg_match('/\Asid_restore_[0-9]{14}_[a-z0-9]{8}\z/', $clone)) {
                    throw new RuntimeException;
                }$compared = 0;
                foreach (config('legacy.tables') as $table) {
                    if (! array_key_exists($table, $m['tables'])) {
                        continue;
                    }foreach (DB::select('SELECT * FROM `'.$clone.'`.`'.$table.'`') as $old) {
                        $now = DB::table($table)->where('id', $old->id)->first();
                        $compared++;
                        if (! $now || (array) $old !== (array) $now) {
                            $changed[] = ['table' => $table, 'id' => $old->id];
                        }
                    }
                }
            }
            $report = ['source_sha256' => $hash, 'byte_exact_archive' => true, 'root_document_lossless' => true, 'source_entries' => $entries->count(), 'entry_mismatches' => $entryMismatch, 'missing_source_entries' => $missingEntries, 'missing_targets' => $missingTargets, 'previous_content_rows_compared' => $compared, 'previous_content_rows_changed' => $changed, 'historical_claims_certified' => false, 'new_media_downloaded' => 0];
            File::put(storage_path('app/private/legacy/last-public-verification.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            $this->line(json_encode($report, JSON_UNESCAPED_SLASHES));

            return $entryMismatch || $missingEntries || $missingTargets || $changed ? self::FAILURE : self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Verification failed ('.get_class($e).' at line '.$e->getLine().'); no records changed.');

            return self::FAILURE;
        }
    }

    private function fingerprint(mixed $value): string
    {
        $normalize = function (mixed $node) use (&$normalize): mixed {
            if (is_array($node)) {
                if (! array_is_list($node)) {
                    ksort($node);
                }foreach ($node as $key => $child) {
                    $node[$key] = $normalize($child);
                }
            }

            return $node;
        };

        return hash('sha256', json_encode($normalize($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR));
    }
}
