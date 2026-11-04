<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class LegacyAudit extends Command
{
    protected $signature = 'legacy:audit';

    protected $description = 'Read-only content counts, duplicate identities and media inventory; writes private JSON only';

    public function handle(): int
    {
        $report = ['generated_at' => now()->toIso8601String(), 'tables' => [], 'duplicates' => [], 'records' => []];
        foreach (config('legacy.tables') as $table) {
            if (! Schema::hasTable($table)) {
                $report['tables'][$table] = ['exists' => false];

                continue;
            }
            $columns = Schema::getColumnListing($table);
            $report['tables'][$table] = ['exists' => true, 'count' => DB::table($table)->count(), 'columns' => $columns];
            if (in_array('is_published', $columns, true)) {
                $report['tables'][$table]['published'] = DB::table($table)->where('is_published', true)->count();
            }
            if ($table === 'articles') {
                $report['tables'][$table]['published'] = DB::table($table)->where('status', 'published')->count();
            }
            foreach (array_filter([config('legacy.identities.'.$table), in_array('source_url', $columns, true) ? ['source_url'] : null]) as $keys) {
                $q = DB::table($table)->select($keys)->selectRaw('COUNT(*) as duplicate_count');
                foreach ($keys as $key) {
                    $q->whereNotNull($key)->where($key, '!=', '');
                }
                foreach ($q->groupBy($keys)->havingRaw('COUNT(*) > 1')->get() as $group) {
                    $report['duplicates'][] = ['table' => $table, 'keys' => $keys, 'group' => $group];
                }
            }
            if (in_array($table, ['biographies', 'achievements', 'career_statistics', 'career_milestones', 'tournaments', 'articles'], true)) {
                $fields = array_values(array_intersect($columns, ['id', 'full_name', 'title', 'name', 'year', 'event_name', 'category', 'source_note', 'source_url', 'date_of_birth', 'birthplace', 'nationality', 'professional_start_year', 'is_published', 'status', 'image', 'profile_image', 'banner_image', 'label', 'value']));
                $report['records'][$table] = DB::table($table)->select($fields)->orderBy('id')->get()->all();
            }
        }
        $root = storage_path('app/public');
        $files = [];
        foreach (File::allFiles($root) as $file) {
            $files[] = ['path' => $file->getRelativePathname(), 'bytes' => $file->getSize(), 'sha256' => hash_file('sha256', $file->getPathname())];
        }$report['public_media'] = $files;
        File::ensureDirectoryExists(storage_path('app/private/legacy'));
        File::put(storage_path('app/private/legacy/current-audit.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        foreach ($report['tables'] as $table => $data) {
            $this->line($table.': '.($data['count'] ?? 'missing'));
        }$this->line('Duplicate groups: '.count($report['duplicates']));
        $this->line('Public-disk files: '.count($files));
        $this->info('Private report: storage/app/private/legacy/current-audit.json; database unchanged.');

        return self::SUCCESS;
    }
}
