# Current readiness update - limited import completed, no production launch

The supplied partial JSON was imported locally as 30 drafts plus an unpublished review category, with all original source information retained in 71 entries and a byte-exact archive. Backup storage/app/private/legacy/backups/20261104-111640-z6mktvZH was actually restored/count-checked in isolated local database sid_restore_20261104111803_q2rhh6zc; original content comparison passed. The supplied source JSON is also backed up. This supersedes the older local-backup restore-pending item below, but does **not** establish complete old-production backup/media availability.

Before any launch: supply missing original media/body/contact/source data; resolve profile conflicts and unsupported published local claims; review and approve drafts, event statuses and rights; rerun real playback/device/publication checks; complete HTTPS/cookie/configuration/CSS/credential readiness. The production site and DNS were not changed. The old full SQL/media export is still unavailable, so full historical completeness is not claimed.

The earlier launch checklist follows for release operations. Treat unchecked full-source, rights, hosting, production backup and approval items as still pending.

---

# Production Launch Checklist - Phase 5 preparation

**NOT APPROVED FOR LAUNCH.** No deployment, DNS changes, upload to the old server, or production overwrite was performed. Import remains blocked by missing owner-verified source files. This is an operator checklist for later approval, not a record of completed production actions. Detailed existing release instructions are retained in PHASE-4.md.

## Sources and editorial approval

- [ ] Obtain a content-only legacy SQL export with schema, stable IDs and relationships; exclude credentials/private inbox data unless explicitly authorized for a secure migration copy.
- [ ] Obtain original uploads/media archive, album/photo/caption/credit/date associations and reuse permission. Public visibility is not proof of permission to reproduce third-party press images or full text.
- [ ] Supply approved video/provider URLs and current contact/social/sponsor/project/equipment/coaching details.
- [ ] Resolve birthplace/date/aggregate/news/schedule conflicts listed in LEGACY-CONTENT-AUDIT.md.
- [ ] Correct or unpublish unsupported published local claims; review draft statistic and existing image association.
- [ ] Import only approved mapped data as drafts, review every record, and record actual totals/failures/duplicates before publication. Do not claim a complete import from a partial link index.

## Hosting and compatibility

- [ ] Use a staging host first, matching the existing tested PHP 8.4 environment, installed Laravel 13.40 dependency lock and compatible MySQL 8.4 environment. Confirm exact production package/platform compatibility using composer install from the lock and composer check-platform-reqs --no-dev; do not upgrade frameworks blindly during content migration.
- [ ] Verify PDO MySQL, fileinfo, OpenSSL, JSON, sessions and image-processing/testing requirements; check all Composer requirements. Ensure sufficient disk for SQL dump, media backup, staged uploads and rollback copy.
- [ ] Document root must be public/. Keep .env, backups, source manifests, vendor, logs and storage/app/private/testing out of the public webroot. The public storage link exposes only storage/app/public.
- [ ] Give the application service account narrowly scoped write access to storage/ and bootstrap/cache; configure ACLs on Windows explicitly. Deny script execution and directory listing in upload locations. Configure upload/body limits for ten 5 MB project images plus the main image and verify readable oversized-request errors.
- [ ] Review production dependencies/security advisories and hosting patch support again before release.

## Environment, HTTPS and delivery

- [ ] Use .env.production.example as a reference, never overwrite existing .env or APP_KEY blindly. Preserve encryption keys needed for existing encrypted data.
- [ ] APP_ENV=production, APP_DEBUG=false, correct HTTPS APP_URL, least-privilege database credentials and persistent session/cache storage. Keep secrets out of commands, screenshots, logs, source control and public reports.
- [ ] HTTPS certificate/renewal, redirect behavior and known trusted proxy/IP settings verified. Secure HTTP-only cookies with appropriate SameSite scope; validate IP rate limiting through the actual proxy.
- [ ] Keep SEO_INDEXABLE=false on staging and until owner approval. Canonical/sitemap origins must match the final host, not localhost or staging.
- [ ] Resolve Tailwind CDN production delivery before release, without changing the approved design. The current application retains CDN per instruction; no Vite/npm/build tools were added. See PHASE-4.md for reviewed static-CSS or separately approved no-Vite generation alternatives. Verify both public and admin styles after any CSS replacement.
- [ ] Rotate local development administrator credentials, retain a second active owner-controlled admin, and verify inactive-account and last-admin protections. Existing passwords were not changed by this task.
- [ ] Review public contact/privacy copy and actual recipient details. Messages currently save to the database/admin inbox. No email notification was implemented; if email is later authorized, configure and test real delivery separately. No queue jobs were added by Phase 5; provision workers only for jobs actually implemented and configured.

## Backups and restore rehearsal

- [x] Local new-site SQL/media integrity backup created: storage/app/private/legacy/backups/20261104-103139-T6wAPW3Q (manifest records hashes, sizes and table counts).
- [ ] Original old-site database/media backup received and integrity-checked.
- [ ] Encrypt/restrict offline/offsite backup copies and agree retention/restore ownership. The local SQL dump contains sensitive application data; do not publish it.
- [ ] Rehearse restoration into a separately provisioned **empty, isolated** database/directory, with correct engine/version and required permissions for routines/triggers/events. Compare all table counts, sample relationships and every media SHA-256. Record the outcome; changing restore_tested in a JSON file is not evidence.
- [ ] No restore into the existing application, legacy production or populated schema is authorized by this checklist. Do not use migrate:fresh, db:wipe or arbitrary cleanup/deletion. Failed backup folders marked FAILED.txt must not be used.

A future authorized restore rehearsal may use the MySQL client against the operator-provisioned empty schema; password is prompted, never embedded:

```powershell
mysql --user=restore_operator --password --database=isolated_empty_restore --execute="SOURCE C:/approved-private-backup/database.sql"
```

This command is an instruction only and was not run. The SQL dump may contain DROP TABLE IF EXISTS statements as part of mysqldump output; that is why the destination must be empty/isolated and explicitly reviewed. Preserve the original backup; restore media into a separate staging directory and compare manifest hashes before any serving/promotion. Do not copy a SQL backup to public/storage.

## Approved release procedure (not executed)

- [ ] Freeze editing briefly, take a fresh verified local/staging and production backup, record release revision and test restore readiness.
- [ ] Approve source mapping/import plan and staged content; create source-specific idempotency ledger/relationship/media handling only after the actual export is available. No generic apply command is supplied under the current source gate.
- [ ] Prepare a separate release directory and shared approved media/configuration, preserving the old production website and rollback release.
- [ ] In the explicitly approved target, install the locked production dependencies and run only reviewed additive migrations/caches:

```powershell
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
composer check-platform-reqs --no-dev
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan site:preflight
```

- [ ] Verify an existing storage link rather than deleting uploads. Do not run DatabaseSeeder or unverified factories/seeders against production; it still contains a development Test User factory and is not a legacy importer.
- [ ] Complete post-import regression/browser/security checks from FINAL-QA-REPORT.md on staging. Inspect logged failures without exposing content/secrets. Production preflight currently fails intentionally on local/CDN configuration.
- [ ] Obtain explicit production deployment approval separately. Current instructions do not authorize promotion or overwriting the old site.

## Domain, DNS and post-launch checks

- [ ] Confirm domain ownership/access, hosting target, final canonical host, certificate coverage and existing DNS records. Preserve MX/SPF/DKIM/DMARC and unrelated records; plan TTL and rollback with the operator.
- [ ] Make DNS changes only with explicit approval and a recorded previous state; no DNS changes were made here.
- [ ] Verify homepage/details/media/contact/admin login/logout over HTTPS, safe publication of only approved records, image files, video playback, contact inbox read status, rate limiting, 404/403 pages and all navigation/internal links.
- [ ] Agree legacy URL redirects and resolve every imported old URL to the correct approved target; routes to unimported or disputed records must not fabricate replacement content.
- [ ] Enable indexing only after approval, re-cache configuration, verify robots/canonical/OG/sitemap and submit the final sitemap through an owner-controlled search console if authorized.
- [ ] Monitor application errors, 404s, contact delivery/inbox review, disk space, backups and certificate renewal. Restrict logs and set a contact-message retention policy.

## Rollback

- [ ] Preserve prior code, database/media backups and key/environment references. Prefer switching code back when schema remains compatible; do not automatically drop imported tables or newly entered content.
- [ ] For a data incident, freeze writes, preserve the failed state and post-backup submissions, obtain explicit recovery approval and restore through a tested, isolated recovery procedure before promotion. Reconcile new records rather than silently discarding them.
- [ ] Restore DNS only to the recorded approved previous target if a DNS rollback is explicitly authorized.
- [ ] Record actual restore/promotion outcomes in CONTENT-MIGRATION-REPORT.md and FINAL-QA-REPORT.md. None of these production actions has been executed.
