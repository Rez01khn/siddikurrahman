<?php

use App\Models\Achievement;
use App\Models\CareerMilestone;
use App\Models\CareerStatistic;

return [
    'modules' => [
        'milestones' => ['label' => 'Career Milestones', 'model' => CareerMilestone::class],
        'achievements' => ['label' => 'Career Achievements', 'model' => Achievement::class],
        'statistics' => ['label' => 'Career Statistics', 'model' => CareerStatistic::class],
    ],
    'categories' => [
        'tour_victory' => 'Tour victory',
        'representation' => 'International representation',
        'award' => 'Award',
        'career_highlight' => 'Career highlight',
        'other' => 'Other verified achievement',
    ],
];
