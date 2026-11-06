<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Navigation by Public Pages
    |--------------------------------------------------------------------------
    */
    'pages' => [
        'homepage' => [
            'label' => 'Homepage',
            'hub_route' => 'admin.pages.home',
            'badge' => 'Home',
            'items' => [
                [
                    'label' => 'Overview & Sections',
                    'route' => 'admin.pages.home',
                    'active' => ['admin.pages.home'],
                ],
                [
                    'label' => 'Hero & Banners',
                    'route' => 'admin.homepage.index',
                    'active' => ['admin.homepage.*'],
                ],
                [
                    'label' => 'Tournaments',
                    'route' => 'admin.tournaments.index',
                    'active' => ['admin.tournaments.*'],
                ],
                [
                    'label' => 'Coaching Tips',
                    'route' => 'admin.coaching.index',
                    'active' => ['admin.coaching.*'],
                ],
                [
                    'label' => 'Photo Gallery',
                    'route' => 'admin.photos.index',
                    'active' => ['admin.photos.*', 'admin.photo-albums.*'],
                ],
                [
                    'label' => 'Video Gallery',
                    'route' => 'admin.videos.index',
                    'active' => ['admin.videos.*'],
                ],
                [
                    'label' => 'Sponsors & Partners',
                    'route' => 'admin.sponsors.index',
                    'active' => ['admin.sponsors.*'],
                ],
            ],
        ],
        'about' => [
            'label' => 'About Page',
            'hub_route' => 'admin.pages.about',
            'badge' => 'About',
            'items' => [
                [
                    'label' => 'Overview & Sections',
                    'route' => 'admin.pages.about',
                    'active' => ['admin.pages.about'],
                ],
                [
                    'label' => 'Biography & Story',
                    'route' => 'admin.biography.edit',
                    'active' => ['admin.biography.*'],
                ],
                [
                    'label' => 'Key Achievements',
                    'route' => 'admin.achievements.index',
                    'active' => ['admin.achievements.*'],
                ],
            ],
        ],
    ],

    'general' => [
        [
            'label' => 'Website Settings',
            'route' => 'admin.settings.edit',
            'active' => ['admin.settings.*'],
        ],
        [
            'label' => 'Contact Messages',
            'route' => 'admin.messages.index',
            'active' => ['admin.messages.*'],
        ],
        [
            'label' => 'Admin Users',
            'route' => 'admin.users.index',
            'active' => ['admin.users.*'],
        ],
    ],
];
