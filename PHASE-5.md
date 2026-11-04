# Phase 5 - Supplied partial-data import checkpoint

## Current status

The user supplied a partial public-page JSON and explicitly requested applying its data. A limited draft import is now complete locally, with lossless source archival and an admin review screen. This replaces the earlier no-source/zero-import checkpoint; complete historical migration and production launch are still not complete.

Actual outcome: 30 new draft content records plus 1 unpublished review category; 71 source entries with the entire original JSON; 0 original source entries missing; 20 previous content rows compared, 0 changed. Existing 2013 achievement linked unchanged; repeated imports add 0 duplicates. No missing image/video/title/body was invented and no new content was published automatically.

## Backup, migration and verification

Migration executed: database/migrations/2026_11_04_111228_create_legacy_import_batches_and_entries_tables.php. It adds two import-ledger tables and nullable tournament event-purse fields without changing existing rows.

A fresh private SQL/media backup was created and actually restored into a newly created isolated local database. Pre-import backup: storage/app/private/legacy/backups/20261104-111640-z6mktvZH. Restore clone: sid_restore_20261104111803_q2rhh6zc (retained, not dropped). Thirty table counts and media hashes passed. The original JSON also has a hash-checked backup copy. No production connection, DNS, old website overwrite or destructive database operation was executed.

Read CONTENT-MIGRATION-REPORT.md for exact mapping, counts, original SHA-256, backup evidence, review workflow and reproducible commands. LEGACY-CONTENT-AUDIT.md retains the earlier 54-observation web census separately; it is not added to the 71-entry supplied-file ledger as a unique historical total.

## Tests and browser checks

Final full suite: 752 passing tests / 2,154 assertions. Sixteen new PublicLegacyImportTest cases cover lossless preservation, idempotency/owner edits, invalid dates, transaction rollback, changed-revision protection, guest/ordinary-user denial, escaped originals/download, draft visibility, backup/apply guards, unconfirmed publication controls, event purse/player-result separation, MySQL JSON object order and missing source entries. Existing regressions remain included.

Actual MySQL verification: original archive hash passed, all 71 source entries and targets present, 0 value mismatches, 20 previous content rows unchanged. This certifies file preservation/import behavior, not every historical claim.

Headless Chrome checked the new admin batch list, batch review and tournament editor at 375/768/1280/1440 px (12 combinations). No measured document overflow, missing images or runtime exceptions. Mobile review and desktop list screenshots were visually inspected. Provider playback, missing image associations and complete historical publication remain pending owner-supplied media and editorial review.

## Files

New:
- app/Services/PublicLegacyImporter.php
- app/Models/LegacyImportBatch.php
- app/Models/LegacyImportEntry.php
- app/Console/Commands/ImportPublicLegacy.php
- app/Console/Commands/LegacyRestoreCheck.php
- app/Console/Commands/VerifyPublicLegacy.php
- app/Http/Controllers/Admin/LegacyImportController.php
- database/migrations/2026_11_04_111228_create_legacy_import_batches_and_entries_tables.php
- resources/views/admin/legacy/index.blade.php
- resources/views/admin/legacy/show.blade.php
- resources/views/components/tournaments/purse.blade.php
- tests/Feature/PublicLegacyImportTest.php

Modified:
- app/Models/Tournament.php
- app/Http/Requests/Admin/SaveTournamentRequest.php
- app/Http/Controllers/Admin/TournamentController.php
- app/Http/Controllers/Admin/ModuleController.php
- config/admin.php
- config/tournaments.php
- routes/web.php
- resources/views/components/admin/sidebar.blade.php
- resources/views/admin/tournaments/form.blade.php
- resources/views/admin/tournaments/show.blade.php
- resources/views/tournaments/show.blade.php
- The five requested Phase 5 reports and FINAL-DEVELOPMENT-REPORT.md

Private artifacts: supplied staging JSON, hash-named byte-exact archive, SQL/media/source backup and manifest, last-public-verification.json, current audit snapshot, earlier report copies, browser runner/screenshots/report under storage/app/testing. These are not public assets or deployment files. No packages/build tools introduced; existing authentication and frontend design preserved. The already installed Boost guideline prerequisite refreshed CLAUDE.md.

## Resume

Open /admin/legacy-imports to review every source entry and mapped draft. Supply missing photo originals, 14 video URLs, complete article/coaching bodies, verified social/sponsor/equipment/contact details and profile conflict resolutions. Review facts in existing module editors before publishing. A complete old-site SQL/media archive is still needed before claiming full historical migration. Do not republish unsupported existing local claims or deploy until the launch checklist is satisfied.
