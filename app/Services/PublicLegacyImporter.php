<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Article;
use App\Models\CareerMilestone;
use App\Models\CoachingTip;
use App\Models\LegacyImportBatch;
use App\Models\NewsCategory;
use App\Models\Tournament;
use App\Models\Video;
use App\Rules\SafeWebUrl;
use App\Support\VideoUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;

class PublicLegacyImporter
{
    public function read(string $path): array
    {
        if (! is_file($path) || filesize($path) > 10 * 1024 * 1024) {
            throw new RuntimeException('Source missing or over 10 MB.');
        }
        $d = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        Validator::make($d, [
            'metadata' => ['required', 'array'], 'source_pages' => ['required', 'array'], 'source_pages.*' => ['required', 'string', 'max:2048', new SafeWebUrl],
            'profile_as_displayed' => ['required', 'array'], 'biography' => ['required', 'array'], 'biography.summary' => ['required', 'string', 'max:50000'],
            'biography.asian_tour_victories' => ['present', 'array', 'max:100'], 'biography.asian_tour_victories.*.event' => ['required', 'string', 'max:230'], 'biography.asian_tour_victories.*.year' => ['required', 'integer', 'between:1900,2100'],
            'biography.other_wins_listed' => ['present', 'array', 'max:100'], 'biography.other_wins_listed.*' => ['required', 'string', 'max:250'], 'biography.asian_development_tour_victories' => ['present', 'array', 'max:100'], 'biography.asian_development_tour_victories.*' => ['required', 'string', 'max:250'], 'biography.historical_highlights' => ['present', 'array', 'max:100'], 'biography.historical_highlights.*' => ['required', 'string', 'max:250'],
            'tournaments' => ['present', 'array', 'max:500'], 'tournaments.*.name' => ['required', 'string', 'max:255'], 'tournaments.*.venue' => ['nullable', 'string', 'max:255'], 'tournaments.*.country' => ['nullable', 'string', 'max:255'], 'tournaments.*.start_date' => ['required', 'date_format:Y-m-d'], 'tournaments.*.end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:tournaments.*.start_date'], 'tournaments.*.source_url' => ['required', 'string', 'max:2048', new SafeWebUrl], 'tournaments.*.tournament_purse_amount' => ['nullable', 'regex:/\A\d{1,14}(?:\.\d{1,2})?\z/'], 'tournaments.*.tournament_purse_currency' => ['nullable', 'regex:/\A[A-Z]{3}\z/'],
            'news_index' => ['present', 'array', 'max:1000'], 'news_index.*.title' => ['required', 'string', 'max:255'], 'news_index.*.date' => ['nullable', 'date_format:Y-m-d'], 'news_index.*.source_listing_url' => ['required', 'string', 'max:2048', new SafeWebUrl], 'news_index.*.article_url' => ['nullable', 'string', 'max:2048', new SafeWebUrl],
            'photo_gallery_index' => ['present', 'array', 'max:1000'], 'photo_gallery_index.*.caption' => ['required', 'string', 'max:2000'],
            'video_gallery_index' => ['present', 'array', 'max:1000'], 'video_gallery_index.*.title_as_displayed' => ['required', 'string', 'max:255'], 'video_gallery_index.*.embed_url' => ['nullable', 'string', 'max:2048', new SafeWebUrl],
            'coaching_tips_homepage_titles' => ['present', 'array', 'max:100'], 'coaching_tips_homepage_titles.*' => ['required', 'string', 'max:255'],
        ])->validate();

        return $d;
    }

    public function apply(string $path): LegacyImportBatch
    {
        $d = $this->read($path);
        $hash = hash_file('sha256', $path);
        $key = 'siddikurrahman-public-partial';
        if ($b = LegacyImportBatch::where('source_sha256', $hash)->first()) {
            return $b;
        }
        if (LegacyImportBatch::where('dataset_key', $key)->exists()) {
            throw new RuntimeException('A changed source revision needs manual comparison; existing imports were preserved.');
        }
        $relative = 'legacy/sources/'.$hash.'.json';
        $archive = storage_path('app/private/'.$relative);
        File::ensureDirectoryExists(dirname($archive));
        if (! is_file($archive)) {
            File::copy($path, $archive);
        }if (! hash_equals($hash, hash_file('sha256', $archive))) {
            throw new RuntimeException('Archive hash mismatch');
        }

        return DB::transaction(function () use ($d, $hash, $key, $relative, $path): LegacyImportBatch {
            $b = LegacyImportBatch::create(['dataset_key' => $key, 'source_sha256' => $hash, 'original_filename' => basename($path), 'archive_path' => $relative]);
            $counts = ['created' => 0, 'linked' => 0, 'review' => 0, 'archived' => 0];
            $entry = function (string $pointer, string $section, $raw, ?Model $model = null, string $note = '', bool $created = false) use ($b, &$counts): void {
                $state = $model ? ($created ? 'draft' : 'linked') : ($pointer === '/' ? 'archived' : 'review');
                $counts[$model ? ($created ? 'created' : 'linked') : ($pointer === '/' ? 'archived' : 'review')]++;
                $b->entries()->create(['source_key' => $pointer, 'section' => $section, 'state' => $state, 'raw_data' => ['original' => $raw], 'target_table' => $model?->getTable(), 'target_id' => $model?->getKey(), 'review_note' => $note]);
            };
            $entry('/', 'source_document', $d, null, 'Lossless complete input, including unknown fields and metadata.');
            $entry('/metadata', 'metadata', $d['metadata']);
            $entry('/source_pages', 'sources', $d['source_pages']);
            $entry('/profile_as_displayed', 'biography', $d['profile_as_displayed'], null, 'Profile merge held: existing published identity is preserved; birthplace conflicts with another legacy page. No current age/weight/earnings inferred.');
            $entry('/biography', 'biography', $d['biography'], null, 'Provided summary is not a full biography. Preserve it and all profile fields for review; do not overwrite the current published biography.');
            $source = $d['source_pages']['biography'];
            $note = 'Supplied partial legacy extraction: '.$source.'. Review pending; not independently verified.';
            foreach (['asian_tour_victories', 'other_wins_listed', 'asian_development_tour_victories'] as $group) {
                foreach ($d['biography'][$group] as $i => $raw) {
                    if (is_array($raw)) {
                        $year = (int) $raw['year'];
                        $event = $raw['event'];
                    } elseif (preg_match('/\A(\d{4})\s+(.+)\z/u', $raw, $m)) {
                        $year = (int) $m[1];
                        $event = $m[2];
                    } else {
                        $entry('/biography/'.$group.'/'.$i, 'achievements', $raw, null, 'Could not determine year/event without guessing.');

                        continue;
                    }
                    $event = str_replace('â€“', '–', $event);
                    $existing = Achievement::where('year', $year)->where(fn ($q) => $q->where('event_name', $event)->orWhere('title', $event)->orWhere('title', $event.' champion'))->first();
                    $created = ! $existing;
                    $r = $existing ?? Achievement::create(['title' => $event, 'year' => $year, 'event_name' => $event, 'category' => $group === 'other_wins_listed' ? 'career_highlight' : 'tour_victory', 'description' => is_string($raw) ? str_replace('â€“', '–', $raw) : $year.' '.$event, 'source_note' => $note, 'is_published' => false, 'is_featured' => false, 'sort_order' => 100 + $i]);
                    $entry('/biography/'.$group.'/'.$i, 'achievements', $raw, $r, $created ? 'Draft legacy claim; raw spelling retained, known encoding artefact repaired only in display text.' : 'Existing achievement linked without changing text, publication or images.', $created);
                }
            }
            foreach ($d['biography']['historical_highlights'] as $i => $raw) {
                $year = preg_match('/\A(\d{4}) /', $raw, $m) ? (int) $m[1] : null;
                if (in_array($raw, ['2013 Hero Indian Open victory', '2010 Brunei Open victory'], true)) {
                    $event = $year === 2013 ? 'Hero Indian Open' : 'Brunei Open';
                    $r = Achievement::where('year', $year)->where('event_name', $event)->first();
                    $entry('/biography/historical_highlights/'.$i, 'milestones', $raw, $r, 'Same victory linked to its achievement; no duplicate milestone.');

                    continue;
                }
                $r = CareerMilestone::firstOrNew(['title' => $raw, 'year' => $year]);
                $new = ! $r->exists;
                if ($new) {
                    $r->fill(['description' => $raw, 'source_note' => $note, 'is_published' => false, 'sort_order' => 100 + $i])->save();
                }$entry('/biography/historical_highlights/'.$i, 'milestones', $raw, $r, 'Historical claim saved for review.', $new);
            }
            foreach ($d['tournaments'] as $i => $raw) {
                $r = Tournament::firstOrNew(['name' => $raw['name'], 'start_date' => $raw['start_date']]);
                $new = ! $r->exists;
                if ($new) {
                    $r->fill(array_intersect_key($raw, array_flip(['venue', 'country', 'end_date', 'source_url', 'tournament_purse_amount', 'tournament_purse_currency'])) + ['status' => 'unconfirmed', 'is_published' => false, 'sort_order' => 100 + $i])->save();
                }$entry('/tournaments/'.$i, 'tournaments', $raw, $r, 'Purse is event prize fund, never player winnings. Event status, participation and result remain unconfirmed.', $new);
            }
            $category = NewsCategory::firstOrCreate(['slug' => 'legacy-archive-review'], ['name' => 'Legacy archive review', 'is_published' => false, 'sort_order' => 100]);
            foreach ($d['news_index'] as $i => $raw) {
                $slug = 'legacy-'.Str::slug($raw['title']).'-'.($raw['date'] ?? 'undated');
                $r = Article::firstOrNew(['slug' => $slug]);
                $new = ! $r->exists;
                if ($new) {
                    $r->fill(['title' => $raw['title'], 'news_category_id' => $category->id, 'source_url' => $raw['article_url'] ?? $raw['source_listing_url'], 'status' => 'draft', 'sort_order' => 100 + $i])->save();
                }$entry('/news_index/'.$i, 'news', $raw, $r, 'Index date retained here, not asserted as publication date; publisher is not assumed author. Body and image unavailable.', $new);
            }
            foreach ($d['photo_gallery_index'] as $i => $raw) {
                $entry('/photo_gallery_index/'.$i, 'photos', $raw, null, 'Caption occurrence preserved separately, including duplicates. No image binary/URL; no fake photo record created.');
            }
            foreach ($d['video_gallery_index'] as $i => $raw) {
                $url = $raw['embed_url'] ?? null;
                $r = null;
                $new = false;
                if ($url && VideoUrl::embed($url)) {
                    $r = Video::firstOrNew(['video_url' => $url]);
                    $new = ! $r->exists;
                    if ($new) {
                        $r->fill(['title' => $raw['title_as_displayed'], 'slug' => 'legacy-'.Str::slug($raw['title_as_displayed']).'-'.substr(hash('sha256', $url), 0, 8), 'is_published' => false, 'sort_order' => 100 + $i])->save();
                    }
                }$entry('/video_gallery_index/'.$i, 'videos', $raw, $r, 'Playback/identity unverified. Missing URLs stay in review; truncated original titles are not expanded.', $new);
            }
            foreach ($d['coaching_tips_homepage_titles'] as $i => $raw) {
                $r = CoachingTip::firstOrNew(['slug' => 'legacy-'.Str::slug($raw)]);
                $new = ! $r->exists;
                if ($new) {
                    $r->fill(['title' => $raw, 'source_url' => $d['source_pages']['home'], 'is_published' => false, 'sort_order' => 100 + $i])->save();
                }$entry('/coaching_tips_homepage_titles/'.$i, 'coaching', $raw, $r, 'Title-only draft: no advice/body invented; possible legacy template text.', $new);
            }
            foreach (['social_works', 'sponsors', 'in_the_bag'] as $section) {
                if (array_key_exists($section, $d)) {
                    $entry('/'.$section, $section, $d[$section], null, 'Insufficient original entity details/media; repeated counts are not independent verified records.');
                }
            }
            $b->update(['summary' => $counts + ['entries' => $b->entries()->count(), 'category_id' => $category->id, 'category_created' => $category->wasRecentlyCreated, 'tournament_results_created' => 0, 'remote_media_downloaded' => 0]]);

            return $b;
        });
    }
}
