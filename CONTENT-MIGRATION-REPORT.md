# Content Migration Report - Supplied Partial JSON Applied

## Current result

The user supplied siddikurrahman_public_data_partial.json and requested applying its data without losing original information. A **limited, source-preserving draft import** has now been executed locally. This supersedes the earlier zero-import checkpoint, which is preserved privately under storage/app/private/legacy/reports-before-partial-import/.

This file explicitly describes itself as partial. Its complete bytes and decoded values are preserved; this is not a claim that the entire historical website or missing media has been migrated or independently verified.

| Source area | Actual handling |
| --- | --- |
| Full original JSON | Byte-exact private archive, SHA-256 checked; full decoded document in ledger root |
| Metadata/source pages/unknown extra fields | Preserved in full; attached text is data, not executable instructions |
| Profile fields and biography summary | Preserved for review; current published biography was not overwritten, birthplace conflict unresolved |
| Listed wins | 8 new draft achievements; existing 2013 championship linked unchanged |
| Historical highlights | 3 new draft milestones; 2 victory references linked to achievements rather than duplicated |
| Tournament schedule | 5 drafts with original names, venue/country/dates and event purse/currency; status Needs verification |
| Player tournament results | 0 created; no scores, rankings, player winnings or participation invented |
| News index | 10 draft articles in one new unpublished administrative review category; no body, author or true publication date inferred |
| News listing dates/publishers/null article URLs | Kept exactly in source entries; index dates are not asserted as publication dates and publishers are not treated as authors |
| Photo captions | All 16 occurrences retained, including repeated captions; no fake image path/album/photo created |
| Video index | 1 draft video with the supplied validated YouTube embed URL; 14 titles retained for review because URLs are null |
| Social Works | Original HeForShe caption, repeated-item count and missing-image flag retained; not converted into six fabricated projects |
| Sponsors and equipment | Original incomplete metadata retained; no invented names/logos/brands |
| Coaching titles | 3 title-only drafts; no coaching body or advice invented |

**Totals:** 30 new draft content records, 1 additional unpublished review category, 3 linked references, 37 review entries and 1 archived root = **71 source ledger entries**. The 37 review entries include metadata/grouped information; they are not 37 unique missing historical records. All newly imported content is unpublished. Existing published/draft states remain unchanged.

## Original-data verification

Source SHA-256: 1c16dd20d9c67029ab6f8d9da899e884643dd28d36ee97b23eaaa995975a968e.

Executed legacy:verify-public against the real local MySQL database:

- Byte-exact original archive: passed.
- Full source document values/types and array order: passed.
- Expected source entries: 71; missing source entries: 0.
- Source-entry value mismatches: 0; missing mapped targets: 0.
- Prior content rows compared with the restored pre-import backup: 20; changed rows: 0.
- Repeated import: 0 additional records; later owner edits are not overwritten.
- Remote media downloaded: 0. Historical facts certified automatically: no.

MySQL may reorder JSON object keys. The verifier compares typed canonical objects while preserving list order; the separate original archive remains byte-for-byte unchanged. A known mojibake dash in one event name was repaired only in its editable display text; the original spelling/bytes remain in the source ledger and download.

## Backup and recovery evidence

Pre-import backup: storage/app/private/legacy/backups/20261104-111640-z6mktvZH.

The backup includes 30 table definitions, stable local row counts, one existing JPEG, SQL/media SHA-256 checks and an additional byte-exact source JSON copy under source-json/. A restore rehearsal was actually executed into the newly created isolated local database sid_restore_20261104111803_q2rhh6zc. Table counts and backed-up media hashes passed. The clone is retained as evidence; no existing database was replaced or dropped. The restoration runner removes mysqldump table-drop lines and refuses production/remote hosts or a backup attempting to change databases. Existing content was then compared with that restored clone after import.

An additive migration creates legacy_import_batches and legacy_import_entries and adds nullable tournament purse/currency fields. Existing records were not deleted. All target/ledger writes share a transaction; a simulated mid-import failure test confirms rollback. Raw source archives are retained on failure. Console failure logs store hashes/exception types, not SQL, credentials or raw secrets.

## Review in the existing website

Sign in and open **Legacy Content Review** in the admin sidebar, or /admin/legacy-imports. Batch 1 contains the 71 source entries. Filter by section/state, inspect original values, download the unchanged original file or open mapped records in their existing editors. The review page and original-file download require an active administrator.

The public frontend is intentionally not filled with unverified claims, fake article bodies, fake photographs or guessed video links. Publish only after review. Tournaments imported with unconfirmed status cannot be published through either the edit form or quick toggle until their status is confirmed. Event purse is stored/displayed separately from player result prize money.

## What remains unavailable

Original photo binaries/URLs and album associations, 14 video URLs, full news bodies/images and confirmed article publication dates, complete biography/conflict resolution, sponsor identities/logos, equipment details, social project dates/descriptions/images, coaching bodies and approved current contact/social details. Complete legacy SQL/media archives remain needed for an exhaustive historical migration. Existing unsupported local claims identified in the earlier audit remain unchanged and need owner review before launch.

No data from the supplied JSON was silently discarded. Information that cannot safely populate a required model field is retained in the review ledger and original download. New source revisions with a different hash are held for manual comparison; the importer never blindly replaces a previously imported dataset.

## Reproducible commands

The following were run locally; migrations and the import are already applied:

```powershell
php artisan migrate
php artisan legacy:import-public storage/app/private/legacy/sources/siddikurrahman_public_data_partial.json
php artisan legacy:backup --mysqldump=C:/laragon/bin/mysql/mysql-8.4.6-winx64/bin/mysqldump.exe
php artisan legacy:restore-check storage/app/private/legacy/backups/20261104-111640-z6mktvZH --mysql=C:/laragon/bin/mysql/mysql-8.4.6-winx64/bin/mysql.exe
php artisan legacy:import-public storage/app/private/legacy/sources/siddikurrahman_public_data_partial.json --apply --backup=storage/app/private/legacy/backups/20261104-111640-z6mktvZH
php artisan legacy:verify-public storage/app/private/legacy/sources/siddikurrahman_public_data_partial.json --backup=storage/app/private/legacy/backups/20261104-111640-z6mktvZH
```

The import defaults to dry run. Apply requires an intact, restore-tested private backup and a local MySQL environment. Existing dataset hashes return without additional writes. A first apply refuses changed pre-import row counts. Source path is restricted to the private staging directory. No media is fetched automatically.

After a failed batch, the transaction rolls back target/ledger inserts; keep the original archive and inspect the private failure journal before retrying. After a successful import, do not blindly restore an old database or delete linked/shared records: use the ledger and isolated restored clone for comparison, keep new records unpublished, and obtain explicit approval for any destructive recovery. Never drop the original database or overwrite the old production site.
