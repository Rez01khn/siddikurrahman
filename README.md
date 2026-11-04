# Siddikur Rahman Official Website

Laravel 13 application with a Blade frontend, custom admin dashboard, Tailwind CSS CDN and vanilla JavaScript. Application data uses MySQL. No frontend build is required.

## Local setup

Requirements: PHP compatible with the locked Composer dependencies (tested with PHP 8.4), Composer, MySQL and the required PHP extensions.

```sh
composer install
```

Copy `.env.example` to `.env` if it does not exist. Configure your own local MySQL connection before running migrations:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=
```

Create the database using your local MySQL administration tool. For a new environment only:

```sh
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan admin:create
php artisan serve
```

Do not regenerate an existing application's encryption key. `admin:create` securely prompts for new credentials; there are no published admin passwords. Sign in at `/admin/login`.

Do not use `composer run setup` or `composer run dev`: those retained Laravel starter scripts invoke frontend build tools and are not this website's setup process.

## Content and migration

Public pages and the admin content modules are database driven. Database contents, uploaded media, private source JSON, backups and local browser profiles are deliberately excluded from Git. A clone provides application code, migrations and tests, not a copy of the local website's data.

Restore approved database/media backups separately into an isolated environment. Preserve source attribution, publication states and media permissions. The private legacy import/review workflow is described in [PHASE-5.md](PHASE-5.md) and [CONTENT-MIGRATION-REPORT.md](CONTENT-MIGRATION-REPORT.md). Missing legacy source data must not be fabricated or automatically published.

## Verification

In a local test environment, without production cached configuration:

```sh
php artisan config:clear
php artisan test --compact
php artisan view:cache
php artisan site:preflight
```

The test suite uses isolated SQLite in memory and fake storage. Never run database-resetting tests against production. The most recent full suite passed 752 tests with 2,154 assertions before repository preparation.

`site:preflight` is read-only and intentionally reports development/CDN settings as release blockers. Tailwind CDN is retained for initial development. Production CSS, HTTPS/cookie configuration, credential rotation, content verification and backup readiness remain required before release.

## Documentation

- [Development report](FINAL-DEVELOPMENT-REPORT.md)
- [Legacy content audit](LEGACY-CONTENT-AUDIT.md)
- [Content migration report](CONTENT-MIGRATION-REPORT.md)
- [QA report](FINAL-QA-REPORT.md)
- [Launch checklist](PRODUCTION-LAUNCH-CHECKLIST.md)

Uploading this repository is not a production deployment. Keep `.env`, credentials, private exports, database dumps and user uploads out of Git.
