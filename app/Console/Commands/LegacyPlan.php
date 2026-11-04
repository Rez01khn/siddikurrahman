<?php

namespace App\Console\Commands;

use App\Rules\SafeWebUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class LegacyPlan extends Command
{
    protected $signature = 'legacy:plan {manifest : Reviewed JSON path under storage/app/private/legacy/sources} {--dry-run : Required; this preparation command never imports}';

    protected $description = 'Validate a legacy staging manifest and detect existing identities without changing records';

    public function handle(): int
    {
        if (! $this->option('dry-run')) {
            $this->error('Only --dry-run is supported. No data was imported.');

            return self::FAILURE;
        }
        $root = realpath(storage_path('app/private/legacy/sources'));
        $path = realpath($this->argument('manifest'));
        if (! $root || ! $path || ! str_starts_with($path, $root.DIRECTORY_SEPARATOR) || ! is_file($path) || filesize($path) > 10 * 1024 * 1024) {
            $this->error('Use a JSON file up to 10 MB inside the private legacy sources directory.');

            return self::FAILURE;
        }
        $issues = [];
        $duplicates = [];
        $seen = [];
        $records = [];
        try {
            $document = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
            $v = Validator::make($document, ['version' => ['required', 'in:1'], 'records' => ['required', 'array', 'min:1', 'max:10000'], 'records.*' => ['required', 'array']]);
            if ($v->fails()) {
                throw new \RuntimeException('No approved records or invalid manifest shape.');
            }$records = $document['records'];
            foreach ($records as $i => $r) {
                $v = Validator::make($r, ['legacy_id' => ['required', 'string', 'max:200'], 'table' => ['required', Rule::in(config('legacy.tables'))], 'source_url' => ['required', 'string', 'max:2048', new SafeWebUrl], 'source_verified' => ['required', 'accepted'], 'reuse_authorized' => ['required', 'accepted'], 'reviewed_by' => ['required', 'string', 'max:120'], 'data' => ['required', 'array']]);
                if ($v->fails()) {
                    $issues[] = ['row' => $i + 1, 'fields' => $v->errors()->keys()];

                    continue;
                }
                $key = $r['table'].'|'.$r['legacy_id'];
                if (isset($seen[$key])) {
                    $issues[] = ['row' => $i + 1, 'error' => 'Duplicate legacy ID in this table'];

                    continue;
                }$seen[$key] = true;
                $columns = Schema::getColumnListing($r['table']);
                if (array_diff(array_keys($r['data']), $columns)) {
                    $issues[] = ['row' => $i + 1, 'error' => 'Unknown target fields'];

                    continue;
                }
                if (! empty($r['data']['is_published']) || isset($r['data']['status']) && $r['data']['status'] === 'published') {
                    $issues[] = ['row' => $i + 1, 'error' => 'Staging must remain draft'];
                }
                $keys = config('legacy.identities.'.$r['table'], []);
                if ($keys && count(array_intersect($keys, array_keys($r['data']))) === count($keys)) {
                    $q = DB::table($r['table']);
                    foreach ($keys as $field) {
                        $q->where($field, $r['data'][$field]);
                    }if ($q->exists()) {
                        $duplicates[] = ['row' => $i + 1, 'table' => $r['table'], 'reason' => 'Existing natural identity; do not overwrite'];
                    }
                }
                if (in_array('source_url', $columns, true) && DB::table($r['table'])->where('source_url', $r['source_url'])->exists()) {
                    $duplicates[] = ['row' => $i + 1, 'table' => $r['table'], 'reason' => 'Existing source URL; review association'];
                }
            }
        } catch (Throwable) {
            $issues[] = ['error' => 'Invalid staging manifest, no approved records or unavailable target schema'];
        }
        $report = ['manifest_sha256' => hash_file('sha256', $path), 'dry_run' => true, 'records_seen' => count($records), 'imported' => 0, 'issues' => $issues, 'existing_matches' => $duplicates, 'apply_available' => false, 'ready_to_import' => false, 'note' => 'Field/date/relationship/media validation and a tested restore are required before implementing an apply command.'];
        File::put(storage_path('app/private/legacy/last-plan.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        $this->line('Records: '.count($records).'; issues: '.count($issues).'; existing matches: '.count($duplicates).'; imported: 0.');
        $this->line('Private report: storage/app/private/legacy/last-plan.json. Apply is intentionally unavailable pending approved source files.');

        return $issues || $duplicates ? self::FAILURE : self::SUCCESS;
    }
}
