# Current audit update - supplied partial JSON

The user-supplied JSON has now been retained in full and applied as drafts where model fields are sufficient: 30 new draft content records, one unpublished review category and 71 source entries. Original JSON byte hash, all source values/list order and all 71 expected entries verified; no source entry or mapped target missing. Twenty earlier content rows were compared with the restored backup and are unchanged.

The supplied file adds 15 video index occurrences (one URL, 14 missing), a HeForShe caption/repeated-item count and profile/coaching metadata. These are user-supplied partial claims, not newly certified historical facts. Six repeated social items are not assumed to be six verified projects. All 16 photo caption occurrences remain separate review entries because no image URLs/binaries were provided. Birthplace remains disputed. Read CONTENT-MIGRATION-REPORT.md for the current mapping and missing assets.

The earlier 54-row web-observation audit follows as **historical context**, not the current import count. The two published unsupported local claims were preserved, not endorsed. Shared source-page URLs in the post-import audit are provenance groups, not proof of duplicate events/articles/tips.

---

# Legacy Content Audit - Phase 5.1

Reviewed 2026-11-04. This is a **partial accessible-source census**, not a complete production export. No content was imported or published in Phase 5. There are **54 candidate occurrences**, including captions and composite highlight rows; these are not 54 verified, unique, import-ready records. Full legacy totals remain unknown.

## Coverage

| Area | Observed candidates / status | Target |
| --- | --- | --- |
| Homepage and Welcome | 1 welcome block; profile claims retained in inventory | homepage_sections |
| Biography | 1 narrative page, basic local profile only | biographies |
| Career history | 9 named wins, 5 numbered composite highlights, 4 aggregate claims | achievements, career_milestones, career_statistics |
| Tournament Schedule | 5 event rows | tournaments |
| Tournament Results | No complete player scorecards established; unknown total | tournament_results |
| News and Articles | 10 first-page links; pages 2/3 not enumerated | articles, news_categories |
| Photo Gallery | 16 caption occurrences; files/album hierarchy unknown | photo_albums, photos |
| Video Gallery | Retrieval cache miss; total unknown | videos |
| Sponsors | Page heading only; identities/logos unknown | sponsors |
| Social Works | Retrieval cache miss; total unknown | social_works, social_work_images |
| In The Bag | Heading only; equipment identities unknown | equipment |
| Coaching and Tips | 3 homepage teaser headings, no approved bodies | coaching_tips |
| Contact | Form fields visible, verified recipient/contact/social details unavailable | website_settings |

## Record-level inventory

For every row, media association is **unknown** unless separately noted. No remote media has been downloaded or authorized for reuse. A year in a caption/title is not a publication/capture date. Source observations are not owner approval. Full metadata, dates and conflicts are in database/data/legacy-phase5-index.json, which preserves previous news/media inventories.

| Key | Original title/name (or missing) | Source | Date evidence | Target | Migration / verification |
| --- | --- | --- | --- | --- | --- |
| home-welcome | WELCOME TO Siddikurs' World | [Source](https://siddikurrahman.com/) | Not established | homepage_sections | Not imported; observed, unapproved |
| biography | Biography of Siddikur Rahman | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | Not established | biographies | Not imported; observed, unapproved |
| win-1 | 2010 Brunei Open | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | 2010 | achievements | Not imported; observed, unapproved |
| win-2 | 2013 Hero Indian Open | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | 2013 | achievements | Existing achievement #1; winner/year cross-checked, no new import |
| win-3 | 2011 Negeri Sembilan Masters Invitational, Malaysia | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | 2011 | achievements | Not imported; observed, unapproved |
| win-4 | 2010 American Express Bangladesh Open | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | 2010 | achievements | Not imported; observed, unapproved |
| win-5 | 2009 Global Green Bangalore Open | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | 2009 | achievements | Not imported; observed, unapproved |
| win-6 | 2008 HUDA-GTPL â€“ Unitech Haryana Open | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | 2008 | achievements | Not imported; observed, unapproved |
| win-7 | 2008 PGTI Players Championship at Poona Golf Club | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | 2008 | achievements | Not imported; observed, unapproved |
| win-8 | 2018 City Bank American Express Dhaka Open | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | 2018 | achievements | Not imported; observed, unapproved |
| win-9 | 2011 Grameenphone Bangladesh Masters | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | 2011 | achievements | Not imported; observed, unapproved |
| career-highlight-1 | No title; numbered source row | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | Not established | career_milestones | Not imported; observed, unapproved |
| career-highlight-2 | No title; numbered source row | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | Not established | career_milestones | Not imported; observed, unapproved |
| career-highlight-3 | No title; numbered source row | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | Not established | career_milestones | Not imported; observed, unapproved |
| career-highlight-4 | No title; numbered source row | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | Not established | career_milestones | Not imported; observed, unapproved |
| career-highlight-5 | No title; numbered source row | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | Not established | career_milestones | Not imported; observed, unapproved |
| metric-16 | Asian Tour Victories | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | Not established | career_statistics | Not imported; observed, unapproved |
| metric-17 | Asian Development Tour victories | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | Not established | career_statistics | Not imported; observed, unapproved |
| metric-18 | Amateur events in Asia | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | Not established | career_statistics | Not imported; observed, unapproved |
| metric-19 | Geographic win breakdown | [Source](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) | Not established | career_statistics | Not imported; observed, unapproved |
| schedule-1 | Asia-Pacific Open Diamond Cup | [Source](https://siddikurrahman.com/Schedule-of-Bangladeshi-Golfer) | 2019-05-09 to 2019-05-12 (event dates) | tournaments | Not imported; observed, unapproved |
| schedule-2 | The 38th GS Caltex Maekyung Open Golf Championship | [Source](https://siddikurrahman.com/Schedule-of-Bangladeshi-Golfer) | 2019-05-02 to 2019-05-05 (event dates) | tournaments | Not imported; observed, unapproved |
| schedule-3 | Bangabandhu Cup Golf Open 2019 | [Source](https://siddikurrahman.com/Schedule-of-Bangladeshi-Golfer) | 2019-04-03 to 2019-04-06 (event dates) | tournaments | Not imported; observed, unapproved |
| schedule-4 | Hero Indian Open | [Source](https://siddikurrahman.com/Schedule-of-Bangladeshi-Golfer) | 2019-03-28 to 2019-03-31 (event dates) | tournaments | Not imported; observed, unapproved |
| schedule-5 | Maybank Championship | [Source](https://siddikurrahman.com/Schedule-of-Bangladeshi-Golfer) | 2019-03-21 to 2019-03-24 (event dates) | tournaments | Not imported; observed, unapproved |
| news-1 | Live Score For Hero Indian Open 2018 | [Source](https://www.asiantour.com/results/Hero-Indian-Open-2018-t1070/1) | 2018-03-08 | articles | Not imported; observed, unapproved |
| news-2 | Live Score for Panasonic OPEN Japan | [Source](https://www.asiantour.com/tournaments/577/leaderboard/) | 2017-09-06 | articles | Not imported; observed, unapproved |
| news-3 | kalerkantho _ 19 June 2017 | [Source](https://www.kalerkantho.com/print-edition/sports/2017/06/19/510608) | 2017-06-19 | articles | Not imported; observed, unapproved |
| news-4 | bdnews24 _ 19 June 2017 | [Source](https://bangla.bdnews24.com/sport/article1351979.bdnews) | 2017-06-19 | articles | Not imported; observed, unapproved |
| news-5 | Alokitobangladesh _19 June 2017 | [Source](https://www.alokitobangladesh.com/todays/details/223533/2017/06/19) | 2017-06-19 | articles | Not imported; observed, unapproved |
| news-6 | Tthedailystar _ 19 June 2017 | [Source](https://www.thedailystar.net/sports/golf/tame-finish-siddikur-1422088) | 2017-06-19 | articles | Not imported; observed, unapproved |
| news-7 | thefinancialexpress _ 15 JUne 2017 after-1st-round | [Source](https://www.thefinancialexpress-bd.com/2017/06/15/73953/Siddikur-shares-12th-slot-after-1st-round) | 2017-06-15 | articles | Not imported; observed, unapproved |
| news-8 | Kalerkontho_ 01 May 2017 | [Source](https://www.kalerkantho.com/print-edition/sports/2017/05/01/492753) | 2017-05-01 | articles | Not imported; observed, unapproved |
| news-9 | Prothom Alo_ 01 May 2017 | [Source](https://www.thefinancialexpress-bd.com/2017/06/15/73953/Siddikur-shares-12th-slot-after-1st-round%09) | 2017-05-01 | articles | Not imported; observed, unapproved |
| news-10 | The Daily Star_ 01 May 2017 | [Source](https://www.thedailystar.net/sports/golf/siddikur-climbs-28th-1398556) | 2017-05-01 | articles | Not imported; observed, unapproved |
| photo-1 | Website Launching | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-2 | Website Launching | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-3 | Olympic Photo | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-4 | Rio 2016 | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-5 | Rio 2016 | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-6 | Rio 2016 | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-7 | CTG Open 2017 | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-8 | Olympic Photo Session | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-9 | Golfer Siddikur Rahman | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-10 | putting action | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-11 | 2nd Asian Tour BD Open | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-12 | At Olympia_2016_Rio | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-13 | Hero Indian Open 2018 | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-14 | at Panasonic Open 2018 | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-15 | Panasonic Open 2018 | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| photo-16 | With trophy of Panasonic Open India 2018 | [Source](https://siddikurrahman.com/Photo-Gallery-of-Golfer-Siddikur-Rahman) | Not established | photos | Not imported; observed, unapproved |
| coaching-1 | TIPS FOR IMPROVING YOUR SERVE | [Source](https://siddikurrahman.com/) | Not established | coaching_tips | Not imported; observed, unapproved |
| coaching-2 | WISH LIST: HOW TO FIX A SLICE | [Source](https://siddikurrahman.com/) | Not established | coaching_tips | Not imported; observed, unapproved |
| coaching-3 | WAYS TO IMPROVE YOUR SHORT GAME | [Source](https://siddikurrahman.com/) | Not established | coaching_tips | Not imported; observed, unapproved |

## Retrieval gaps

- **Tournament Results**: No complete player result rows retrieved; result links alone are insufficient. [Legacy source](https://siddikurrahman.com/NewsAndResult-of-Siddikur-Rahman).
- **Video Gallery**: Cache miss; titles/provider URLs unknown. [Legacy source](https://siddikurrahman.com/Videos-of-Worldclass-Bangladeshi-Golfer).
- **Sponsors**: Page text has no identifiable sponsor entities/logos. [Legacy source](https://siddikurrahman.com/Sponsors-Golfer-Siddikur-Rahman).
- **Social Works**: Cache miss; entities unknown. [Legacy source](https://siddikurrahman.com/SocialWorks-of-Siddikur-Rahman).
- **In The Bag**: Page text has no identifiable equipment records. [Legacy source](https://siddikurrahman.com/InTheBag-Golf-Equipments).
- **Contact Information**: Cache miss; homepage has form fields, no verified recipient/contact/social URLs. [Legacy source](https://siddikurrahman.com/Contact-With-Golfer-Siddikur-Rahman).

## Conflicts and editorial decisions

- [Legacy homepage](https://siddikurrahman.com/) and [biography](https://siddikurrahman.com/BIO-of-Golfer-Siddikur-Rahman) give different birthplaces (Dhaka versus Madaripur). Preserve both claims privately; request owner-approved birthplace. Do not fill the current null field automatically. Fixed age 32 is stale; career earnings 1,204 lacks a reliable currency/as-of definition. Full-name expansion, birthday, height/weight, college, affiliations and family details require review.
- [Legacy schedule](https://siddikurrahman.com/Schedule-of-Bangladeshi-Golfer) uses explicit From/To headers. The homepage displays dates in the opposite visual order. Map from schedule headers, not homepage position. Event purse is not the player's prize money. Bangabandhu Cup and Hero Indian Open share a legacy destination link; official URLs need review. No round scores, results, current status or winnings were inferred.
- News row 9 has a publisher/domain mismatch and an encoded tab. Its normalized URL appears related to row 7: **one possible duplicate pair**, not a proven duplicate article. News row 10's [publisher page](https://www.thedailystar.net/sports/golf/siddikur-climbs-28th-1398556) displays 30 April 2017 publication / 4 May update, while the index says 1 May. Keep the dates distinct; no article body was copied. Full-body and photo reuse permission is not established.
- Repeated photo labels form two caption groups (Website Launching twice and Rio 2016 three times). Do not deduplicate photographs by captions. Captioned trophies do not independently prove event victories.
- Coaching teaser wording may include template copy, particularly the serve heading. Do not attribute it as approved golfer advice without an owner-reviewed original.

## Current local content (not newly imported)

| Table | Rows | Published where applicable |
| --- | --- | --- |
| homepage_sections | 10 | not applicable |
| biographies | 1 | 1 |
| career_milestones | 1 | 1 |
| achievements | 2 | 2 |
| career_statistics | 1 | 0 |
| tournaments | 0 | 0 |
| tournament_results | 0 | 0 |
| news_categories | 5 | 5 |
| articles | 0 | 0 |
| photo_albums | 0 | 0 |
| photos | 0 | 0 |
| videos | 0 | 0 |
| sponsors | 0 | 0 |
| social_works | 0 | 0 |
| social_work_images | 0 | not applicable |
| equipment | 0 | 0 |
| coaching_tips | 0 | 0 |
| website_settings | 0 | not applicable |

The published basic biography is owner-supplied identity/profession/nationality, not a completed historical biography. Existing achievement #1 (2013 Hero Indian Open championship) matches the [Indian Golf Union winners table](https://indiangolfunion.org/india-open/); it was already seeded before this phase. Existing milestone #1 (World Champion) and achievement #2 have no credible verification note; do not treat them as migrated/verified. Statistic #1 is an unverified draft. Existing records have been preserved without deleting or changing publication. Owner review/unpublication of unsupported claims is a launch prerequisite.

One existing JPEG (187,042 bytes) is linked to milestone #1; identity, event association and usage rights are unverified. Its private audit/backup preserves its path and SHA-256, not an inferred caption. Ten homepage records are CMS/concept content; five news categories are proposed taxonomy, not migrated historical articles. Detected database natural-key/source duplicate groups: 0 under the configured checks; semantic/event duplicates still require review.

## Field mapping / import gate

| Legacy evidence | Target | Safety decision |
| --- | --- | --- |
| Welcome headings/text | homepage_sections section_key introduction/hero | Merge only after owner review; do not overwrite approved design copy blindly |
| Profile/narrative | biographies full_name, date_of_birth, birthplace, professional_start_year, full_biography, additional_details, source_note | Singleton merge with field-level conflict decisions; retain existing identity |
| Named confirmed honour | achievements title/category/year/event_name/source_note | Check existing 2013 record; year only when exact date unknown |
| Chronological highlight | career_milestones title/year/event_date/description/source_note | Split composite rows manually; avoid duplicated honours/results |
| Verified aggregate | career_statistics label/value/source_note | Require definition, as-of date and amateur/pro scope; never infer totals |
| Schedule row | tournaments name/venue/country/start_date/end_date/source_url | Source page plus stable legacy ID; verify official URL/status; no generated results |
| Player scorecard | tournament_results tournament_id/nullable rounds/status/source_url | Resolve event first, one result per event; purse never maps to player prize_amount |
| News link and approved article | articles + news_category_id | Require authorized body/accurate date; retain publisher source; no invented text |
| Album and original file | photo_albums/photos | Resolve original album/photo IDs; validate bytes, MIME, dimensions, alt/credits/rights |
| Provider link | videos video_url | Validate existing supported YouTube/Vimeo URLs, no arbitrary iframe |
| Sponsor/project/equipment/tip | matching dedicated table + child images | Verify identities/dates/rights; do not derive a relationship from an unlabelled logo |
| Public contact/social data | website_settings | Owner-approved current details only; never import old inbox/private contacts as public data |

No original SQL export, media archive or signed record manifest is available. Stop before applying imports. Request a content-only SQL export with schema/stable IDs/relationships, the original uploads archive and filename-to-record mapping, approved video/contact details and reuse permissions. Exclude credentials, private contact messages and unrelated personal data from the supplied migration copy.
