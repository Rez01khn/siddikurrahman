<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ProductionCheck extends Command
{
    protected $signature = 'site:preflight';

    protected $description = 'Read-only checks for production preparation; never changes environment, credentials or data';

    public function handle(): int
    {
        $databaseReady = false;
        $administrator = false;
        try {
            $databaseReady = collect(['users', 'homepage_sections', 'biographies', 'tournaments', 'articles', 'photo_albums', 'videos', 'sponsors', 'social_works', 'equipment', 'coaching_tips', 'contact_messages', 'website_settings'])->every(fn ($table) => Schema::hasTable($table));
            $administrator = Schema::hasColumn('users', 'is_active') && User::where('is_admin', true)->where('is_active', true)->exists();
        } catch (Throwable) {/* Report a generic check result, never connection secrets. */
        }
        $productionCss = collect(['site', 'admin-base'])->every(
            fn ($layout) => ! str_contains(file_get_contents(resource_path('views/layouts/'.$layout.'.blade.php')), 'cdn.tailwindcss.com')
        );
        $checks = [
            'Production environment' => app()->isProduction(),
            'Debug disabled' => ! config('app.debug'),
            'Encryption key configured' => (bool) config('app.key'),
            'HTTPS application URL' => filter_var(config('app.url'), FILTER_VALIDATE_URL)
                && parse_url(config('app.url'), PHP_URL_SCHEME) === 'https',
            'MySQL connection configured' => config('database.default') === 'mysql',
            'Secure HTTP-only session cookie' => config('session.secure') && config('session.http_only'),
            'Required database tables' => $databaseReady,
            'Active administrator exists' => $administrator,
            'Public storage link resolves correctly' => realpath(public_path('storage')) === realpath(storage_path('app/public'))
                && is_dir(public_path('storage')),
            'Writable storage and cache' => is_writable(storage_path()) && is_writable(base_path('bootstrap/cache')),
            'Production CSS supplied' => $productionCss,
        ];
        foreach ($checks as $label => $ok) {
            $this->line(($ok ? 'PASS' : 'FAIL').' '.$label);
        }
        $this->warn('Review owner-approved content, media rights, administrator password strength, backups, trusted proxies and CSS/font delivery before release. No deployment was performed.');

        return in_array(false, array_map(fn ($v) => (bool) $v, $checks), true) ? self::FAILURE : self::SUCCESS;
    }
}
