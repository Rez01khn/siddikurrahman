<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Biography;
use App\Models\CoachingTip;
use App\Models\HomepageSection;
use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Video;
use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        User::updateOrCreate(
            ['email' => 'admin@siddikurrahman.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('12345'),
                'is_admin' => true,
                'is_active' => true,
            ]
        );

        // 2. Website Settings
        WebsiteSetting::updateOrCreate(
            ['id' => 1],
            [
                'site_name' => "Siddikur's World",
                'contact_email' => 'contact@siddikurrahman.com',
                'contact_phone' => '+880 1700-000000',
                'contact_address' => 'Kurmitola Golf Club, Dhaka, Bangladesh',
                'footer_text' => 'Official website of Mohammad Siddikur Rahman. Representing Bangladesh on the global golf stage.',
                'seo_title' => 'Mohammad Siddikur Rahman · Official Website & Portfolio',
                'seo_description' => 'Official website of Bangladeshi professional golfer Mohammad Siddikur Rahman. Discover achievements, schedule, tips, gallery and biography.',
                'social_links' => [
                    'facebook' => 'https://www.facebook.com/',
                    'youtube' => 'https://www.youtube.com/',
                    'linkedin' => 'https://www.linkedin.com/',
                    'instagram' => 'https://www.instagram.com/',
                ],
            ]
        );

        // 3. Homepage Sections (Hero, Career Highlights Card, Beyond Banner)
        foreach (config('homepage') as $key => $config) {
            HomepageSection::updateOrCreate(
                ['section_key' => $key],
                array_merge($config['defaults'], [
                    'is_active' => true,
                    'sort_order' => $config['defaults']['sort_order'] ?? 0,
                ])
            );
        }

        // 4. Biography
        Biography::updateOrCreate(
            ['id' => 1],
            [
                'full_name' => 'Mohammad Siddikur Rahman',
                'professional_title' => 'GOLFER • CHAMPION • INSPIRATION',
                'short_introduction' => "Worked as a ball boy at Kurmitola Golf Club in Bangladesh to support his studies. Made his first club with a broken screw into a bed sheet on a metal rod. Was picked from over 1,010 kids to train by his club and represented his country in the Asian Games. He currently attached to Kurmitola Golf Club as an honorary member.\n\nEnjoyed his best result in 2017 at the Bashundhara Bangladesh Open where he finished second. He was also runner-up at the 2016 Panasonic Open India. Ended his Asian Tour victory drought with a thrilling victory in 2013 at the Hero Indian Open.",
                'full_biography' => "Worked as a ball boy at Kurmitola Golf Club in Bangladesh to support his studies. Made his first club with a broken screw into a bed sheet on a metal rod. Was picked from over 1,010 kids to train by his club and represented his country in the Asian Games. He currently attached to Kurmitola Golf Club as an honorary member.\n\nEnjoyed his best result in 2017 at the Bashundhara Bangladesh Open where he finished second. He was also runner-up at the 2016 Panasonic Open India. Ended his Asian Tour victory drought with a thrilling victory in 2013 at the Hero Indian Open.\n\nSiddikur became the first Bangladeshi ever to qualify for the Olympic Games after finishing 56th in the final eligibility list for the Rio 2016 Games. He previously had a wild card selection for the 2016 Olympics, when he finished 47th.\n\nAs a new born player from a different segment of sports, he gained more than we could think. As an amateur, Siddikur won twelve events in Asia, winning five times in Bangladesh, two in Pakistan, Sri Lanka, and Nepal and one in India.",
                'date_of_birth' => '1984-11-20',
                'birthplace' => 'Dhaka, Bangladesh',
                'nationality' => 'Bangladeshi',
                'professional_start_year' => 2005,
                'additional_details' => [
                    'profile_info' => [
                        'age' => '32',
                        'career_earnings' => '$1,204,000+',
                        'family' => 'Wife & Family',
                        'height' => '5 ft 5 in',
                        'weight' => '152 lb',
                        'college' => 'Dhaka College',
                        'tour_status' => 'Asian Tour Member',
                        'turned_pro_label' => '2005 (Asian Tour)',
                    ],
                    'asian_tour_victories' => "2011 Singapore Open\n2013 Hero Indian Open",
                    'other_wins' => "2011 Master Invitational, Malaysia\n2010 American Express Bangladesh Open\n2009 Global Green Bangladesh Open\n2008 BDOA Open – United Haryana Open\n2008 PGTI Players Championship at Poona Golf Club",
                    'wins_summary' => 'Five wins in Bangladesh, two each in Pakistan, Sri Lanka and Nepal, one in India',
                    'remarkable_journey_quote' => 'Siddikur became the first Bangladeshi ever to qualify for the Olympic Games after finishing 56th in the final eligibility list for the Rio 2016 Games. He previously had a wild card selection for the 2016 Olympics, when he finished 47th.',
                    'remarkable_journey_narrative' => 'As a new born player from a different segment of sports, he gained more than we could think. As an amateur, Siddikur won twelve events in Asia, winning five times in Bangladesh, two in Pakistan, Sri Lanka, and Nepal and one in India.',
                ],
                'is_published' => true,
            ]
        );

        // 5. Key Achievements
        $achievements = [
            [
                'title' => "Panasonic Open\nIndia",
                'year' => 2013,
                'category' => 'Asian Tour Victory',
                'description' => 'Historic Asian Tour Victory in New Delhi.',
                'image_alt' => 'Mohammad Siddikur Rahman celebrating victory',
                'sort_order' => 1,
            ],
            [
                'title' => "Hero Indian\nOpen",
                'year' => 2010,
                'category' => 'Historic Asian Tour Title',
                'description' => 'First Bangladeshi golfer to win on the Asian Tour.',
                'image_alt' => 'Siddikur Rahman powerful golf swing',
                'sort_order' => 2,
            ],
            [
                'title' => "Rio Olympics\nFlag Bearer",
                'year' => 2016,
                'category' => 'Historic Milestone',
                'description' => 'First Bangladeshi athlete to qualify directly for the Olympic Games.',
                'image_alt' => 'Rio 2016 Olympic Golf representation',
                'sort_order' => 3,
            ],
            [
                'title' => "30+ Professional\nTitles",
                'year' => 2005,
                'category' => 'National Record',
                'description' => 'Domestic & Regional Dominance across Asian Circuits.',
                'image_alt' => 'Career golf trophy presentation',
                'sort_order' => 4,
            ],
            [
                'title' => 'Master Invitational Malaysia Champion',
                'year' => 2011,
                'category' => 'Asian Tour',
                'description' => 'Captured the prestigious Master Invitational title in Malaysia with an exceptional display of putting and precision driving.',
                'image_alt' => 'Siddikur Rahman holding the Master Invitational trophy',
                'sort_order' => 5,
            ],
            [
                'title' => 'American Express Bangladesh Open Winner',
                'year' => 2010,
                'category' => 'Major Win',
                'description' => 'A memorable triumph on home soil at Kurmitola Golf Club, thrilling thousands of local supporters.',
                'image_alt' => 'Siddikur Rahman triumphant at Kurmitola Golf Club',
                'sort_order' => 6,
            ],
            [
                'title' => 'PGTI Players Championship Winner',
                'year' => 2008,
                'category' => 'Major Win',
                'description' => 'Clinched the PGTI Players Championship title at Poona Golf Club with a masterful final round.',
                'image_alt' => 'Siddikur Rahman with trophy at Poona Golf Club',
                'sort_order' => 7,
            ],
            [
                'title' => 'AfrAsia Bank Mauritius Open Runner-Up',
                'year' => 2016,
                'category' => 'Asian Tour',
                'description' => 'A scintillating runner-up finish on the tri-sanctioned European Tour, Asian Tour, and Sunshine Tour stage.',
                'image_alt' => 'Siddikur Rahman playing at Mauritius Open',
                'sort_order' => 8,
            ],
        ];
        foreach ($achievements as $ach) {
            Achievement::updateOrCreate(
                ['title' => $ach['title']],
                array_merge($ach, ['is_published' => true, 'is_featured' => true])
            );
        }

        // 6. Photo Album & Photos
        $album = PhotoAlbum::updateOrCreate(
            ['slug' => 'moments-on-the-course'],
            [
                'title' => 'Moments on the Course',
                'description' => 'Moments from international tournaments and course action.',
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        $photos = [
            [
                'caption' => 'Asian Tour Championship Swing',
                'alt_text' => 'Asian Tour tournament swing at Delhi Golf Club',
                'photographer_credit' => 'Delhi Golf Club',
                'image' => 'images/portfolio/gallery-1.jpg',
                'sort_order' => 1,
            ],
            [
                'caption' => '18th Hole Strategic Focus',
                'alt_text' => 'Fairway discussion with caddie at Hero Indian Open',
                'photographer_credit' => 'Hero Indian Open',
                'image' => 'images/portfolio/gallery-3.jpg',
                'sort_order' => 2,
            ],
            [
                'caption' => 'Signature Driver Swing',
                'alt_text' => 'Iconic power driver swing at Kurmitola Golf Club',
                'photographer_credit' => 'Kurmitola Golf Club',
                'image' => 'images/portfolio/hero-golfer.jpg',
                'sort_order' => 3,
            ],
            [
                'caption' => 'Precision Fairway Wood',
                'alt_text' => 'Precision Fairway Wood Shot during Asian Tour Event',
                'photographer_credit' => 'Asian Tour Event',
                'image' => 'images/portfolio/siddikur_29092023_9851_ati-Picsart-AiImageEnhancer.jpg',
                'sort_order' => 4,
            ],
        ];
        foreach ($photos as $p) {
            Photo::updateOrCreate(
                ['photo_album_id' => $album->id, 'caption' => $p['caption']],
                array_merge($p, ['is_published' => true])
            );
        }

        // 7. Videos
        $videos = [
            [
                'title' => 'Championship Victory Moment',
                'slug' => 'championship-victory-moment',
                'description' => '01:45 • Panasonic Open India',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'image' => 'images/portfolio/gallery-2.jpg',
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'title' => 'Iron Approach Shot Analysis',
                'slug' => 'iron-approach-shot-analysis',
                'description' => '02:20 • Olympic Games Rio 2016',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'image' => 'images/portfolio/gallery-4.jpg',
                'sort_order' => 2,
                'is_published' => true,
            ],
        ];
        foreach ($videos as $v) {
            Video::updateOrCreate(['slug' => $v['slug']], $v);
        }

        // 8. Coaching Tips
        $coachingTips = [
            [
                'title' => 'TIPS FOR IMPROVING YOUR SERVE',
                'slug' => 'tips-for-improving-your-serve',
                'category' => 'COACHING & TIPS, TECHNICS',
                'excerpt' => 'Improve precision and power with fundamental posture adjustments.',
                'description' => 'Improve precision and power with fundamental posture adjustments. Master your grip and release on full driver shots.',
                'sort_order' => 1,
            ],
            [
                'title' => 'WISH LIST: HOW TO FIX A SLICE',
                'slug' => 'wish-list-how-to-fix-a-slice',
                'category' => 'COACHING & TIPS, FULL SWING, TECHNICS',
                'excerpt' => 'Comprehensive guide to clubhead path and face control to eliminate unwanted slice.',
                'description' => 'Comprehensive guide to clubhead path and face control to eliminate unwanted slice. Learn swing plane alignments.',
                'sort_order' => 2,
            ],
            [
                'title' => 'WAYS TO IMPROVE YOUR SHORT GAME',
                'slug' => 'ways-to-improve-your-short-game',
                'category' => 'COACHING & TIPS, SHORT GAME',
                'excerpt' => 'Chipping, pitching and putting drills honed over 15+ years on international professional tours.',
                'description' => 'Chipping, pitching and putting drills honed over 15+ years on international professional tours. Save strokes around the green.',
                'sort_order' => 3,
            ],
        ];
        foreach ($coachingTips as $tip) {
            CoachingTip::updateOrCreate(
                ['slug' => $tip['slug']],
                array_merge($tip, ['is_published' => true])
            );
        }

        // 9. Tournament Schedule
        $tournaments = [
            [
                'name' => 'Asia-Pacific Open Diamond Cup',
                'tour' => 'Asian Tour & JGTO',
                'venue' => 'Sobu Country Club',
                'city' => 'Chiba',
                'country' => 'Japan',
                'start_date' => '2027-05-09',
                'end_date' => '2027-05-12',
                'status' => 'upcoming',
                'sort_order' => 1,
            ],
            [
                'name' => 'The 38th GS Caltex Maekyung Open Golf Championship',
                'tour' => 'Asian Tour & KGT',
                'venue' => 'Namseoul Country Club',
                'city' => 'Seoul',
                'country' => 'South Korea',
                'start_date' => '2027-05-02',
                'end_date' => '2027-05-05',
                'status' => 'upcoming',
                'sort_order' => 2,
            ],
            [
                'name' => 'Bangabandhu Cup Golf Open',
                'tour' => 'Asian Tour Flagship',
                'venue' => 'Kurmitola Golf Club',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'start_date' => '2027-04-03',
                'end_date' => '2027-04-06',
                'status' => 'upcoming',
                'sort_order' => 3,
            ],
            [
                'name' => 'Hero Indian Open',
                'tour' => 'DP World Tour & Asian Tour',
                'venue' => 'DLF Golf & CC',
                'city' => 'New Delhi',
                'country' => 'India',
                'start_date' => '2027-03-28',
                'end_date' => '2027-03-31',
                'status' => 'upcoming',
                'sort_order' => 4,
            ],
            [
                'name' => 'Maybank Championship',
                'tour' => 'European Tour & Asian Tour',
                'venue' => 'Saujana Golf & CC',
                'city' => 'Kuala Lumpur',
                'country' => 'Malaysia',
                'start_date' => '2027-03-21',
                'end_date' => '2027-03-24',
                'status' => 'upcoming',
                'sort_order' => 5,
            ],
        ];
        foreach ($tournaments as $t) {
            Tournament::updateOrCreate(
                ['name' => $t['name']],
                array_merge($t, ['is_published' => true])
            );
        }

        // 10. Sponsors & Partners
        $sponsors = [
            ['name' => 'Bridgestone Golf', 'slug' => 'bridgestone-golf', 'category' => 'Equipment & Apparel', 'sort_order' => 1],
            ['name' => 'Titleist', 'slug' => 'titleist', 'category' => 'Official Ball & Clubs', 'sort_order' => 2],
            ['name' => 'PING', 'slug' => 'ping', 'category' => 'Club Partner', 'sort_order' => 3],
            ['name' => 'Callaway', 'slug' => 'callaway', 'category' => 'Golf Gear', 'sort_order' => 4],
            ['name' => 'FootJoy', 'slug' => 'footjoy', 'category' => 'Footwear & Gloves', 'sort_order' => 5],
            ['name' => 'Srixon', 'slug' => 'srixon', 'category' => 'Tour Equipment', 'sort_order' => 6],
        ];
        foreach ($sponsors as $s) {
            Sponsor::updateOrCreate(
                ['slug' => $s['slug']],
                array_merge($s, ['is_published' => true])
            );
        }
    }
}
