<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Visual Theme & Color Palette
    |--------------------------------------------------------------------------
    */
    'colors' => [
        'forest' => '#0D2216',        // Deep Forest Green (Header, Hero, Gallery, Contact, Footer)
        'forest_deep' => '#0A1B11',   // Deepest Footer Green
        'forest_panel' => '#18291D',  // Contact Section Dark Green
        'forest_gallery' => '#132519', // Gallery Section Dark Green
        'accent_green' => '#14422B',  // Primary Green Buttons & Accents
        'dark_btn' => '#122A1D',      // Dark Green Pill Buttons on Light Sections
        'cream' => '#F7F5EE',         // Warm Off-White / Cream (Highlights, Achievements, Sponsors)
        'cream_about' => '#F1EDE3',   // Warm Cream (About Me)
        'cream_banner' => '#F5F1E9',  // Lifestyle Banner Cream (Beyond the Course)
        'gold' => '#C5A47E',          // Accent / Gold (Buttons, accents, subtle borders)
        'charcoal' => '#1E1E1E',      // Primary Dark Text on Light Sections
        'white' => '#FFFFFF',         // Primary Light Text on Dark Sections
    ],

    /*
    |--------------------------------------------------------------------------
    | Typography & Font Families
    |--------------------------------------------------------------------------
    */
    'fonts' => [
        'google_fonts_url' => 'https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Sacramento&display=swap',
        'heading' => ['Playfair Display', 'Cormorant Garamond', 'Georgia', 'serif'],
        'body' => ['Plus Jakarta Sans', 'Inter', 'system-ui', 'sans-serif'],
        'signature' => ['Sacramento', 'Alex Brush', 'cursive'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation & Social Links
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        ['label' => 'Home', 'href' => '/', 'active' => true],
        ['label' => 'About', 'href' => '/about', 'active' => false],
        ['label' => 'Career', 'href' => '/#career-highlights', 'active' => false],
        ['label' => 'Gallery', 'href' => '/#gallery', 'active' => false],
        ['label' => 'Coaching', 'href' => '/#coaching', 'active' => false],
        ['label' => 'Schedule', 'href' => '/#schedule', 'active' => false],
        ['label' => 'Sponsors', 'href' => '/#sponsors', 'active' => false],
        ['label' => 'Contact', 'href' => '/#contact-section', 'active' => false],
    ],

    'socials' => [
        'facebook' => 'https://www.facebook.com/',
        'youtube' => 'https://www.youtube.com/',
        'linkedin' => 'https://www.linkedin.com/',
        'instagram' => 'https://www.instagram.com/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Personal Info (About Section)
    |--------------------------------------------------------------------------
    */
    'personal_info' => [
        'full_name' => 'Mohammad Siddikur Rahman',
        'dob' => '20 November 1984',
        'age' => '32',
        'career_earnings' => '$1,204,000+',
        'family' => 'Wife & Family',
        'height' => '5 ft 5 in',
        'weight' => '152 lb',
        'college' => 'Dhaka College',
        'location' => 'Dhaka, Bangladesh',
        'turned_pro' => '2005',
    ],

    /*
    |--------------------------------------------------------------------------
    | Key Achievements Cards
    |--------------------------------------------------------------------------
    */
    'achievements' => [
        [
            'image' => 'images/portfolio/achievement-1.jpg',
            'title' => "Panasonic Open\nIndia",
            'year' => '2013 & 2018',
            'badge' => 'Asian Tour Victory',
            'alt' => 'Mohammad Siddikur Rahman celebrating victory',
        ],
        [
            'image' => 'images/portfolio/achievement-2.jpg',
            'title' => "Hero Indian\nOpen",
            'year' => '2010 Champion',
            'badge' => 'Historic Asian Tour Title',
            'alt' => 'Siddikur Rahman powerful golf swing',
        ],
        [
            'image' => 'images/portfolio/achievement-3.jpg',
            'title' => "Rio Olympics\nFlag Bearer",
            'year' => '2016 Rio',
            'badge' => 'Olympic Games',
            'alt' => 'Siddikur Rahman Rio 2016 Olympic Games',
        ],
        [
            'image' => 'images/portfolio/achievement-4.jpg',
            'title' => "Asian Tour\nMilestone",
            'year' => '2005 - Present',
            'badge' => 'International Tour',
            'alt' => 'Siddikur Rahman fairway action shot',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gallery Images
    |--------------------------------------------------------------------------
    */
    'gallery' => [
        'tall_left' => [
            'image' => 'images/portfolio/gallery-1.jpg',
            'caption' => 'Siddikur Rahman — Tournament swing follow-through',
            'alt' => 'Siddikur Rahman in blue polo shirt mid-swing',
        ],
        'middle_top' => [
            'image' => 'images/portfolio/gallery-2.jpg',
            'caption' => 'Siddikur Rahman — Iron shot on the course',
            'alt' => 'Siddikur Rahman playing iron shot in red polo',
        ],
        'middle_bottom' => [
            'image' => 'images/portfolio/gallery-3.jpg',
            'caption' => 'Siddikur Rahman — Fairway drive follow-through',
            'alt' => 'Siddikur Rahman in white polo full swing',
        ],
        'tall_right' => [
            'image' => 'images/portfolio/gallery-4.jpg',
            'caption' => 'Siddikur Rahman — Walking the fairway at golden hour',
            'alt' => 'Siddikur Rahman walking down sunlit fairway carrying golf bag',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tournament Schedule
    |--------------------------------------------------------------------------
    */
    'schedule' => [
        [
            'name' => 'Asia-Pacific Open Diamond Cup',
            'tour' => 'Asian Tour & JGTO',
            'location' => 'Sobu Country Club, Chiba, Japan',
            'date_range' => '09 May – 12 May 2019',
        ],
        [
            'name' => 'The 38th GS Caltex Maekyung Open Golf Championship',
            'tour' => 'Asian Tour & KGT',
            'location' => 'Namseoul Country Club, Seoul, South Korea',
            'date_range' => '02 May – 05 May 2019',
        ],
        [
            'name' => 'Bangabandhu Cup Golf Open 2019',
            'tour' => 'Asian Tour Flagship',
            'location' => 'Kurmitola Golf Club, Dhaka, Bangladesh',
            'date_range' => '03 Apr – 06 Apr 2019',
        ],
        [
            'name' => 'Hero Indian Open',
            'tour' => 'DP World Tour & Asian Tour',
            'location' => 'DLF Golf & CC, New Delhi, India',
            'date_range' => '28 Mar – 31 Mar 2019',
        ],
        [
            'name' => 'Maybank Championship',
            'tour' => 'European Tour & Asian Tour',
            'location' => 'Saujana Golf & CC, Kuala Lumpur, Malaysia',
            'date_range' => '21 Mar – 24 Mar 2019',
        ],
    ],
];
